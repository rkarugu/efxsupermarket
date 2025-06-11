<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IncentivesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $incentives =  [
            [
                'name' => 'Tonnage',
                'slug' => 'tonnage',
                'target' => 'salesman',
                'type' => 'flat',
                'group'=>null,
                'target_reward' => json_encode([
                    [
                        'reward' => 1000, // Earned amount
                        'target' => 100,
                        'operation' => 'greater_than',
                        'title'=>"Tonnage  Greater than 100",
                    ]
                ]),
            ],
            [
                'name' => 'Cartons',
                'slug' => 'cartons',
                'target' => 'driver',
                'type' => 'range',
                'group'=>"pack_type",
                'target_reward' => json_encode([
                    [
                        'reward' => 1000, // Earned amount
                        'target' => 30,
                        'operation' => 'greater_than',
                        'title'=>"Cartons  Greater than 30",
                    ]
                ]),
            ],
            [
                'name' => 'Dozens',
                'slug' => 'dozens',
                'target' => 'driver',
                'type' => 'range',
                'group'=>"pack_type",
                'target_reward' => json_encode([
                    [
                        'reward' => 1000, // Earned amount
                        'target' => 30,
                        'operation' => 'greater_than',
                        'title'=>"Dozens  Greater than 30",
                    ]
                ]),
            ],
            [
                'name' => 'Bulk',
                'slug' => 'bulk',
                'target' => 'driver',
                'type' => 'range',
                'group'=>"pack_type",
                'target_reward' => json_encode([
                    [
                        'reward' => 1000,
                        'target' => 30,
                        'operation' => 'greater_than',
                        'title'=>"Bulk is greater_than 30",
                    ]
                ]),
            ],
            [
                'name' => 'Met Customers',
                'slug' => 'met_customers',
                'target' => 'driver',
                'type' => 'range',
                'target_reward' => json_encode([
                        [
                            'reward' => 2000, // Earned amount (based on your condition)
                            'target' => 100,
                            'operation' => 'greater_than',
                            'title'=>"Met customers is greater_than 1000",
                        ],
                        [
                            'reward' => 1000, // Earned amount (based on your condition)
                            'target' => 80,
                            'operation' => 'greater_than',
                            'title'=>"Met Customers is greater_than 80",
                        ]
                    ]
                ),
            ],
            [
                'name' => 'On Site Shifts',
                'slug'=>'onsite',
                'target' => 'driver',
                'type' => 'range',
                'target_reward' => json_encode([
                    [
                        'reward' => 2000, // Earned amount (based on your condition)
                        'target' => 100,
                        'operation' => 'greater_than',
                        'title'=>"Onsite Shifts is greater_than 100",
                    ],[
                        'reward' => 1000, // Earned amount (based on your condition)
                        'target' => 80,
                        'operation' => 'greater_than',
                        'title'=>"Onsite Shifts is greater_than 80",
                    ]
                ]),
            ],
            [
                'name' => 'Shifts by 6:30AM',
                'slug' => 'early_shifts',
                'target' => 'driver',
                'type' => 'range',
                'target_reward' => json_encode([
                    [
                        'reward' => 2000, // Earned amount (based on your condition)
                        'target' => 100 ,
                        'operation' => 'greater_than',
                        'title'=>"Early Shifts is greater_than 100",
                    ],[
                        'reward' => 1000, // Earned amount (based on your condition)
                        'target' => 80,
                        'operation' => 'greater_than',
                        'title'=>"Early Shifts is greater_than 80",
                    ]
                ]),
            ],
            [
                'name' => 'Returns',
                'slug' => 'returns',
                'target' => 'driver',
                'type' => 'range',
                'target_reward' => json_encode([
                    [
                        'reward' => 1000, // Earned amount (based on your condition)
                        'target' => 10000,
                        'operation' => 'less_than',
                        'title'=>"Returns is less_than 10,000",
                    ],[
                        'reward' => 2000, // Earned amount (based on your condition)
                        'target' => 0,
                        'operation' => 'equal',
                        'title'=>"Returns is equal 0",
                    ]
                ]),
            ],
            [
                'name' => 'Immediate Pay',
                'slug' => 'pay_on_delivery',
                'target' => 'driver',
                'type' => 'range',
                'target_reward' => json_encode([
                    [
                        'reward' => 1500,
                        'target' => 100,
                        'operation' => 'equal',
                        'title'=>"Pay on delivery is equal 100",
                    ]
                ]),
            ],
            [
                'name' => 'Time Management',
                'slug'=>'time_management',
                'target' => 'driver',
                'type' => 'range',
                'target_reward' => json_encode([
                    [
                        'reward' => 1500, // Earned amount (based on your condition)
                        'target' => 100,
                        'operation' => 'equal',
                        'title'=>"Time Management is equal 100",
                    ]
                ]),
            ],
            [
                'name' => 'System Usage',
                'slug' => 'system_usage',
                'target' => 'driver',
                'type' => 'flat',
                'target_reward' => json_encode([
                    [
                        'reward' => 1500, // Earned amount
                        'target' => 100,
                        'operation' => 'equal',
                        'title'=>"System Usage is equal 100",
                    ]
                ]),
            ],
            [
                'name' => 'Early Shift',
                'slug'=>'shift_by_6am',
                'target' => 'driver',
                'type' => 'range',
                'target_reward' => json_encode([
                    [
                        'reward' => 1500, // Earned amount (based on your condition)
                        'target' => 100,
                        'operation' => 'equal',
                        'title'=>"Early Shifts is equal 100",
                        ]
                ]),
            ],
            [
                'name' => 'Load Prev day',
                'slug'=>'load_prev_day',
                'target' => 'driver',
                'type' => 'range',
                'target_reward' => json_encode([
                    [
                        'reward' => 1500, // Earned amount (based on your condition)
                        'target' => 100 ,
                        'operation' => 'equal',
                        'title'=>"Loaded Previous Day is equal 100",
                    ]
                ]),
            ],
            [
                'name' => 'Back On Time',
                'slug' => 'back_on_time',
                'target' => 'driver',
                'type' => 'range',
                'target_reward' => json_encode([
                    [
                        'reward' => 1500, // Earned amount (based on your condition)
                        'target' => 100,
                        'operation' => 'equal',
                        'title'=>"Back on Time is equal 100",
                    ]
                ]),
            ],
            [
                'name' => 'Fuel',
                'slug'=>'fuel',
                'target' => 'driver',
                'type' => 'flat',
                'target_reward' => json_encode([
                    [
                        'reward' => 1500, // Earned amount
                        'target' => 100,
                        'operation' => 'less_than',
                        'title'=>"Fueled  Less than count",
                    ],
                    [
                        'reward' => 1000, // Earn
                        'target' => 100,
                        'operation' => 'equal',
                        'title'=>"Fueled  Equal count",
                    ]
                ]),
            ],
            [
                'name' => 'Turn Boy',
                'slug' => 'turn_boy_incentive',
                'target' => 'driver',
                'type' => 'flat',
                'target_reward' => json_encode([
                    [
                        'reward' => 50, // Earned amount
                        'target' => 0 ,
                        'operation' => 'share',
                        'title'=>"Turn Boy Incentive",
                    ]
                ]),
            ],
        ];

        DB::table('incentive_settings')->truncate();
        foreach ($incentives as $incentive) {
            DB::table('incentive_settings')->updateOrInsert(
                ['name' => $incentive['name']],
                $incentive
            );
        }
    }
}
