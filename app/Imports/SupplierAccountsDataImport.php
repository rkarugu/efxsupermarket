<?php

namespace App\Imports;

use App\Model\WaSupplier;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SupplierAccountsDataImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows): void
    {
        foreach ($rows as $row) {
            $supplier = WaSupplier::where('supplier_code', $row['supplier_code'])->first();

            if (is_null($supplier)) {
                continue;
            }

            $supplier->update([
                'kra_pin' => $row['kra_pin'],
                'tax_withhold' => $row['withholding_tax'],
                'bank_name' => $row['bank_name'],
                'bank_branch' => $row['bank_branch'],
                'bank_account_no' => $row['bank_account_no'],
                'bank_swift' => $row['bank_swift'],
            ]);
        }
    }
}
