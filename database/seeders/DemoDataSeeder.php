<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\Loan;
use App\Models\LoanSchedule;
use App\Models\LoanType;
use App\Models\Payment;
use App\Models\User;
use App\Enums\LoanStatus;
use App\Enums\ScheduleStatus;
use App\Enums\PaymentMethod;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating demo data...');

        // Get the head office branch or create branches
        $headOffice = Branch::first();

        // Create additional branches
        $branches = collect([$headOffice]);

        if (Branch::count() < 3) {
            $branches->push(Branch::create([
                'name' => 'North Branch',
                'code' => 'NB001',
                'address' => '456 North Street, Finance City',
                'phone' => '+91 9876543211',
                'is_active' => true,
            ]));

            $branches->push(Branch::create([
                'name' => 'South Branch',
                'code' => 'SB001',
                'address' => '789 South Avenue, Finance City',
                'phone' => '+91 9876543212',
                'is_active' => true,
            ]));
        }

        $loanTypes = LoanType::all();

        // Get agents
        $agents = User::role('agent')->get();

        // Create customers for each branch
        $customerNames = [
            'Rajesh Kumar',
            'Priya Sharma',
            'Amit Singh',
            'Sunita Devi',
            'Vikram Patel',
            'Anita Gupta',
            'Suresh Yadav',
            'Meena Kumari',
            'Rakesh Verma',
            'Kavita Singh',
            'Mahesh Sharma',
            'Pooja Rani',
            'Deepak Kumar',
            'Neha Agarwal',
            'Ashok Jain',
        ];

        foreach ($branches as $branch) {
            $this->command->info("Creating customers and loans for: {$branch->name}");

            // Get agents for this branch
            $branchAgents = $agents->where('branch_id', $branch->id);
            if ($branchAgents->isEmpty()) {
                $branchAgents = $agents;
            }

            // Create 5 customers per branch
            for ($i = 0; $i < 5; $i++) {
                $customerName = $customerNames[array_rand($customerNames)];

                $customer = Customer::create([
                    'branch_id' => $branch->id,
                    'customer_code' => Customer::generateCode($branch->id),
                    'name' => $customerName . ' #' . rand(100, 999),
                    'phone' => '+91 98' . rand(10000000, 99999999),
                    'email' => strtolower(str_replace(' ', '.', $customerName)) . rand(1, 99) . '@example.com',
                    'address' => rand(1, 500) . ' Demo Street, City ' . rand(1, 50),
                    'id_proof_type' => ['aadhar', 'pan', 'voter_id'][rand(0, 2)],
                    'id_proof_number' => strtoupper(substr(md5(rand()), 0, 12)),
                    'occupation' => ['Business', 'Service', 'Self-employed', 'Farmer', 'Shop Owner'][rand(0, 4)],
                    'monthly_income' => rand(15, 100) * 1000,
                    'created_by' => 1,
                    'assigned_agent_id' => $branchAgents->random()->id ?? null,
                    'is_active' => true,
                ]);

                // Create 1-3 loans per customer
                $numLoans = rand(1, 3);
                for ($j = 0; $j < $numLoans; $j++) {
                    $this->createLoanWithSchedules($customer, $loanTypes->random(), $branchAgents);
                }
            }
        }

        $this->command->info('Demo data created successfully!');
    }

    protected function createLoanWithSchedules(Customer $customer, LoanType $loanType, $agents): void
    {
        $principal = rand(5, 50) * 1000;
        $interestAmount = $loanType->calculateInterest($principal);
        $totalAmount = $principal + $interestAmount;
        $installments = $loanType->default_duration;

        $startDate = Carbon::now()->subDays(rand(1, 60));
        $endDate = clone $startDate;
        for ($i = 0; $i < $installments; $i++) {
            $endDate = $loanType->frequency->addInterval($endDate);
        }

        $installmentAmount = round($totalAmount / $installments, 2);

        // Randomly determine loan state
        $status = [LoanStatus::ACTIVE, LoanStatus::ACTIVE, LoanStatus::ACTIVE, LoanStatus::COMPLETED][rand(0, 3)];

        $loan = Loan::create([
            'branch_id' => $customer->branch_id,
            'loan_number' => 'LN' . strtoupper(substr(md5(rand()), 0, 8)),
            'customer_id' => $customer->id,
            'loan_type_id' => $loanType->id,
            'issued_by' => 1,
            'assigned_agent_id' => $agents->random()->id ?? null,
            'principal_amount' => $principal,
            'interest_amount' => $interestAmount,
            'total_amount' => $totalAmount,
            'amount_paid' => 0,
            'amount_pending' => $totalAmount,
            'penalty_amount' => 0,
            'total_installments' => $installments,
            'paid_installments' => 0,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => LoanStatus::ACTIVE, // Will update later
            'notes' => 'Demo loan created by seeder',
        ]);

        // Generate schedules
        $currentDate = Carbon::parse($startDate);
        $schedules = [];

        for ($i = 1; $i <= $installments; $i++) {
            $currentDate = $loanType->frequency->addInterval($currentDate);

            $scheduleStatus = ScheduleStatus::PENDING;
            if ($currentDate->isBefore(Carbon::now())) {
                // Past due - randomly mark as paid, partial, or overdue
                $scheduleStatus = [ScheduleStatus::PAID, ScheduleStatus::PARTIAL, ScheduleStatus::OVERDUE, ScheduleStatus::PENDING][rand(0, 3)];
            }

            $amountPaid = 0;
            if ($scheduleStatus === ScheduleStatus::PAID) {
                $amountPaid = $installmentAmount;
            } elseif ($scheduleStatus === ScheduleStatus::PARTIAL) {
                $amountPaid = round($installmentAmount * (rand(20, 80) / 100), 2);
            }

            $schedule = LoanSchedule::create([
                'loan_id' => $loan->id,
                'installment_number' => $i,
                'amount_due' => $installmentAmount,
                'penalty_amount' => 0,
                'amount_paid' => $amountPaid,
                'due_date' => $currentDate->copy(),
                'paid_date' => $amountPaid > 0 ? $currentDate->copy() : null,
                'status' => $scheduleStatus,
            ]);

            $schedules[] = $schedule;

            // Create payment if paid
            if ($amountPaid > 0) {
                Payment::create([
                    'loan_id' => $loan->id,
                    'schedule_id' => $schedule->id,
                    'collected_by' => 1,
                    'receipt_number' => 'RCP' . strtoupper(substr(md5(rand()), 0, 8)),
                    'principal_amount' => $amountPaid,
                    'penalty_amount' => 0,
                    'total_amount' => $amountPaid,
                    'payment_method' => PaymentMethod::cases()[rand(0, 4)],
                    'collected_at' => $currentDate->copy(),
                ]);
            }
        }

        // Update loan totals
        $loan->recalculateAmounts();

        // Check if completed
        if ($loan->amount_pending <= 0) {
            $loan->update(['status' => LoanStatus::COMPLETED]);
        }
    }
}
