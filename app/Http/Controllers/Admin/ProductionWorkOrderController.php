<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\ProductionWorkOrder;
use App\Model\StockAdjustment;
use App\Model\WaAccountingPeriod;
use App\Model\WaGlTran;
use App\Model\WaInventoryItem;
use App\Model\WaInventoryItemRawMaterial;
use App\Model\WaLocationAndStore;
use App\Model\WaNumerSeriesCode;
use App\Model\WaStockMove;
use App\ProductionProcess;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class ProductionWorkOrderController extends Controller
{
    protected $model;
    protected $baseRouteName;
    protected $baseTitle;
    protected $resourceFolder;

    public function __construct()
    {
        $this->model = 'work-orders';
        $this->baseRouteName = 'work-orders';
        $this->baseTitle = 'Work Orders';
        $this->resourceFolder = 'admin.work_orders';
    }

    public function index()
    {
        $title = $this->baseTitle;
        $breadcum = [
            $title => route("$this->baseRouteName.index"),
            'Listing' => ''
        ];

        return view("$this->resourceFolder.index", [
            'model' => $this->model,
            'title' => $title,
            'breadcum' => $breadcum,
            'base_route_name' => $this->baseRouteName,
        ]);
    }

    public function datatable(Request $request)
    {
        $limit = $request->input('length');
        $start = $request->input('start');

        $workOrders = ProductionWorkOrder::with(['inventoryItem', 'inventoryItem.pack_size'])
            ->latest()
            ->offset($start)
            ->limit($limit)
            ->get()->map(function (ProductionWorkOrder $workOrder) {
                $workOrderPayload = $this->getWorkOrderPayload($workOrder);

                $actionLinks = "<div class='action-button-div'>";

                $viewOrderLink = "<a href='" . route("$this->baseRouteName.show", $workOrder->id) . "' title='View Work Order'>
                                    <i class='fa fa-eye text-info fa-lg'></i>
                                  </a>";
                $actionLinks .= $viewOrderLink;

                if (($workOrder->status == 'not_started') && $workOrderPayload['bom_is_available']) {
                    $startOrderLink = "<form action='" . route("$this->baseRouteName.start", $workOrder->id) . "' 
                                              method='post' style='display: inline-block;' id='start-work-form' title='Start Work Order'> 
                                              " . csrf_field() . "
                                              <button type='submit'><i class='fa fa-play-circle text-primary fa-lg'></i></button>
                                        </form>";
                    $actionLinks .= $startOrderLink;
                }

                if ($workOrder->status == 'in_progress') {
                    $pauseOrderLink = "<form action='" . route("$this->baseRouteName.pause", $workOrder->id) . "' 
                                              method='post' style='display: inline-block;' id='pause-work-form' title='Pause Work Order'> 
                                              " . csrf_field() . "
                                              <button type='submit'><i class='fa fa-pause-circle text-warning fa-lg'></i></button>
                                        </form>";
                    $actionLinks .= $pauseOrderLink;

                    $completeOrderLink = "<form action='" . route("$this->baseRouteName.complete", $workOrder->id) . "' 
                                              method='post' style='display: inline-block;' id='complete-work-form' title='Complete Work Order'> 
                                              " . csrf_field() . "
                                              <button type='submit'><i class='fa fa-check-circle text-success fa-lg'></i></button>
                                        </form>";
                    $actionLinks .= $completeOrderLink;
                }

                if ($workOrder->status == 'paused') {
                    $resumeOrderLink = "<form action='" . route("$this->baseRouteName.resume", $workOrder->id) . "' 
                                              method='post' style='display: inline-block;' id='resume-work-form' title='Resume Work Order'> 
                                              " . csrf_field() . "
                                              <button type='submit'><i class='fa fa-play-circle text-success fa-lg'></i></button>
                                        </form>";
                    $actionLinks .= $resumeOrderLink;
                }

                if ($workOrder->status == 'not_started') {
                    $voidOrderLink = "<form action='" . route("$this->baseRouteName.void", $workOrder->id) . "' 
                                              method='post' style='display: inline-block;' id='void-work-form' title='Void Work Order'> 
                                              " . csrf_field() . "
                                              <button type='submit'><i class='fa fa-trash text-danger fa-lg'></i></button>
                                        </form>";
                    $actionLinks .= $voidOrderLink;
                }

                $actionLinks .= "</div>";
                $workOrderPayload['actions'] = $actionLinks;

                return $workOrderPayload;
            });

        $responsePayload = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => count($workOrders),
            "recordsFiltered" => count($workOrders),
            "data" => $workOrders
        );

        return response()->json($responsePayload);
    }

    private function getWorkOrderPayload(ProductionWorkOrder $workOrder): array
    {
        $productionItem = $workOrder->inventoryItem;
        $productionPlant = WaLocationAndStore::select('location_name', 'id')->find($workOrder->production_plant_id);
        return [
            'id' => $workOrder->id,
            'order_reference' => $workOrder->getOrderReference(),
            'production_item' => $productionItem->title,
            'production_item_qoh' => getItemAvailableQuantity($productionItem->stock_id_code, $productionItem->wa_location_and_store_id),
            'order_date' => Carbon::parse($workOrder->created_at)->toDayDateTimeString(),
            'production_plant' => $productionPlant->location_name,
            'description' => $workOrder->description ?? '-',
            'status' => $workOrder->getStatus(),
            'bom_is_available' => $workOrder->getBomAvailability(),
            'bom_availability' => $workOrder->getBomAvailability() ? 'In Stock' : 'Unavailable',
            'bom' => $productionItem->bom->map(function (WaInventoryItemRawMaterial $bomItem, $key) use ($workOrder, $productionItem) {
                $payload = [
                    'id' => $bomItem->id,
                    'raw_material_name' => $bomItem->raw_material()->title,
                    'base_quantity' => $bomItem->quantity,
                    'required_quantity' => (float)$bomItem->quantity * (float)$workOrder->production_quantity,
                    'unit_cost' => $bomItem->raw_material()->standard_cost,
                    'qoh' => (float)Arr::get($bomItem->raw_material()->getstockmoves, 'qauntity')
                ];

                $payload['total_cost'] = format_amount_with_currency($payload['unit_cost'] * $payload['required_quantity']);
                $payload['unit_cost'] = format_amount_with_currency($payload['unit_cost']);
                $payload['availability'] = ($payload['qoh'] >= $payload['required_quantity']) ? 'In Stock' : 'Unavailable';

                return $payload;
            }),
            'operations' => $productionItem->processes()->orderBy('step_number')->get()
                ->map(function (ProductionProcess $process, int $key) {
                    return [
                        'step_number' => $process->pivot->step_number,
                        'operation' => $process->operation,
                        'duration' => CarbonInterval::minutes($process->pivot->duration)->cascade()->forHumans(),
                    ];
                }),
        ];
    }

    public function create()
    {
        $title = 'Add Work Order';
        $breadcum = [
            $this->baseTitle => route("$this->baseRouteName.index"),
            $title => ''
        ];

        $nextOrderNumber = 1;
        if ($lastOrder = ProductionWorkOrder::latest()->first()) {
            $nextOrderNumber = $lastOrder->id + 1;
        }

        return view("$this->resourceFolder.create", [
            'model' => $this->model,
            'title' => $title,
            'breadcum' => $breadcum,
            'base_route_name' => $this->baseRouteName,
            'order_number' => "WO-00$nextOrderNumber",
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        // TODO: Validate
        ProductionWorkOrder::create([
            'wa_inventory_item_id' => $request->wa_inventory_item_id,
            'production_quantity' => $request->production_quantity,
            'production_plant_id' => $request->production_plant_id,
            'description' => $request->description,
        ]);

        return redirect()->route("$this->baseRouteName.index")->with('success', 'Work Order created successfully');
    }

    public function show($id)
    {
        $title = $this->baseTitle;

        $order = ProductionWorkOrder::find($id);

        $breadcum = [
            $title => route("$this->baseRouteName.index"),
            $order->getOrderReference() => ''
        ];

        return view("$this->resourceFolder.show", [
            'model' => $this->model,
            'title' => $title,
            'breadcum' => $breadcum,
            'base_route_name' => $this->baseRouteName,
            'work_order' => $this->getWorkOrderPayload($order)
        ]);
    }

    public function edit(ProductionWorkOrder $order)
    {
    }

    public function start($id): RedirectResponse
    {
        DB::beginTransaction();

        try {
            // Move stock
            $order = ProductionWorkOrder::find($id);
            $productionItem = $order->inventoryItem;
            $bomItems = $productionItem->bom;
            foreach ($bomItems as $bomItem) {
                $rawMaterialItem = $bomItem->raw_material();
                $adjustmentQuantity = ((float)$order->production_quantity * (float)$bomItem->quantity) * -1;
                $this->adjustItemStock($rawMaterialItem, $adjustmentQuantity, $order->production_plant_id);
            }

            // Update work order status
            $order->update(['status' => 'in_progress']);

            DB::commit();
            return redirect()->route("$this->baseRouteName.index")->with('success', 'Work order started successfully');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'An error was encountered while attempting to start work order. Please try again.']);
        }
    }

    public function pause($id): RedirectResponse
    {
        try {
            $order = ProductionWorkOrder::find($id);
            $order->update(['status' => 'paused']);

            return redirect()->route("$this->baseRouteName.index")->with('success', 'Work order paused successfully');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors(['error' => 'An error was encountered while attempting to pause work order. Please try again.']);
        }
    }

    public function resume($id): RedirectResponse
    {
        try {
            $order = ProductionWorkOrder::find($id);
            $order->update(['status' => 'in_progress']);

            return redirect()->route("$this->baseRouteName.index")->with('success', 'Work order resumed successfully');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors(['error' => 'An error was encountered while attempting to resume work order. Please try again.']);
        }
    }

    public function complete($id): RedirectResponse
    {
        DB::beginTransaction();

        try {
            $order = ProductionWorkOrder::find($id);
            $order->update(['status' => 'completed']);

            $productionItem = $order->inventoryItem;
            $adjustmentQuantity = (float)$order->production_quantity;
            $this->adjustItemStock($productionItem, $adjustmentQuantity, $order->production_plant_id);

            DB::commit();
            return redirect()->route("$this->baseRouteName.index")->with('success', 'Work order completed successfully');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'An error was encountered while attempting to complete work order. Please try again.']);
        }
    }

    public function void($id)
    {
        try {
            $order = ProductionWorkOrder::find($id);
            $order->delete();

            return redirect()->route("$this->baseRouteName.index")->with('success', 'Work order voided successfully');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors(['error' => 'An error was encountered while attempting to complete work order. Please try again.']);
        }
    }

    public function getLocationStores(): JsonResponse
    {
        $locations = WaLocationAndStore::select('location_name', 'id')->get();
        return response()->json($locations);
    }

    public function getProducibleProducts(): JsonResponse
    {
        $producibleProducts = WaInventoryItem::with(['bom', 'bom.inventory_item', 'processes'])
            ->producible()
            ->select(['id', 'title'])
            ->get();

        $responsePayload = [];
        foreach ($producibleProducts as $producibleProduct) {
            $bom = $producibleProduct->bom->map(function (WaInventoryItemRawMaterial $bomItem, $key) {
                return [
                    'raw_material_name' => $bomItem->raw_material()->title,
                    'base_quantity' => $bomItem->quantity,
                    'unit_cost' => $bomItem->raw_material()->standard_cost,
                ];
            });

            $processes = $producibleProduct->processes()->orderBy('step_number')->get()
                ->map(function (ProductionProcess $process, int $key) {
                    return [
                        'step_number' => $process->pivot->step_number,
                        'operation' => $process->operation,
                        'duration' => CarbonInterval::minutes($process->pivot->duration)->cascade()->forHumans(),
                    ];
                });

            $itemPayload = [
                'id' => $producibleProduct->id,
                'title' => $producibleProduct->title,
                'bom' => $bom->all(),
                'processes' => $processes->all()
            ];

            $responsePayload[] = $itemPayload;
        }

        return response()->json($responsePayload);
    }

    private function adjustItemStock(WaInventoryItem $item, $adjustmentQuantity, $locationStoreId)
    {
        $logged_user_profile = getLoggeduserProfile();
        $entity = new StockAdjustment();
        $entity->user_id = $logged_user_profile->id;
        $entity->item_id = $item->id;
        $entity->wa_location_and_store_id = $locationStoreId;
        $entity->adjustment_quantity = $adjustmentQuantity;
        $entity->comments = null;

        $adjustmentCode = getCodeWithNumberSeries('ITEM ADJUSTMENT');
        $entity->item_adjustment_code = $adjustmentCode;
        $entity->save();

        $series_module = $item_adj = WaNumerSeriesCode::where('module', 'ITEM ADJUSTMENT')->first();
        $WaAccountingPeriod = WaAccountingPeriod::where('is_current_period', '1')->first();
        $dateTime = date('Y-m-d H:i:s');

        $stockMove = new WaStockMove();
        $stockMove->user_id = $logged_user_profile->id;
        $stockMove->stock_adjustment_id = $entity->id;
        $stockMove->restaurant_id = $logged_user_profile->restaurant_id;
        $stockMove->wa_location_and_store_id = $entity->wa_location_and_store_id;
        $stockMove->wa_inventory_item_id = $item->id;
        $stockMove->standard_cost = $item->standard_cost;
        $stockMove->qauntity = $adjustmentQuantity;
        $stockMove->new_qoh = ($item->getAllFromStockMoves->where('wa_location_and_store_id', @$entity->wa_location_and_store_id)->sum('qauntity') ?? 0) + $stockMove->qauntity;
        $stockMove->stock_id_code = $item->stock_id_code;
        $stockMove->grn_type_number = $series_module->type_number;
        $stockMove->document_no = $adjustmentCode;
        $stockMove->grn_last_nuber_used = $series_module->last_number_used;
        $stockMove->price = $item->standard_cost;
        $stockMove->refrence = $entity->comments;
        $stockMove->save();

        $dr = new WaGlTran();
        $dr->stock_adjustment_id = $entity->id;
        $dr->grn_type_number = $series_module->type_number;
        $dr->transaction_type = $item_adj->description;
        $dr->transaction_no = $adjustmentCode;
        $dr->grn_last_used_number = $series_module->last_number_used;
        $dr->trans_date = $dateTime;
        $dr->restaurant_id = getLoggeduserProfile()->restaurant_id;
        $dr->period_number = $WaAccountingPeriod ? $WaAccountingPeriod->period_no : null;

        $dr->account = $item->getInventoryCategoryDetail->getStockGlDetail->account_code;
        $dr->amount = abs($item->standard_cost * $adjustmentQuantity);
        if ($adjustmentQuantity < '0') {
            //             $dr->amount = '-'.abs($item_row->standard_cost * $adjustment_quantity);
            $dr->account = $item->getInventoryCategoryDetail->getusageGlDetail->account_code;
        }
        $dr->narrative = $item->stock_id_code . '/' . $item->title . '/' . $item->standard_cost . '@' . $adjustmentQuantity;
        $dr->save();

        $dr = new WaGlTran();
        $dr->stock_adjustment_id = $entity->id;
        $dr->grn_type_number = $series_module->type_number;
        $dr->grn_last_used_number = $series_module->last_number_used;
        $dr->transaction_type = $item_adj->description;
        $dr->transaction_no = $adjustmentCode;
        $dr->trans_date = $dateTime;
        $dr->restaurant_id = getLoggeduserProfile()->restaurant_id;

        $dr->period_number = $WaAccountingPeriod ? $WaAccountingPeriod->period_no : null;
        $dr->account = $item->getInventoryCategoryDetail->getPricevarianceGlDetail->account_code;
        $tamount = $item->standard_cost * $adjustmentQuantity;

        $dr->amount = '-' . abs($tamount);
        if ($adjustmentQuantity < '0') {
            //           $dr->amount = abs($item_row->standard_cost * $adjustment_quantity);
            $dr->account = $item->getInventoryCategoryDetail->getStockGlDetail->account_code;
        }
        $dr->narrative = $item->stock_id_code . '/' . $item->title . '/' . $item->standard_cost . '@' . $adjustmentQuantity;
        $dr->save();


        updateUniqueNumberSeries('ITEM ADJUSTMENT', $adjustmentCode);
    }
}
