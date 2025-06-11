<?php

namespace App\Imports;


use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class PaymentBulkUploadImport2 implements ToCollection, WithHeadingRow, WithChunkReading
{

    public $verification;
    public $request;


    public function __construct($verification,$request)
    {
        $this->verification = $verification;
        $this->request = $request;
    }


    /**
     * @param Collection $collection
     */

    public function collection(Collection $collection): void
    {
        DB::transaction(function () use ($collection) {
            for ($i=0; $i < count($collection); $i++) { 
                try{
                    $row=$collection[$i];
                    if(is_int($row['date'])){
                        $date = intval($row['date']);
                        $date = Date::excelToDateTimeObject($date)->format('Y-m-d');
                    } else{
                        $date = date('Y-m-d', strtotime($row['date']));
                    }
                    
                    // $dateRange = $this->getDatesInRange($this->verification->start_date, $this->verification->end_date);
                    // if(in_array($date,$dateRange)){
                        DB::table('payment_verification_banks')->insert([
                            'payment_verification_id' => $this->verification,
                            'reference' => $row['reference'],
                            'amount' => (float)(str_replace(',', '', $row['amount'])),
                            'bank_date' => $date
                        ]);
                    // }
                } catch(\Exception $e)
                {
                    dd($e);
                }
            }
        });
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    function getDatesInRange($date1, $date2, $format = 'Y-m-d' ) {
        $dates = array();
        $current = strtotime($date1);
        $date2 = strtotime($date2);
        $stepVal = '+1 day';
        while( $current <= $date2 ) {
           $dates[] = date($format, $current);
           $current = strtotime($stepVal, $current);
        }
        return $dates;
    }
}
