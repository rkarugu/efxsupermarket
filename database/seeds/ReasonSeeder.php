<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //

        

        // $reasons = [
        //     [
        //         'name' => 'Request Items',
        //     ],
        //     [
        //         'name' => 'Request to take order',
        //     ],
        //     [
        //         'name' => 'Shop closed',
        //     ],
        //     [
        //         'name' => 'Customer feedback',
        //     ],
        //     [
        //         'name' => 'Other',
        //     ]

        // ];

        // DB::table('report_reasons')->insert($reasons);

        // uncomment to insert salesman reporting issues options
       



        $reasons = [
            [
                'name' => 'Price Conflict',
                'created_at' =>Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Shop Closed',
                'created_at' =>Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'No Order',
                'created_at' =>Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'New Products',
                'created_at' =>Carbon::now(),
                'updated_at' => Carbon::now(),
            ]

        ];

        DB::table('salesman_reporting_reasons')->insert($reasons);




        $reasons = [
            [
                'reporting_reason_id' => 1,
                'reason_option' => 'Item Code',
                'data_type' => 'string',
                'reason_option_key_name' => 'item_code',
                'created_at' =>Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'reporting_reason_id' => 1,
                'reason_option' => 'Picture',
                'data_type' => 'picture',
                'reason_option_key_name' => 'product_picture',
                'created_at' =>Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'reporting_reason_id' => 2,
                'reason_option' => 'Shop is Closed',
                'data_type' => 'picture',
                'reason_option_key_name' => 'shop_closed_picture',
                'created_at' =>Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'reporting_reason_id' => 4,
                'reason_option' => 'Product Name',
                'data_type' => 'string',
                'reason_option_key_name' => 'product_name',
                'created_at' =>Carbon::now(),
                'updated_at' => Carbon::now(),
            ]

        ];

        DB::table('salesman_reporting_reason_options')->insert($reasons);
    }
}
