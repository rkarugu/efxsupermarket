<?php

namespace App\Imports\HR;

use App\Models\Casual;
use App\Models\Gender;
use App\Model\Restaurant;
use App\Models\Nationality;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;


class CasualsImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows, SkipsOnFailure
{
    use Importable, SkipsFailures;

    public function model(array $row)
    {
        return Casual::updateOrCreate([
                'phone_no' => $row['phone_no'],
            ],
            [
                'first_name' => $row['first_name'],
                'middle_name' => $row['middle_name'],
                'last_name' => $row['last_name'],
                'date_of_birth' => $row['date_of_birth'] ? $this->formatDate($row['date_of_birth']) : null,
                'id_no' => $row['id_no'],
                'phone_no' => $row['phone_no'],
                'email' => $row['email'],
                'gender_id' => Gender::where('name', $row['gender'])->first()->id,
                'nationality_id' => Nationality::where('name', $row['nationality'])->first()->id,
                'branch_id' => Restaurant::where('name', $row['branch'])->first()->id,
                'active' => $row['active'],
            ]
        );
    }

    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'nullable|integer',
            'id_no' => 'nullable',
            'phone_no' => 'required|string|min:10|max:255',
            'email' => 'nullable|email|max:255',
            'gender' => 'required|exists:genders,name',
            'nationality' => 'required|exists:nationalities,name',
            'branch' => 'required|exists:restaurants,name',
            'active' => 'nullable|in:yes,no',
        ];
    }

    public function formatDate($value)
    {
        return Carbon::parse(Date::excelToDateTimeObject($value))->format('Y-m-d');
    }
}
