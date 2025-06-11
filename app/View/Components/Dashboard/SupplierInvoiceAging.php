<?php

namespace App\View\Components\Dashboard;

use App\Model\WaUserSupplier;
use App\WaSupplierInvoice;
use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\View\Component;

class SupplierInvoiceAging extends Component
{
    protected $supplierIds = [];

    public function __construct()
    {
        if (!auth()->user()->isAdministrator()) {
            $this->supplierIds = WaUserSupplier::where('user_id', auth()->user()->id)->get()
                ->pluck('wa_supplier_id')->toArray();
        }
    }

    public function render(): View|Closure|string
    {
        return view('components.dashboard.supplier-invoice-aging', [
            'mostDaysPayable' => $this->getMostDaysPayable(),
            'aging' => $this->getInvoiceAging(),
        ]);
    }

    protected function getMostDaysPayable()
    {
        $result = WaSupplierInvoice::query()
            ->select([
                DB::raw('supplier_invoice_number as invoice_no'),
                DB::raw('DATEDIFF(CURDATE(), supplier_invoice_date) as days')
            ])
            ->when(count($this->supplierIds) > 0, function ($query) {
                $query->whereIn('supplier_id', $this->supplierIds);
            })
            ->whereDoesntHave('payments')
            ->oldest('supplier_invoice_date')
            ->first();

        return $result;
    }

    protected function getInvoiceAging()
    {
        $days_30 = WaSupplierInvoice::where('supplier_invoice_date', '>', now()->subDays(30)->toDateString())
            ->when(count($this->supplierIds) > 0, function ($query) {
                $query->whereIn('supplier_id', $this->supplierIds);
            })
            ->whereDoesntHave('payments')
            ->get();

        $days_30_60 = WaSupplierInvoice::whereBetween('supplier_invoice_date', [
            now()->subDays(60)->toDateString(),
            now()->subDays(30)->toDateString(),
        ])->when(count($this->supplierIds) > 0, function ($query) {
            $query->whereIn('supplier_id', $this->supplierIds);
        })
            ->whereDoesntHave('payments')
            ->get();

        $days_60_90 = WaSupplierInvoice::whereBetween('supplier_invoice_date', [
            now()->subDays(90)->toDateString(),
            now()->subDays(60)->toDateString(),
        ])->when(count($this->supplierIds) > 0, function ($query) {
            $query->whereIn('supplier_id', $this->supplierIds);
        })
            ->whereDoesntHave('payments')
            ->get();

        $days_90_120 = WaSupplierInvoice::whereBetween('supplier_invoice_date', [
            now()->subDays(120)->toDateString(),
            now()->subDays(90)->toDateString(),
        ])->when(count($this->supplierIds) > 0, function ($query) {
            $query->whereIn('supplier_id', $this->supplierIds);
        })
            ->whereDoesntHave('payments')
            ->get();

        $days_120 = WaSupplierInvoice::where('supplier_invoice_date', '<', now()->subDays(120)->toDateString())
            ->when(count($this->supplierIds) > 0, function ($query) {
                $query->whereIn('supplier_id', $this->supplierIds);
            })->whereDoesntHave('payments')
            ->get();

        return  collect([
            $days_30->count(),
            $days_30_60->count(),
            $days_60_90->count(),
            $days_90_120->count(),
            $days_120->count(),
        ]);
    }
}
