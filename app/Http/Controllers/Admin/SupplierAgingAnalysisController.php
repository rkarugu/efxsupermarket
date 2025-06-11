<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\WaSupplier;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class SupplierAgingAnalysisController extends Controller
{
    protected $model;
    protected $title;

    public function __construct()
    {
        $this->model = 'supplier-aging-analysis';
        $this->title = 'Supplier Aging Analysis';
    }

    public function index(Request $request)
    {
        if (!can('customer-aging-analysis', 'sales-and-receivables-reports')) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        $date = request()->filled('date') ? Carbon::parse(request()->date) : now();

        $intervals = [];
        $end_30 = $date;
        $start_30 = $end_30->copy()->subDays(30);

        $end_60 = $start_30->copy()->subDay(1);
        $start_60 = $end_60->copy()->subDays(30);

        $end_90 = $start_60->copy()->subDay(1);
        $start_90 = $end_90->copy()->subDays(30);

        $end_120 = $start_90->copy()->subDay(1);
        $start_120 = $end_120->copy()->subDays(30);

        $intervals = [
            'days_0_30' => [$start_30->format('Y-m-d 00:00:00'), $end_30->format('Y-m-d 23:59:59')],
            'days_31_60' => [$start_60->format('Y-m-d 00:00:00'), $end_60->format('Y-m-d 23:59:59')],
            'days_61_90' => [$start_90->format('Y-m-d 00:00:00'), $end_90->format('Y-m-d 23:59:59')],
            'days_91_120' => [$start_120->format('Y-m-d 00:00:00'), $end_120->format('Y-m-d 23:59:59')],
            'days_120' => [$start_120->copy()->subDay(1)->format('Y-m-d 00:00:00')],
        ];

        $select = [];

        foreach ($intervals as $key => $interval) {
            if($key == 'days_120'){
                $select[] = DB::raw("(SELECT SUM(total_amount_inc_vat - withholding_amount - allocated_amount) FROM wa_supp_trans WHERE grn_type_number = 20 AND created_at < '".$interval[0]."' AND wa_supp_trans.supplier_no = wa_suppliers.supplier_code) AS $key");
                
                break;
            }

            $select[] = DB::raw("(SELECT SUM(total_amount_inc_vat - withholding_amount - allocated_amount) FROM wa_supp_trans WHERE grn_type_number = 20 AND created_at BETWEEN '".$interval[0]."' AND '".$interval[1]."' AND wa_supp_trans.supplier_no = wa_suppliers.supplier_code) AS $key");
        }

        $query = WaSupplier::query()
            ->select(array_merge([
                "name AS supplier_name",
                DB::raw("(SELECT SUM(total_amount_inc_vat - withholding_amount - allocated_amount) FROM wa_supp_trans WHERE grn_type_number = 20 AND wa_supp_trans.supplier_no = wa_suppliers.supplier_code) AS total_balance")
            ], $select))
            ->whereRaw("(SELECT SUM(total_amount_inc_vat - withholding_amount - allocated_amount) FROM wa_supp_trans WHERE grn_type_number = 20 AND wa_supp_trans.supplier_no = wa_suppliers.supplier_code) > 0");

        if (request()->wantsJson()) {
            return DataTables::eloquent($query)
                ->editColumn('days_0_30', function ($record) {
                    return manageAmountFormat($record->days_0_30);
                })
                ->editColumn('days_31_60', function ($record) {
                    return manageAmountFormat($record->days_31_60);
                })
                ->editColumn('days_61_90', function ($record) {
                    return manageAmountFormat($record->days_61_90);
                })
                ->editColumn('days_91_120', function ($record) {
                    return manageAmountFormat($record->days_91_120);
                })
                ->editColumn('days_120', function ($record) {
                    return manageAmountFormat($record->days_120);
                })
                ->editColumn('total_balance', function ($record) {
                    return manageAmountFormat($record->total_balance);
                })
                ->toJson();
        }

        if ($request->print) {
            $items = $query->get();
            $pdf = Pdf::loadView('admin.supplier_aging_analysis.pdf', [$items]);
            $report_name = 'supplier_aging_analysis_' . date('Y_m_d_H_i_A');

            return $pdf->download($report_name . '.pdf');
        }

        $breadcum = ['Accounts Payables' => '', 'Report' => '', $this->title => ''];

        return view('admin.supplier_aging_analysis.sheet', [
            'title' => $this->title,
            'model' => $this->model,
            'breadcum' => $breadcum
        ]);
    }
}
