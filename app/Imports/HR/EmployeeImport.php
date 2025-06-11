<?php

namespace App\Imports\HR;

use Exception;
use App\Models\Bank;
use App\Models\Gender;
use App\Models\Employee;
use App\Models\JobGrade;
use App\Models\JobTitle;
use App\Model\Restaurant;
use App\Models\BankBranch;
use App\Models\Salutation;
use App\Model\WaDepartment;
use App\Models\Nationality;
use App\Models\PaymentMode;
use App\Models\Relationship;
use App\Models\MaritalStatus;
use App\Models\EducationLevel;
use App\Models\EmploymentType;
use Illuminate\Support\Carbon;
use App\Models\EmploymentStatus;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class EmployeeImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows, SkipsOnFailure
{
    use Importable, SkipsFailures;

    public function model(array $row)
    {
        DB::BeginTransaction();
        try {
            $employee = Employee::updateOrCreate([
                'id_no' => $row['id_no'],
                'pin_no' => $row['pin_no'],
            ],[
                'first_name' => $row['first_name'],
                'middle_name' => $row['middle_name'],
                'last_name' => $row['last_name'],
                'date_of_birth' => $this->formatDate($row['date_of_birth']),
                'gender_id' => Gender::where('name', $row['gender'])->first()->id,
                'salutation_id' => Salutation::where('name', $row['salutation'])->first()->id,
                'marital_status_id' => MaritalStatus::where('name', $row['marital_status'])->first()->id,
                'nationality_id' => Nationality::where('name', $row['nationality'])->first()->id,
                'education_level_id' => EducationLevel::where('name', $row['education_level'])->first()->id,
                'passport_no' => $row['passport_no'],
                'email' => $row['email'],
                'work_email' => $row['work_email'],
                'phone_no' => $row['phone_no'],
                'alternative_phone_no' => $row['alternative_phone_no'],
                'residential_address' => $row['residential_address'],
                'postal_address' => $row['postal_address'],
                'postal_code' => $row['postal_code'],
                'branch_id' => Restaurant::where('name', $row['branch'])->first()->id,
                'department_id' => WaDepartment::where('department_name', $row['department'])->first()->id,
                'employment_status_id' => EmploymentStatus::where('name', 'Active')->first()->id,
                'employment_type_id' => EmploymentType::where('name', $row['employment_type'])->first()->id,
                'job_title_id' => JobTitle::where('name', $row['job_title'])->first()->id,
                'job_grade_id' => JobGrade::where('name', $row['job_grade'])->first()->id,
                'employment_date' => $this->formatDate($row['employment_date']),
                'is_line_manager' => $row['is_line_manager'],
                'payroll_no' => $row['payroll_no'],
                'nssf_no' => $row['nssf_no'],
                'nhif_no' => $row['nhif_no'],
                'helb_no' => $row['helb_no'],
                'basic_pay' => $row['basic_pay'],
                'inclusive_of_house_allowance' => $row['basic_pay_inclusive_of_house_allowance'],
                'eligible_for_overtime' => $row['eligible_for_overtime'],
                'payment_mode_id' => PaymentMode::where('name', $row['payment_mode'])->first()->id,

            ]);

            if (isset(
                $row['emergency_contact_name'],
                $row['emergency_contact_phone_no'],
                $row['emergency_contact_email'],
                $row['emergency_contact_relationship'],
            )) {
                $relationship = Relationship::where('name', $row['emergency_contact_relationship'])->first();
    
                if (!$relationship) {
                    $relationship = Relationship::where('name', 'Other')->first();
                }

                $employee->emergencyContacts()->updateOrCreate([
                    'relationship_id' => $relationship->id,
                    'full_name' => $row['emergency_contact_name'],
                ],[
                    'custom_relationship' => $relationship->name == 'Other' ? $row['emergency_contact_relationship'] : null,
                    'email' => $row['emergency_contact_email'],
                    'phone_no' => $row['emergency_contact_phone_no'],
                ]);
            }

            if (isset(
                $row['bank'],
                $row['bank_branch'],
                $row['account_no'],
            )) {
                $employee->employeeBankAccounts()->updateOrCreate([
                    'bank_id' => Bank::where('name', $row['bank'])->first()->id,
                    'bank_branch_id' => BankBranch::where('name', $row['bank_branch'])->first()->id,
                ],[
                    'account_name' => $row['account_name'] ?? $employee->full_name,
                    'account_no' => $row['account_no'],
                    'primary' => true,
                ]);
            }

            if (isset(
                $row['branch_id'], 
                $row['department_id'], 
                $row['employment_type_id'], 
                $row['job_title_id'], 
                $row['employment_date'], 
            )) {
                $employee->contracts()->updateOrCreate([
                    'branch_id' => $row['branch_id'],
                ],[
                    'department_id' => $row['department_id'],
                    'employment_type_id' => $row['employment_type_id'],
                    'job_title_id' => $row['job_title_id'],
                    'start_date' => $row['employment_date'],
                    'end_date' => $row['contract_end_date'],
                ]);
            }

            DB::commit();
            
            return $employee;
        } catch (Exception $e) {
            DB::rollback();
            // 
        }
    }

    public function rules(): array
    {
        return [
            'first_name' => 'nullable|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|integer',
            'gender' => 'required|exists:genders,name',
            'salutation' => 'nullable|exists:salutations,name',
            'marital_status' => 'nullable|exists:marital_statuses,name',
            'nationality' => 'required|exists:nationalities,name',
            'education_level' => 'required|exists:education_levels,name',
            'id_no' => 'required',
            'passport_no' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'work_email' => 'nullable|email|max:255',
            'phone_no' => 'required|string|min:10|max:255',
            'alternative_phone_no' => 'nullable|string|min:10|max:255',
            'residential_address' => 'required|string|max:255',
            'postal_address' => 'nullable|max:255',
            'postal_code' => 'nullable|string|max:255',
            'branch' => 'required|exists:restaurants,name',
            'department' => 'required|exists:wa_departments,department_name',
            'employment_type' => 'required|exists:employment_types,name',
            'job_title' => 'required|exists:job_titles,name',
            'job_grade' => 'required|exists:job_grades,name',
            'employment_date' => 'required|integer',
            'contract_end_date' => 'nullable|integer',
            'is_line_manager' => 'required|in:yes,no',
            'payroll_no' => 'required|max:255',
            'pin_no' => 'required|max:255',
            'nssf_no' => 'required|max:255',
            'nhif_no' => 'required|max:255',
            'helb_no' => 'nullable|string|max:255',
            'basic_pay' => 'required|numeric',
            'basic_pay_inclusive_of_house_allowance' => 'required|in:yes,no',
            'eligible_for_overtime' => 'required|in:yes,no',
            'payment_mode' => 'required|exists:payment_modes,name',
            'bank' => 'nullable|exists:banks,name',
            'bank_branch' => 'nullable|exists:bank_branches,name',
            'account_name' => 'nullable|max:255',
            'account_no' => 'nullable|max:255',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone_no' => 'nullable|string|min:10|max:255',
            'emergency_contact_email' => 'nullable|email|max:255',
            'emergency_contact_relationship' => 'nullable|exists:relationships,name',
        ];
    }

    public function formatDate($value)
    {
        return Carbon::parse(Date::excelToDateTimeObject($value))->format('Y-m-d');
    }
}
