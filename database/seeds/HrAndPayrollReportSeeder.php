<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\ModuleReport;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class HrAndPayrollReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $module = Module::firstOrCreate([
            'module_title' => 'HR And Payroll',
        ]);

        $module->modulereportcategories()->where('category_title', 'Sales Reports')->delete();

        $moduleReportCategory = $module->modulereportcategories()->firstOrCreate([
            'category_title' => 'Payroll Reports',
            'module_id' => $module->id,
        ]);

        $payrollReports = [
            [
                'title' => 'Paymaster Report',
                'route' => 'hr.payroll.paymaster-report',
                'model' => 'payroll-reports-paymaster',
                'permission' => 'payroll-reports-paymaster___view'
            ],
            [
                'title' => 'Payroll Summary Report',
                'route' => 'hr.payroll.payroll-summary-report',
                'model' => 'payroll-reports-payroll-summary',
                'permission' => 'payroll-reports-payroll-summary___view'
            ],
            [
                'title' => 'Earnings Report',
                'route' => 'hr.payroll.earnings-report',
                'model' => 'payroll-reports-earnings',
                'permission' => 'payroll-reports-earnings___view'
            ],
            [
                'title' => 'Deductions Report',
                'route' => 'hr.payroll.deductions-report',
                'model' => 'payroll-reports-deductions',
                'permission' => 'payroll-reports-deductions___view'
            ],
            [
                'title' => 'Consolidated Payroll Report',
                'route' => 'hr.payroll.consolidated-payroll-report',
                'model' => 'payroll-reports-consolidated-payroll',
                'permission' => 'payroll-reports-consolidated-payroll___view'
            ],
        ];

        foreach($payrollReports as $report) {
            ModuleReport::firstOrCreate([
                'report_title' => $report['title'],
            ], [
                'module_report_category_id' => $moduleReportCategory->id,
                'report_route' => $report['route'],
                'report_model' => $report['model'],
                'report_permission' => $report['permission'],
            ]);
        }

        $moduleReportCategory = $module->modulereportcategories()->firstOrCreate([
            'category_title' => 'Deductions Reports',
            'module_id' => $module->id,
        ]);

        $deductionReports = [
            [
                'title' => 'PAYE Report',
                'route' => 'hr.payroll.reports.paye-report',
                'model' => 'payroll-reports-paye-deductions',
                'permission' => 'payroll-reports-paye-deductions___view'
            ],
            [
                'title' => 'NSSF Report',
                'route' => 'hr.payroll.reports.nssf-report',
                'model' => 'payroll-reports-nssf-deductions',
                'permission' => 'payroll-reports-nssf-deductions___view'
            ],
            [
                'title' => 'SHIF Report',
                'route' => 'hr.payroll.reports.shif-report',
                'model' => 'payroll-reports-shif-deductions',
                'permission' => 'payroll-reports-shif-deductions___view'
            ],
            [
                'title' => 'Housing Levy Report',
                'route' => 'hr.payroll.reports.housing-levy-report',
                'model' => 'payroll-reports-housing-levy-deductions',
                'permission' => 'payroll-reports-housing-levy-deductions___view'
            ],
            [
                'title' => 'Other Deductions Report',
                'route' => 'hr.payroll.reports.other-deductions-report',
                'model' => 'payroll-reports-other-deductions',
                'permission' => 'payroll-reports-other-deductions___view'
            ],
        ];

        foreach($deductionReports as $report) {
            ModuleReport::firstOrCreate([
                'report_title' => $report['title'],
            ], [
                'module_report_category_id' => $moduleReportCategory->id,
                'report_route' => $report['route'],
                'report_model' => $report['model'],
                'report_permission' => $report['permission'],
            ]);
        }
    }
}
