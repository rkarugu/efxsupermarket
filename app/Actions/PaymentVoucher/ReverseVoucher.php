<?php

namespace App\Actions\PaymentVoucher;

use App\Model\WaBanktran;
use App\Model\WaGlTran;
use App\Model\WaSuppTran;
use App\PaymentVoucher;

class ReverseVoucher
{
    public function reverse(PaymentVoucher $voucher)
    {
        WaSuppTran::where('document_no', $voucher->number)->delete();

        WaGlTran::where('transaction_no', $voucher->number)->delete();
        
        WaBanktran::where('document_no', $voucher->number)->delete();

        $voucher->update([
            'status' => PaymentVoucher::APPROVED,
        ]);
    }
}
