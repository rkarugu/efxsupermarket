<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\BankController;
use App\Http\Controllers\Admin\NHIFController;
use App\Http\Controllers\Admin\NssfController;
use App\Http\Controllers\Admin\PayeController;
use App\Http\Controllers\Admin\ShifController;
use App\Http\Controllers\Admin\SaccoController;
use App\Http\Controllers\Admin\CasualController;
use App\Http\Controllers\Admin\GenderController;
use App\Http\Controllers\Admin\ReliefController;
use App\Http\Controllers\Admin\EarningController;
use App\Http\Controllers\Admin\PensionController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\JobGradeController;
use App\Http\Controllers\Admin\JobGroupController;
use App\Http\Controllers\Admin\JobLevelController;
use App\Http\Controllers\Admin\LoanTypeController;
use App\Http\Controllers\Admin\AllowanceController;
use App\Http\Controllers\Admin\DeductionController;
use App\Http\Controllers\Admin\JobTitlesController;
use App\Http\Controllers\Admin\BankBranchController;
use App\Http\Controllers\Admin\CommissionController;
use App\Http\Controllers\Admin\SalutationController;
use App\Http\Controllers\Admin\HousingLevyController;
use App\Http\Controllers\Admin\NationalityController;
use App\Http\Controllers\Admin\DocumentTypeController;
use App\Http\Controllers\Admin\HrBulkUploadController;
use App\Http\Controllers\Admin\PayrollMonthController;
use App\Http\Controllers\Admin\RelationshipController;
use App\Http\Controllers\Admin\HrPaymentModeController;
use App\Http\Controllers\Admin\MaritalStatusController;
use App\Http\Controllers\Admin\NonCashBenfitController;
use App\Http\Controllers\Admin\EducationlevelController;
use App\Http\Controllers\Admin\EmploymentTypeController;
use App\Http\Controllers\Admin\PayrollSettingController;
use App\Http\Controllers\Admin\CustomParameterController;
use App\Http\Controllers\Admin\HrPayrollReportController;
use App\Http\Controllers\Admin\TerminationTypeController;
use App\Http\Controllers\Admin\CasualsPayPeriodController;
use App\Http\Controllers\Admin\DisciplineActionController;
use App\Http\Controllers\Admin\EmployeeDocumentController;
use App\Http\Controllers\Admin\EmploymentStatusController;
use App\Http\Controllers\Admin\PaymentFrequencyController;
use App\Http\Controllers\Admin\DisciplineCategoryController;
use App\Http\Controllers\Admin\EmployeeBeneficiaryController;
use App\Http\Controllers\Admin\CasualPayDisbursementController;
use App\Http\Controllers\Admin\EmployeeEducationHistoryController;
use App\Http\Controllers\Admin\EmployeeEmergencyContactController;
use App\Http\Controllers\Admin\EmployeeProfessionalHistoryController;

Route::middleware('auth:sanctum')->prefix('hr')->group(function () {
    Route::prefix('configurations')->group(function () {
        // GENDER ROUTES
        Route::get('gender-list', [GenderController::class, 'genderList']);
        Route::post('gender-create', [GenderController::class, 'genderCreate']);
        Route::post('gender-edit', [GenderController::class, 'genderEdit']);
        Route::delete('gender-delete/{id}', [GenderController::class, 'genderDelete']);

        // EMPLOYMENT TYPE ROUTES
        Route::get('employment-types-list', [EmploymentTypeController::class, 'employmentTypeList']);
        Route::post('employment-types-create', [EmploymentTypeController::class, 'employmentTypeCreate']);
        Route::post('employment-types-edit', [EmploymentTypeController::class, 'employmentTypeEdit']);
        Route::delete('employment-types-delete/{id}', [EmploymentTypeController::class, 'employmentTypeDelete']);

        // EMPLOYMENT STATUSES ROUTES
        Route::get('employment-statuses-list', [EmploymentStatusController::class, 'employmentStatusList']);
        Route::post('employment-statuses-create', [EmploymentStatusController::class, 'employmentStatusCreate']);
        Route::post('employment-statuses-edit', [EmploymentStatusController::class, 'employmentStatusEdit']);
        Route::delete('employment-statuses-delete/{id}', [EmploymentStatusController::class, 'employmentStatusDelete']);

        // JOB GROUPS ROUTES
        Route::get('job-groups-list', [JobGroupController::class, 'jobGroupList']);
        Route::post('job-groups-create', [JobGroupController::class, 'jobGroupCreate']);
        Route::post('job-groups-edit', [JobGroupController::class, 'jobGroupEdit']);
        Route::delete('job-groups-delete/{id}', [JobGroupController::class, 'jobGroupDelete']);

        Route::post('job-groups-bulk-upload', [HrBulkUploadController::class, 'bulkUploadJobGroups']);

        // JOB GROUPS ROUTES
        Route::get('job-levels-list', [JobLevelController::class, 'jobLevelList']);
        Route::post('job-levels-create', [JobLevelController::class, 'jobLevelCreate']);
        Route::post('job-levels-edit', [JobLevelController::class, 'jobLevelEdit']);
        Route::delete('job-levels-delete/{id}', [JobLevelController::class, 'jobLevelDelete']);

        Route::post('job-levels-bulk-upload', [HrBulkUploadController::class, 'bulkUploadJobLevels']);

        // JOB TITLES ROUTES
        Route::get('job-titles-list', [JobTitlesController::class, 'jobTitleList']);
        Route::post('job-titles-create', [JobTitlesController::class, 'jobTitleCreate']);
        Route::post('job-titles-edit', [JobTitlesController::class, 'jobTitleEdit']);
        Route::delete('job-titles-delete/{id}', [JobTitlesController::class, 'jobTitleDelete']);

        Route::post('job-titles-bulk-upload', [HrBulkUploadController::class, 'bulkUploadJobTitles']);

        // JOB GRADES ROUTES
        Route::get('job-grades-list', [JobGradeController::class, 'jobGradeList']);
        Route::post('job-grades-create', [JobGradeController::class, 'jobGradeCreate']);
        Route::post('job-grades-edit', [JobGradeController::class, 'jobGradeEdit']);
        Route::delete('job-grades-delete/{id}', [JobGradeController::class, 'jobGradeDelete']);

        Route::post('job-grades-bulk-upload', [HrBulkUploadController::class, 'bulkUploadJobGrades']);

        // JOB GRADES ROUTES
        Route::get('salutations-list', [SalutationController::class, 'salutationList']);
        Route::post('salutations-create', [SalutationController::class, 'salutationCreate']);
        Route::post('salutations-edit', [SalutationController::class, 'salutationEdit']);
        Route::delete('salutations-delete/{id}', [SalutationController::class, 'salutationDelete']);

        // NATIONALITIES ROUTES
        Route::get('nationalities-list', [NationalityController::class, 'nationalityList']);
        Route::post('nationalities-create', [NationalityController::class, 'nationalityCreate']);
        Route::post('nationalities-edit', [NationalityController::class, 'nationalityEdit']);
        Route::delete('nationalities-delete/{id}', [NationalityController::class, 'nationalityDelete']);

        Route::post('nationalities-bulk-upload', [HrBulkUploadController::class, 'bulkUploadNationalities']);

        // MARITAL STATUSES ROUTES
        Route::get('marital-statuses-list', [MaritalStatusController::class, 'maritalStatusList']);
        Route::post('marital-statuses-create', [MaritalStatusController::class, 'maritalStatusCreate']);
        Route::post('marital-statuses-edit', [MaritalStatusController::class, 'maritalStatusEdit']);
        Route::delete('marital-statuses-delete/{id}', [MaritalStatusController::class, 'maritalStatusDelete']);

        // TERMINATION TYPES ROUTES
        Route::get('termination-types-list', [TerminationTypeController::class, 'terminationTypeList']);
        Route::post('termination-types-create', [TerminationTypeController::class, 'terminationTypeCreate']);
        Route::post('termination-types-edit', [TerminationTypeController::class, 'terminationTypeEdit']);
        Route::delete('termination-types-delete/{id}', [TerminationTypeController::class, 'terminationTypeDelete']);

        // INDISCIPLINE CATEGORY ROUTES
        Route::get('discipline-categories-list', [DisciplineCategoryController::class, 'disciplineCategoryList']);
        Route::post('discipline-categories-create', [DisciplineCategoryController::class, 'disciplineCategoryCreate']);
        Route::post('discipline-categories-edit', [DisciplineCategoryController::class, 'disciplineCategoryEdit']);
        Route::delete('discipline-categories-delete/{id}', [DisciplineCategoryController::class, 'disciplineCategoryDelete']);

        // DISCIPLINE ACTION ROUTES
        Route::get('discipline-actions-list', [DisciplineActionController::class, 'disciplineActionList']);
        Route::post('discipline-actions-create', [DisciplineActionController::class, 'disciplineActionCreate']);
        Route::post('discipline-actions-edit', [DisciplineActionController::class, 'disciplineActionEdit']);
        Route::delete('discipline-actions-delete/{id}', [DisciplineActionController::class, 'disciplineActionDelete']);

        // EDUCATION LEVELS ROUTES
        Route::get('education-levels-list', [EducationlevelController::class, 'educationLevelList']);
        Route::post('education-levels-create', [EducationlevelController::class, 'educationLevelCreate']);
        Route::post('education-levels-edit', [EducationlevelController::class, 'educationLevelEdit']);
        Route::delete('education-levels-delete/{id}', [EducationlevelController::class, 'educationLevelDelete']);

        // NHIF ROUTES
        Route::get('nhif-list', [NHIFController::class, 'nhifList']);
        Route::post('nhif-create', [NHIFController::class, 'nhifCreate']);
        Route::post('nhif-edit', [NHIFController::class, 'nhifEdit']);
        Route::delete('nhif-delete/{id}', [NHIFController::class, 'nhifDelete']);

        // PAYE ROUTES
        Route::get('paye-list', [PayeController::class, 'payeList']);
        Route::post('paye-create', [PayeController::class, 'payeCreate']);
        Route::post('paye-edit', [PayeController::class, 'payeEdit']);
        Route::delete('paye-delete/{id}', [PayeController::class, 'payeDelete']);

        // PENSION ROUTES
        Route::get('pension-list', [PensionController::class, 'pensionList']);
        Route::post('pension-create', [PensionController::class, 'pensionCreate']);
        Route::post('pension-edit', [PensionController::class, 'pensionEdit']);
        Route::delete('pension-delete/{id}', [PensionController::class, 'pensionDelete']);

        // NON CASH BENEFITS ROUTES
        Route::get('non-cash-benefit-list', [NonCashBenfitController::class, 'nonCashBenefitList']);
        Route::post('non-cash-benefit-create', [NonCashBenfitController::class, 'nonCashBenefitCreate']);
        Route::post('non-cash-benefit-edit', [NonCashBenfitController::class, 'nonCashBenefitEdit']);
        Route::delete('non-cash-benefit-delete/{id}', [NonCashBenfitController::class, 'nonCashBenefitDelete']);

        // ALLOWANCES ROUTES
        Route::get('allowance-list', [AllowanceController::class, 'allowanceList']);
        Route::post('allowance-create', [AllowanceController::class, 'allowanceCreate']);
        Route::post('allowance-edit', [AllowanceController::class, 'allowanceEdit']);
        Route::delete('allowance-delete/{id}', [AllowanceController::class, 'allowanceDelete']);

        // COMMISSIONS ROUTES
        Route::get('commission-list', [CommissionController::class, 'commissionList']);
        Route::post('commission-create', [CommissionController::class, 'commissionCreate']);
        Route::post('commission-edit', [CommissionController::class, 'commissionEdit']);
        Route::delete('commission-delete/{id}', [CommissionController::class, 'commissionDelete']);

        // SACCO ROUTES
        Route::get('sacco-list', [SaccoController::class, 'saccoList']);
        Route::post('sacco-create', [SaccoController::class, 'saccoCreate']);
        Route::post('sacco-edit', [SaccoController::class, 'saccoEdit']);
        Route::delete('sacco-delete/{id}', [SaccoController::class, 'saccoDelete']);

        // CUSTOM PARAMETER ROUTES
        Route::get('custom-parameter-list', [CustomParameterController::class, 'customParameterList']);
        Route::post('custom-parameter-create', [CustomParameterController::class, 'customParameterCreate']);
        Route::post('custom-parameter-edit', [CustomParameterController::class, 'customParameterEdit']);
        Route::delete('custom-parameter-delete/{id}', [CustomParameterController::class, 'customParameterDelete']);

        // LOAN TYPES ROUTES
        Route::get('loan-type-list', [LoanTypeController::class, 'loanTypeList']);
        Route::post('loan-type-create', [LoanTypeController::class, 'loanTypeCreate']);
        Route::post('loan-type-edit', [LoanTypeController::class, 'loanTypeEdit']);
        Route::delete('loan-type-delete/{id}', [LoanTypeController::class, 'loanTypeDelete']);

        // BANK ROUTES
        Route::get('bank-list', [BankController::class, 'bankList']);
        Route::post('bank-create', [BankController::class, 'bankCreate']);
        Route::post('bank-edit', [BankController::class, 'bankEdit']);
        Route::delete('bank-delete/{id}', [BankController::class, 'bankDelete']);

        Route::post('banks-bulk-upload', [HrBulkUploadController::class, 'bulkUploadBanks']);

        // BANK BRANCH ROUTES
        Route::get('bank-branch-list', [BankBranchController::class, 'bankBranchList']);
        Route::post('bank-branch-create', [BankBranchController::class, 'bankBranchCreate']);
        Route::post('bank-branch-edit', [BankBranchController::class, 'bankBranchEdit']);
        Route::delete('bank-branch-delete/{id}', [BankBranchController::class, 'bankBranchDelete']);

        // PAYMENT MODES ROUTES
        Route::get('payment-mode-list', [HrPaymentModeController::class, 'paymentModeList']);
        Route::post('payment-mode-create', [HrPaymentModeController::class, 'paymentModeCreate']);
        Route::post('payment-mode-edit', [HrPaymentModeController::class, 'paymentModeEdit']);
        Route::delete('payment-mode-delete/{id}', [HrPaymentModeController::class, 'paymentModeDelete']);

        // PAYMENT FREQUENCIES ROUTES
        Route::get('payment-frequency-list', [PaymentFrequencyController::class, 'paymentFrequencyList']);
        Route::post('payment-frequency-create', [PaymentFrequencyController::class, 'paymentFrequencyCreate']);
        Route::post('payment-frequency-edit', [PaymentFrequencyController::class, 'paymentFrequencyEdit']);
        Route::delete('payment-frequency-delete/{id}', [PaymentFrequencyController::class, 'paymentFrequencyDelete']);

        // Relationships
        Route::apiResource('relationships', RelationshipController::class);
        Route::post('relationships/{id}', [RelationshipController::class, 'update']);
        
        Route::apiResource('document-types', DocumentTypeController::class);
        Route::post('document-types/{id}', [DocumentTypeController::class, 'update']);

        Route::apiResource('earnings', EarningController::class);
        Route::post('earnings/{earning}', [EarningController::class, 'update']);

        Route::apiResource('deductions', DeductionController::class);
        Route::post('deductions/{deduction}', [DeductionController::class, 'update']);
        
        // Route::post('deduction-brackets', [DeductionBracketController::class, 'saveBrackets']);

        Route::apiResource('reliefs', ReliefController::class);
        Route::post('reliefs/{relief}', [ReliefController::class, 'update']);

        Route::apiResource('payes', PayeController::class);
        Route::post('payes/{paye}', [PayeController::class, 'update']);

        Route::apiResource('nssfs', NssfController::class);
        Route::post('nssfs/{nssf}', [NssfController::class, 'update']);

        Route::apiResource('shif', ShifController::class);
        Route::post('shif/{shif}', [ShifController::class, 'update']);

        Route::apiResource('housing-levy', HousingLevyController::class);
        Route::post('housing-levy/{housingLevy}', [HousingLevyController::class, 'update']);

        Route::apiResource('payroll-settings', PayrollSettingController::class);
        Route::post('payroll-settings/{setting}', [PayrollSettingController::class, 'update']);
    });

    Route::prefix('management')->group(function () {
        Route::get('employees-list', [EmployeeController::class, 'employeesList']);
        Route::post('employees-create', [EmployeeController::class, 'employeesCreate']);
        Route::post('employees-draft-edit', [EmployeeController::class, 'employeesDraftEdit']);
        Route::post('employees-edit', [EmployeeController::class, 'employeesEdit']);

        Route::get('line-managers', [EmployeeController::class, 'lineManagers']);
        Route::get('line-managers-by-branch/{branchId}', [EmployeeController::class, 'lineManagersByBranch']);

        Route::post('employees-bulk-upload', [HrBulkUploadController::class, 'bulkUploadEmployees']);
        Route::post('casuals-bulk-upload', [HrBulkUploadController::class, 'bulkUploadCasuals']);

        Route::get('employee-emergency-contacts-list/{employee}', [EmployeeEmergencyContactController::class, 'employeeEmergencyContactList']);
        Route::post('employee-emergency-contacts-create', [EmployeeEmergencyContactController::class, 'employeeEmergencyContactCreate']);
        Route::post('employee-emergency-contacts-edit/{id}', [EmployeeEmergencyContactController::class, 'employeeEmergencyContactEdit']);
        Route::delete('employee-emergency-contacts-delete/{id}', [EmployeeEmergencyContactController::class, 'employeeEmergencyContactDelete']);

        Route::get('employee-education-histories-list/{employee}', [EmployeeEducationHistoryController::class, 'employeeEducationHistoryList']);
        Route::post('employee-education-histories-create', [EmployeeEducationHistoryController::class, 'employeeEducationHistoryCreate']);
        Route::post('employee-education-histories-edit/{id}', [EmployeeEducationHistoryController::class, 'employeeEducationHistoryEdit']);
        Route::delete('employee-education-histories-delete/{id}', [EmployeeEducationHistoryController::class, 'employeeEducationHistoryDelete']);

        Route::get('employee-professional-histories-list/{employee}', [EmployeeProfessionalHistoryController::class, 'employeeProfessionalHistoryList']);
        Route::post('employee-professional-histories-create', [EmployeeProfessionalHistoryController::class, 'employeeProfessionalHistoryCreate']);
        Route::post('employee-professional-histories-edit/{id}', [EmployeeProfessionalHistoryController::class, 'employeeProfessionalHistoryEdit']);
        Route::delete('employee-professional-histories-delete/{id}', [EmployeeProfessionalHistoryController::class, 'employeeProfessionalHistoryDelete']);

        Route::get('employee-documents-list/{employee}', [EmployeeDocumentController::class, 'employeeDocumentList']);
        Route::post('employee-documents-create', [EmployeeDocumentController::class, 'employeeDocumentCreate']);
        Route::post('employee-documents-edit/{id}', [EmployeeDocumentController::class, 'employeeDocumentEdit']);
        Route::delete('employee-documents-delete/{id}', [EmployeeDocumentController::class, 'employeeDocumentDelete']);

        // Employee Beneficiaries
        Route::get('employee-beneficiaries-list/{employee}', [EmployeeBeneficiaryController::class, 'employeeBeneficiariesList']);
        Route::post('employee-beneficiaries-update', [EmployeeBeneficiaryController::class, 'employeeBeneficiariesUpdate']);

        Route::apiResource('casuals', CasualController::class);
        Route::post('casuals/{casual}', [CasualController::class, 'update']);
        Route::post('casuals-activate/{casual}', [CasualController::class, 'activate']);
        Route::post('casuals-deactivate/{casual}', [CasualController::class, 'deactivate']);
    });

    Route::prefix('payroll')->group(function () {
        Route::get('payroll-months-list', [PayrollMonthController::class, 'payrollMonthsList']);
        Route::post('payroll-month-open', [PayrollMonthController::class, 'openPayrollMonth']);
        Route::get('payroll-month/{id}', [PayrollMonthController::class, 'payrollMonth']);
        Route::post('payroll-month/{id}/close', [PayrollMonthController::class, 'payrollMonthClose']);
        
        Route::post('payroll-months/{id}/earnings-and-deductions-upload', [HrBulkUploadController::class, 'earningsAndDeductionsUpload']);
        
        Route::post('payroll-month-detail-edit/{payrollMonthDetail}', [PayrollMonthController::class, 'payrollMonthDetailEdit']);

        Route::get('process-payroll/{payrollMonth}', [PayrollMonthController::class, 'processPayroll']);

        // CASUAL PAY PERIODS
        Route::get('casuals-pay-periods-list', [CasualsPayPeriodController::class, 'casualsPayPeriodsList']);
        Route::post('casuals-pay-periods-open', [CasualsPayPeriodController::class, 'casualsPayPeriodsOpen']);
        Route::get('casuals-pay-period/{casualsPayPeriod}', [CasualsPayPeriodController::class, 'casualsPayPeriod']);
        Route::post('casuals-pay-period-details/{casualsPayPeriod}/update', [CasualsPayPeriodController::class, 'casualsPayPeriodDetailsUpdate']);
        Route::post('casuals-pay-period-details/{casualsPayPeriod}/approve', [CasualsPayPeriodController::class, 'casualsPayPeriodDetailsApprove']);
        Route::post('casuals-pay-period-details/{casualsPayPeriod}/refresh-casuals-list', [CasualsPayPeriodController::class, 'casualsPayPeriodDetailsRefreshCasualsList']);
        
        Route::post('casuals-pay-period-details/{id}/upload-register', [CasualsPayPeriodController::class, 'casualsPayPeriodDetailsUploadRegister']);
        
        Route::prefix('casual-pay')->group(function () {
            Route::get('successful-disbursements', [CasualPayDisbursementController::class, 'successfulDisbursements']);
            Route::get('failed-disbursements', [CasualPayDisbursementController::class, 'failedDisbursements']);
            Route::get('expunged-disbursements', [CasualPayDisbursementController::class, 'expungedDisbursements']);
            Route::post('failed-disbursements-recheck-and-resend', [CasualPayDisbursementController::class, 'failedDisbursementsRecheckAndResend']);            
            Route::post('failed-disbursements-expunge', [CasualPayDisbursementController::class, 'failedDisbursementsExpunge']);            
        });

        // REPORTS
        Route::prefix('reports')->group(function () {
            Route::post('paymaster-report', [HrPayrollReportController::class, 'generatePaymasterReport']);
            Route::post('payroll-summary-report', [HrPayrollReportController::class, 'generatePayrollSummaryReport']);
            Route::post('earnings-report', [HrPayrollReportController::class, 'generateEarningsReport']);
            Route::post('deductions-report', [HrPayrollReportController::class, 'generateDeductionsReport']);
            Route::post('consolidated-payroll-report', [HrPayrollReportController::class, 'generateConsolidatedPayrollReport']);
            
            Route::post('paye-deductions-report', [HrPayrollReportController::class, 'generatePayeDeductionsReport']);
            Route::post('nssf-deductions-report', [HrPayrollReportController::class, 'generateNssfDeductionsReport']);
            Route::post('shif-deductions-report', [HrPayrollReportController::class, 'generateShifDeductionsReport']);
            Route::post('housing-levy-deductions-report', [HrPayrollReportController::class, 'generateHousingLevyDeductionsReport']);
            Route::post('other-deductions-report', [HrPayrollReportController::class, 'generateOtherDeductionsReport']);
        });
    });
});

Route::post('hr/payroll/casual-pay/disbursement/{disbursementId}/callback', [CasualPayDisbursementController::class, 'disbursementCallback']);
