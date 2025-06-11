<?php

namespace App\Exports\HR;

use Carbon\CarbonPeriod;
use Illuminate\Support\Carbon;
use App\Models\CasualsPayPeriod;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class CasualsPayPeriodRegisterTemplate implements FromCollection, WithHeadings, ShouldAutoSize, WithStrictNullComparison
{
    protected $headings;
    
    public function __construct(protected Collection $data, protected $id)
    {
        $payPeriod = CasualsPayPeriod::with('casuals')->find($this->id);

        $this->headings = [
            'Phone No.'
        ];

        $period = CarbonPeriod::create(Carbon::parse($payPeriod->start_date), Carbon::parse($payPeriod->end_date));

        foreach ($period as $date) {
            array_push($this->headings, $date->format('Y-m-d'));
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
}
