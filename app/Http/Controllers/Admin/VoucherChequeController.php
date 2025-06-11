<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\PaymentVoucherCheque;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VoucherChequeController extends Controller
{
    public function store(Request $request, $code)
    {
        $this->validate($request, [
            'cheq_number' => 'required'
        ]);

        $cheque = PaymentVoucherCheque::create([
            'wa_supplier_code' => $code,
            'number' => $request->input('cheq_number'),
            'payment_voucher_id' => $request->input('payment_voucher_id'),
            'date' => $request->input('cheq_date'),
            'amount' => $request->input('cheq_amount'),
        ]);

        if (Str::contains($request->input('cheq_number'), 'CHQ-')) {
            updateUniqueNumberSeries('CHEQUES', $cheque->number);
        }

        return response()->json([
            'success' => true,
            'cheq_number' => getCodeWithNumberSeries('CHEQUES')
        ]);
    }

    public function destroy(Request $request, $code)
    {
        $cheque = PaymentVoucherCheque::query()
            ->where('number', $request->cheq_number)->firstOrFail();

        $cheque->delete();

        return response()->json([
            'success' => true,
            'cheq_number' => getCodeWithNumberSeries('CHEQUES')
        ]);
    }
}
