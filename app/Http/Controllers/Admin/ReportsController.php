<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Illuminate\Support\Facades\Auth;
use Session;
use App\Model\WaInventoryItem;
use App\Model\WaStockCount;
use App\Model\WaRouteCustomer;
use App\Model\WaLocationAndStore;
use App\Model\Route;
use App\Model\WaStockMove;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SalesItemsDataExport;
use App\Exports\CommonReportDataExport;
use App\Exports\GeneralExcelExport;
use App\Exports\SupplierUserDataExport;
use App\Exports\PurchasesItemsDataExport;
use App\Exports\TransferInwardsDataExport;
use App\Exports\SlowMovingReportDataExport;
use App\Model\WaInventoryCategory;
use App\Model\NWaInventoryLocationTransfer;
use App\Model\NWaInventoryLocationTransferItem;
use App\Model\WaSupplier;
use App\Model\User;
use App\Model\WaUserSupplier;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use App\Interfaces\LocationStoreInterface;
use App\Mail\Inventory\LocationStockSummary;
use App\Mail\Inventory\LocationStockSummaryMail;
use App\Model\Restaurant;
use App\Model\WaCustomer;
use App\Model\WaInventoryAssignedItems;
use App\Services\ExcelDownloadService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session as FacadesSession;

class ReportsController extends Controller
{

    protected $model;
    protected $title;
    protected $pmodule;
    private LocationStoreInterface $locationRepository;

    public function __construct(LocationStoreInterface $locationRepository)
    {
        $this->model = 'reports';
        $this->title = 'Reports';
        $this->pmodule = 'reports';
        $this->locationRepository = $locationRepository;
    }

    public function index()
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $lists = WaStockCount::with('getAssociateItemDetail', 'getAssociateLocationDetail')->get();
            $breadcum = [$title => route('admin.stock-counts'), 'Listing' => ''];
            return view('admin.stock_counts.index', compact('lists', 'title', 'model', 'breadcum', 'pmodule', 'permission'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function suggested_order_report(Request $request)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = "maintain-items";
        $title = "Inventory";
        $model = 'maintain-items-suggested-order-report';

        $user = getLoggeduserProfile();


        if (isset($permission[$pmodule . '___suggested_order_report']) || $permission == 'superadmin') {
            $b = "";
            if ($request->branch) {
                $b = 'AND wa_stock_moves.restaurant_id = ' . $request->branch;
            }

            $items = WaInventoryItem::with(['getUnitOfMeausureDetail'])->select(
                [
                    'wa_inventory_items.*',
                    'wa_location_and_stores.location_name',
                    'wa_location_and_stores.id as location_id',
                    'wa_inventory_location_stock_status.re_order_level',
                    'wa_inventory_location_stock_status.max_stock as max_stock_f',
                    'wa_suppliers.id as supplier_id',
                    'wa_suppliers.name as supplier',
                    'wa_unit_of_measures.id as bin_location_id',
                    'wa_unit_of_measures.title as bin_location',
                    DB::RAW('(select COALESCE(SUM(wa_stock_moves.qauntity), 0) from wa_stock_moves where wa_stock_moves.wa_inventory_item_id = wa_inventory_items.id and wa_stock_moves.wa_location_and_store_id = wa_inventory_location_stock_status.wa_location_and_stores_id) as qty_inhand')
                ]
            )
                ->join('wa_inventory_location_uom', function ($e) {
                    $e->on('wa_inventory_items.id', '=', 'wa_inventory_location_uom.inventory_id');
                })
                ->join('wa_inventory_location_stock_status', function ($e) {
                    $e->on('wa_inventory_location_stock_status.wa_inventory_item_id', '=', 'wa_inventory_items.id')
                        ->whereRaw('wa_inventory_location_stock_status.wa_location_and_stores_id = wa_inventory_location_uom.location_id')
                        ->where('wa_inventory_location_stock_status.re_order_level', '>', 0);
                })
                ->join('wa_inventory_item_suppliers', function ($e) use ($request) {
                    $e->on('wa_inventory_items.id', '=', 'wa_inventory_item_suppliers.wa_inventory_item_id');
                })
                ->leftJoin('wa_location_and_stores', function ($e) {
                    $e->on('wa_inventory_location_uom.location_id', '=', 'wa_location_and_stores.id');
                })
                ->leftJoin('wa_suppliers', function ($e) {
                    $e->on('wa_inventory_item_suppliers.wa_supplier_id', '=', 'wa_suppliers.id');
                })
                ->leftJoin('wa_unit_of_measures', function ($e) {
                    $e->on('wa_inventory_location_uom.uom_id', '=', 'wa_unit_of_measures.id');
                })
                ->join('pack_sizes', 'wa_inventory_items.pack_size_id', '=', 'pack_sizes.id')
                ->where('pack_sizes.can_order', 1)
                ->where('wa_inventory_items.status', 1)
                ->orderBy('wa_inventory_items.id')
                ->having('qty_inhand', '<=', DB::RAW('wa_inventory_location_stock_status.re_order_level'))
                ->get();

            $stores = [];
            $suppliers = [];
            foreach ($items as $item) {
                $storeExists = collect($stores)->where('id', $item->location_id)->first();
                if (!$storeExists) {
                    $stores[] = ['id' => $item->location_id, 'name' => $item->location_name];
                }

                $supplierExists = collect($suppliers)->where('id', $item->supplier_id)->first();
                if (!$supplierExists) {
                    $suppliers[] = ['id' => $item->supplier_id, 'name' => $item->supplier];
                }
            }

            if ($user->role_id != 1) {
                $items = $items->filter(function ($item) use ($request) {
                    return $item->location_id == getLoggeduserProfile()->wa_location_and_store_id;
                });
            }
            if (!isset($permission[$model . '___view-all-bins']) || $permission != 'superadmin') {
                $items = $items->filter(function ($item) use ($request) {
                    return $item->bin_location_id == getLoggeduserProfile()->wa_location_and_store_id;
                });
            }

            if ($request->store) {
                $items = $items->filter(function ($item) use ($request) {
                    return $item->location_id == $request->store;
                });
            }

            if ($request->supplier) {
                $items = $items->filter(function ($item) use ($request) {
                    return $item->supplier_id == $request->supplier;
                });
            }


            if ($request->excel) {
                $data = [];
                foreach ($items as $key => $item) {
                    $data[] = [
                        'Stock ID Code' => $item->stock_id_code,
                        'Title' => $item->title,
                        'Location' => $item->location_name,
                        'Bin Location' => $item->bin_location ?? '-',
                        'Max Stock' => $item->max_stock_f,
                        'Re Order Level' => $item->re_order_level,
                        'QOH' => $item->qty_inhand,
                        'Qty To Order' => (float)$item->max_stock_f - (float)$item->qty_inhand,

                    ];
                }
                return $this->downloadExcelFile($data, 'xls', 'suggested_order_report');
            }
            $breadcum = [$title => route('reports.suggested_order_report'), 'Listing' => ''];
            $branches = $this->getRestaurantList();
            return view('admin.maintaininvetoryitems.suggested_order_report', compact('items', 'title', 'model', 'breadcum', 'pmodule', 'permission', 'suppliers', 'stores'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function suggested_order_report_for_purchases(Request $request)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = "suggested-orders";
        $title = "Suggested Orders";
        $model = 'suggested-orders';

        $b = "";
        if ($request->branch) {
            $b = 'AND wa_stock_moves.restaurant_id = ' . $request->branch;
        }

        $start = now()->subDays(30)->format('Y-m-d 00:00:00');
        $end = now()->format('Y-m-d 23:59:59');

        $soldQty = WaStockMove::query()
            ->selectRaw('wa_inventory_item_id, SUM(qauntity) as sold_qty')
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('wa_inventory_item_id')
            ->where(function ($query) {
                $query->where('document_no', 'like', 'INV-%')
                    ->orWhere('document_no', 'like', 'RTN-%');
            });

        $items = WaInventoryItem::with(['getUnitOfMeausureDetail'])->select(
            [
                'wa_inventory_items.*',
                'wa_location_and_stores.location_name',
                'wa_location_and_stores.id as location_id',
                'wa_inventory_location_stock_status.re_order_level',
                'wa_inventory_location_stock_status.max_stock as max_stock_f',
                'wa_suppliers.id as supplier_id',
                'wa_suppliers.name as supplier',
                'wa_unit_of_measures.title as bin_location',
                'wa_unit_of_measures.id as bin_location_id',
                DB::RAW('IFNULL(sold_qty,0.00) AS sold_qty'),
                DB::RAW('(select COALESCE(SUM(wa_stock_moves.qauntity), 0) from wa_stock_moves where wa_stock_moves.wa_inventory_item_id = wa_inventory_items.id and wa_stock_moves.wa_location_and_store_id = wa_inventory_location_stock_status.wa_location_and_stores_id) as qty_inhand')
            ]
        )
            ->join('wa_inventory_location_uom', function ($e) {
                $e->on('wa_inventory_items.id', '=', 'wa_inventory_location_uom.inventory_id');
            })
            ->join('wa_inventory_location_stock_status', function ($e) {
                $e->on('wa_inventory_location_stock_status.wa_inventory_item_id', '=', 'wa_inventory_items.id')
                    ->whereRaw('wa_inventory_location_stock_status.wa_location_and_stores_id = wa_inventory_location_uom.location_id')
                    ->where('wa_inventory_location_stock_status.re_order_level', '>', 0);
            })
            ->join('wa_inventory_item_suppliers', function ($e) use ($request) {
                $e->on('wa_inventory_items.id', '=', 'wa_inventory_item_suppliers.wa_inventory_item_id');
            })
            ->leftJoin('wa_location_and_stores', function ($e) {
                $e->on('wa_inventory_location_uom.location_id', '=', 'wa_location_and_stores.id');
            })
            ->leftJoin('wa_suppliers', function ($e) {
                $e->on('wa_inventory_item_suppliers.wa_supplier_id', '=', 'wa_suppliers.id');
            })
            ->leftJoin('wa_unit_of_measures', function ($e) {
                $e->on('wa_inventory_location_uom.uom_id', '=', 'wa_unit_of_measures.id');
            })
            ->leftJoinSub($soldQty, 'sold_qtys', function ($e) {
                $e->on('sold_qtys.wa_inventory_item_id', '=', 'wa_inventory_items.id');
            })
            ->orderBy('wa_inventory_items.id')
            ->havingRaw('IFNULL(qty_inhand,0) <= wa_inventory_location_stock_status.re_order_level')
            ->havingRaw('IFNULL(sold_qty,0) > 0')
            ->get();

        $stores = [];
        $suppliers = [];
        foreach ($items as $item) {
            $storeExists = collect($stores)->where('id', $item->location_id)->first();
            if (!$storeExists) {
                $stores[] = ['id' => $item->location_id, 'name' => $item->location_name];
            }

            $supplierExists = collect($suppliers)->where('id', $item->supplier_id)->first();
            if (!$supplierExists) {
                $suppliers[] = ['id' => $item->supplier_id, 'name' => $item->supplier];
            }
        }

        $user = getLoggeduserProfile();
        if ($user->role_id != 1) {
            $items = $items->filter(function ($item) use ($request) {
                return $item->location_id == getLoggeduserProfile()->wa_location_and_store_id;
            });
        }
        if (!isset($permission[$model . '___view-all-bins']) || $permission != 'superadmin') {
            $items = $items->filter(function ($item) use ($request) {
                return $item->bin_location_id == getLoggeduserProfile()->wa_location_and_store_id;
            });
        }


        if ($request->store) {
            $items = $items->filter(function ($item) use ($request) {
                return $item->location_id == $request->store;
            });
        }

        if ($request->supplier) {
            $items = $items->filter(function ($item) use ($request) {
                return $item->supplier_id == $request->supplier;
            });
        }


        if ($request->excel) {
            $data = [];
            foreach ($items as $key => $item) {
                $data[] = [
                    'Stock ID Code' => $item->stock_id_code,
                    'Title' => $item->title,
                    'Location' => $item->location_name,
                    'Bin Location' => $item->bin_location ?? '-',
                    'Max Stock' => $item->max_stock_f,
                    'Re Order Level' => $item->re_order_level,
                    'QOH' => $item->qty_inhand,
                    'Qty To Order' => (float)$item->max_stock_f - (float)$item->qty_inhand,
                ];
            }
            return $this->downloadExcelFile($data, 'xls', 'suggested_order_report');
        }
        $breadcum = [$title => route('reports.suggested_order_report'), 'Listing' => ''];
        $branches = $this->getRestaurantList();
        return view('admin.externalrequisition.suggested_orders', compact('items', 'title', 'model', 'breadcum', 'pmodule', 'permission', 'suppliers', 'stores'));
    }

    public function items_negetive_listing(Request $request)
    {
        // dd($request->all());
        $permission = $this->mypermissionsforAModule();
        $pmodule = "maintain-items";
        $title = "Inventory";
        $model = 'items_negetive_listing';
        if (isset($permission[$pmodule . '___negetive_stock_report']) || $permission == 'superadmin') {
            $b = "";
            if ($request->branch) {
                $b = 'AND wa_stock_moves.wa_location_and_store_id = ' . $request->branch;
            }
            $items = WaInventoryItem::with(['pack_size', 'getInventoryCategoryDetail'])->select(
                [
                    'wa_inventory_items.*',
                    DB::RAW('(select sum(qauntity) from wa_stock_moves where wa_stock_moves.wa_inventory_item_id = wa_inventory_items.id) as qty_inhand')
                ]
            )
                ->orderBy('wa_inventory_items.id')
                ->having('qty_inhand', '<', 0)
                ->get();

            $breadcum = [$title => route('reports.items_negetive_listing'), 'Listing' => ''];
            // $branches = $this->getRestaurantList();
            $branches = WaLocationAndStore::pluck('location_name as name', 'id');
            return view('admin.maintaininvetoryitems.items_negetive_listing', compact('items', 'title', 'model', 'breadcum', 'pmodule', 'permission', 'branches'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function inventory_location_stock_summary(Request $request)
    {
        
        $permission = $this->mypermissionsforAModule();
        $pmodule = "maintain-items";
        $title = "Inventory";
        $model = 'inventory_location_stock_summary';

        if (isset($permission['inventory-reports___inventory-location-stock-report']) || $permission == 'superadmin') {
            if ($request->has('categorize')) {
                return $this->categorizedInventorySummary($request);
            }

            $locations = WaLocationAndStore::where('is_physical_store', '1')
                ->where('location_name', '<>', 'THIKA')->get();

            $type = $request->input('type');
            $title = $type == 'values' ? "BRANCH STOCKS VALUE REPORT" : "BRANCH STOCKS REPORT";

            $items = $this->locationRepository->getStockBalance();

            if ($request->has('recipient')) {
                $description = '';
                $supplier = WaSupplier::find(request()->supplier);
                $pdf = Pdf::loadView('admin.maintaininvetoryitems.reports.full', compact('items', 'title', 'type', 'description', 'model', 'pmodule', 'locations', 'supplier'));
                $pdf->setPaper('a4', 'landscape')
                    ->setWarnings(false)
                    ->set_option("enable_php", true);

                $mail = new LocationStockSummaryMail($pdf->output(), $request->message, $supplier);

                $recipients = collect(explode(',', $request->recipient));
                $cc = collect(explode(',', $request->cc));
                Mail::to($recipients->unique())
                    ->cc($cc)
                    ->send($mail);

                Session::flash('success', 'Email sent successfully');

                return redirect()->route('reports.inventory_location_stock_summary');
            }

            if (request()->has('print')) {
                $description = '';
                if (request()->category) {
                    $description .= "Category: " . WaInventoryCategory::find(request()->category)->name;
                }
                if (request()->supplier) {
                    $description .= "Supplier: " .($supplier =  WaSupplier::find(request()->supplier)->name);
                }

                $items = $items->get();


                $pdf = \Pdf::loadView('admin.maintaininvetoryitems.reports.full', compact('items', 'title', 'type', 'description', 'model', 'pmodule', 'locations'));
                $pdf->setPaper('a4', 'landscape')
                ->setWarnings(false);
                $pdf->render();
                $canvas = $pdf->getDomPDF()->getCanvas();
                $canvas->page_script(function ($pageNumber, $pageCount, $canvas, $fontMetrics) {
                    $text = "Page $pageNumber / $pageCount";
                    $marginRight = 7;
                    $marginBottom = 5;
                    $fontSize = 10;
                    $font = $fontMetrics->getFont('Helvetica');
                    $textWidth = $fontMetrics->getTextWidth($text, $font, $fontSize);
                    $xPosition = $canvas->get_width() - $textWidth - $marginRight;
                    $yPosition = $canvas->get_height() - ($canvas->get_height() - $marginBottom);
                    $canvas->text($xPosition, $yPosition, $text, $font, $fontSize);
                });

            
                return $pdf->download(str($supplier)->upper()->replace(' ', '_') . "_STOCK_REPORT.pdf");
            }
            $breadcum = [$title => route('reports.inventory_location_stock_summary'), 'Listing' => ''];
            $categories = \App\Model\WaInventoryCategory::get();
            $suppliers = WaSupplier::get();

            return view('admin.maintaininvetoryitems.inventory_location_stock_summary', compact('items', 'title', 'type', 'title', 'model', 'breadcum', 'pmodule', 'permission', 'categories', 'locations', 'suppliers'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function inventory_location_as_at(Request $request)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = "inventory-location-as-at";
        $title = "Inventory";
        $model = 'inventory_location_as_at';

        if (isset($permission['inventory-reports___inventory-location-as-at']) || $permission == 'superadmin') {
            if ($request->has('categorize')) {
                return $this->categorizedInventorySummary($request);
            }

            $locations = WaLocationAndStore::where('is_physical_store', '1')
                ->where('location_name', '<>', 'THIKA')->get();

            $type = $request->input('type');
            $start_date = $request->start_date;
            $title = $type == 'values' ? "BRANCH STOCKS VALUE REPORT" : "BRANCH STOCKS REPORT";

            $items = $this->locationRepository->getStockBalanceAsAt();

            if (request()->has('print')) {
                $description = '';
                if (request()->category) {
                    $description .= "Category: " . WaInventoryCategory::find(request()->category)->name;
                }
                if (request()->supplier) {
                    $description .= "Supplier: " . WaSupplier::find(request()->supplier)->name;
                }

                if (request()->supplier) {
                    $description .= "Supplier: " . WaSupplier::find(request()->supplier)->name;
                }


                $items = $items->get();

                $pdf = \Pdf::loadView('admin.maintaininvetoryitems.partials.as_at', compact('items', 'title', 'type', 'description', 'model', 'pmodule', 'locations', 'start_date'));

                return $pdf->setPaper('a4', 'landscape')
                    ->setWarnings(false)
                    ->download('inventory_location_as_at_' . $start_date . '.pdf');
            }
            $breadcum = [$title => route('reports.inventory_location_as_at'), 'Listing' => ''];
            $categories = \App\Model\WaInventoryCategory::get();
            $suppliers = WaSupplier::get();

            return view('admin.maintaininvetoryitems.inventory_location_as_at', compact('items', 'title', 'type', 'title', 'model', 'breadcum', 'pmodule', 'permission', 'categories', 'locations', 'suppliers', 'start_date'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }




    public function items_data_sales()
    {
        if (!can('items-data-sales', 'inventory-reports')) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }
        $this->model = 'items-data-sales';
        $this->title = 'Item Data Sales Report';
        $this->pmodule = "items-data-sales";
        $this->breadcum = [$this->title => route('reports.items-data-sales'), 'Listing' => ''];
        $from = request()->filled('from') ? request()->from . ' 00:00:00' : now()->startOfMonth()->format('Y-m-d 00:00:00');
        $to = request()->filled('to') ? request()->to . ' 23:59:59' : now()->format('Y-m-d 23:59:59');

        $select = [
            'wa_inventory_items.id',
            'wa_inventory_items.stock_id_code',
            'wa_inventory_items.title'
        ];

        $locations = WaLocationAndStore::get();
        $suppliers = WaSupplier::get();
        if (request()->supplier) {
            $supplierexcel = WaSupplier::find(request()->supplier)->name;
        } else {
            $supplierexcel = '';
        }

        foreach ($locations as $location) {
            $select[] = DB::RAW("IFNULL(sales_$location->id.total_sales_$location->id,0) + IFNULL(ROUND(packs_$location->id.total_pack_sales_$location->id,1), 0) AS total_item_sales_$location->id");
        }


        $query = WaInventoryItem::query()
            ->select($select)
            ->with([
                'suppliers'
            ]);

        foreach ($locations as $location) {

            $salesSub = WaStockMove::query()
                ->select([
                    'wa_inventory_item_id',
                    DB::raw('ABS(SUM(qauntity)) as total_sales_' . $location->id)
                ])
                ->where('wa_location_and_store_id', $location->id)
                ->where(function ($query) {
                    $query->where('document_no', 'like', 'INV-%')
                        ->orWhere('document_no', 'like', 'RTN-%');
                })
                ->whereBetween('created_at', [$from, $to])
                ->groupBy('stock_id_code');

            $smallPacksSub = WaStockMove::query()
                ->select([
                    'items.wa_inventory_item_id',
                    DB::raw('ABS(SUM(qauntity) / conversion_factor) as total_pack_sales_' . $location->id)
                ])
                ->leftJoin('wa_inventory_assigned_items as items', 'items.destination_item_id', '=', 'wa_stock_moves.wa_inventory_item_id')
                ->where('wa_location_and_store_id', $location->id)
                ->where(function ($query) {
                    $query->where('document_no', 'like', 'INV-%')
                        ->orWhere('document_no', 'like', 'RTN-%');
                })
                ->whereBetween('wa_stock_moves.created_at', [$from, $to])
                ->groupBy('stock_id_code');

            $query->leftJoinSub($salesSub, "sales_$location->id", "sales_$location->id.wa_inventory_item_id", '=', 'wa_inventory_items.id');
            $query->leftJoinSub($smallPacksSub, "packs_$location->id", "packs_$location->id.wa_inventory_item_id", '=', 'wa_inventory_items.id');
        }

        $query->whereIn('wa_inventory_items.pack_size_id', [8, 7, 3, 5, 2, 4, 13]);

        if (request()->filled('supplier')) {
            $query->whereHas('suppliers', function ($query) {
                $query->where('wa_suppliers.id',  request()->supplier);
            });
        }

        if (request()->action == 'excel') {
            $view = view(
                'admin.maintaininvetoryitems.data_sales_pdf',
                [
                    'model' => $this->model,
                    'title' => $this->title,
                    'pmodule' => $this->pmodule,
                    'locations' => $locations,
                    'suppliers' => $suppliers,
                    'breadcum' => $this->breadcum,
                    'data' => $query->get(),
                    'from' =>  $from = Carbon::parse($from)->toDateString(),
                    'to' => $to = Carbon::parse($to)->toDateString(),
                    'supplierexcel' => $supplierexcel,
                ]
            );
            return Excel::download(new SalesItemsDataExport($view), 'Sales Items Data Report' . '(' . $from . ' to ' . $to . ').xlsx');
        }

        if (request()->wantsJson()) {
            return DataTables::eloquent($query)
                ->addColumn('suppliers', function ($inventory) {
                    return implode(',', $inventory->suppliers->pluck('name')->toArray());
                })
                ->addColumn('total_sales', function ($inventory) use ($locations) {
                    $total = 0;
                    foreach ($locations as $location) {
                        $attribute = "total_item_sales_$location->id";
                        $total += $inventory->$attribute;
                    }
                    return $total;
                })
                ->toJson();
        }

        return view('admin.maintaininvetoryitems.items_data_sales', [
            'model' => $this->model,
            'title' => $this->title,
            'pmodule' => $this->pmodule,
            'locations' => $locations,
            'suppliers' => $suppliers,
            'breadcum' => $this->breadcum,
        ]);
    }


    public function downloadExcelFile($data, $type, $file_name)
    {

        $export = new SalesItemsDataExport(collect($data));
        return Excel::download($export, 'suggested-order.xls');
    }

    protected function categorizedInventorySummary(Request $request)
    {
        $pmodule = "maintain-items";
        $title = "Inventory";
        $model = 'inventory_location_stock_summary';

        $locations = WaLocationAndStore::where('is_physical_store', '1')
            ->where('location_name', '<>', 'THIKA')->get();

        $invCategories = WaInventoryCategory::query()
            ->with([
                'getinventoryitems' => function ($query) use ($request) {
                    $query->with('getAllFromStockMoves')
                        ->whereHas('inventory_item_suppliers', function ($e) use ($request) {
                            if ($request->supplier) {
                                $e->where('wa_supplier_id', $request->supplier);
                            }
                        });
                }
            ])
            ->whereHas('getinventoryitems', function ($query) use ($request) {
                $query->whereHas('inventory_item_suppliers', function ($e) use ($request) {
                    if ($request->supplier) {
                        $e->where('wa_supplier_id', $request->supplier);
                    }
                });
            })
            ->where(function ($e) use ($request) {
                if ($request->category) {
                    $e->where('id', $request->category);
                }
            });


        $type = $request->input('type');
        $title = $type == 'values' ? "BRANCH STOCKS VALUE REPORT" : "BRANCH STOCKS REPORT";

        if ($request->has('print')) {
            $description = '';
            if ($request->category) {
                $description .= "Category: " . WaInventoryCategory::find($request->category)->name;
            }
            if ($request->supplier) {
                $description .= "Supplier: " . WaSupplier::find($request->supplier)->name;
            }

            $items = $invCategories->get();
            $items->each(function ($category) use ($locations) {
                $category->getinventoryitems->each(function ($item) use ($locations) {
                    foreach ($locations as $location) {
                        $item->setAttribute("qty_inhand_$location->id", $item->getAllFromStockMoves->where('wa_location_and_store_id', $location->id)->sum('qauntity'));
                    }
                });
            });

            $pdf = \Pdf::loadView('admin.maintaininvetoryitems.reports.categorized', compact('items', 'description', 'title', 'type', 'locations'));

            return $pdf->setPaper('a4', 'landscape')
                ->setWarnings(false)
                ->download('stock_summary_' . date('d_m_Y_H_i_s') . '.pdf');
        }

        $items = $invCategories->paginate(10);
        $items->each(function ($category) use ($locations) {
            $category->getinventoryitems->each(function ($item) use ($locations) {
                foreach ($locations as $location) {
                    $item->setAttribute("qty_inhand_$location->id", $item->getAllFromStockMoves->where('wa_location_and_store_id', $location->id)->sum('qauntity'));
                }
            });
        });

        $breadcum = [$title => route('reports.inventory_location_stock_summary'), 'Listing' => ''];
        $categories = \App\Model\WaInventoryCategory::get();
        $categorized = true;

        return view('admin.maintaininvetoryitems.inventory_location_stock_summary', compact('invCategories', 'categorized', 'items', 'title', 'type', 'categories', 'locations', 'model', 'pmodule'));
    }


    public function items_data_purchases()
    {
        // if (!can('items-data-purchases', 'inventory-reports')) {
        if (!can('promotion-items', 'inventory-reports')) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }
        $this->model = 'items-data-purchases';
        $this->title = 'Item Data Purchases Report';
        $this->pmodule = 'items-data-purchases';
        $this->breadcum = [$this->title => route('reports.items_data_purchase_report'), 'Listing' => ''];
        $from = request()->filled('from') ? request()->from . ' 00:00:00' : now()->startOfMonth()->format('Y-m-d 00:00:00');
        $to = request()->filled('to') ? request()->to . ' 23:59:59' : now()->format('Y-m-d 23:59:59');

        $select = [
            'wa_inventory_items.id',
            'wa_inventory_items.stock_id_code',
            'wa_inventory_items.title'
        ];

        $locations = WaLocationAndStore::get();
        $suppliers = WaSupplier::get();

        if (request()->supplier) {

            $supplierexcel = WaSupplier::find(request()->supplier)->name;
        } else {
            $supplierexcel = '';
        }

        foreach ($locations as $location) {
            $select[] = DB::RAW("IFNULL(sales_$location->id.total_sales_$location->id,0)  AS total_item_sales_$location->id");
        }

        $totalSalesExpression = '(';
        $first = true;
        foreach ($locations as $location) {
            if (!$first) {
                $totalSalesExpression .= ' + ';
            }
            $totalSalesExpression .= "IFNULL(sales_$location->id.total_sales_$location->id, 0)";
            $first = false;
        }
        $totalSalesExpression .= ') AS total_sales_all_locations';

        $select[] = DB::raw($totalSalesExpression);


        $query = WaInventoryItem::query()
            ->select($select)
            ->with([
                'suppliers'
            ]);

        foreach ($locations as $location) {

            $purchasesSub = WaStockMove::query()
                ->select([
                    'wa_inventory_item_id',
                    DB::raw('ABS(SUM(qauntity)) as total_sales_' . $location->id)
                ])
                ->where('wa_location_and_store_id', $location->id)
                ->where('document_no', 'like', 'GRN-%')
                ->whereBetween('created_at', [$from, $to])
                ->groupBy('wa_inventory_item_id');


            $query->leftJoinSub($purchasesSub, "sales_$location->id", "sales_$location->id.wa_inventory_item_id", '=', 'wa_inventory_items.id');
        }

        $query->whereIn('wa_inventory_items.pack_size_id', [8, 7, 3, 5, 2, 4, 13]);

        if (request()->filled('supplier')) {
            $query->whereHas('suppliers', function ($query) {
                $query->where('wa_suppliers.id',  request()->supplier);
            });
        }

        if (request()->wantsJson()) {
            return DataTables::eloquent($query)
                ->addColumn('suppliers', function ($inventory) {
                    return implode(',', $inventory->suppliers->pluck('name')->toArray());
                })
                ->toJson();
        }

        if (request()->action == 'excel') {
            $view = view(
                'admin.maintaininvetoryitems.data_purchasepdf',
                [
                    'model' => $this->model,
                    'title' => $this->title,
                    'pmodule' => $this->pmodule,
                    'locations' => $locations,
                    'suppliers' => $suppliers,
                    'breadcum' => $this->breadcum,
                    'data' => $query->get(),
                    'from' =>  $from = Carbon::parse($from)->toDateString(),
                    'to' => $to = Carbon::parse($to)->toDateString(),
                    'supplierexcel' => $supplierexcel,
                ]
            );
            return Excel::download(new PurchasesItemsDataExport($view), 'Purchase Items Data Report' . '(' . $from . ' to ' . $to . ').xlsx');
        }

        return view('admin.maintaininvetoryitems.items_data_purchases', [
            'model' => $this->model,
            'title' => $this->title,
            'pmodule' => $this->pmodule,
            'locations' => $locations,
            'suppliers' => $suppliers,
            'breadcum' => $this->breadcum,

            'data' => $query->get(),
            'from' => $from,
            'to' => $to,
            'supplierexcel' => $supplierexcel,
        ]);
    }


    public function transferInwardsReport(Request $request)
    {
        /*dd($request->all());*/

        if (!can('transfer-inwards-report', 'inventory-reports')) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }
        $this->model = 'transfer-inwards-report';
        $this->title = 'Transfer Inwards Outwards Report';
        $this->pmodule = 'transfer-inwards-report';
        $this->breadcum = [$this->title => route('reports.transfer_inwards_report'), 'Listing' => ''];

        $start_date = $request->start_date ?  $request->start_date : date('Y-m-d');
        $end_date = $request->end_date ?  $request->end_date : date('Y-m-d', strtotime('+ 1 days'));

        $this->transfers = [];

        $transfer = NWaInventoryLocationTransfer::with(['getRelatedItem'])
            ->join('users', 'n_wa_inventory_location_transfers.user_id', '=', 'users.id')
            ->join('wa_location_and_stores', 'n_wa_inventory_location_transfers.from_store_location_id', 'wa_location_and_stores.id')
            ->join('wa_location_and_stores as to', 'n_wa_inventory_location_transfers.to_store_location_id', 'to.id')
            ->select(
                'n_wa_inventory_location_transfers.id',
                'n_wa_inventory_location_transfers.transfer_no',
                'n_wa_inventory_location_transfers.updated_at',
                'n_wa_inventory_location_transfers.manual_doc_number',
                'users.name',
                'wa_location_and_stores.location_name',
                'to.location_name as too',
            );
        $transfer->when($start_date && $end_date, function ($query) use ($start_date, $end_date) {
            return $query->whereBetween('n_wa_inventory_location_transfers.updated_at', [$start_date, $end_date]);
        });

        if ($request->type == 'Outwards') {
            $transfer->where('from_store_location_id', $request->store);
        }

        if ($request->type == 'Inwards') {
            $transfer->where('to_store_location_id', $request->store);
        }


        $transfers = $transfer->orderBy('n_wa_inventory_location_transfers.created_at', 'Desc')->get();



        $stores = WaLocationAndStore::get();

        if ($request->manage == 'excel') {

            $view = view(
                'admin.maintaininvetoryitems.transfer_inwards_reportpdf',
                [
                    'model' => $this->model,
                    'title' => $this->title,
                    'pmodule' => $this->pmodule,
                    'breadcum' => $this->breadcum,
                    'transfers' => $transfers,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'stores' => $stores,
                    'items' => $transfers,
                    'start_date' =>  $start_date = Carbon::parse($start_date)->toDateString(),
                    'end_date' => $end_date = Carbon::parse($end_date)->toDateString(),

                ]
            );
            return Excel::download(new TransferInwardsDataExport($view), $this->title . '.xlsx');
        }

        if ($request->manage == 'pdf') {

            $pdf = \Pdf::loadView(
                'admin.maintaininvetoryitems.transfer_inwards_outwards_report_pdf',
                [
                    'model' => $this->model,
                    'title' => $this->title,
                    'pmodule' => $this->pmodule,
                    'breadcum' => $this->breadcum,
                    'transfers' => $transfers,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'stores' => $stores,
                    'items' => $transfers,
                    'start_date' =>  $start_date = Carbon::parse($start_date)->toDateString(),
                    'end_date' => $end_date = Carbon::parse($end_date)->toDateString(),

                ]
            );

            return $pdf->setPaper('a4', 'landscape')
                ->setWarnings(false)
                ->download($this->title . date('_d_m_Y_H_i_s') . '.pdf');
        }

        if ($request->manage == 'summary') {

            $pdf = \Pdf::loadView(
                'admin.maintaininvetoryitems.transfer_inwards_report_summary',
                [
                    'model' => $this->model,
                    'title' => $this->title,
                    'pmodule' => $this->pmodule,
                    'breadcum' => $this->breadcum,
                    'transfers' => $transfers,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'stores' => $stores,
                    'items' => $transfers,
                    'start_date' =>  $start_date = Carbon::parse($start_date)->toDateString(),
                    'end_date' => $end_date = Carbon::parse($end_date)->toDateString(),

                ]
            );

            return $pdf->setPaper('a4', 'landscape')
                ->setWarnings(false)
                ->download($this->title . date('_d_m_Y_H_i_s') . '.pdf');
        }


        return view('admin.maintaininvetoryitems.transfer_inwards_report', [
            'model' => $this->model,
            'title' => $this->title,
            'pmodule' => $this->pmodule,
            'breadcum' => $this->breadcum,
            'transfers' => $transfers,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'stores' => $stores,
            'items' => $transfers,

        ]);
    }

    public function slowMovingItems(Request $request)
    {

        if (!can('slow-moving-items-report', 'inventory-reports')) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }
        $this->model = 'slow-moving-items-report';
        $this->title = 'Slow Moving Items Report';
        $this->pmodule = 'slow-moving-items-report';
        $this->breadcum = [$this->title => route('reports.slow_moving_items_report'), 'Listing' => ''];

        $start_date = $request->start_date ?  $request->start_date : date('Y-m-d');
        $end_date = $request->end_date ?  $request->end_date : date('Y-m-d');
        $sold = $request->sold;
        $selectedID = $request->id;
        $puser = $request->user;

        $this->movings = [];

        $salesSub = WaStockMove::query()
            ->select([
                'wa_inventory_item_id',
                DB::raw('ABS(SUM(qauntity)) as total_sales')
            ])
            ->where(function ($query) {
                $query->where('document_no', 'like', 'INV-%')
                    ->orWhere('document_no', 'like', 'RTN-%');
            })
            ->groupBy('stock_id_code');

        $qohSub = WaStockMove::query()
            ->select([
                'wa_inventory_item_id',
                DB::raw('SUM(qauntity) as quantity')
            ])
            ->groupBy('stock_id_code');

        $lastSoldSUb = WaStockMove::query()
            ->select([
                'wa_inventory_item_id',
                DB::raw('Max(created_at) as last_sold')
            ])
            ->where('document_no', 'like', 'INV-%')
            ->groupBy('stock_id_code');

        $lastPurchaseSUb = WaStockMove::query()
            ->select([
                'wa_inventory_item_id',
                DB::raw('Max(created_at) as last_purchase')
            ])
            ->where('document_no', 'like', 'GRN-%')
            ->groupBy('stock_id_code');

        $procumenetUser = WaStockMove::query()
            ->select([
                'wa_inventory_item_id',
                DB::raw('Max(user_id) as procurement_user')
            ])
            ->where('document_no', 'like', 'GRN-%')
            ->groupBy('stock_id_code');


        //dd($procumenetUser);


        $query = WaInventoryItem::query()
            ->leftJoinSub($qohSub, 'qoh', 'qoh.wa_inventory_item_id', '=', 'wa_inventory_items.id')
            ->leftJoinSub($lastPurchaseSUb, 'last_purchase', 'last_purchase.wa_inventory_item_id', '=', 'wa_inventory_items.id')
            ->leftJoinSub($salesSub, 'total_sales', 'total_sales.wa_inventory_item_id', '=', 'wa_inventory_items.id')
            ->leftJoinSub($lastSoldSUb, 'last_sold', 'last_sold.wa_inventory_item_id', '=', 'wa_inventory_items.id')
            ->leftJoinSub($procumenetUser, 'procurement_user', 'procurement_user.wa_inventory_item_id', '=', 'wa_inventory_items.id')
            ->leftjoin('wa_inventory_item_suppliers','wa_inventory_items.id','=','wa_inventory_item_suppliers.wa_inventory_item_id')
            ->leftjoin('wa_user_suppliers','wa_inventory_item_suppliers.wa_supplier_id','=','wa_user_suppliers.wa_supplier_id')
            ->leftJoin('wa_inventory_categories', 'wa_inventory_items.wa_inventory_category_id', '=', 'wa_inventory_categories.id')
            ->leftjoin('users','wa_user_suppliers.user_id','=','users.id') 
            ->select([
                'wa_inventory_items.id',
                'wa_inventory_items.stock_id_code',
                'wa_inventory_items.title',
                'wa_inventory_items.wa_inventory_category_id',
                'last_purchase.last_purchase as last_purchase',
                'procurement_user.procurement_user as procurement_user',
                'users.name as supplierUsers',
                'wa_inventory_categories.category_description',
                DB::raw('IFNULL(qoh.quantity, 0) as qoh'),
                DB::raw('IFNULL(total_sales.total_sales, 0) as total_sales'),
                DB::raw('IFNULL(last_sold.last_sold, 0) as last_sold')
            ])->with([
                'suppliers'
            ])->where('wa_inventory_items.status','1')
            ->when($start_date && $end_date, function ($query) use ($start_date, $end_date) {
                return $query->whereBetween('last_sold', [$start_date, $end_date]);
            })->when($selectedID, function ($query) use ($selectedID) {
                return $query->where('wa_inventory_items.id', $selectedID);
            })->when($puser, function ($query) use ($puser) {
                return $query->where('wa_user_suppliers.user_id', $puser);
            })->when($sold, function ($query) use ($sold) {
                return $query->where('total_sales', '<=', $sold);
            })
            ->orderBy('wa_inventory_categories.category_description')
            ->orderBy('last_sold', 'Desc')
            ->groupBy('wa_inventory_items.stock_id_code');

        // $movings = $query->get();
        // $inventoryItems = WaInventoryItem::get();
        $users = User::get();        

        if ($request->manage == 'excel') {
            $view = view(
                'admin.maintaininvetoryitems.slow_moving_items_pdf',
                [
                    'model' => $this->model,
                    'title' => $this->title,
                    'pmodule' => $this->pmodule,
                    'breadcum' => $this->breadcum,
                    'movings' => $query->get(),
                    'start_date' =>  $start_date = Carbon::parse($start_date)->toDateString(),
                    'end_date' => $end_date = Carbon::parse($end_date)->toDateString(),
                    'sold' => $sold,
                ]
            );
            return Excel::download(new CommonReportDataExport($view), $this->title . '.xlsx');
        }
        if ($request->manage == 'pdf') {

            $pdf = \Pdf::loadView(
                'admin.maintaininvetoryitems.slow_moving_items_pdf_download',
                [
                    'model' => $this->model,
                    'title' => $this->title,
                    'pmodule' => $this->pmodule,
                    'breadcum' => $this->breadcum,
                    'movings' => $query->get(),
                    'start_date' =>  $start_date = Carbon::parse($start_date)->toDateString(),
                    'end_date' => $end_date = Carbon::parse($end_date)->toDateString(),
                    'sold' => $sold,

                ]
            );

            return $pdf->setPaper('a4', 'landscape')
                ->setWarnings(false)
                ->download($this->title . date('_d_m_Y_H_i_s') . '.pdf');
        }
        
        return view('admin.maintaininvetoryitems.slow_moving_items', [
            'model' => $this->model,
            'title' => $this->title,
            'pmodule' => $this->pmodule,
            'breadcum' => $this->breadcum,
            'movings' => $query->get(),
            'start_date' => $start_date,
            'end_date' => $end_date,
            // 'inventoryItems' => $inventoryItems,
            'sold' => $sold,
            'users' => $users,
        ]);
    }
    public function route_performance_report(Request $request)
    {
        if ($request->ajax() || $request->action == 'excel') {
            try {
                $supplier = WaSupplier::find($request->supplier);
                $dateFilter = null;
                $customers  = WaCustomer::join('wa_route_customers', function ($e) {
                    $e->on('wa_route_customers.customer_id', '=', 'wa_customers.id');
                })
                    ->join('routes', function ($e) {
                        $e->on('routes.id', '=', 'wa_route_customers.route_id');
                    })->where(function ($e) use ($request) {
                        if ($request->branch) {
                            $e->where('routes.restaurant_id', $request->branch);
                        }
                    })
                    ->orderBy('wa_customers.customer_name')->pluck('wa_customers.customer_name', 'wa_customers.id')->toArray();
                if ($request->from && $request->to) {
                    $fromDate = \Carbon\Carbon::parse($request->from)->startOfDay();
                    $toDate = \Carbon\Carbon::parse($request->to)->endOfDay();
                    $dateFilter = "and wa_internal_requisition_items.created_at between '$fromDate' and '$toDate'";
                }
                $select = [
                    'wa_inventory_items.*',
                    'asi.wa_inventory_item_id as mother_item_id',
                    'asi.destination_item_id as destination_item_id',
                    'asi.conversion_factor as conversion_factor'
                ];
                foreach ($customers as $key => $customer) {
                    $select[] = DB::raw("(select sum(wa_internal_requisition_items.quantity) from wa_internal_requisition_items 
                left join wa_internal_requisitions on wa_internal_requisitions.id = wa_internal_requisition_items.wa_internal_requisition_id
                where wa_internal_requisitions.customer_id = " . $key . " and 
                (wa_internal_requisition_items.wa_inventory_item_id = child.id )
                $dateFilter ) as qty_" . $key);
                    $select[] = DB::RAW('(SELECT SUM(wa_inventory_location_transfer_item_returns.received_quantity)
                FROM wa_inventory_location_transfers 
                LEFT JOIN wa_inventory_location_transfer_items ON wa_inventory_location_transfer_items.wa_inventory_location_transfer_id = wa_inventory_location_transfers.id
                LEFT JOIN wa_inventory_location_transfer_item_returns ON wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_item_id = wa_inventory_location_transfer_items.id
                WHERE wa_inventory_location_transfers.customer_id = ' . $key . '  AND 
                (wa_inventory_location_transfer_items.wa_inventory_item_id = child.id)
                
                AND (DATE(wa_inventory_location_transfer_item_returns.created_at)  BETWEEN "' . $request->from . '" AND "' . $request->to . '")) as returns_' . $key);

                    //mother items
                    $select[] = DB::raw("(select sum(wa_internal_requisition_items.quantity) from wa_internal_requisition_items 
                left join wa_internal_requisitions on wa_internal_requisitions.id = wa_internal_requisition_items.wa_internal_requisition_id
                where wa_internal_requisitions.customer_id = " . $key . " and 
                (wa_internal_requisition_items.wa_inventory_item_id = wa_inventory_items.id)
                $dateFilter ) as mother_qty_" . $key);

                    $select[] = DB::RAW('(SELECT SUM(wa_inventory_location_transfer_item_returns.received_quantity)
                FROM wa_inventory_location_transfers 
                LEFT JOIN wa_inventory_location_transfer_items ON wa_inventory_location_transfer_items.wa_inventory_location_transfer_id = wa_inventory_location_transfers.id
                LEFT JOIN wa_inventory_location_transfer_item_returns ON wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_item_id = wa_inventory_location_transfer_items.id
                WHERE wa_inventory_location_transfers.customer_id = ' . $key . '  AND   
                ( wa_inventory_location_transfer_items.wa_inventory_item_id = wa_inventory_items.id)
                AND (DATE(wa_inventory_location_transfer_item_returns.created_at)  BETWEEN "' . $request->from . '" AND "' . $request->to . '")) as mother_returns_' . $key);
                }

                $data = WaInventoryItem::select($select)
                    ->leftjoin('wa_inventory_assigned_items as asi', function ($e) {
                        $e->on('asi.wa_inventory_item_id', '=', 'wa_inventory_items.id');
                    })
                    ->leftjoin('wa_inventory_items as child', function ($e) {
                        $e->on('asi.destination_item_id', '=', 'child.id');
                    })
                    ->whereIn('wa_inventory_items.pack_size_id', [8, 7, 3, 5, 2, 4, 13])
                    ->whereHas('inventory_item_suppliers', function ($e) use ($supplier) {
                        if ($supplier) {
                            $e->where('wa_supplier_id', $supplier->id);
                        }
                    })
                    ->groupBy('wa_inventory_items.id')
                    ->orderBy('wa_inventory_items.id');

                $data = $data->get();
                // dd($data);

                // $groupedData = $data->groupBy('mother_item_id');

                // foreach ($groupedData as $motherItemId => $items) {
                //     $motherItem = $data->where('id', $motherItemId)->first();

                //     if ($motherItem && $motherItem->conversion_factor != 0) {
                //         foreach ($customers as $key => $customer) {
                //             $qtyKey = 'qty_' . $key;
                //             $returnsKey = 'returns_' . $key;

                //             $newQty = $items->sum("$qtyKey") / $motherItem->conversion_factor;
                //             $newReturns = $items->sum("$returnsKey") / $motherItem->conversion_factor;

                //             $motherItem->$qtyKey += number_format($newQty, 2);
                //             $motherItem->$returnsKey += number_format($newReturns, 2);

                //         }
                //     }
                // }
                if ($request->action == 'excel') {
                    if (request()->action == 'excel') {
                        $view = view(
                            'admin.maintaininvetoryitems.item_route_performance_report_pdf',
                            [
                                'model' => $this->model,
                                'title' => $this->title,
                                'pmodule' => $this->pmodule,
                                'customers' => $customers,
                                'data' => $data,
                                'from' =>  $fromDate,
                                'to' => $toDate,
                                'supplierexcel' => WaSupplier::find($request->supplier)->name,
                            ]
                        );
                        return Excel::download(new CommonReportDataExport($view), 'Items Route Performance Report' . '(' . $fromDate . ' to ' . $toDate . ').xlsx');
                    }
                } else {
                    return response()->json([
                        'result' => 1,
                        'data' => $data,
                        'message' => 'Ok',
                        'customers' => $customers
                    ]);
                }
            } catch (\Throwable $th) {
                return response()->json([
                    'result' => -1,
                    'data' => [],
                    'message' => $th->getMessage(),
                ]);
            }
        }
        $branches = Restaurant::get();
        return view('admin.reports.route_performance_report', [
            'model' => 'item-sales-route-performance-report',
            'title' => 'Item Sales Route Performance',
            'pmodule' => $this->pmodule,
            'branches' => $branches,
            'breadcum' => ['Item Sales Route Performance' => route('reports.route_performance_report'), 'Listing' => ''],
        ]);
    }


    public function supplierUserReport(Request $request)
    {

        if (!can('supplier-user-report', 'inventory-reports')) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }
        $this->model = 'supplier-user-report';
        $this->title = 'Suppliers User Report';
        $this->pmodule = 'supplier-user-report';
        $this->breadcum = [$this->title => route('reports.supplier_user_report'), 'Listing' => ''];

        $start_date = $request->start_date ?  $request->start_date : date('Y-m-d');
        $end_date = $request->end_date ?  $request->end_date : date('Y-m-d', strtotime('+ 1 days'));
        $userx = $request->username;



        $supplier = WaUserSupplier::join('users', 'wa_user_suppliers.user_id', '=', 'users.id')->join('wa_suppliers', 'wa_user_suppliers.wa_supplier_id', '=', 'wa_suppliers.id')->select('users.name', 'wa_suppliers.name as suppname')->get()->groupBy('name');


        $supplier->when($start_date && $end_date, function ($query) use ($start_date, $end_date) {
            return $query->whereBetween('wa_user_suppliers.created_at', [$start_date, $end_date]);
        });

        $supplier->when($userx, function ($query) use ($userx) {
            return $query->where('users.name', $userx);
        });


        $suppliers = $supplier;


        if ($request->manage == 'excel') {
            $view = view(
                'admin.maintaininvetoryitems.supplier_user_report_pdf',
                [
                    'title' => $this->title,
                    'suppliers' => $suppliers,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'start_date' =>  $start_date = Carbon::parse($start_date)->toDateString(),
                    'end_date' => $end_date = Carbon::parse($end_date)->toDateString(),

                ]
            );
            return Excel::download(new SupplierUserDataExport($view), $this->title . '.xlsx');
        }

        if ($request->manage == 'pdf') {

            $pdf = \Pdf::loadView(
                'admin.maintaininvetoryitems.supplier_user_report_pdf',
                [
                    'title' => $this->title,
                    'suppliers' => $suppliers,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'start_date' =>  $start_date = Carbon::parse($start_date)->toDateString(),
                    'end_date' => $end_date = Carbon::parse($end_date)->toDateString(),

                ]
            );

            return $pdf->setPaper('a4', 'landscape')
                ->setWarnings(false)
                ->download($this->title . date('_d_m_Y_H_i_s') . '.pdf');
        }

        return view('admin.maintaininvetoryitems.supplier_user_report', [
            'model' => $this->model,
            'title' => $this->title,
            'pmodule' => $this->pmodule,
            'breadcum' => $this->breadcum,
            'suppliers' => $suppliers,
            'start_date' => $start_date,
            'end_date' => $end_date,
        ]);
    }

     public function transferInwardDownload($transfer_no)
    {
        $list =   NWaInventoryLocationTransfer::where('transfer_no', $transfer_no)->with(['getRelatedItem.getInventoryItemDetail' => function ($query) {
            $query->orderBy('stock_id_code', 'DESC');
        }])->first();


        $itemsdata =   NWaInventoryLocationTransferItem::where('wa_inventory_location_transfer_id', $list->id)->with(['getInventoryItemDetail' => function ($query) {
            $query->orderBy('stock_id_code', 'DESC');
        }])->get();


        $pdf = PDF::loadView('admin.maintaininvetoryitems.transfer_download_pdf', compact('list', 'itemsdata'));
        return $pdf->download('transfer_' . date('Y_m_d_h_i_s') . '.pdf');
    }
}
