<?php

namespace App\Http\Controllers\Admin;

use App\WaDemand;
use App\Model\User;
use App\ReturnedGrn;
use App\Model\Restaurant;
use App\Model\WaSupplier;
use App\Model\WaStockMove;
use Illuminate\Http\Request;
use App\Models\WaStoreReturn;
use App\Model\WaInventoryItem;
use App\Model\WaPurchaseOrder;
use App\Models\WaReturnDemand;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use App\Model\WaExternalRequisition;
use Illuminate\Support\Facades\Auth;
use App\Services\ExcelDownloadService;

class SupplierOverviewController extends Controller
{
    protected $model = 'suppliers-overview';
    protected $base_route = 'maintain-suppliers';
    protected $resource_folder  = 'admin.suppliers-overview';
    protected $base_title = 'Suppliers Overview';
    protected $title = 'Suppliers Overview';
    
    public function index()
    {
        if (!can('view', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        $model = $this->model;
        $title = $this->title;
        $breadcum = ['Maintain Suppliers' => '', 'Suppliers Dashboard' => ''];

        $user = Auth::user();

        $suppliers = $this->getUserSuppliers();

        $demands = $this->getUnprocessedDemands($user);

        $returns = $this->getPendingGoodReturns(Auth::user());

        $purchaseOrders = $this->purchaseOrderAgeing();

        $branchRequisitions = $this->getBranchRequisitions();

        $stats = (Object) [
            'suppliers_count' => $suppliers->count(),
            'suppliers_total_balance' => $suppliers->sum('supp_trans_sum_total_amount_inc_vat'),
            'suppliers_items_count' => WaInventoryItem::whereHas('suppliers', fn ($query) => $query->procurementRole($user))->count(),
            'demands_count' =>$demands->count(),
            'total_demands_amount' => $demands->sum('demand_amount'),
            'total_returns_amount' => $returns->sum(fn ($return) => $return->totalCost()),
            'returns_count' => $returns->count(),
            'purchase_orders_count' => $purchaseOrders->count(),
            'branch_requisitions_count' => $branchRequisitions->count()
        ];
        
        // Department performance chart data
        $departmentPerformanceChartData = WaStockMove::with(['inventoryItem' => function ($query) {
            $query->with(['category' => fn ($query) => $query->select('wa_inventory_categories.id', 'category_description')])
                ->select('wa_inventory_items.id', 'wa_inventory_items.wa_inventory_category_id');
        }])
            ->whereHas('inventoryItem', function($query) use ($user) {
                $query->whereHas('suppliers', function($query) use ($user) {
                    $query->procurementRole($user);
                });
            })
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            // ->whereBetween('created_at', [Carbon::parse('2024-03-1'), Carbon::parse('2024-03-31')])
            ->where('document_no', 'like', '%INV%')
            ->select(
                'wa_stock_moves.wa_inventory_item_id', 
                'wa_stock_moves.total_cost', 
                'wa_stock_moves.created_at'
            )
            ->get()
            ->groupBy(function ($stockMove) {
                return $stockMove->inventoryItem->category->category_description;
            })
            ->sortKeys()
            ->map(function ($categoryGroup) {
                return $categoryGroup->sum('total_cost');
            });
        
        $supplierCreditLimits = $suppliers->map(function ($supplier) {
            $supplier->variance = $supplier->credit_limit - $supplier->supp_trans_sum_total_amount_inc_vat;

            return $supplier;
        })
            ->sortBy('variance')
            ->values();
        
        
        $supplierTargets = WaSupplier::with(['products.stockMoves' => function ($query) {
            $query->where('document_no', 'like', '%INV%')
                ->whereDate('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
        }])
            ->procurementRole(Auth::user())
            ->get()
            ->map(function($supplier) {
                $supplier->monthly_sales = $supplier->products->sum(function($product) {
                    return $product->stockMoves->sum('total_cost');
                });
                if ($supplier->monthly_target != 0) {
                    $supplier->percentage_target_achieved = round(($supplier->monthly_target - $supplier->monthly_sales) / $supplier->monthly_target * 100);
                } else {
                    $supplier->percentage_target_achieved = 0;
                }


                return $supplier;
            })
            ->sortBy('percentage_target_achieved')
            ->values();

        return view(
            'admin.suppliers-overview.index', 
            compact(
                'model', 
                'title', 
                'breadcum', 
                'suppliers', 
                'demands', 
                'user', 
                'returns', 
                'purchaseOrders', 
                'branchRequisitions', 
                'stats', 
                'departmentPerformanceChartData',
                'supplierCreditLimits',
                'supplierTargets'
            ));
    }

    public function suppliersList()
    {
        if (!can('view', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        } 

        $model = $this->model;
        $title = $this->title;
        $breadcum = ['Maintain Suppliers' => '', 'Suppliers Dashboard' => route('suppliers-overview.index'), 'My Suppliers' => ''];

        $suppliers = $this->getUserSuppliers()
            ->sortByDesc('supp_trans_sum_total_amount_inc_vat')
            ->values();

        foreach ($suppliers as $supplier) {
            foreach($supplier->products as $product) {
                $product->stock_value = $product->stock_moves_sum_qauntity * (float)$product->standard_cost;
            }
            
            $supplier->stock_value = $supplier->products->sum('stock_value');

            $supplier->payable_amount = $supplier->supp_trans_sum_total_amount_inc_vat - $supplier->stock_value;
        }

        return view('admin.suppliers-overview.suppliers', compact('model', 'title', 'breadcum', 'suppliers'));
    }

    public function printSuppliers()
    {
        $suppliers = $suppliers = $this->getUserSuppliers(Auth::user());
        
        $export_array = [];
        foreach($suppliers as $supplier){
            $export_array[]=[
                $supplier->supplier_code,
                $supplier->name,
                $supplier->address,
                $supplier->telephone,
                $supplier->email,
                $supplier->supplier_since,
                $supplier->paymentTerm?->term_description,
                number_format($supplier->supp_trans_sum_total_amount_inc_vat, 2)
            ];
        }

        $export_array[] = [
            '',
            '',
            '',
            '',
            '',
            '',
            'Grand Total',
            number_format($suppliers->sum('supplier_balance'), 2)
        ];

        $report_name = 'my_suppliers_' . date('Y_m_d_H_i_A');
        
        return ExcelDownloadService::download($report_name, collect($export_array), ['SUPPLIER CODE','SUPPLIER NAME','ADDRESS','TELEPHONE','EMAIL','SUPPLIER SINCE','PAYMENT TERMS','TOTAL BALANCE']);
    }

    public function suppliersSalesByCategory(Request $request)
    {
        if (!can('view', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        } 

        $model = $this->model;
        $title = $this->title;
        $breadcum = ['Maintain Suppliers' => '', 'Suppliers Dashboard' => route('suppliers-overview.index'), 'Supplier Sales' => ''];

        $category = $request->query('category');
        $startDate = ($request->query('startDate') ? Carbon::parse($request->query('startDate')) : now()->startOfMonth())->format('Y-m-d');
        $endDate = ($request->query('endDate') ? Carbon::parse($request->query('endDate')) : now()->endOfMonth())->format('Y-m-d');

        $salesData = $this->getSuppliersSalesByCategory($category, $startDate, $endDate, Auth::user());

        return view('admin.suppliers-overview.sales-category', compact('model', 'title', 'breadcum', 'salesData', 'category', 'startDate', 'endDate'));
    }

    public function printSuppliersSalesByCategory(Request $request)
    {
        $user = Auth::user();

        $category = $request->query('category');
        $startDate = $request->query('start_date') ? Carbon::parse($request->query('start_date')) : now()->startOfMonth();
        $endDate = $request->query('end_date') ? Carbon::parse($request->query('end_date')) : now()->endOfMonth();

        $salesData = $this->getSuppliersSalesByCategory($category, $startDate, $endDate, $user);

        $pdf = PDF::loadView('admin.suppliers-overview.sales-category-print', compact('salesData', 'category', 'startDate', 'endDate'));

        return $pdf->stream("$category-sales-{$startDate->format('Y-m-d')}-{$endDate->format('Y-m-d')}.pdf");
    }

    public function lposWithoutGrn()
    {
        if (!can('view', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        } 

        $model = $this->model;
        $title = $this->title;
        $breadcum = ['Maintain Suppliers' => '', 'Suppliers Dashboard' => route('suppliers-overview.index'), 'My Suppliers' => ''];

        $purchaseOrders = $this->purchaseOrderAgeing();

        return view('admin.suppliers-overview.lpos-without-grn', compact('model', 'title', 'breadcum', 'purchaseOrders'));
    }

    public function unprocessedDemands()
    {
        if (!can('view', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        } 

        $model = $this->model;
        $title = $this->title;
        $breadcum = ['Maintain Suppliers' => '', 'Suppliers Dashboard' => route('suppliers-overview.index'), 'My Suppliers' => ''];

        $user = Auth::user();
        
        $demands = $this->getUnprocessedDemands($user);

        return view('admin.suppliers-overview.unprocessed-demands', compact('model', 'title', 'breadcum', 'demands', 'user'));
    }

    public function pendingGoodReturns()
    {
        if (!can('view', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        } 

        $model = $this->model;
        $title = $this->title;
        $breadcum = ['Maintain Suppliers' => '', 'Suppliers Dashboard' => route('suppliers-overview.index'), 'My Suppliers' => ''];

        $user = Auth::user();
        
        $returns = $this->getPendingGoodReturns($user);

        return view('admin.suppliers-overview.pending-good-returns', compact('model', 'title', 'breadcum', 'returns', 'user'));
    }

    public function departmentalPerformance()
    {
        if (!can('view', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        } 

        $model = $this->model;
        $title = $this->title;
        $breadcum = ['Maintain Suppliers' => '', 'Suppliers Dashboard' => route('suppliers-overview.index'), 'My Suppliers' => ''];

        $user = Auth::user();

        return view('admin.suppliers-overview.departmental-performance', compact('model', 'title', 'breadcum', 'user'));
    }

    public function branchRequisitions(Request $request)
    {
        if (!can('view', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        } 

        $model = $this->model;
        $title = $this->title;
        $breadcum = ['Maintain Suppliers' => '', 'Suppliers Dashboard' => route('suppliers-overview.index'), 'My Suppliers' => ''];

        $user = Auth::user();

        $branchRequisitions = $this->getBranchRequisitions();

        // Filters
        if ($request->user) {
            $branchRequisitions = $branchRequisitions->filter(fn ($branchRequisition) => $branchRequisition->user->id == $request->user);
        }
        if ($request->branch) {
            $branchRequisitions = $branchRequisitions->filter(fn ($branchRequisition) => $branchRequisition->branch->id == $request->branch);
        }

        $branches = Restaurant::orderBy('name')->get();

        $users = $branchRequisitions->map(function ($branchRequisition) {
            return $branchRequisition->user;
        })
            ->unique('name')
            ->sortBy('name')
            ->values();

        return view('admin.suppliers-overview.branch-requisitions', compact('model', 'title', 'breadcum', 'branchRequisitions', 'branches', 'users'));
    }
    
    protected function getUserSuppliers()
    {
        return WaSupplier::with([
            'paymentTerm', 
            'products' => fn ($query) => $query->withSum('stockMoves', 'qauntity')
        ])
            ->withSum('suppTrans', 'total_amount_inc_vat')
            // Check for HQ Procurement role using hardcoded role_id
            ->procurementRole(Auth::user())
            ->get();
    }

    public function userSuppliersSales($user_id)
    {
        $user = User::find($user_id);
        
        $stockMovesData = WaStockMove::with(['inventoryItem' => function ($query) {
            $query->with(['category' => fn ($query) => $query->select('wa_inventory_categories.id', 'category_description')])
                ->select('wa_inventory_items.id', 'wa_inventory_items.wa_inventory_category_id');
        }])
            ->whereHas('inventoryItem', function($query) use ($user) {
                $query->whereHas('suppliers', function($query) use ($user) {
                    $query->procurementRole($user);
                });
            })
            ->whereBetween('created_at', [now()->startOfYear(), now()->endOfYear()])
            ->where('document_no', 'like', '%INV%')
            ->select(
                'wa_stock_moves.wa_inventory_item_id', 
                'wa_stock_moves.total_cost', 
                'wa_stock_moves.created_at'
            )
            ->get()
            ->groupBy(function ($stockMove) {
                return $stockMove->inventoryItem->category->category_description;
            })
            ->sortKeys()
            ->map(function ($categoryGroup) {
                return $categoryGroup->groupBy(function ($stockMove) {
                    return $stockMove->created_at->format('m');
                })->map(function ($monthGroup) {
                    return $monthGroup->sum('total_cost');
                });
            });

        // For consistency with turnover sales
        $data = [];
        $loop = 0;
        foreach($stockMovesData as $category => $stockMoveData) {
            array_push($data, [$category => []]);

            foreach($stockMoveData as $month => $totalAmount) {
                array_push($data[$loop][$category], [
                    $month => round($totalAmount, 2)
                ]);
            }

            $loop++;
        }

        return response()->json($data);
    }

    public function userSuppliersCategorySales(Request $request, $user_id)
    {
        $user = User::find($user_id);

        $category = $request->query('category');
        $startDate = $request->query('start_date') ? Carbon::parse($request->query('start_date')) : now()->startOfMonth();
        $endDate = $request->query('end_date') ? Carbon::parse($request->query('end_date')) : now()->endOfMonth();

        return response()->json($this->getSuppliersSalesByCategory($category, $startDate, $endDate, $user));
    }

    public function purchaseOrderAgeing()
    {
        return WaPurchaseOrder::with('supplier', 'branch', 'department', 'user')
            ->withSum('purchaseOrderItems', 'total_cost_with_vat')
            ->whereHas('supplier', fn ($query) =>$query->procurementRole(Auth::user()))
            ->whereDoesntHave('grn')
            ->where('status', 'APPROVED')
            ->where('updated_at', '<', now()->subWeek())
            ->get()
            ->map(function ($purchaseOrder) {
                $purchaseOrder->ageing = now()->diffInDays($purchaseOrder->updated_at);

                return $purchaseOrder;
            })
            ->sortByDesc('ageing')
            ->values();
    }

    public function getBranchRequisitions()
    {
        return WaExternalRequisition::with([
            'supplier' => fn ($query) =>$query->procurementRole(Auth::user()),
            'user',
            'branch',
            'department',
            'bin',
            'store_location'
        ])
            ->withCount('externalRequisitionItems')
            ->where('status','APPROVED')
            ->where('is_hide','No')
            ->get();
    }

    public function getUnprocessedDemands($user)
    {
        $priceDemands = WaDemand::with('user', 'user.userRestaurent', 'supplier')->withCount('demandItems')
            ->whereHas('supplier', function($query) use ($user) {
                $query->procurementRole($user);
            })
            ->where('processed', false)
            ->get();

        $returnDemands = WaReturnDemand::with('user', 'user.userRestaurent', 'supplier')->withCount('returnDemandItems')
            ->whereHas('supplier', function($query) use ($user) {
                $query->procurementRole($user);
            })
            ->where('processed', false)
            ->get();

        return $priceDemands
            ->concat($returnDemands)
            ->map(function($demand) {
                $demand->ageing = $demand->created_at->diffInDays(now());
                return $demand;
            })
            ->sortByDesc(function($demand) {
                return $demand->ageing;
            })
            ->values();
    }

    public function getPendingGoodReturns($user)
    {
        $storeReturns = WaStoreReturn::with('user', 'supplier')->withCount('storeReturnItems')
            ->whereHas('supplier', function($query) use ($user) {
                $query->procurementRole($user);
            })
            ->where('approved', false)
            ->where('rejected', false)
            ->get();

        $grnReturns = ReturnedGrn::with('user', 'supplier', 'grn')
            ->whereHas('supplier', function($query) use ($user) {
                $query->procurementRole($user);
            })
            ->where('approved', false)
            ->where('rejected', false)
            ->get()
            ->unique('return_number');

        return $storeReturns->concat($grnReturns)->sortByDesc('created_at')->values();
    }

    public function getSuppliersSalesByCategory($category, $startDate, $endDate, $user)
    {
        $stockMovesData = WaStockMove::with('inventoryItem.suppliers', 'inventoryItem.category')
            ->whereHas('inventoryItem', fn ($query) => $query->whereHas('suppliers', fn ($query) => $query->procurementRole($user)))
            ->whereHas('inventoryItem.category', fn ($query) => $query->where('category_description', $category))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('document_no', 'like', '%INV%')
            ->get()
            ->groupBy(function ($stockMove) {
                return $stockMove->inventoryItem->suppliers->random()->name;
            })
            ->sortKeys()
            ->map(function ($supplierGroup) {
                return round($supplierGroup->sum('total_cost'), 2);
            })
            ->sortDesc();

        $salesData = [];

        foreach($stockMovesData as $supplierName => $amount) {
            array_push($salesData, (Object)[
                'supplier_name' => $supplierName,
                'amount' => $amount
            ]);
        }

        return collect($salesData);
    }
}
