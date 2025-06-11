<?php

namespace App\Imports\HR;

use App\Models\JobGrade;
use App\Models\JobLevel;


class JobGradesImport extends BaseImport
{
    public function model($row)
    {
        return JobGrade::firstOrCreate([
            'job_level_id' => JobLevel::where('name', $row['job_level'])->first()->id,
            'name' => $row['job_grade'],
            'min_salary' => $row['minimum_salary'],
            'max_salary' => $row['maximum_salary'],
            'description' => $row['description']
        ]);
    }

    public function rules(): array
    {
        return [
            'job_level' => 'required|exists:job_levels,name',
            'job_grade' => 'required|string|max:255',
            'minimum_salary' => 'required|integer',
            'maximum_salary' => 'required|integer',
            'description' => 'nullable|string'
        ]; 
    }
}
