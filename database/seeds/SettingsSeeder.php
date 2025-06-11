<?php

namespace Database\Seeders;

use App\Model\Setting;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    protected $items = [
        ['name' => 'UNDELIVERED_LPO_EXPRIRY', 'value' => '60', 'type' => 'number'],
        ['name' => 'MAXIMUM_PENDING_RTS_DAYS', 'value' => '3', 'type' => 'number'],
        ['name' => 'MAXIMUM_APPROVED_RTS_DAYS', 'value' => '7', 'type' => 'number'],
        ['name' => 'DISPATCH_CALLOUT_DELAY_TIME', 'value' => '60', 'type' => 'number'],
        ['name' => 'MAXIMUM_SEND_GRN_DOCUMENTS_DAYS', 'value' => '3', 'type' => 'number'],
        ['name' => 'MAXIMUM_UNPROCESSED_GRN_DAYS', 'value' => '3', 'type' => 'number'],
        ['name' => 'CHECK_OFFSITE_DISTANCE', 'value' => true, 'type' => 'boolean'],
        ['name' => 'MAX_OFFSITE_DISTANCE', 'value' => '500', 'type' => 'number'],
    ];

    public function run(): void
    {
        foreach ($this->items as $item) {
            Setting::firstOrCreate([
                'name' => $item['name']
            ], [
                'slug' => Str::slug($item['name']),
                'description' => $item['value'],
                'parameter_type' => $item['type'],
            ]);
        }
    }
}
