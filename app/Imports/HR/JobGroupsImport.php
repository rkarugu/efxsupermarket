<?php

namespace App\Imports\HR;

use App\Models\JobGroup;


class JobGroupsImport extends BaseImport
{
    public function model($row) {
        return JobGroup::firstOrCreate([
            'name' => $row['job_group'],
            'description' => $row['description'],
        ]);
    }

    public function rules(): array {
        return [
            'job_group' => 'required|string|max:255',
            'description' => 'nullable|string'
        ];
    }
}
