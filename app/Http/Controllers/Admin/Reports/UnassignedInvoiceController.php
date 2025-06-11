<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Http\Controllers\Admin\UnsignedInvoiceController;
use Session;
use Carbon\Carbon;
use App\Model\Route;
use App\Model\WaEsd;
use App\Model\WaEsdDetails;
use Illuminate\Http\Request;
use App\Model\WaSalesInvoice;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Model\WaInternalRequisition;
use Illuminate\Support\Facades\Auth;
use App\Services\ExcelDownloadService;
use App\Model\WaInternalRequisitionItem;

class UnassignedInvoiceController extends Controller
{

    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct()
    {
        $this->model = 'sales-and-receivables-reports';
        $this->title = 'Unassigned Invoices Report';
        $this->pmodule = 'sales-and-receivables-reports';
    }

    public function index(Request $request)
    {
        if ($request->intent == 'RE-SIGN') {
            return UnsignedInvoiceController::resignAll($request);
        }

        $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;
        $permission = $this->mypermissionsforAModule();

        $start_date = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::today()->startOfDay()->format('Y-m-d H:i:s');
        $end_date = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::today()->endOfDay()->format('Y-m-d H:i:s');

        if (isset($permission[$model . '___customer_invoices']) || $permission == 'superadmin') {
            $routes = Route::select('id', 'route_name')->get();
            $invoices = WaEsdDetails::whereBetween('created_at', [$start_date, $end_date])->where('status', '=', 0)->get();

            $allInvoicesNotInEsd = DB::table('wa_internal_requisitions')
            ->whereBetween('wa_internal_requisitions.created_at', [$start_date, $end_date])
            ->leftJoin('wa_esd_details', 'wa_esd_details.invoice_number', '=', 'wa_internal_requisitions.requisition_no')
            ->whereNull('wa_esd_details.invoice_number')
            ->pluck('wa_internal_requisitions.requisition_no')
            ->toArray();
            $invoicesids = $invoices->pluck('invoice_number')->toArray();
            $invoicesids = array_merge($invoicesids, $allInvoicesNotInEsd);
            $internalrequisitionitems = WaInternalRequisition::whereIn('requisition_no', $invoicesids)
                ->with(['getRelatedItem.getInventoryItemDetail','esd_details'])
                ->withSum('getRelatedItem', 'vat_amount')
                ->withSum('getRelatedItem', 'total_cost_with_vat');
            

            if ($request->has('route')) {
                $internalrequisitionitems->whereHas('route', function ($query) use ($request) {
                    $query->where('id', $request->route);
                });
            }

            $internalrequisitionitems = $internalrequisitionitems->get();
           
            $combinedData = $internalrequisitionitems->map(function($data) {
                $description = 'No record found';
                if($data->esd_details?->count() > 0) {
                    $esdDetail = json_decode($data->esd_details[0]?->description, true);
                    if ($esdDetail && isset($esdDetail['error_status'])) {
                        $description = $esdDetail['error_status'];
                    }
                }
                $data->description = $description;
                return $data;
            });
            if ($request->has('intent') && $request->intent == 'EXCEL') {
                $headings = ['INVOICE NUMBER', 'INVOICE AMOUNT', 'TAX TOTAL', 'ERROR STATUS'];
                $filename = "UNASSIGNED INVOICES REPORT $start_date - $end_date";
                $excelData = [];
                $combinedData = $combinedData->load('esd_details');


                foreach ($combinedData as $item) {
                    $errorStatus = $item?->description; 
                    $excelData[] = [
                        'Invoice Number' => $item->requisition_no,
                        'Amount' => manageAmountFormat($item->get_related_item_sum_total_cost_with_vat),
                        'tax' => $item->get_related_item_sum_vat_amount,
                        'Error Status' => $errorStatus
                    ];
                }
                $excelData = collect($excelData);
                return ExcelDownloadService::download($filename, $excelData, $headings);
            }
            
            $user = Auth::user();

            $breadcum = [$title => '', 'Unsigned Invoices' => ''];
            return view('admin.salesreceiablesreports.unsigned_invoices', compact('title', 'model', 'breadcum', 'combinedData', 'routes', 'user'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function getRelatedItems(Request $request)
    {
        $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;
        $permission = $this->mypermissionsforAModule();

        $wainternalrequisitionitems = WaInternalRequisition::where('requisition_no', $request->invoice_number)
            ->whereBetween('created_at', [$request->start_date, $request->end_date])
            ->with('getRelatedItem.getInventoryItemDetail')->first();

        if ($request->has('intent') && $request->intent == 'EXCEL') {
            $headings = ['ITEM ID', 'ITEM CODE', 'ITEM DESCRIPTION'];
            $filename = "UNASSIGNED ITEMS INVOICES REPORT $request->start_date - $request->end_date";

            $excelData = [];

            if (isset($wainternalrequisitionitems->getRelatedItem)) {
                foreach ($wainternalrequisitionitems->getRelatedItem as $wainternalrequisitionitem) {
                    $payload = [
                        'ITEM ID' => $wainternalrequisitionitem->getInventoryItemDetail->id,
                        'ITEM CODE' => $wainternalrequisitionitem->getInventoryItemDetail->stock_id_code,
                        'ITEM DESCRIPTION' => $wainternalrequisitionitem->getInventoryItemDetail?->description,
                    ];
                    $excelData[] = $payload;
                }
            }

            return ExcelDownloadService::download($filename, collect($excelData), $headings);
        }

        $breadcum = [$title => '', 'Unsigned Invoices Items' => ''];
        return view('admin.salesreceiablesreports.unassigned_invoices_items', compact('title', 'model', 'breadcum', 'wainternalrequisitionitems'));
    }

}
