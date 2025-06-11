<?php

namespace Database\Seeders;

use App\Model\VehicleType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VehicleTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            'Motorcycle',
            'Passenger Car',
            'Van/Panel',
            'Bus',
            'Single Unit Truck',
            'Single-Trailer Combination Truck',
            'Multiple-Trailer Combination Truck',
        ];

        foreach ($types as $type) {
            VehicleType::updateOrCreate(
                ['name' => $type],
                ['name' => $type]
            );
        }
    }
}
