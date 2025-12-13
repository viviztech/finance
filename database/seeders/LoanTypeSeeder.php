<?php

namespace Database\Seeders;

use App\Enums\InterestType;
use App\Enums\LoanFrequency;
use App\Enums\PenaltyType;
use App\Models\LoanType;
use Illuminate\Database\Seeder;

class LoanTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $loanTypes = [
            [
                'name' => 'Daily Gold Loan',
                'slug' => 'daily-gold-loan',
                'frequency' => LoanFrequency::DAILY,
                'interest_type' => InterestType::PERCENTAGE,
                'interest_rate' => 10,
                'default_duration' => 100,
                'min_amount' => 5000,
                'max_amount' => 100000,
                'penalty_enabled' => true,
                'penalty_type' => PenaltyType::FIXED,
                'penalty_rate' => 50,
                'grace_period_days' => 3,
                'description' => 'Daily collection loan with 10% interest, ideal for small traders',
            ],
            [
                'name' => 'Weekly Personal Loan',
                'slug' => 'weekly-personal-loan',
                'frequency' => LoanFrequency::WEEKLY,
                'interest_type' => InterestType::PERCENTAGE,
                'interest_rate' => 15,
                'default_duration' => 20,
                'min_amount' => 10000,
                'max_amount' => 200000,
                'penalty_enabled' => true,
                'penalty_type' => PenaltyType::PERCENTAGE,
                'penalty_rate' => 2,
                'grace_period_days' => 5,
                'description' => 'Weekly collection loan with 15% interest for personal needs',
            ],
            [
                'name' => 'Bi-Weekly Business Loan',
                'slug' => 'biweekly-business-loan',
                'frequency' => LoanFrequency::BIWEEKLY,
                'interest_type' => InterestType::PERCENTAGE,
                'interest_rate' => 12,
                'default_duration' => 12,
                'min_amount' => 25000,
                'max_amount' => 500000,
                'penalty_enabled' => true,
                'penalty_type' => PenaltyType::PERCENTAGE,
                'penalty_rate' => 1.5,
                'grace_period_days' => 7,
                'description' => 'Bi-weekly collection loan with 12% interest for small businesses',
            ],
            [
                'name' => 'Monthly EMI Loan',
                'slug' => 'monthly-emi-loan',
                'frequency' => LoanFrequency::MONTHLY,
                'interest_type' => InterestType::PERCENTAGE,
                'interest_rate' => 18,
                'default_duration' => 12,
                'min_amount' => 50000,
                'max_amount' => 1000000,
                'penalty_enabled' => true,
                'penalty_type' => PenaltyType::PERCENTAGE,
                'penalty_rate' => 2,
                'grace_period_days' => 10,
                'description' => 'Monthly EMI loan with 18% interest for larger requirements',
            ],
            [
                'name' => 'Daily Fixed Interest Loan',
                'slug' => 'daily-fixed-loan',
                'frequency' => LoanFrequency::DAILY,
                'interest_type' => InterestType::FIXED,
                'interest_rate' => 1000,
                'default_duration' => 100,
                'min_amount' => 10000,
                'max_amount' => 50000,
                'penalty_enabled' => false,
                'description' => 'Daily loan with fixed ₹1000 interest, simple and transparent',
            ],
        ];

        foreach ($loanTypes as $loanType) {
            LoanType::firstOrCreate(
                ['slug' => $loanType['slug']],
                $loanType
            );
        }

        $this->command->info('Default loan types seeded successfully!');
        $this->command->table(
            ['Name', 'Frequency', 'Interest'],
            LoanType::all()->map(fn($lt) => [
                $lt->name,
                $lt->frequency->label(),
                $lt->formatted_interest,
            ])->toArray()
        );
    }
}
