<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class RunQueueWork extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:run-queue-work';
    public  $timeout =280;
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
        if ($this->shouldRunQueueWork()) {
            Artisan::call('queue:work', ['--stop-when-empty' => true]);
            $this->info('Queue work command executed.');
        }
    }
    protected function shouldRunQueueWork()
    {
        // Check if the queue worker is not already running
        return DB::table('jobs')->count() >= 1;
    }
}
