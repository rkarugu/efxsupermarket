<?php

namespace Database\Seeders;

use App\Models\ModuleReport;
use App\Models\ModuleReportCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class PurchaesReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // if (!Schema::hasTable('module_reports')) {
        //     Schema::create('module_reports', function ($table) {
        //         $table->id();
        //         $table->unsignedBigInteger('module_report_category_id');
        //         $table->string('report_title')->unique();
        //         $table->string('report_route');
        //         $table->string('report_model');
        //         $table->string('report_permission');
        //         $table->timestamps();
        //     });
        // }

        $purchaesCategory = ModuleReportCategory::where('module_id', 2)
        ->first();

        $reports = [
            [
                'title' => 'LPO Status & Leadtime Report',
                'route' => 'lpo-status-and-leatime-reports',
                'model' => 'lpo-status-and-leadtime-report',
                'permission' => 'purchases-reports___lpo-status-and-leadtime',
            ],
        ];

        foreach ($reports as $report) {
            ModuleReport::firstOrCreate(
                ['report_title' => $report['title']],
                [
                    'module_report_category_id' => $purchaesCategory->id,
                    'report_route' => $report['route'],
                    'report_model' => $report['model'],
                    'report_permission' => $report['permission'],
                ]
            );
        }
    }
}
