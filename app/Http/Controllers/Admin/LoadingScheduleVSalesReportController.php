<?php

namespace App\Http\Controllers\Admin;

use App\DeliverySchedule;
use App\Http\Controllers\Controller;
use App\Model\Restaurant;
use App\Model\Route;
use App\SalesmanShift;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoadingScheduleVSalesReportController extends Controller
{
    public function generate(Request $request)
    {
        $title = 'Loading Schedule vs Stock Report';
        $model = 'loading-schedule-vs-sales-report';
        $breadcum = ['Sales & Receivable Reports' => '', 'Loading Schedule vs Stock Report' => ''];
        $user = getLoggeduserProfile();

        $routes = DB::table('routes')->select('id', 'route_name')->get();
        $branches = DB::table('restaurants')->select('id', 'name')->get();

        $invoices = [];

        if ($request->intent) {
            $shift = SalesmanShift::with(['orders', 'salesman', 'relatedRoute'])->latest()->where('route_id', $request->route_id)
                ->whereDate('created_at', '=', $request->date)
                ->first();

            if (!$shift) {
                return redirect()->back()->withErrors(['message' => 'The selected route did not have a sales shift on the selected date']);
            }

            $branch = Restaurant::find($shift->relatedRoute->restaurant_id)->name;
            $shift->date = Carbon::parse($shift->created_at)->toDateString();
            $route = DB::table('routes')->where('id', $request->route_id)->select('id', 'route_name')->first();
            $invoices = DB::table('wa_internal_requisitions')->where('wa_shift_id', $shift->id)
                ->get()->map(function ($invoice) use ($route) {
                    return [
                        'document_number' => $invoice->requisition_no,
                        'route' => $route->route_name,
                        'date' => Carbon::parse($invoice->created_at)->format('Y-m-d H:i:s'),
                        'total' => DB::table('wa_internal_requisition_items')->where('wa_internal_requisition_id', $invoice->id)->sum('total_cost')
                    ];
                });

            if ($request->intent == 'PDF') {
                $schedule = DeliverySchedule::latest()->with(['vehicle', 'driver'])->where('shift_id', $shift->id)->first();
                $shift->invoices = implode(',', collect($invoices)->pluck('document_number')->toArray());
                $report_name = "{$route->route_name}_LOADING_SCHEDULE_VS_STOCK_REPORT-" . Carbon::parse($request->date)->format('Y-m-d H:i:s');
                $pdf = PDF::loadView('admin.salesreceiablesreports.loading_schedule_vs_stock_pdf', compact('schedule', 'shift', 'invoices', 'branch'));
                return $pdf->download($report_name . '.pdf');
            }
        }

        return view('admin.salesreceiablesreports.loading_schedule_vs_sales', compact('title', 'model', 'breadcum', 'routes', 'branches', 'invoices'));
    }
}
