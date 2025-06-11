<?php

namespace App\Exports\HR;

use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Style\Color;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class EmployeesBulkUpload implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithStrictNullComparison, WithColumnWidths
{
    protected $headings = [
        'First Name *',
        'Middle Name',
        'Last Name *',
        'Date of Birth *',
        'Gender [] *',
        'Salutation []',
        'Marital Status []',
        'Nationality [] *',
        'Education Level [] *',
        'ID No. *',
        'Passport No.',
        'Email *',
        'Work Email',
        'Phone No. *',
        'Alternative Phone No.',
        'Residential Address *',
        'Postal Address',
        'Postal Code',
        'Branch [] *',
        'Department [] *',
        'Employment Type [] *',
        'Job Title [] *',
        'Job Grade [] *',
        'Employment Date *',
        'Contract End Date',
        'Is Line Manager? *',
        'Payroll No. *',
        'PIN No. *',
        'NSSF No. *',
        'NHIF No. *',
        'HELB No.',
        'Basic Pay *',
        'Basic Pay Inclusive of House Allowance? *',
        'Eligible For Overtime? *',
        'Payment Mode []',
        'Bank',
        'Bank Branch',
        'Account Name',
        'Account No.',
        'Emergency Contact Name *',
        'Emergency Contact Phone No. *',
        'Emergency Contact Email *',
        'Emergency Contact Relationship *',
    ];
    
    public function __construct(protected Collection $data, protected string $type)
    {
        if ($type == 'error') {
            array_push($this->headings, 'Errors');
        }
    }

    public function collection(): Collection
    {
        return $this->data;
    }

    public function headings(): array
    {
        return $this->headings;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20
        ];
    }

    public function styles(Worksheet $sheet): void
    {
        $sheet->getStyle(1)->getFont()->setBold(true);
        
        if ($this->type == 'template') {
            $sheet->getStyle('A5:A19')->getFont()->setColor(new Color('ff0000'));
        } else if ($this->type == 'error') {
            $sheet->getStyle('AR')->getFont()->setColor(new Color('ff0000'));
        }
    }
}
