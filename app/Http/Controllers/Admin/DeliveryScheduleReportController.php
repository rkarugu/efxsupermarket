<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\DeliverySchedule;
use App\Model\Restaurant;
use App\Model\Route;
use App\SalesmanShift;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DeliveryScheduleReportController extends Controller
{
    public function generate(Request $request)
    {
        $title = 'Delivery Schedule Report';
        $model = 'delivery-schedule-report';
        $breadcum = ['Sales & Receivable Reports' => '', 'Delivery Schedules Report' => ''];
        $user = getLoggeduserProfile();

        // $routes = DB::table('routes')->select('id', 'route_name')->get();
        // $branches = DB::table('restaurants')->select('id', 'name')->get();
        $routes = Route::all();
        $branches = Restaurant::all();
        $deliverySchedules = DeliverySchedule::latest()->with('route', 'shift', 'vehicle', 'driver');


        if ($request->intent) {
            $deliverySchedules = $deliverySchedules->
                // whereDate('expected_delivery_date', '=', $request->date)
                whereBetween('expected_delivery_date', [$request->date.' 00:00:00', $request->date.' 23:59:59'])

                ->get();
            $branch = Restaurant::find($request->branch_id)->name;

            if ($deliverySchedules->isEmpty()) {
                return redirect()->back()->withErrors(['message' => 'The selected route did not have any deliveries scheduled on  this date']);
            }

            if ($request->intent == 'PDF') {
                $deliveryDate = Carbon::parse($request->date)->toDateString();
                $report_name = Carbon::parse($request->date)->format('Y-m-d H:i:s') . "DELIVERY_SCHEDULE_REPORT";
                $pdf = PDF::loadView('admin.salesreceiablesreports.delivery_schedule_report_pdf', compact('deliverySchedules', 'deliveryDate', 'branch'));
                return $pdf->download($report_name . '.pdf');
            }
        }

        return view('admin.salesreceiablesreports.delivery_schedule_report', compact('title', 'model', 'breadcum', 'routes', 'branches', 'deliverySchedules'));
    }
}
