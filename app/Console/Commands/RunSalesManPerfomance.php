<?php

namespace App\Console\Commands;

use App\Services\SalesManPerformanceService;
use Illuminate\Console\Command;

class RunSalesManPerfomance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:pp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $service =new SalesManPerformanceService();
        $service->tonnage();
    }
}
