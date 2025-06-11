<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Http\Controllers\Controller;
use App\Jobs\PerformPostSaleActions;
use App\Jobs\UnbalancedCompletedInvoices;
use App\Model\WaDebtorTran;
use App\Model\WaGlTran;
use App\Model\WaInternalRequisition;
use App\Model\WaInventoryLocationTransfer;
use App\Model\WaInventoryLocationTransferItem;
use App\Model\WaStockMove;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\LazyCollection;

class UnbalancedReportController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct()
    {
        $this->model = 'unbalanced_invoices_report';
        $this->title = 'Unbalanced invoice Report';
        $this->pmodule = 'sales-and-receivables-reports';
    }

    public function index(Request $request)
    {
        $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;
        $from = $request->from ?? today();
        $to = $request->to ?? today();


        if (!can('unbalanced-invoices', $this->pmodule)) {
            Session::flash('warning', pageRestrictedMessage());
            return redirect()->back();
        }

        /*get all invoices for the day*/
        $unbalance_invoices = [];

           WaInternalRequisition::whereBetween('requisition_date', [$from, $to])
            ->with('getRelatedItem','getRelatedItem.getInventoryItemDetail', 'stockMoves', 'debtorTrans')
            ->withCount(['stockMoves', 'getRelatedItem'])
            ->chunk(100, function ($invoices) use (&$unbalance_invoices) {
                foreach ($invoices as $invoice) {
                    $item_count = $invoice->get_related_item_count;
                    $move_count = $invoice->stock_moves_count;

                    if ($item_count != $move_count) {
                        $unbalance_invoices[] = $invoice;
                    }
                    $invoice_total = (int) $invoice->getOrderTotal();
                    $debtor_tran_amount = (int) $invoice->totalDebtors();
                    if ($invoice_total != $debtor_tran_amount) {
                        $unbalance_invoices[] = $invoice;
                    }
                }
            });


        $items = collect($unbalance_invoices)->unique();


        if ($request->get('manage-request') == "print") {
            return view('admin.salesreceiablesreports.unbalanced_invoices_pdf', compact('title','model','items'));
        }
        if ($request->get('manage-request') == "pdf") {
            $pdf =PDF::loadView('admin.salesreceiablesreports.unbalanced_invoices_pdf', compact('title','model','items'));
            return $pdf->download('customer_sales_summary_report' . date('Y_m_d_h_i_s') . '.pdf');
        }

        return view("admin.salesreceiablesreports.completed-transactions-report", compact('title','model','items', 'pmodule'));

    }

    public function processInvoice(Request $request, $id)
    {

        try {
           DB::beginTransaction();

            $requisition = WaInternalRequisition::with('getRelatedItem','getRelatedItem.getInventoryItemDetail', 'stockMoves', 'debtorTrans')->find($id);

            /*clear moves*/
            WaStockMove::where('document_no', $requisition->requisition_no)->delete();

            /*clear transfers*/
            $trasfer  = WaInventoryLocationTransfer::where('transfer_no', $requisition->requisition_no)->first();
            /*delete trasfer items*/
            if ($trasfer)
            {
                WaInventoryLocationTransferItem::where('wa_inventory_location_transfer_id', $trasfer->id)->delete();
                $trasfer->delete();
            }
            /*delete debtors*/
            WaDebtorTran::where('document_no',  $requisition->requisition_no)->delete();

            /*delete  GL*/
            WaGlTran::where('transaction_no', $requisition->requisition_no)->delete();

            PerformPostSaleActions::dispatch($requisition)->afterCommit();

            DB::commit();

            Session::flash('message', 'Invoice Reprocessed Successfully');
           return redirect()->back()->with('message','Invoice Reprocessed Successfully');
        }catch (\Throwable $throwable)
        {
            DB::rollBack();
            Session::flash('warning', $throwable->getMessage());
            return redirect()->back()->with('warning',$throwable->getMessage());
        }

    }
}
