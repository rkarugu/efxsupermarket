<?php

namespace App\Exports\Finance;
 
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
class GLJournalInquiryExport implements FromCollection, WithHeadings
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return collect($this->data);
    }

    public function headings(): array
    {
        
        return [
            'Date',
            'Transaction type',
            'Transaction No',
            'Account',
            'Account Description',
            'Narrative',
            'Reference',
            'Tag',
            'Debit',
            'Credit',
            
        ];
    }
}
