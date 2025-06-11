<?php

namespace App\Imports\HR;

use App\Models\Casual;
use Carbon\CarbonPeriod;
use App\Models\PayrollSetting;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use App\Models\CasualsPayPeriod;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class CasualsPayPeriodRegisterImport implements ToCollection, WithHeadingRow, WithValidation, SkipsEmptyRows, SkipsOnFailure
{
    use Importable, SkipsFailures;
    
    protected $period;
    protected $payPeriod;
    protected $casualPay;
    
    public function __construct($payPeriodId)
    {
        $this->casualPay = PayrollSetting::where('name', 'Casual Pay')->first()->value;
        $this->payPeriod = CasualsPayPeriod::find($payPeriodId);
        $this->period = CarbonPeriod::create(Carbon::parse($this->payPeriod->start_date), Carbon::parse($this->payPeriod->end_date));
    }

    public function collection(Collection $rows)
    {
        foreach($rows as $i => $row) {
            $payPeriodDetail = $this->payPeriod->casualsPayPeriodDetails()
                ->whereHas('casual', fn ($casual) => $casual->where('phone_no', $row['phone_no']))
                ->first();

            if (!$payPeriodDetail) {
                continue;
            }

            $amount = 0;
            $dates = [];

            foreach ($this->period as $i => $date) {
                $index = str_replace('-', '_', $date->format('Y-m-d'));
                $present = $row[$index] == 'p' || $row[$index] == 'P' ? true : false;
                
                $dates[$date->format('Y-m-d')] = $present;

                $amount += $present ? (float) $this->casualPay : 0;
            }

            $payPeriodDetail->update([
                'dates' => $dates,
                'amount' => $amount
            ]);
        }
    }

    public function rules(): array
    {
        $rules = [
            'phone_no' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    $casual = Casual::where('phone_no', $value)
                        ->where('active', true)
                        ->first();
                    
                    if (!$casual) { // Adjust this condition based on your needs
                        $fail("Casual with this phone no. does not exist or is inactive.");
                    }
                },
            ],
        ];

        foreach ($this->period as $date) {
            $index = str_replace('-', '_', $date->format('Y-m-d'));
            $rules["$index"] = 'required|string|in:p,a,P,A';
        }

        return $rules;
    }
}
