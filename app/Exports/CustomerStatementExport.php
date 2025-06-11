<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use App\Model\WaNumerSeriesCode;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class CustomerStatementExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $data;

    public function __construct(Collection $data)
    {
        $this->data = $data;
    }

    public function collection(): Collection
    {
        $opBal = 0;
        $totalDebit = 0;
        $totalCredit = 0;
        $totalBalance = 0;

        $formattedData = $this->data->map(function($item) use (&$opBal, &$totalDebit, &$totalCredit, &$totalBalance) {
            $number_series_list = WaNumerSeriesCode::getNumberSeriesTypeList();

            $type = $number_series_list[$item->type_number];
            $docType = explode('-', $item->document_no);                

            $documentNumber = $item->document_no;
            if (count($docType) > 0 && in_array($docType[0], ['RTN'])) {
                $documentNumber = $item->document_no;
            } elseif (count($docType) > 0 && in_array($docType[0], ['INV'])) {
                $documentNumber = $item->document_no;
            }

            $balance = $opBal + (float)$item->amount;
            $opBal = $balance;

            $debit = ($item->amount > 0) ? $item->amount : 0;
            $credit = ($item->amount < 0) ? $item->amount : 0;

            $totalDebit += $debit;
            $totalCredit += $credit;
            $totalBalance = $balance;

            return [
                'DATE' => Carbon::parse($item->created_at)->toDateString(),
                'TYPE' => $type,
                'DOCUMENT' => $documentNumber,
                'NAME/REFERENCE' => $item->invoice_customer_name ? $item->invoice_customer_name : $item->reference,
                'DEBIT' => ($item->amount > 0) ? manageAmountFormat($item->amount) : '',
                'CREDIT' => ($item->amount < 0) ? manageAmountFormat($item->amount) : '',
                'TRANS BAL' => manageAmountFormat($item->amount),
                'BALANCE' => manageAmountFormat($balance),
            ];
        });
        
        $formattedData->push([
            'DATE' => '',
            'TYPE' => '',
            'DOCUMENT' => '',
            'NAME/REFERENCE' => 'TOTAL',
            'DEBIT' => manageAmountFormat($totalDebit),
            'CREDIT' => manageAmountFormat($totalCredit),
            'TRANS BAL' => '',
            'BALANCE' => manageAmountFormat($totalBalance),
        ]);

        return $formattedData;
    }

    public function headings(): array
    {
         return [
            'DATE',
            'TYPE',
            'DOCUMENT',
            'NAME/REFERENCE',
            'DEBIT',
            'CREDIT',
            'TRANS BAL',
            'BALANCE'            
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Boldens heading (first) row.
        $sheet->getStyle(1)->getFont()->setBold(true);
    }
}
