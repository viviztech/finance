<?php

namespace App\Console\Commands;

use App\Services\PenaltyService;
use Illuminate\Console\Command;

class ApplyOverduePenalties extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'penalties:apply';

    /**
     * The console command description.
     */
    protected $description = 'Apply penalties to all overdue loan schedules';

    /**
     * Execute the console command.
     */
    public function handle(PenaltyService $penaltyService): int
    {
        $this->info('Applying overdue penalties...');

        $penalties = $penaltyService->applyOverduePenalties();

        if (count($penalties) > 0) {
            $this->info("Applied " . count($penalties) . " penalties.");

            foreach ($penalties as $penalty) {
                $this->line("  - Loan #{$penalty->loan_id}, Schedule #{$penalty->schedule_id}: ₹{$penalty->amount}");
            }
        } else {
            $this->info('No new penalties to apply.');
        }

        return Command::SUCCESS;
    }
}
