<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class ItemReturnSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $reasons = [
            [
                'name' => 'Damaged',
            ],
            [
                'name' => 'Other',
            ]

        ];

        DB::table('item_return_reasons')->insert($reasons);
    }
}
