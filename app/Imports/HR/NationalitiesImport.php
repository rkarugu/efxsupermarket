<?php

namespace App\Imports\HR;

use App\Models\Nationality;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class NationalitiesImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows, SkipsOnFailure
{
    use Importable, SkipsFailures;

    public function model(array $row)
    {
        $nationality = Nationality::firstOrCreate([
            'name' => $row['nationality']
        ]);
        
        return $nationality;
    }

    public function rules(): array
    {
        return [
            'nationality' => 'required|string|max:255'
        ];
    }
}
