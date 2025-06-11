<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Model\User;
use App\Model\Route;
use App\SalesmanShift;
use App\DeliverySchedule;
use App\OffsiteShiftRequest;
use App\Models\PettyCashType;
use Illuminate\Console\Command;
use App\Services\DeliveryService;
use App\Services\DispatchService;
use Illuminate\Support\Facades\DB;
use App\SalesmanShiftReopenRequest;
use App\SalesmanShiftStoreDispatch;
use Illuminate\Support\Facades\Log;
use App\Jobs\CreateDeliverySchedule;
use App\Model\WaInternalRequisition;
use App\Models\PettyCashTransaction;
use App\Jobs\PrepareStoreParkingList;
use App\Model\DeliveryCentres;
use App\Models\TravelExpenseTransaction;

class AutoCloseSalesmanShifts extends Command
{
    public function __construct(protected DispatchService $dispatchService)
    {
        parent::__construct();
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:auto-close-salesman-shifts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically closes all open salesman shifts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //unlock  all  centres outside transaction
        $centers = DeliveryCentres::all();
        foreach($centers as $center){
            $center->is_active = true;
            $center->save();
        } 
        DB::transaction(function () {
            OffsiteShiftRequest::where('status', 'pending')->update(['status' => 'expired']);
            $openAndPendingShifts = SalesmanShift::where('status', 'open')->get();
            foreach ($openAndPendingShifts as $shift) {
                $shift->update(['closed_time' => Carbon::now(), 'status' => 'close']);
                $route = Route::withCount('waRouteCustomer')->find($shift->route_id);

                $existingDispatchIds = SalesmanShiftStoreDispatch::where('shift_id', $shift->id)->pluck('id')->toArray();
                SalesmanShiftStoreDispatch::destroy($existingDispatchIds);

                $existingDeliveryIds = DeliverySchedule::where('shift_id', $shift->id)->pluck('id')->toArray();
                DeliverySchedule::destroy($existingDeliveryIds);

                if ($shiftHasOrders = WaInternalRequisition::where('wa_shift_id', $shift->id)->count() > 0) {
                    $user = User::find($shift->salesman_id);
                    DispatchService::prepareLoadingSheets($shift->id, $user);
                    DeliveryService::createDeliverySchedule($shift->id, $shift->route_id);

                    try {
                        $today = Carbon::now()->toDateString();

                        $shiftdata = DB::table('salesman_shift_customers')
                            ->where('salesman_shift_id', $shift->id)
                            ->where('visited', '1')
                            ->orderBy('updated_at', 'asc')
                            ->first();

                        if ($shiftdata) {
                            $travelExpenseIncentive = DB::table('travel_expense_transactions')->where('user_id', $user->id)
                                ->orderBy('created_at', 'DESC')
                                ->where('shift_id', $shift->id)
                                ->where('shift_type', 'order_taking')
                                ->first();

                            $orderShiftType = DB::table('salesman_shift_customers')
                                ->select('salesman_shift_type', DB::raw('count(*) as count'))
                                ->where('salesman_shift_id', $shift->id)
                                ->where('visited', 1)
                                ->groupBy('salesman_shift_type')
                                ->union(
                                    DB::table('salesman_shift_customers')
                                        ->select(DB::raw("'total' as salesman_shift_type"), DB::raw('count(*) as count'))
                                        ->where('salesman_shift_id', $shift->id)
                                        ->where('visited', 1)
                                )
                                ->pluck('count', 'salesman_shift_type');

                            $incentiveAmount = 0;
                            $total_incentive_amount = 0;
                            $routeoffsiteallowance = floatval($route->offsite_allowance) / floatval($route->wa_route_customer_count);
                            $routeonsiteallowance = floatval($route->travel_expense) / floatval($route->wa_route_customer_count);

                            $onsitecount = $orderShiftType->get('onsite', 0);
                            $offsitecount = $orderShiftType->get('offsite', 0);

                            if ($offsitecount > 0 && $onsitecount > 0) {
                                $total_incentive_amount = $route->offsite_allowance + $route->travel_expense;
                            } else if ($offsitecount <= 0 && $onsitecount > 0) {
                                $total_incentive_amount = $route->travel_expense;
                            } else if ($offsitecount > 0 && $onsitecount <= 0) {
                                $total_incentive_amount = $route->offsite_allowance;
                            }

                            if (!$travelExpenseIncentive) {
                                if ($shiftdata->salesman_shift_type == 'onsite') {

                                    $documentNumber = getCodeWithNumberSeries('PETTY_CASH');
                                    $walletType = PettyCashType::where('slug', 'travel-expense')->first();

                                    $calculatedIncentiveAmount = 0;
                                    $total_customers = $route->wa_route_customer_count;
                                    $customers_visited = $total_customers - $onsitecount;

                                    if (floatval($total_customers) > 0) {
                                        $calculatedrouteonsiteallowance = floatval($route->travel_expense) / floatval($total_customers);
                                    } else {
                                        $calculatedrouteonsiteallowance = 0;
                                    }

                                    if (floatval($customers_visited) > 0) {
                                        $calculatedrouteoffsiteallowance = floatval($route->offsite_allowance) / floatval($total_customers);
                                    } else {
                                        $calculatedrouteoffsiteallowance = 0;
                                    }

                                    $calculatedoffsiteamount = floatval($offsitecount * $calculatedrouteoffsiteallowance);
                                    $calculatedonsiteamount = floatval($onsitecount * $calculatedrouteonsiteallowance);
                                    $calculatedIncentiveAmount = ceil($calculatedonsiteamount + $calculatedoffsiteamount);

                                    if (floatval($calculatedIncentiveAmount) < $total_incentive_amount) {
                                        $incentive = TravelExpenseTransaction::create([
                                            'transaction_type' => 'incentive',
                                            'user_id' => $user->id,
                                            'route_id' => $route->id,
                                            'shift_id' => $shift->id,
                                            'shift_type' => 'order_taking',
                                            'amount' => $calculatedIncentiveAmount,
                                            'document_no' => $documentNumber,
                                            'wallet_type' => $walletType?->title,
                                            'wallet_type_id' => $walletType?->id,
                                            'reference' => "$user->name/$route->route_name/TRAVEL EXPENSE",
                                            'narrative' => "Salesman travel expense for route $route->route_name",
                                        ]);

                                        PettyCashTransaction::create([
                                            'user_id' => $incentive->user_id,
                                            'amount' => $incentive->amount,
                                            'document_no' => $incentive->document_no,
                                            'wallet_type' => $incentive->wallet_type,
                                            'wallet_type_id' => $incentive->wallet_type_id,
                                            'parent_id' => $incentive->id,
                                            'reference' => $incentive->reference,
                                            'narrative' => $incentive->narrative,
                                            'call_back_status' => 'complete',
                                        ]);
                                        updateUniqueNumberSeries('PETTY_CASH', $documentNumber);
                                    } else {
                                        $incentive = TravelExpenseTransaction::create([
                                            'transaction_type' => 'incentive',
                                            'user_id' => $user->id,
                                            'route_id' => $route->id,
                                            'shift_id' => $shift->id,
                                            'shift_type' => 'order_taking',
                                            'amount' => $total_incentive_amount,
                                            'document_no' => $documentNumber,
                                            'wallet_type' => $walletType?->title,
                                            'wallet_type_id' => $walletType?->id,
                                            'reference' => "$user->name/$route->route_name/TRAVEL EXPENSE",
                                            'narrative' => "Salesman travel expense for route $route->route_name",
                                        ]);

                                        PettyCashTransaction::create([
                                            'user_id' => $incentive->user_id,
                                            'amount' => $incentive->amount,
                                            'document_no' => $incentive->document_no,
                                            'wallet_type' => $incentive->wallet_type,
                                            'wallet_type_id' => $incentive->wallet_type_id,
                                            'parent_id' => $incentive->id,
                                            'reference' => $incentive->reference,
                                            'narrative' => $incentive->narrative,
                                            'call_back_status' => 'complete',
                                        ]);
                                        updateUniqueNumberSeries('PETTY_CASH', $documentNumber);
                                    }
                                } else if ($shiftdata->salesman_shift_type == 'offsite') {

                                    $documentNumber = getCodeWithNumberSeries('PETTY_CASH');
                                    $walletType = PettyCashType::where('slug', 'travel-expense')->first();

                                    $calculatedIncentiveAmount = 0;
                                    $total_customers = $route->wa_route_customer_count;
                                    $customers_visited = $total_customers - $offsitecount;

                                    if (floatval($customers_visited) > 0) {
                                        $calculatedrouteonsiteallowance = floatval($route->travel_expense) / floatval($total_customers);
                                    } else {
                                        $calculatedrouteonsiteallowance = 0;
                                    }

                                    if (floatval($total_customers) > 0) {
                                        $calculatedrouteoffsiteallowance = floatval($route->offsite_allowance) / floatval($total_customers);
                                    } else {
                                        $calculatedrouteoffsiteallowance = 0;
                                    }

                                    $calculatedonsiteamount = floatval($onsitecount * $calculatedrouteonsiteallowance);
                                    $calculatedoffsiteamount = floatval($offsitecount * $calculatedrouteoffsiteallowance);
                                    $calculatedIncentiveAmount = ceil($calculatedoffsiteamount + $calculatedonsiteamount);

                                    if (floatval($calculatedIncentiveAmount) < $total_incentive_amount) {
                                        $incentive = TravelExpenseTransaction::create([
                                            'transaction_type' => 'incentive',
                                            'user_id' => $user->id,
                                            'route_id' => $route->id,
                                            'shift_id' => $shift->id,
                                            'shift_type' => 'order_taking',
                                            'amount' => $calculatedIncentiveAmount,
                                            'document_no' => $documentNumber,
                                            'wallet_type' => $walletType?->title,
                                            'wallet_type_id' => $walletType?->id,
                                            'reference' => "$user->name/$route->route_name/TRAVEL EXPENSE",
                                            'narrative' => "Salesman travel expense for route $route->route_name",
                                        ]);

                                        PettyCashTransaction::create([
                                            'user_id' => $incentive->user_id,
                                            'amount' => $incentive->amount,
                                            'document_no' => $incentive->document_no,
                                            'wallet_type' => $incentive->wallet_type,
                                            'wallet_type_id' => $incentive->wallet_type_id,
                                            'parent_id' => $incentive->id,
                                            'reference' => $incentive->reference,
                                            'narrative' => $incentive->narrative,
                                            'call_back_status' => 'complete',
                                        ]);
                                        updateUniqueNumberSeries('PETTY_CASH', $documentNumber);
                                    } else {
                                        $incentive = TravelExpenseTransaction::create([
                                            'transaction_type' => 'incentive',
                                            'user_id' => $user->id,
                                            'route_id' => $route->id,
                                            'shift_id' => $shift->id,
                                            'shift_type' => 'order_taking',
                                            'amount' => $total_incentive_amount,
                                            'document_no' => $documentNumber,
                                            'wallet_type' => $walletType?->title,
                                            'wallet_type_id' => $walletType?->id,
                                            'reference' => "$user->name/$route->route_name/TRAVEL EXPENSE",
                                            'narrative' => "Salesman travel expense for route $route->route_name",
                                        ]);

                                        PettyCashTransaction::create([
                                            'user_id' => $incentive->user_id,
                                            'amount' => $incentive->amount,
                                            'document_no' => $incentive->document_no,
                                            'wallet_type' => $incentive->wallet_type,
                                            'wallet_type_id' => $incentive->wallet_type_id,
                                            'parent_id' => $incentive->id,
                                            'reference' => $incentive->reference,
                                            'narrative' => $incentive->narrative,
                                            'call_back_status' => 'complete',
                                        ]);
                                        updateUniqueNumberSeries('PETTY_CASH', $documentNumber);
                                    }
                                }
                            } else {
                                if ($shiftdata->salesman_shift_type == 'onsite') {
                                    $calculatedIncentiveAmount = 0;
                                    $total_customers = $route->wa_route_customer_count;
                                    $customers_visited = $total_customers - $onsitecount;

                                    if (floatval($total_customers) > 0) {
                                        $calculatedrouteonsiteallowance = floatval($route->travel_expense) / floatval($total_customers);
                                    } else {
                                        $calculatedrouteonsiteallowance = 0;
                                    }

                                    if (floatval($customers_visited) > 0) {
                                        $calculatedrouteoffsiteallowance = floatval($route->offsite_allowance) / floatval($total_customers);
                                    } else {
                                        $calculatedrouteoffsiteallowance = 0;
                                    }

                                    $calculatedoffsiteamount = floatval($offsitecount * $calculatedrouteoffsiteallowance);
                                    $calculatedonsiteamount = floatval($onsitecount * $calculatedrouteonsiteallowance);
                                    $calculatedIncentiveAmount = ceil($calculatedonsiteamount + $calculatedoffsiteamount);

                                    if (floatval($calculatedIncentiveAmount) < $total_incentive_amount) {
                                        $incentivedata = TravelExpenseTransaction::where('shift_id', $shift->id)->where('shift_type', 'order_taking')->first();
                                        $incentivedata->amount = $calculatedIncentiveAmount;
                                        $incentivedata->save();
                                        $pettycashdata = PettyCashTransaction::where('parent_id', $incentivedata->id)->where('user_id', $incentivedata->user_id)->first();
                                        $pettycashdata->amount =  $incentivedata->amount;
                                        $pettycashdata->save();
                                    } else {
                                        $incentivedata = TravelExpenseTransaction::where('shift_id', $shift->id)->where('shift_type', 'order_taking')->first();
                                        $incentivedata->amount = $total_incentive_amount;
                                        $incentivedata->save();
                                        $pettycashdata = PettyCashTransaction::where('parent_id', $incentivedata->id)->where('user_id', $incentivedata->user_id)->first();
                                        $pettycashdata->amount =  $incentivedata->amount;
                                        $pettycashdata->save();
                                    }
                                } else if ($shiftdata->salesman_shift_type == 'offsite') {
                                    $calculatedIncentiveAmount = 0;
                                    $total_customers = $route->wa_route_customer_count;
                                    $customers_visited = $total_customers - $offsitecount;

                                    if (floatval($customers_visited) > 0) {
                                        $calculatedrouteonsiteallowance = floatval($route->travel_expense) / floatval($total_customers);
                                    } else {
                                        $calculatedrouteonsiteallowance = 0;
                                    }

                                    if (floatval($total_customers) > 0) {
                                        $calculatedrouteoffsiteallowance = floatval($route->offsite_allowance) / floatval($total_customers);
                                    } else {
                                        $calculatedrouteoffsiteallowance = 0;
                                    }

                                    $calculatedonsiteamount = floatval($onsitecount * $calculatedrouteonsiteallowance);
                                    $calculatedoffsiteamount = floatval($offsitecount * $calculatedrouteoffsiteallowance);
                                    $calculatedIncentiveAmount = ceil($calculatedoffsiteamount + $calculatedonsiteamount);

                                    if (floatval($calculatedIncentiveAmount) < $total_incentive_amount) {
                                        $incentivedata = TravelExpenseTransaction::where('shift_id', $shift->id)->where('shift_type', 'order_taking')->first();
                                        $incentivedata->amount = $calculatedIncentiveAmount;
                                        $incentivedata->save();
                                        $pettycashdata = PettyCashTransaction::where('parent_id', $incentivedata->id)->where('user_id', $incentivedata->user_id)->first();
                                        $pettycashdata->amount =  $incentivedata->amount;
                                        $pettycashdata->save();
                                    } else {
                                        $incentivedata = TravelExpenseTransaction::where('shift_id', $shift->id)->where('shift_type', 'order_taking')->first();
                                        $incentivedata->amount = $total_incentive_amount;
                                        $incentivedata->save();
                                        $pettycashdata = PettyCashTransaction::where('parent_id', $incentivedata->id)->where('user_id', $incentivedata->user_id)->first();
                                        $pettycashdata->amount =  $incentivedata->amount;
                                        $pettycashdata->save();
                                    }
                                }
                            }
                        }
                    } catch (\Exception $e) {
                        Log::error("close salesman shift command error" . $e->getMessage());
                    }
                }
            }
        });
    }
}
