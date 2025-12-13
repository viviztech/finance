<?php

namespace Tests\Unit\Services;

use App\Models\Loan;
use App\Models\LoanType;
use App\Models\Customer;
use App\Models\Branch;
use App\Models\User;
use App\Services\ScheduleGeneratorService;
use App\Enums\LoanFrequency;
use App\Enums\LoanStatus;
use App\Enums\InterestType;
use App\Enums\ScheduleStatus;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ScheduleGeneratorServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ScheduleGeneratorService $generator;
    protected Branch $branch;
    protected User $user;
    protected Customer $customer;
    protected LoanType $loanType;

    protected function setUp(): void
    {
        parent::setUp();
        $this->generator = new ScheduleGeneratorService();

        $this->branch = Branch::create([
            'name' => 'Test Branch',
            'code' => 'TB001',
            'is_active' => true,
        ]);

        $this->user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'branch_id' => $this->branch->id,
            'is_active' => true,
        ]);

        $this->customer = Customer::create([
            'branch_id' => $this->branch->id,
            'customer_code' => 'CUS001',
            'name' => 'Test Customer',
            'phone' => '+91 9999999999',
            'address' => '123 Test Street',
            'created_by' => $this->user->id,
            'is_active' => true,
        ]);

        $this->loanType = LoanType::create([
            'name' => 'Daily Loan',
            'frequency' => LoanFrequency::DAILY,
            'interest_type' => InterestType::FIXED,
            'interest_rate' => 100,
            'default_duration' => 10,
            'is_active' => true,
        ]);
    }

    /** @test */
    public function it_generates_correct_number_of_schedules()
    {
        $loan = $this->createLoan(10);

        $this->generator->generate($loan);

        $this->assertCount(10, $loan->schedules);
    }

    /** @test */
    public function it_sets_correct_installment_numbers()
    {
        $loan = $this->createLoan(5);

        $this->generator->generate($loan);

        $installmentNumbers = $loan->schedules()->pluck('installment_number')->toArray();
        $this->assertEquals([1, 2, 3, 4, 5], $installmentNumbers);
    }

    /** @test */
    public function it_generates_daily_schedule_dates()
    {
        $loan = $this->createLoan(5, LoanFrequency::DAILY, '2024-01-01');

        $this->generator->generate($loan);

        $dueDates = $loan->schedules()->orderBy('installment_number')->pluck('due_date')->toArray();

        $this->assertEquals('2024-01-02', Carbon::parse($dueDates[0])->format('Y-m-d'));
        $this->assertEquals('2024-01-03', Carbon::parse($dueDates[1])->format('Y-m-d'));
        $this->assertEquals('2024-01-04', Carbon::parse($dueDates[2])->format('Y-m-d'));
    }

    /** @test */
    public function it_generates_weekly_schedule_dates()
    {
        $weeklyLoanType = LoanType::create([
            'name' => 'Weekly Loan',
            'frequency' => LoanFrequency::WEEKLY,
            'interest_type' => InterestType::FIXED,
            'interest_rate' => 200,
            'default_duration' => 4,
            'is_active' => true,
        ]);

        $loan = $this->createLoan(4, LoanFrequency::WEEKLY, '2024-01-01', $weeklyLoanType);

        $this->generator->generate($loan);

        $dueDates = $loan->schedules()->orderBy('installment_number')->pluck('due_date')->toArray();

        $this->assertEquals('2024-01-08', Carbon::parse($dueDates[0])->format('Y-m-d'));
        $this->assertEquals('2024-01-15', Carbon::parse($dueDates[1])->format('Y-m-d'));
        $this->assertEquals('2024-01-22', Carbon::parse($dueDates[2])->format('Y-m-d'));
    }

    /** @test */
    public function it_generates_monthly_schedule_dates()
    {
        $monthlyLoanType = LoanType::create([
            'name' => 'Monthly Loan',
            'frequency' => LoanFrequency::MONTHLY,
            'interest_type' => InterestType::PERCENTAGE,
            'interest_rate' => 5,
            'default_duration' => 3,
            'is_active' => true,
        ]);

        $loan = $this->createLoan(3, LoanFrequency::MONTHLY, '2024-01-15', $monthlyLoanType);

        $this->generator->generate($loan);

        $dueDates = $loan->schedules()->orderBy('installment_number')->pluck('due_date')->toArray();

        $this->assertEquals('2024-02-15', Carbon::parse($dueDates[0])->format('Y-m-d'));
        $this->assertEquals('2024-03-15', Carbon::parse($dueDates[1])->format('Y-m-d'));
        $this->assertEquals('2024-04-15', Carbon::parse($dueDates[2])->format('Y-m-d'));
    }

    /** @test */
    public function it_sets_all_schedules_to_pending_status()
    {
        $loan = $this->createLoan(5);

        $this->generator->generate($loan);

        $allPending = $loan->schedules->every(fn($s) => $s->status === ScheduleStatus::PENDING);
        $this->assertTrue($allPending);
    }

    /** @test */
    public function it_distributes_amount_across_installments()
    {
        $loan = $this->createLoan(10);
        $loan->update(['total_amount' => 1050]);

        $this->generator->generate($loan);

        $totalDue = $loan->schedules()->sum('amount_due');
        $this->assertEquals(1050, $totalDue);
    }

    /** @test */
    public function it_throws_exception_when_regenerating_with_payments()
    {
        $loan = $this->createLoan(5);
        $this->generator->generate($loan);

        // Simulate payment
        $loan->update(['amount_paid' => 100]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Cannot regenerate schedules');

        $this->generator->regenerate($loan);
    }

    /** @test */
    public function it_calculates_end_date_correctly()
    {
        $startDate = Carbon::parse('2024-01-01');

        $endDate = $this->generator->calculateEndDate($startDate, $this->loanType, 30);

        // Daily loan, 30 installments = 30 days later
        $this->assertEquals('2024-01-31', $endDate->format('Y-m-d'));
    }

    protected function createLoan(
        int $installments = 10,
        LoanFrequency $frequency = LoanFrequency::DAILY,
        string $startDate = '2024-01-01',
        ?LoanType $loanType = null
    ): Loan {
        $loanType = $loanType ?? $this->loanType;

        return Loan::create([
            'branch_id' => $this->branch->id,
            'loan_number' => 'LN' . rand(10000, 99999),
            'customer_id' => $this->customer->id,
            'loan_type_id' => $loanType->id,
            'issued_by' => $this->user->id,
            'principal_amount' => 10000,
            'interest_amount' => $loanType->interest_rate,
            'total_amount' => 10000 + $loanType->interest_rate,
            'amount_paid' => 0,
            'amount_pending' => 10000 + $loanType->interest_rate,
            'penalty_amount' => 0,
            'total_installments' => $installments,
            'paid_installments' => 0,
            'start_date' => $startDate,
            'end_date' => Carbon::parse($startDate)->addDays($installments),
            'status' => LoanStatus::ACTIVE,
        ]);
    }
}
