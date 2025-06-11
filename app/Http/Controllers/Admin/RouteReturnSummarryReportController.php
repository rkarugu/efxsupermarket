<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ExcelDownloadService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RouteReturnSummarryReportController extends Controller
{
      protected $resource_folder;
      protected $title;
      protected $model;
      protected $pmodule;


  
      public function __construct()
      {
          $this->resource_folder = "admin.route_return_summary_reports";
          $this->title = "Route Return Summary Report";
          $this->model = "route-return-summary-report";
          $this->pmodule = "sales-and-receivables-reports";
      }
  
      public function index(Request $request)
      {
          $model = $this->model;
          $pmodule = $this->pmodule;
          $resource_folder = $this->resource_folder;
          $pmodule = $this->pmodule;
          $title = $this->title;
          $start_date = $request->start_date ?? \Carbon\Carbon::now()->toDateString();
          $end_date = $request->end_date?? \Carbon\Carbon::now()->toDateString();
          $returns = DB::table('wa_inventory_location_transfer_item_returns')
          ->whereBetween('wa_inventory_location_transfer_item_returns.created_at', [$start_date.' 00:00:00', $end_date.' 23:59:59'])
          ->where('wa_inventory_location_transfer_item_returns.status', '=', 'received')
          ->orderBy('wa_inventory_location_transfer_item_returns.created_at', 'DESC')
          ->select(
              'routes.route_name as route_name',
              'users.name as salesman',
              // 'wa_inventory_location_transfers.route as route',
              'wa_inventory_location_transfers.route_id as route_id',
              DB::raw('count(distinct wa_inventory_location_transfer_item_returns.return_number) as return_count'),
              DB::raw('sum(wa_inventory_location_transfer_item_returns.return_quantity * wa_inventory_location_transfer_items.selling_price) AS total_returns'),
              
          )
          ->leftJoin('wa_inventory_location_transfer_items', 'wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_item_id', '=', 'wa_inventory_location_transfer_items.id')
          ->leftJoin('wa_inventory_location_transfers', 'wa_inventory_location_transfer_items.wa_inventory_location_transfer_id', '=', 'wa_inventory_location_transfers.id')
          ->leftJoin('routes','wa_inventory_location_transfers.route_id', '=', 'routes.id') 
          ->leftJoin('route_user', 'route_user.route_id', '=', 'wa_inventory_location_transfers.route_id')
          ->leftJoin('users', 'users.id', '=', 'route_user.user_id')
          ->where('users.role_id', '=', 4)
          ->groupBy('route_id');
          $returns = $returns->get();
          if($request->type && $request->type == 'Excel'){
            $user = Auth::user();
            $data = [];
            foreach($returns as $return){
              $payload = [
                'route' => $return->route_name,
                'user' => $return->salesman,
                'count' => $return->return_count,
                'amount' => $return->total_returns,

              ];
              $data[] = $payload;
            }

            return ExcelDownloadService::download("Route-Returns-Summary-Report-$start_date-$end_date", collect($data), ['ROUTE', 'SALESMAN', 'RETURN COUNT', 'TOTAL AMOUNT']);

          }
          if($request->type && $request->type == 'PDF'){
            $user = Auth::user();
            $pdf = Pdf::loadView('admin.route_return_summary_report.route_returns_summary_pdf', compact('user', 'returns'))->setPaper('a4', 'portrait');

            return $pdf->download('Route-Returns-Summary_report' . $start_date . '-' . $end_date . '.pdf');

          }
          
          return view('admin.route_return_summary_report.index', compact('model', 'pmodule', 'title', 'returns'));
      }
}
