<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DeleteReportEntry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:delete-report-entry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete a test report entry';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $reports = [
            'Driver Performance Report',
            'Salesman Performance Report',
        ];

        foreach ($reports as $report) {

            $deleted = DB::table('module_reports')
            ->where('report_title', $report)
            ->delete();
        }
    }
}
