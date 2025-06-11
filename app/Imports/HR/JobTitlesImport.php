<?php

namespace App\Imports\HR;

use App\Models\JobLevel;
use App\Models\JobTitle;


class JobTitlesImport extends BaseImport
{
    public function model($row)
    {
        return JobTitle::firstOrCreate([
            'job_level_id' => JobLevel::where('name', $row['job_level'])->first()->id,
            'name' => $row['job_title'],
            'description' => $row['description']
        ]);
    }

    public function rules(): array
    {
        return [
            'job_level' => 'required|exists:job_levels,name',
            'job_title' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]; 
    }
}
