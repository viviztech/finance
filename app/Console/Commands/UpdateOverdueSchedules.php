<?php

namespace App\Console\Commands;

use App\Models\LoanSchedule;
use App\Enums\ScheduleStatus;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateOverdueSchedules extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'schedules:update-overdue';

    /**
     * The console command description.
     */
    protected $description = 'Mark overdue schedules with the OVERDUE status';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Updating overdue schedules...');

        $count = LoanSchedule::query()
            ->whereIn('status', [ScheduleStatus::PENDING, ScheduleStatus::PARTIAL])
            ->whereDate('due_date', '<', Carbon::today())
            ->update(['status' => ScheduleStatus::OVERDUE]);

        $this->info("Updated {$count} schedules to OVERDUE status.");

        return Command::SUCCESS;
    }
}
