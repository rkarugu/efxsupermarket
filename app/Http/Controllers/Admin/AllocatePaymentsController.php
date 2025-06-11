<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\PaymentMethod;
use App\Model\Restaurant;
use App\Model\WaPosCashSales;
use App\Model\WaPosCashSalesPayments;
use App\Model\WaRouteCustomer;
use App\Models\PaymentVerification;
use App\Models\PaymentVerificationBank;
use App\WaTenderEntry;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AllocatePaymentsController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;
    protected  $basePath;

    public function __construct(Request $request)
    {
        $this->model = 'pos-payments-consumption';
        $this->title = 'POS Payments';
        $this->pmodule = 'pos-payments-consumption';
        $this->basePath = 'admin.pos_payments';
    }
    public function index(Request $request)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $breadcum = 'Allocate Payments';
        $branches = Restaurant::all();
        $paymentMethods = PaymentMethod::all();
        $start =  Carbon::parse('2024-10-25')->toDateString();
        $user = Auth::user();

        $reference = $request->reference ?? null;
        if($request->manage_request && $request->manage_request == 'filter'){
            $payments = DB::table('payment_verification_banks')
                ->whereBetween('bank_date', [$start, Carbon::now()->toDateString()])
                ->where('reference', 'like', '%'.$request->reference.'%')
                ->where('status', 'Pending');
                // 1 and 162
            if($user->role_id != 1 &&  $user->role_id != 162){
                $payments = $payments->whereNotIn('channel',['EQUITY BANK', 'KENYA COMMERCIAL BANK']);
            }
            $payments = $payments->get();
        }else{
            $payments = [];
        }
      
        return view('admin.pos_payments.allocate_payments', compact('title', 'model', 'breadcum', 'pmodule', 'permission', 'paymentMethods', 'branches', 'payments', 'reference'));

    }
    public  function getCashSales(Request $request)
    {
        $user = Auth::user();
        $receipts = DB::table('wa_pos_cash_sales');
            // ->whereDate('created_at', Carbon::now()->toDateString());     
        // if($user->role_id != 1){
        //     $receipts = $receipts->where('branch_id', $user->restaurant_id);
        // }
         $receipts = $receipts->get();
         return response()->json([
            'receipts' => $receipts
        ]);

    } 
    public function processManualAllocation(Request $request)
    {
        DB::beginTransaction();
        try {
        $user = Auth::user();
        $paymentVerificationRecord = PaymentVerificationBank::find($request->payment_id);
        $sale = WaPosCashSales::find($request->receipt_id);
        $reference = $request->reference;
        $method = PaymentMethod::with(['paymentGlAccount'])->where('title', $paymentVerificationRecord->channel)->first();
        $tenderEntry = WaTenderEntry::where('reference', $paymentVerificationRecord->reference)->where('amount', $paymentVerificationRecord->amount)->first();
        if($tenderEntry){
            if($tenderEntry->consumed == 1){
                return response()->json(['error' => true,'message' => 'Reference already used!'], 400);
            }else{
                //delete cash allocations
                if ($request->deallocate_cash &&  $request->deallocate_cash == 'true'){
                    $cashPayment = WaPosCashSalesPayments::where('wa_pos_cash_sales_id', $sale->id)->where('payment_method_id', 14)->first();
                    $variance = $cashPayment->amount - $tenderEntry->amount;
                    if ($variance <= 0){
                        $cashPayment->delete();
                    }else{
                        $cashPayment->amount = $variance;
                        $cashPayment->save();
                    }
                }
                //create payment
                $posPayment = new WaPosCashSalesPayments();
                $posPayment->wa_pos_cash_sales_id = $sale->id;
                $posPayment->payment_method_id = $method->id;
                $posPayment->gl_account_id = $method->paymentGlAccount->id;
                $posPayment->gl_account_name = $method->paymentGlAccount->account_code;
                $posPayment->balancing_account_id = $method->paymentGlAccount->id;
                $posPayment->amount = $paymentVerificationRecord->amount;
                $posPayment->remarks = 'Manual Payment Allocation By :'. $user->name;
                $posPayment->wa_tender_entry_id = $tenderEntry->id;
                $posPayment->branch_id = $sale->branch_id;
                $posPayment->cashier_id = $sale->attending_cashier;
                $posPayment->payment_reference = $paymentVerificationRecord->reference;
                $posPayment->created_at = $sale->created_at;
                $posPayment->updated_at = $sale->updated_at;
                $posPayment->save();

                //mark entry as consumed
                $tenderEntry->consumed = 1;
                $tenderEntry->save();   
                DB::commit();
                return response()->json(['result' => 1,'message' => 'Payment Allocation Successful!']);

            }
            

        }else{
            //create tender entry
            $documentNo = getCodeWithNumberSeries('RECEIPT');
            updateUniqueNumberSeries('RECEIPT', $documentNo);

            $tenderEntry = new WaTenderEntry();
            $tenderEntry->document_no = $documentNo;
            $tenderEntry->channel = $paymentVerificationRecord->channel;
            $tenderEntry->reference = $paymentVerificationRecord->reference;
            $tenderEntry->account_code = $method->paymentGlAccount->account_code;
            $tenderEntry->customer_id = WaRouteCustomer::find($sale->wa_route_customer_id)->customer_id;
            $tenderEntry->trans_date = $sale->created_at;
            $tenderEntry->wa_payment_method_id = $method->id;;
            $tenderEntry->amount =  $paymentVerificationRecord->amount;
            $tenderEntry->paid_by = $user->id;
            $tenderEntry->cashier_id = $sale->attending_cashier;
            $tenderEntry->consumed = true;
            $tenderEntry->created_at = $sale->created_at;
            $tenderEntry->updated_at = $sale->updated_at;
            $tenderEntry->save();
            
            if ($request->deallocate_cash &&  $request->deallocate_cash == 'true'){
                $cashPayment  =  WaPosCashSalesPayments::where('wa_pos_cash_sales_id', $sale->id)->where('payment_method_id', 14)->first();
                $variance = $cashPayment->amount - $tenderEntry->amount;
                if ($variance <= 0){
                    $cashPayment->delete();
                }else{
                    $cashPayment->amount = $variance;
                    $cashPayment->save();
                }
            }

            $posPayment = new WaPosCashSalesPayments();
            $posPayment->wa_pos_cash_sales_id = $sale->id;
            $posPayment->payment_method_id = $method->id;
            $posPayment->gl_account_id = $method->paymentGlAccount->id;
            $posPayment->gl_account_name = $method->paymentGlAccount->account_code;
            $posPayment->balancing_account_id = $method->paymentGlAccount->id;
            $posPayment->amount = $paymentVerificationRecord->amount;
            $posPayment->remarks = 'Manual Payment Allocation By :'. $user->name;
            $posPayment->wa_tender_entry_id = $tenderEntry->id;
            $posPayment->payment_reference = $paymentVerificationRecord->reference;
            $posPayment->branch_id = $sale->branch_id;
            $posPayment->cashier_id = $sale->attending_cashier;
            $posPayment->created_at = $sale->created_at;
            $posPayment->updated_at = $sale->updated_at;
            $posPayment->save();
            DB::commit();

            //create payment record
            return response()->json(['result' => 1,'message' => 'Payment Allocation Successful!']);

        }

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => true,'message' => $th->getMessage()], 400);

        }
       
    }
}
