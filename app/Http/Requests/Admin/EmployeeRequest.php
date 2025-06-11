<?php

namespace App\Http\Requests\Admin;

use Illuminate\Support\Str;
use Illuminate\Foundation\Http\FormRequest;

class EmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'first_name' => 'nullable|string',
            'middle_name' => 'nullable|string',
            'last_name' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'id_no' => 'nullable|string',
            'passport_no' => 'nullable|string',
            'gender_id' => 'nullable|integer|exists:genders,id',
            'salutation_id' => 'nullable|integer|exists:salutations,id',
            'marital_status_id' => 'nullable|integer|exists:marital_statuses,id',
            'nationality_id' => 'nullable|integer|exists:nationalities,id',
            'payment_mode_id' => 'nullable|integer|exists:payment_modes,id',
            'education_level_id' => 'nullable|integer|exists:education_levels,id',
            'image' => 'nullable|file',
            'email' => 'nullable|string|email',
            'phone_no' => 'nullable|string',
            'alternative_phone_no' => 'nullable|string',
            'residential_address' => 'nullable|string',
            'postal_address' => 'nullable|string',
            'postal_code' => 'nullable|string',
            'branch_id' => 'nullable|integer|exists:restaurants,id',
            'department_id' => 'nullable|integer|exists:wa_departments,id',
            'employment_type_id' => 'nullable|integer|exists:employment_types,id',
            'job_title_id' => 'nullable|integer|exists:job_titles,id',
            'job_grade_id' => 'nullable|integer|exists:job_grades,id',
            'employee_no' => 'nullable|string',
            'work_email' => 'nullable|string|email',
            'employment_date' => 'nullable|date',
            'terminated_date' => 'nullable|date',
            'employment_status_id' => 'nullable|integer|exists:employment_statuses,id',
            'line_manager_id' => 'nullable|integer|exists:employees,id',
            'pin_no' => 'nullable|string',
            'nssf_no' => 'nullable|string',
            'nhif_no' => 'nullable|string',
            'helb_no' => 'nullable|string',
            'basic_pay' => 'nullable|integer',
            'inclusive_of_house_allowance' => 'nullable',
            'is_line_manager' => 'nullable',
            'is_draft' => 'nullable',
        ];

        if (Str::endsWith($this->url(), 'edit')) {
            $rules['id'] = 'required|integer|exists:employees,id';
        }
        
        return $rules;
    }
}
