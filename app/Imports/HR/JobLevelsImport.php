<?php

namespace App\Imports\HR;

use App\Models\JobGroup;
use App\Models\JobLevel;

class JobLevelsImport extends BaseImport
{
    public function model($row)
    {
        return JobLevel::firstOrCreate([
            'job_group_id' => JobGroup::where('name', $row['job_group'])->first()->id,
            'name' => $row['job_level'],
            'description' => $row['description']
        ]);
    }

    public function rules(): array
    {
        return [
            'job_group' => 'required|exists:job_groups,name',
            'job_level' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]; 
    }
}
