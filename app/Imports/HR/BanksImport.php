<?php

namespace App\Imports\HR;

use App\Models\Bank;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class BanksImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows, SkipsOnFailure
{
    use Importable, SkipsFailures;

    public function model(array $row)
    {
        $bankCode = $row['bank_code'];
        $branchCode = $row['branch_code'];
        if ($row['bank_ref']) {
            $bankCode = substr($row['bank_ref'], 0, 2);
            $branchCode = substr($row['bank_ref'], 2);
        }
        
        $bank = Bank::firstOrCreate([
            'name' => $row['bank'],
            'code' => $bankCode
        ]);

        $bank->branches()->firstOrCreate([
            'name' => $row['branch'],
            'branch_code' => $branchCode,
        ]);
        
        return $bank;
    }

    public function rules(): array
    {
        return [
            'bank' => 'required|string|max:255',
            'bank_code' => 'required_if:bank_ref,null|nullable|string|max:255',
            'branch' => 'required|string|max:255',
            'branch_code' => 'required_if:bank_ref,null|nullable|string|max:255',
            'bank_ref' => 'required_if:bank_code,null,branch_code,null|nullable|string|max:255',
        ];
    }
}
