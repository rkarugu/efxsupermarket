<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\WaInternalRequisition;
use App\Model\WaInternalRequisitionItem;
use App\Model\WaInventoryItem;
use App\Model\WaRouteCustomer;
use App\Model\WaCustomer;
use App\Model\Route;
use App\Model\WaLocationAndStore;
use App\Model\WaInventoryLocationUom;
use App\Model\WaUnitOfMeasure;
use App\Model\DeliveryCentres;
use App\Model\WaStockMove;
use App\SalesmanShift;
use App\Model\WaNumerSeriesCode;
use App\Jobs\PerformPostSaleActions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class SalesmanOrderController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct()
    {
        $this->model = 'salesman-orders';
        $this->title = 'Salesman Orders';
    }

    /**
     * Display salesman dashboard
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Load the routes relationship
        $user->load('routes');

        // Check if user is a salesman (you may need to adjust role checking based on your system)
        if (!$this->isSalesman($user)) {
            Session::flash('error', 'Access denied. This section is for salesmen only.');
            return redirect()->back();
        }

        $title = 'Salesman Dashboard';
        $model = $this->model;

        // Get current active shift
        $activeShift = SalesmanShift::where('salesman_id', $user->id)
            ->where('status', 'open')
            ->latest()
            ->first();

        // Get today's orders
        $todaysOrders = WaInternalRequisition::where('user_id', $user->id)
            ->whereDate('created_at', Carbon::today())
            ->with(['getRouteCustomer', 'getRelatedItem'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Get route customers for the salesman's route
        $routeCustomers = collect();
        $userRoute = $user->routes()->first(); // Get first assigned route
        if ($userRoute) {
            $routeCustomers = WaRouteCustomer::where('route_id', $userRoute->id)
                ->where('status', 'approved')
                ->orderBy('bussiness_name')
                ->get();
        }

        $breadcum = [$title => '', 'Dashboard' => ''];
        
        return view('admin.salesman_orders.index', compact(
            'title', 'model', 'breadcum', 'activeShift', 'todaysOrders', 'routeCustomers', 'user'
        ));
    }

    /**
     * Show order creation form
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        
        // Load the routes relationship
        $user->load('routes');
        
        // Check if user is a salesman
        if (!$this->isSalesman($user)) {
            Session::flash('error', 'Access denied. This section is for salesmen only.');
            return redirect()->back();
        }

        // Check if salesman has an active shift
        $activeShift = SalesmanShift::where('salesman_id', $user->id)
            ->where('status', 'open')
            ->latest()
            ->first();

        if (!$activeShift) {
            Session::flash('error', 'You need to have an active shift to create orders. Please contact your supervisor.');
            return redirect()->route('salesman-orders.index');
        }

        $title = 'Create New Order';
        $model = $this->model;

        // Get route customers
        $userRoute = $user->routes()->first(); // Get first assigned route
        $routeCustomers = collect();
        if ($userRoute) {
            $routeCustomers = WaRouteCustomer::where('route_id', $userRoute->id)
                ->where('status', 'approved')
                ->orderBy('bussiness_name')
                ->get();
        }

        $breadcum = [$this->title => route($model.'.index'), 'Create Order' => ''];
        
        // Variables needed for partials.shortcuts
        $selling_allowance = $user->drop_limit ?? 50000; // Default allowance for salesmen
        $permission = $user->permissions ?? []; // User permissions array
        $data = (object)['customer_phone_number' => '']; // Default data object
        
        return view('admin.salesman_orders.create', compact(
            'title', 'model', 'breadcum', 'routeCustomers', 'activeShift', 'user',
            'selling_allowance', 'permission', 'data'
        ));
    }

    /**
     * Store a new order
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!$this->isSalesman($user)) {
            return response()->json(['success' => false, 'message' => 'Access denied']);
        }

        $validator = Validator::make($request->all(), [
            'wa_route_customer_id' => 'required|exists:wa_route_customers,id',
            'items' => 'required|array|min:1',
            'items.*.wa_inventory_item_id' => 'required|exists:wa_inventory_items,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.selling_price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        // Get active shift
        $activeShift = SalesmanShift::where('salesman_id', $user->id)
            ->where('status', 'open')
            ->latest()
            ->first();

        if (!$activeShift) {
            return response()->json(['success' => false, 'message' => 'No active shift found']);
        }

        // Get customer details
        $customer = WaRouteCustomer::with('route')->find($request->wa_route_customer_id);
        if (!$customer) {
            return response()->json(['success' => false, 'message' => 'Customer not found']);
        }

        DB::beginTransaction();
        try {
            // Generate requisition number
            $requisitionNo = $this->generateRequisitionNumber();
            
            // Create internal requisition (invoice)
            $requisition = WaInternalRequisition::create([
                'requisition_no' => $requisitionNo,
                'slug' => strtolower($requisitionNo),
                'user_id' => $user->id,
                'restaurant_id' => $user->restaurant_id,
                'to_store_id' => $user->wa_location_and_store_id,
                'wa_location_and_store_id' => $user->wa_location_and_store_id,
                'requisition_date' => Carbon::now(),
                'name' => $customer->name,
                'route_id' => $customer->route_id,
                'route' => $customer->route->route_name ?? '',
                'customer_id' => $customer->customer_id,
                'wa_route_customer_id' => $customer->id,
                'customer' => $customer->name,
                'customer_phone_number' => $customer->phone,
                'customer_pin' => $customer->kra_pin,
                'status' => 'APPROVED',
                'wa_shift_id' => $activeShift->id,
                'shift_type' => $activeShift->shift_type ?? 'regular',
                'invoice_type' => 'Backend',
            ]);

            // Add order items
            $dateTime = Carbon::now();
            $items = [];
            foreach ($request->items as $item) {
                $inventoryItem = WaInventoryItem::find($item['wa_inventory_item_id']);
                
                $items[] = [
                    'wa_internal_requisition_id' => $requisition->id,
                    'wa_inventory_item_id' => $item['wa_inventory_item_id'],
                    'quantity' => $item['quantity'],
                    'standard_cost' => $inventoryItem->standard_cost,
                    'selling_price' => $item['selling_price'],
                    'total_cost' => $item['quantity'] * $item['selling_price'],
                    'tax_manager_id' => $inventoryItem->tax_manager_id,
                    'vat_rate' => 0, // You may need to calculate this based on tax_manager
                    'vat_amount' => 0, // You may need to calculate this
                    'total_cost_with_vat' => $item['quantity'] * $item['selling_price'],
                    'store_location_id' => $user->wa_location_and_store_id,
                    'hs_code' => $inventoryItem->hs_code,
                    'discount' => $item['discount'] ?? 0,
                    'discount_description' => '',
                    'created_at' => $dateTime,
                    'updated_at' => $dateTime,
                ];
            }
            
            WaInternalRequisitionItem::insert($items);

            // Trigger post-sale actions (stock moves, transfers, etc.)
            PerformPostSaleActions::dispatch($requisition)->afterCommit();

            DB::commit();

            return response()->json([
                'success' => true, 
                'message' => 'Order created successfully',
                'order_id' => $requisition->id,
                'requisition_no' => $requisition->requisition_no
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Salesman order creation failed: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'customer_id' => $request->wa_route_customer_id,
                'items' => $request->items
            ]);
            return response()->json(['success' => false, 'message' => 'Error creating order: ' . $e->getMessage()]);
        }
    }

    /**
     * Show order details
     */
    public function show($id)
    {
        $user = Auth::user();
        
        if (!$this->isSalesman($user)) {
            Session::flash('error', 'Access denied.');
            return redirect()->back();
        }

        $order = WaInternalRequisition::with([
                'getRouteCustomer', 
                'getRelatedItem.getInventoryItemDetail.pack_size',
                'getrelatedEmployee',
                'shift',
                'route'
            ])
            ->where('user_id', $user->id)
            ->findOrFail($id);

        $title = 'Order Details';
        $model = $this->model;
        $breadcum = [$this->title => route($model.'.index'), 'Order Details' => ''];

        return view('admin.salesman_orders.show', compact('title', 'model', 'breadcum', 'order'));
    }

    /**
     * Get available inventory items for salesman (using stock moves like SalesInvoiceController)
     */
    private function getAvailableInventoryItemsForUser($user)
    {
        // Get items that have bin locations first (more efficient than checking each item)
        $itemsWithBins = DB::table('wa_inventory_items as items')
            ->join('wa_inventory_location_uom as bins', 'items.id', '=', 'bins.inventory_id')
            ->leftJoin('wa_unit_of_measures as units', 'items.wa_unit_of_measure_id', '=', 'units.id')
            ->where('bins.location_id', $user->wa_location_and_store_id)
            ->where(function($query) {
                $query->whereNull('items.block_this')
                      ->orWhere('items.block_this', '!=', 1);
            })
            ->select([
                'items.id',
                'items.title',
                'items.standard_cost',
                'items.selling_price',
                'units.title as unit_name'
            ])
            ->orderBy('items.title')
            ->limit(100) // Limit to prevent timeout
            ->get();

        // Calculate stock quantities using stock moves (same as SalesInvoiceController)
        $availableItems = collect();
        
        foreach ($itemsWithBins as $item) {
            $itemQoh = WaStockMove::where('wa_location_and_store_id', $user->wa_location_and_store_id)
                ->where('wa_inventory_item_id', $item->id)
                ->sum('qauntity');

            // Only include items with positive quantity
            if ($itemQoh > 0) {
                $item->available_quantity = $itemQoh;
                $availableItems->push($item);
            }
        }

        return $availableItems;
    }

    /**
     * AJAX endpoint for inventory search (used by partials.shortcuts)
     */
    public function getAvailableInventoryItems(Request $request)
    {
        $user = Auth::user();
        
        if (!$this->isSalesman($user)) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        $search = $request->get('search', '');
        $storeLocationId = $request->get('store_location_id', $user->wa_location_and_store_id);
        
        // Get items that match search and have bin locations
        $itemsQuery = DB::table('wa_inventory_items as items')
            ->join('wa_inventory_location_uom as bins', 'items.id', '=', 'bins.inventory_id')
            ->leftJoin('wa_unit_of_measures as units', 'items.wa_unit_of_measure_id', '=', 'units.id')
            ->where('bins.location_id', $storeLocationId)
            ->where(function($query) {
                $query->whereNull('items.block_this')
                      ->orWhere('items.block_this', '!=', 1);
            });

        // Add search filter if provided
        if (!empty($search)) {
            $itemsQuery->where('items.title', 'LIKE', '%' . $search . '%');
        }

        $items = $itemsQuery->select([
                'items.id',
                'items.title',
                'items.standard_cost',
                'items.selling_price',
                'units.title as unit_name'
            ])
            ->orderBy('items.title')
            ->limit(50)
            ->get();

        // Build HTML response like POS system expects
        $html = '';
        foreach ($items as $item) {
            // Calculate stock quantity
            $itemQoh = WaStockMove::where('wa_location_and_store_id', $storeLocationId)
                ->where('wa_inventory_item_id', $item->id)
                ->sum('qauntity');

            if ($itemQoh > 0) {
                $html .= '<div class="textDataItem" data-id="' . $item->id . '" style="padding: 8px; border-bottom: 1px solid #eee; cursor: pointer;">';
                $html .= '<strong>' . $item->title . '</strong><br>';
                $html .= 'Stock: ' . number_format($itemQoh, 2) . ' ' . ($item->unit_name ?? 'Units');
                $html .= '<br>Price: KSh ' . number_format($item->selling_price, 2);
                $html .= '</div>';
            }
        }

        if (empty($html)) {
            $html = '<div style="padding: 8px; color: #999;">No items found</div>';
        }

        return response()->json([
            'view' => $html,
            'results' => $items->count()
        ]);
    }

    /**
     * Check if user is a salesman
     */
    private function isSalesman($user)
    {
        if (!$user) {
            return false;
        }

        $roleName = '';
        if (isset($user->userRole)) {
            // Some installations use 'name', others 'title'
            $roleName = $user->userRole->name ?? $user->userRole->title ?? '';
        }

        $hasRoute = false;
        try {
            // Check if user has routes assigned via many-to-many relationship
            $hasRoute = method_exists($user, 'routes') && $user->routes()->exists();
        } catch (\Throwable $e) {
            $hasRoute = !empty($user->route);
        }

        $salesRoleIds = config('salesman.sales_role_ids', [169, 170]);
        $salesKeywords = config('salesman.sales_role_keywords', ['sales', 'salesman', 'representative']);
        
        $isSalesRoleId = in_array((int) $user->role_id, $salesRoleIds);
        
        $roleLooksSales = false;
        foreach ($salesKeywords as $keyword) {
            if (stripos($roleName, $keyword) !== false) {
                $roleLooksSales = true;
                break;
            }
        }

        return ($hasRoute || $roleLooksSales || $isSalesRoleId);
    }

    /**
     * Generate requisition number
     */
    private function generateRequisitionNumber()
    {
        try {
            $code = getCodeWithNumberSeries('Sales Order');
            if ($code) {
                updateUniqueNumberSeries('Sales Order', $code);
                return $code;
            }
        } catch (\Exception $e) {
            \Log::warning('Number series not found for Sales Order: ' . $e->getMessage());
        }
        
        // Fallback: Generate a simple requisition number
        $prefix = 'SO';
        $date = Carbon::now()->format('Ymd');
        $lastOrder = WaInternalRequisition::whereDate('created_at', Carbon::today())
            ->where('requisition_no', 'LIKE', $prefix . $date . '%')
            ->orderBy('id', 'desc')
            ->first();
        
        if ($lastOrder) {
            $lastNumber = intval(substr($lastOrder->requisition_no, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return $prefix . $date . $newNumber;
    }

    /**
     * Get route customers for AJAX
     */
    public function getRouteCustomers(Request $request)
    {
        $user = Auth::user();
        
        $userRoute = $user->routes()->first();
        if (!$userRoute) {
            return response()->json(['customers' => []]);
        }

        $customers = WaRouteCustomer::where('route_id', $userRoute->id)
            ->where('status', 'approved')
            ->select('id', 'bussiness_name', 'name', 'phone')
            ->orderBy('bussiness_name')
            ->get();

        return response()->json(['customers' => $customers]);
    }

    /**
     * Search inventory items for AJAX (POS-style)
     */
    public function searchItems(Request $request)
    {
        $user = Auth::user();
        $query = $request->get('search', '');

        if (strlen($query) < 3) {
            return '<table class="table table-bordered"><tr><td>Type at least 3 characters</td></tr></table>';
        }

        try {
            // Get items with bin locations
            $items = DB::table('wa_inventory_items as items')
                ->join('wa_inventory_location_uom as bins', 'items.id', '=', 'bins.inventory_id')
                ->leftJoin('wa_unit_of_measures as units', 'items.wa_unit_of_measure_id', '=', 'units.id')
                ->where('bins.location_id', $user->wa_location_and_store_id)
                ->where('items.title', 'LIKE', '%' . $query . '%')
                ->where(function($q) {
                    $q->whereNull('items.block_this')
                      ->orWhere('items.block_this', '!=', 1);
                })
                ->select([
                    'items.id',
                    'items.title',
                    'items.standard_cost',
                    'items.selling_price',
                    'units.title as unit_name'
                ])
                ->limit(20)
                ->get();

            if ($items->isEmpty()) {
                return '<table class="table table-bordered"><tr><td>No items found</td></tr></table>';
            }

            // Build HTML response like POS system
            $html = '<table class="table table-bordered">';
            
            foreach ($items as $item) {
                // Calculate stock for this item
                $itemQoh = WaStockMove::where('wa_location_and_store_id', $user->wa_location_and_store_id)
                    ->where('wa_inventory_item_id', $item->id)
                    ->sum('qauntity');

                if ($itemQoh > 0) {
                    $html .= '<tr class="SelectedLi" onclick="selectItem(' . $item->id . ', \'' . addslashes($item->title) . '\', \'' . addslashes($item->unit_name ?: '') . '\', ' . $item->selling_price . ', ' . $itemQoh . ')">';
                    $html .= '<td>';
                    $html .= '<strong>' . htmlspecialchars($item->title) . '</strong><br>';
                    $html .= '<small>Unit: ' . htmlspecialchars($item->unit_name ?: 'N/A') . ' | Price: KSh ' . number_format($item->selling_price, 2) . ' | Available: ' . $itemQoh . '</small>';
                    $html .= '</td>';
                    $html .= '</tr>';
                }
            }
            
            $html .= '</table>';
            
            return $html;

        } catch (\Exception $e) {
            return '<table class="table table-bordered"><tr><td>Error: ' . htmlspecialchars($e->getMessage()) . '</td></tr></table>';
        }
    }

    /**
     * Get inventory item details for AJAX
     */
    public function getItemDetails(Request $request)
    {
        $user = Auth::user();
        $itemId = $request->get('id'); // Changed from item_id to id to match our AJAX call

        // Get item details with bin location check
        $itemDetails = DB::table('wa_inventory_items as items')
            ->join('wa_inventory_location_uom as bins', 'items.id', '=', 'bins.inventory_id')
            ->leftJoin('wa_unit_of_measures as units', 'items.wa_unit_of_measure_id', '=', 'units.id')
            ->where('items.id', $itemId)
            ->where('bins.location_id', $user->wa_location_and_store_id)
            ->select([
                'items.id',
                'items.title',
                'items.standard_cost',
                'items.selling_price',
                'units.title as unit_name'
            ])
            ->first();

        if (!$itemDetails) {
            return response()->json(['error' => 'Item not found or not available at this location']);
        }

        // Calculate stock quantity using stock moves (same as SalesInvoiceController)
        $itemQoh = WaStockMove::where('wa_location_and_store_id', $user->wa_location_and_store_id)
            ->where('wa_inventory_item_id', $itemId)
            ->sum('qauntity');

        // Return format expected by our JavaScript
        return response()->json([
            'id' => $itemDetails->id,
            'title' => $itemDetails->title,
            'selling_price' => $itemDetails->selling_price ?? 0,
            'unit_name' => $itemDetails->unit_name ?? 'Units',
            'available_quantity' => $itemQoh,
        ]);
    }

    /**
     * Open shift for salesman
     */
    public function openShift(Request $request)
    {
        $user = Auth::user();
        
        if (!$this->isSalesman($user)) {
            return response()->json(['success' => false, 'message' => 'Access denied']);
        }

        // Check if there's already an open shift
        $existingShift = SalesmanShift::where('salesman_id', $user->id)
            ->where('status', 'open')
            ->first();

        if ($existingShift) {
            return response()->json(['success' => false, 'message' => 'You already have an open shift']);
        }

        $validator = Validator::make($request->all(), [
            'shift_type' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        try {
            $shift = new SalesmanShift();
            $shift->salesman_id = $user->id;
            $userRoute = $user->routes()->first();
            $shift->route_id = $userRoute ? $userRoute->id : null;
            $shift->shift_type = $request->shift_type;
            $shift->start_time = Carbon::now();
            $shift->status = 'open';
            $shift->save();

            return response()->json([
                'success' => true, 
                'message' => 'Shift opened successfully',
                'shift_id' => $shift->id
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error opening shift: ' . $e->getMessage()]);
        }
    }

    /**
     * Close shift for salesman
     */
    public function closeShift(Request $request)
    {
        $user = Auth::user();
        
        if (!$this->isSalesman($user)) {
            return response()->json(['success' => false, 'message' => 'Access denied']);
        }

        $activeShift = SalesmanShift::where('salesman_id', $user->id)
            ->where('status', 'open')
            ->latest()
            ->first();

        if (!$activeShift) {
            return response()->json(['success' => false, 'message' => 'No active shift found']);
        }

        try {
            $activeShift->status = 'close';
            $activeShift->closed_time = Carbon::now();
            $activeShift->save();

            return response()->json([
                'success' => true, 
                'message' => 'Shift closed successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error closing shift: ' . $e->getMessage()]);
        }
    }

    /**
     * Search inventory items for order creation
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchInventory(Request $request)
    {
        try {
            $data = WaInventoryItem::select([
                'wa_inventory_items.*',
                DB::RAW('(select SUM(wa_stock_moves.qauntity) FROM wa_stock_moves where wa_stock_moves.wa_inventory_item_id = wa_inventory_items.id AND wa_stock_moves.wa_location_and_store_id = ' . ($request->store_location_id ?? 'wa_inventory_items.store_location_id') . ') as quantity'),
            ])
                ->join('pack_sizes', 'wa_inventory_items.pack_size_id', '=', 'pack_sizes.id')
                ->where('status', 1)
                ->where(function ($q) use ($request) {
                    if ($request->search) {
                        $q->where('wa_inventory_items.title', 'LIKE', "%$request->search%");
                        $q->orWhere('stock_id_code', 'LIKE', "%$request->search%");
                    }
                })->where(function ($e) use ($request) {
                    if ($request->store_c) {
                        $e->where('store_c_deleted', 0);
                    }
                })->limit(30)->get();

            // Format data for salesman order taking (simpler format)
            $results = [];
            foreach ($data as $item) {
                $qoh = WaStockMove::where('wa_inventory_item_id', $item->id)
                    ->where('wa_location_and_store_id', $request->store_location_id)
                    ->sum('qauntity');
                
                $results[] = [
                    'id' => $item->id,
                    'item_name' => $item->title,
                    'stock_code' => $item->stock_id_code,
                    'unit_name' => $item->pack_size->title ?? 'Unit',
                    'available_stock' => $qoh ?? 0,
                    'selling_price' => $item->selling_price
                ];
            }
            
            return response()->json($results);
            
        } catch (\Exception $e) {
            \Log::error('Error in searchInventory: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return response()->json([
                'error' => 'An error occurred while searching inventory: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test search route for debugging
     */
    public function testSearch(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Search route is working!',
            'timestamp' => now()
        ]);
    }
}
