<?php

namespace App\Console\Commands\Telematics;

use App\Models\VehicleTelematicsData;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CopyDataToNewTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:copy-telematics';

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
        DB::connection('telematics')->beginTransaction();
        try {
            $query = DB::table('vehicle_telematics_data')
                ->whereBetween('created_at', ['2024-07-13 00:00:00', '2024-07-13 11:59:59']);

            $records = [];
            $query->orderBy('id')->chunk(1000, function ($rows) use ($records) {
                $records = array_merge($records, $rows);
            });

            DB::connection('telematics')->table('vehicle_telematics')->insert($records);

//            $query->orderBy('id')->chunk(1000, function ($records) {
//                $insertData = [];

//                foreach ($records as $record) {
//                    $decodedData = json_decode($record->data, true);
//                    foreach ($decodedData as $index => $data) {
//                        $insertData[] = [
//                            'device_number' => $data['device.name'],
//                            'latitude' => $data['position.latitude'] ?? null,
//                            'longitude' => $data['position.longitude'] ?? null,
//                            'speed' => $data['position.speed'] ?? null,
//                            'direction' => $data['position.direction'] ?? null,
//                            'fuel_level' => $data['escort.lls.value.1'] ?? null,
//                            'mileage' => $data['vehicle.mileage'] ?? null,
//                            'ignition_status' => $data['engine.ignition.status'] ?? null,
//                            'timestamp' => Carbon::createFromTimestamp($data['timestamp']),
//                            'created_at' => Carbon::createFromTimestamp($data['timestamp']),
//                            'updated_at' => Carbon::createFromTimestamp($data['timestamp']),
//                            'raw_timestamp' => $data['timestamp'],
//                            'data' => json_encode($decodedData),
//                            'data_index' => $index,
//                        ];
//                    }
//                }
//            });

            DB::connection('telematics')->commit();
            $this->info("success");
        } catch (\Throwable $e) {
            DB::connection('telematics')->rollBack();
            $this->info("Failed with {$e->getMessage()}");
        }
    }
}
