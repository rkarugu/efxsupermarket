<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Model\PaymentModes;
use Illuminate\Http\Request;
use App\Models\WaPaymentMode;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class PaymentModeController extends Controller
{
    protected $model;
    protected $title;

    public function __construct()
    {
        $this->model = 'payment-modes';
        $this->title = 'Payment Modes';
    }

    public function index()
    {
        if (!can('view', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        if (request()->wantsJson()) {
            $query = WaPaymentMode::query();

            return DataTables::eloquent($query)
                ->addIndexColumn()
                ->addColumn('actions', function ($paymentMode) {
                    return view('admin.payment_modes.action', ['paymentMode' => $paymentMode]);
                })
                ->toJson();
        }

        $breadcum = [
            'Payment Modes' => route('payment-modes.index')
        ];

        return view('admin.payment_modes.index', [
            'model' => $this->model,
            'title' => $this->title,
            'breadcum' => $breadcum,
        ]);
    }

    public function store(Request $request)
    {
        if (!can('add', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        WaPaymentMode::create([
            'mode' => $request->input('mode'),
            'description' => $request->input('description'),
        ]);

        Session::flash('success', 'Payment Mode created successfully.');

        return redirect()->route('payment-modes.index');
    }

    public function update(Request $request, WaPaymentMode $paymentMode)
    {
        if (!can('edit', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        $paymentMode->update([
            'mode' => $request->input('mode'),
            'description' => $request->input('description'),
        ]);

        Session::flash('success', 'Payment Mode updated successfully.');

        return redirect()->route('payment-modes.index');
    }

    public function destroy(WaPaymentMode $paymentMode)
    {
        if (!can('delete', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        if ($paymentMode->vouchers()->exists()) {
            return redirect()->route('payment-modes.index')->withErrors('Payment mode already in use. It canot be deleted');
        }

        $paymentMode->delete();

        Session::flash('success', 'Payment Mode deleted successfully.');

        return redirect()->route('payment-modes.index');
    }
}
