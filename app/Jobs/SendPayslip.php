<?php

namespace App\Jobs;

use Exception;
use App\Mail\Payslip;
use Illuminate\Bus\Queueable;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\PayrollMonthDetail;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class SendPayslip implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(protected PayrollMonthDetail $payrollMonthDetail, protected Collection $reliefs)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $reliefs = $this->reliefs;
        $payrollMonthDetail = $this->payrollMonthDetail;
        
        try {
            $pdf = Pdf::loadView('admin.hr.payroll.payslip', compact('payrollMonthDetail', 'reliefs'));

            $payslip = $pdf->output();
            
            Mail::to($this->payrollMonthDetail->employee->email)->send(new Payslip($this->payrollMonthDetail, $payslip));
            
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
