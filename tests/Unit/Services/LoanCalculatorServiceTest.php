<?php

namespace Tests\Unit\Services;

use App\Models\LoanType;
use App\Services\LoanCalculatorService;
use App\Enums\LoanFrequency;
use App\Enums\InterestType;
use App\Enums\PenaltyType;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoanCalculatorServiceTest extends TestCase
{
    use RefreshDatabase;

    protected LoanCalculatorService $calculator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->calculator = new LoanCalculatorService();
    }

    /** @test */
    public function it_calculates_fixed_interest_correctly()
    {
        $loanType = LoanType::create([
            'name' => 'Fixed Interest Loan',
            'frequency' => LoanFrequency::DAILY,
            'interest_type' => InterestType::FIXED,
            'interest_rate' => 500,
            'default_duration' => 100,
            'is_active' => true,
        ]);

        $result = $this->calculator->calculate(10000, $loanType);

        $this->assertEquals(10000, $result['principal_amount']);
        $this->assertEquals(500, $result['interest_amount']);
        $this->assertEquals(10500, $result['total_amount']);
        $this->assertEquals(100, $result['total_installments']);
        $this->assertEquals(105, $result['installment_amount']);
    }

    /** @test */
    public function it_calculates_percentage_interest_correctly()
    {
        $loanType = LoanType::create([
            'name' => 'Percentage Interest Loan',
            'frequency' => LoanFrequency::MONTHLY,
            'interest_type' => InterestType::PERCENTAGE,
            'interest_rate' => 10, // 10%
            'default_duration' => 12,
            'is_active' => true,
        ]);

        $result = $this->calculator->calculate(10000, $loanType);

        $this->assertEquals(10000, $result['principal_amount']);
        $this->assertEquals(1000, $result['interest_amount']); // 10% of 10000
        $this->assertEquals(11000, $result['total_amount']);
        $this->assertEquals(12, $result['total_installments']);
    }

    /** @test */
    public function it_uses_custom_installment_count_when_provided()
    {
        $loanType = LoanType::create([
            'name' => 'Custom Installments',
            'frequency' => LoanFrequency::WEEKLY,
            'interest_type' => InterestType::FIXED,
            'interest_rate' => 200,
            'default_duration' => 10,
            'is_active' => true,
        ]);

        $result = $this->calculator->calculate(5000, $loanType, 20);

        $this->assertEquals(5200, $result['total_amount']);
        $this->assertEquals(20, $result['total_installments']); // Custom count, not default
        $this->assertEquals(260, $result['installment_amount']);
    }

    /** @test */
    public function it_validates_minimum_amount_correctly()
    {
        $loanType = LoanType::create([
            'name' => 'Min Amount Loan',
            'frequency' => LoanFrequency::DAILY,
            'interest_type' => InterestType::FIXED,
            'interest_rate' => 100,
            'default_duration' => 30,
            'min_amount' => 5000,
            'is_active' => true,
        ]);

        $errors = $this->calculator->validateAmount(3000, $loanType);

        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('Minimum', $errors[0]);
    }

    /** @test */
    public function it_validates_maximum_amount_correctly()
    {
        $loanType = LoanType::create([
            'name' => 'Max Amount Loan',
            'frequency' => LoanFrequency::DAILY,
            'interest_type' => InterestType::FIXED,
            'interest_rate' => 100,
            'default_duration' => 30,
            'max_amount' => 50000,
            'is_active' => true,
        ]);

        $errors = $this->calculator->validateAmount(60000, $loanType);

        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('Maximum', $errors[0]);
    }

    /** @test */
    public function it_calculates_effective_interest_rate()
    {
        $rate = $this->calculator->getEffectiveInterestRate(10000, 500);

        $this->assertEquals(5, $rate); // 500/10000 * 100 = 5%
    }

    /** @test */
    public function it_returns_zero_for_zero_principal()
    {
        $rate = $this->calculator->getEffectiveInterestRate(0, 500);

        $this->assertEquals(0, $rate);
    }

    /** @test */
    public function it_handles_last_installment_rounding()
    {
        $loanType = LoanType::create([
            'name' => 'Rounding Test',
            'frequency' => LoanFrequency::DAILY,
            'interest_type' => InterestType::FIXED,
            'interest_rate' => 333,
            'default_duration' => 30,
            'is_active' => true,
        ]);

        $result = $this->calculator->calculate(10000, $loanType);

        // Verify total is correct despite rounding
        $regularTotal = $result['installment_amount'] * ($result['total_installments'] - 1);
        $actualTotal = $regularTotal + $result['last_installment_amount'];

        $this->assertEquals($result['total_amount'], $actualTotal);
    }
}
