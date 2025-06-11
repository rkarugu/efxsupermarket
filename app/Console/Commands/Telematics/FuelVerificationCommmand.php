<?php

namespace App\Console\Commands\Telematics;

use App\Models\FuelVerificationRecord;
use Carbon\Carbon;
use Illuminate\Console\Command;

class FuelVerificationCommmand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fuel-verification-commmand';

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
        $yesterday = Carbon::now()->subDay()->toDateString();
        $record = FuelVerificationRecord::whereDate('fueling_date', $yesterday)->first();
        if (!$record) {
            FuelVerificationRecord::create([
                'branch_id' => 10,
                'fueling_date' => $yesterday,
                'verification_date' => Carbon::now()->toDateString()
            ]);
        }
    }
}
