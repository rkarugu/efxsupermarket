<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\HrPayrollController;
use App\Http\Controllers\Admin\HrBulkUploadController;
use App\Http\Controllers\Admin\HRManagementController;
use App\Http\Controllers\Admin\HrPayrollReportController;
use App\Http\Controllers\Admin\CasualsPayPeriodController;
use App\Http\Controllers\Admin\HRConfigurationsController;
use App\Http\Controllers\Admin\CasualPayDisbursementController;

Route::prefix('admin/hr')->group(function () {
    Route::prefix('configurations')->group(function () {
        Route::get('general', [HRConfigurationsController::class, 'general'])->name('hr.configurations.general');
        Route::get('payroll', [HRConfigurationsController::class, 'payroll'])->name('hr.configurations.payroll');
        Route::get('banking', [HRConfigurationsController::class, 'banking'])->name('hr.configurations.banking');

        Route::get('general/job-groups-bulk-upload', [HrBulkUploadController::class, 'jobGroupsBulkUploadTemplate'])->name('hr.configurations.job-groups-bulk-upload-template');
        Route::get('general/job-levels-bulk-upload', [HrBulkUploadController::class, 'jobLevelsBulkUploadTemplate'])->name('hr.configurations.job-levels-bulk-upload-template');
        Route::get('general/job-grades-bulk-upload', [HrBulkUploadController::class, 'jobGradesBulkUploadTemplate'])->name('hr.configurations.job-grades-bulk-upload-template');
        Route::get('general/job-titles-bulk-upload', [HrBulkUploadController::class, 'jobTitlesBulkUploadTemplate'])->name('hr.configurations.job-titles-bulk-upload-template');
        Route::get('general/nationalities-bulk-upload', [HrBulkUploadController::class, 'nationalitiesBulkUploadTemplate'])->name('hr.configurations.nationalities-bulk-upload-template');
        Route::get('banking/banks-bulk-upload', [HrBulkUploadController::class, 'banksBulkUploadTemplate'])->name('hr.configurations.banks-bulk-upload-template');
    });

    Route::prefix('management')->group(function () {
        Route::get('employee-drafts', [HRManagementController::class, 'employeeDrafts'])->name('hr.management.employee-drafts');
        Route::get('employees', [HRManagementController::class, 'employees'])->name('hr.management.employees');
        Route::get('employees/create', [HRManagementController::class, 'employeesCreate'])->name('hr.management.employees-create');
        Route::get('employees/{employee}', [HRManagementController::class, 'employeeDetails'])->name('hr.management.employee-details');
    
        Route::get('employee-bulk-upload-template', [HrBulkUploadController::class, 'bulkUploadEmployeesTemplate'])->name('hr.management.bulk-upload-template');

        Route::get('casuals', [HRManagementController::class, 'casuals'])->name('hr.management.casuals');
        Route::get('casuals/create', [HRManagementController::class, 'casualsCreate'])->name('hr.management.casuals-create');
        Route::get('casuals/{casual}/edit', [HRManagementController::class, 'casualsEdit'])->name('hr.management.casuals-edit');
        Route::get('casuals-bulk-upload-template', [HrBulkUploadController::class, 'bulkUploadCasualsTemplate'])->name('hr.management.casuals-bulk-upload-template');
    });

    Route::prefix('payroll')->group(function () {
        Route::get('payroll-months', [HrPayrollController::class, 'payrollMonths'])->name('hr.payroll.payroll-months');
        Route::get('payroll-months/{id}/details', [HrPayrollController::class, 'payrollMonthDetails'])->name('hr.payroll.payroll-month-details');
        Route::get('payroll-months/{payrollMonth}/paymaster-report', [HrPayrollController::class, 'paymasterReport'])->name('hr.payroll.paymaster-report');
        Route::get('payroll-month-detail/{payrollMonthDetail}/payslip', [HrPayrollController::class, 'payslip'])->name('hr.payroll.payslip');
        
        Route::get('payroll-months/{id}/earnings-and-deductions-template', [HrBulkUploadController::class, 'earningsAndDeductionsTemplate'])->name('hr.payroll.payroll-month-earnings-and-deductions-template');
        
        Route::prefix('casuals-pay')->group(function () {
            Route::get('pay-periods', [CasualsPayPeriodController::class, 'casualsPayPeriods'])->name('hr.payroll.casuals-pay.pay-periods');
            Route::get('pay-periods/{casualsPayPeriod}/print', [CasualsPayPeriodController::class, 'casualsPayPeriodPrint'])->name('hr.payroll.casuals-pay.pay-period-print');
            Route::get('pay-periods/{id}/details', [CasualsPayPeriodController::class, 'casualsPayPeriodDetails'])->name('hr.payroll.casuals-pay.pay-period-details');
            Route::get('pay-periods/{id}/register-template', [CasualsPayPeriodController::class, 'casualsPayPeriodRegisterTemplate'])->name('hr.payroll.casuals-pay.pay-period-register-template');

            Route::get('successful-disbursements', [CasualPayDisbursementController::class, 'showSuccessfulDisbursementsPage'])->name('hr.payroll.casuals-pay.successful-disbursements');
            Route::get('failed-disbursements', [CasualPayDisbursementController::class, 'showFailedDisbursementsPage'])->name('hr.payroll.casuals-pay.failed-disbursements');
            Route::get('expunged-disbursements', [CasualPayDisbursementController::class, 'showExpungedDisbursementsPage'])->name('hr.payroll.casuals-pay.expunged-disbursements');
        });
        
        // REPORTS
        Route::prefix('reports')->group(function () {
            Route::get('', [HrPayrollReportController::class, 'reportsPage'])->name('hr.payroll.payroll-reports');
            Route::get('paymaster-report', [HrPayrollReportController::class, 'paymasterReport'])->name('hr.payroll.paymaster-report');
            Route::get('payroll-summary-report', [HrPayrollReportController::class, 'payrollSummaryReport'])->name('hr.payroll.payroll-summary-report');
            Route::get('earnings-report', [HrPayrollReportController::class, 'earningsReport'])->name('hr.payroll.earnings-report');
            Route::get('deductions-report', [HrPayrollReportController::class, 'deductionsReport'])->name('hr.payroll.deductions-report');
            Route::get('consolidated-payroll-report', [HrPayrollReportController::class, 'consolidatedPayrollReport'])->name('hr.payroll.consolidated-payroll-report');

            Route::get('paye-report', [HrPayrollReportController::class, 'payeReport'])->name('hr.payroll.reports.paye-report');
            Route::get('nssf-report', [HrPayrollReportController::class, 'nssfReport'])->name('hr.payroll.reports.nssf-report');
            Route::get('shif-report', [HrPayrollReportController::class, 'shifReport'])->name('hr.payroll.reports.shif-report');
            Route::get('housing-levy-report', [HrPayrollReportController::class, 'housingLevyReport'])->name('hr.payroll.reports.housing-levy-report');
            Route::get('other-deductions-report', [HrPayrollReportController::class, 'otherDeductionsReport'])->name('hr.payroll.reports.other-deductions-report');
        });
    });
});
