<?php

namespace Tests\Unit\Services;

use App\Models\Loan;
use App\Models\LoanType;
use App\Models\LoanSchedule;
use App\Models\Customer;
use App\Models\Branch;
use App\Models\User;
use App\Models\Penalty;
use App\Services\PenaltyService;
use App\Enums\LoanFrequency;
use App\Enums\LoanStatus;
use App\Enums\InterestType;
use App\Enums\PenaltyType;
use App\Enums\ScheduleStatus;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PenaltyServiceTest extends TestCase
{
    use RefreshDatabase;

    protected PenaltyService $penaltyService;
    protected Branch $branch;
    protected User $user;
    protected Customer $customer;
    protected LoanType $loanType;

    protected function setUp(): void
    {
        parent::setUp();
        $this->penaltyService = new PenaltyService();

        $this->branch = Branch::create([
            'name' => 'Test Branch',
            'code' => 'TB001',
            'is_active' => true,
        ]);

        $this->user = User::create([
            'name' => 'Test User',
            'email' => 'penalty-test@example.com',
            'password' => bcrypt('password'),
            'branch_id' => $this->branch->id,
            'is_active' => true,
        ]);

        $this->customer = Customer::create([
            'branch_id' => $this->branch->id,
            'customer_code' => 'CUS002',
            'name' => 'Penalty Test Customer',
            'phone' => '+91 8888888888',
            'address' => '456 Test Avenue',
            'created_by' => $this->user->id,
            'is_active' => true,
        ]);

        $this->loanType = LoanType::create([
            'name' => 'Penalty Loan',
            'frequency' => LoanFrequency::DAILY,
            'interest_type' => InterestType::FIXED,
            'interest_rate' => 100,
            'default_duration' => 10,
            'penalty_enabled' => true,
            'penalty_type' => PenaltyType::FIXED,
            'penalty_rate' => 50,
            'grace_period_days' => 1,
            'is_active' => true,
        ]);
    }

    /** @test */
    public function it_applies_fixed_penalty_for_overdue_schedule()
    {
        $loan = $this->createLoan();
        $schedule = $this->createOverdueSchedule($loan, 5); // 5 days overdue

        $penalty = $this->penaltyService->calculateAndApplyPenalty($schedule);

        $this->assertNotNull($penalty);
        $this->assertEquals(50, $penalty->amount); // Fixed penalty amount
        $this->assertFalse($penalty->is_waived);
    }

    /** @test */
    public function it_applies_percentage_penalty_for_overdue_schedule()
    {
        $percentageLoanType = LoanType::create([
            'name' => 'Percentage Penalty Loan',
            'frequency' => LoanFrequency::DAILY,
            'interest_type' => InterestType::FIXED,
            'interest_rate' => 100,
            'default_duration' => 10,
            'penalty_enabled' => true,
            'penalty_type' => PenaltyType::PERCENTAGE,
            'penalty_rate' => 5, // 5%
            'grace_period_days' => 0,
            'is_active' => true,
        ]);

        $loan = $this->createLoan($percentageLoanType);
        $schedule = $this->createOverdueSchedule($loan, 2); // 2 days overdue, amount_due = 1000

        $penalty = $this->penaltyService->calculateAndApplyPenalty($schedule);

        $this->assertNotNull($penalty);
        $this->assertEquals(50, $penalty->amount); // 5% of 1000
    }

    /** @test */
    public function it_respects_grace_period()
    {
        $loan = $this->createLoan();
        // Schedule is only 1 day overdue, grace period is 1 day
        $schedule = $this->createOverdueSchedule($loan, 1);

        $penalty = $this->penaltyService->calculateAndApplyPenalty($schedule);

        $this->assertNull($penalty); // No penalty within grace period
    }

    /** @test */
    public function it_does_not_apply_penalty_when_disabled()
    {
        $noPenaltyLoanType = LoanType::create([
            'name' => 'No Penalty Loan',
            'frequency' => LoanFrequency::DAILY,
            'interest_type' => InterestType::FIXED,
            'interest_rate' => 100,
            'default_duration' => 10,
            'penalty_enabled' => false,
            'is_active' => true,
        ]);

        $loan = $this->createLoan($noPenaltyLoanType);
        $schedule = $this->createOverdueSchedule($loan, 10);

        $penalty = $this->penaltyService->calculateAndApplyPenalty($schedule);

        $this->assertNull($penalty);
    }

    /** @test */
    public function it_does_not_duplicate_penalty_on_same_day()
    {
        $loan = $this->createLoan();
        $schedule = $this->createOverdueSchedule($loan, 5);

        // Apply first penalty
        $penalty1 = $this->penaltyService->calculateAndApplyPenalty($schedule);
        $this->assertNotNull($penalty1);

        // Try to apply again
        $penalty2 = $this->penaltyService->calculateAndApplyPenalty($schedule);
        $this->assertNull($penalty2);

        // Only one penalty exists
        $this->assertEquals(1, Penalty::where('schedule_id', $schedule->id)->count());
    }

    /** @test */
    public function it_waives_penalty_correctly()
    {
        $loan = $this->createLoan();
        $schedule = $this->createOverdueSchedule($loan, 5);

        $penalty = $this->penaltyService->calculateAndApplyPenalty($schedule);
        $this->assertNotNull($penalty);

        // Waive the penalty
        $this->penaltyService->waivePenalty($penalty, $this->user->id, 'Customer hardship');

        $penalty->refresh();
        $this->assertTrue($penalty->is_waived);
        $this->assertEquals($this->user->id, $penalty->waived_by);
        $this->assertEquals('Customer hardship', $penalty->waiver_reason);
        $this->assertNotNull($penalty->waived_at);
    }

    /** @test */
    public function it_updates_schedule_penalty_amount()
    {
        $loan = $this->createLoan();
        $schedule = $this->createOverdueSchedule($loan, 5);

        $initialPenalty = $schedule->penalty_amount;
        $this->penaltyService->calculateAndApplyPenalty($schedule);

        $schedule->refresh();
        $this->assertEquals($initialPenalty + 50, $schedule->penalty_amount);
    }

    /** @test */
    public function it_updates_loan_penalty_amount()
    {
        $loan = $this->createLoan();
        $schedule = $this->createOverdueSchedule($loan, 5);

        $initialPenalty = $loan->penalty_amount;
        $this->penaltyService->calculateAndApplyPenalty($schedule);

        $loan->refresh();
        $this->assertEquals($initialPenalty + 50, $loan->penalty_amount);
    }

    /** @test */
    public function waiving_penalty_updates_totals()
    {
        $loan = $this->createLoan();
        $schedule = $this->createOverdueSchedule($loan, 5);

        $penalty = $this->penaltyService->calculateAndApplyPenalty($schedule);

        $penaltyAmount = $penalty->amount;
        $loanPenaltyAfterApply = $loan->fresh()->penalty_amount;

        $this->penaltyService->waivePenalty($penalty, $this->user->id);

        $loan->refresh();
        $schedule->refresh();

        $this->assertEquals($loanPenaltyAfterApply - $penaltyAmount, $loan->penalty_amount);
    }

    /** @test */
    public function it_throws_exception_when_waiving_already_waived_penalty()
    {
        $loan = $this->createLoan();
        $schedule = $this->createOverdueSchedule($loan, 5);

        $penalty = $this->penaltyService->calculateAndApplyPenalty($schedule);
        $this->penaltyService->waivePenalty($penalty, $this->user->id);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('already waived');

        $penalty->refresh();
        $this->penaltyService->waivePenalty($penalty, $this->user->id);
    }

    /** @test */
    public function it_calculates_total_penalties_for_loan()
    {
        $loan = $this->createLoan();

        // Create multiple schedules with penalties
        for ($i = 0; $i < 3; $i++) {
            $schedule = $this->createOverdueSchedule($loan, 5 + $i);
            $schedule->update(['due_date' => Carbon::now()->subDays(5 + $i)]);
            $this->penaltyService->calculateAndApplyPenalty($schedule);
        }

        $total = $this->penaltyService->getTotalPenalties($loan);

        $this->assertEquals(150, $total); // 3 * 50
    }

    protected function createLoan(?LoanType $loanType = null): Loan
    {
        $loanType = $loanType ?? $this->loanType;

        return Loan::create([
            'branch_id' => $this->branch->id,
            'loan_number' => 'LN' . rand(10000, 99999),
            'customer_id' => $this->customer->id,
            'loan_type_id' => $loanType->id,
            'issued_by' => $this->user->id,
            'principal_amount' => 10000,
            'interest_amount' => 100,
            'total_amount' => 10100,
            'amount_paid' => 0,
            'amount_pending' => 10100,
            'penalty_amount' => 0,
            'total_installments' => 10,
            'paid_installments' => 0,
            'start_date' => Carbon::now()->subDays(20),
            'end_date' => Carbon::now()->addDays(10),
            'status' => LoanStatus::ACTIVE,
        ]);
    }

    protected function createOverdueSchedule(Loan $loan, int $daysOverdue = 5): LoanSchedule
    {
        return LoanSchedule::create([
            'loan_id' => $loan->id,
            'installment_number' => rand(1, 10),
            'amount_due' => 1000,
            'penalty_amount' => 0,
            'amount_paid' => 0,
            'due_date' => Carbon::now()->subDays($daysOverdue),
            'status' => ScheduleStatus::OVERDUE,
        ]);
    }
}
