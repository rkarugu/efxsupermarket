<?php

namespace App\Http\Controllers\Admin;

use App\DiscountBand;
use App\Exports\GeneralExcelExport;
use App\Exports\ItemsWithDiscountsExport;
use App\Http\Controllers\Controller;
use App\Model\WaInventoryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;

class ItemDiscountController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;
    protected  $basePath;

    public function __construct(Request $request)
    {
        $this->model = 'maintan-items';
        $this->title = 'Maintain items';
        $this->pmodule = 'maintain-items';
        $this->basePath = 'admin.maintaininvetoryitems.discount';
    }
    public function index($itemId)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $basePath = $this->basePath;

        $inventoryItem = WaInventoryItem::find($itemId);
        $discountBands = DiscountBand::where('inventory_item_id', $inventoryItem->id)->get();
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {

            $breadcum = [$title => route('maintain-items.index'), 'Listing' => ''];
            return view($basePath . '.index', compact('title', 'model', 'breadcum', 'pmodule', 'permission', 'inventoryItem', 'discountBands'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }
    public function create($itemId)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $basePath = $this->basePath;
        $breadcum = [$title => route('item-centre.show', $itemId), 'Create' => ''];

        $inventoryItem = WaInventoryItem::find($itemId);
        if (isset($permission[$this->pmodule . '___manage-discount']) || $permission == 'superadmin') {

            return view($basePath . '.create', compact('title', 'model', 'breadcum', 'inventoryItem'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }
    public function store(Request $request, $itemId)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $basePath = $this->basePath;
        $breadcum = [$title => route('item-centre.show', $itemId), 'Create' => ''];

        try {
            $inventoryItem = WaInventoryItem::find($itemId);
            $discountBand = new DiscountBand();
            $discountBand->inventory_item_id = $inventoryItem->id;
            $discountBand->discount_amount = $request->discount_amount;
            $discountBand->from_quantity = $request->from_quantity;
            $discountBand->to_quantity = $request->to_quantity;
            $discountBand->status = 'APPROVED';
            $discountBand->initiated_by = getLoggeduserProfile()->id;

            $discountBand->save();
            return redirect()->route("item-centre.show", $itemId)
                ->with('success', 'Discount Band Created successfully');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors(['errors' => $e->getMessage()]);
        }
    }

    public function edit($discountBandId)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $basePath = $this->basePath;
        $discountBand = DiscountBand::find($discountBandId);
        $breadcum = [$title => route('item-centre.show', $discountBand->inventory_item_id), 'Create' => ''];

        if (isset($permission[$this->pmodule . '___manage-discount']) || $permission == 'superadmin') {

            return view($basePath . '.edit', compact('title', 'model', 'breadcum', 'discountBand'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function update(Request $request, $discountBandId)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $basePath = $this->basePath;
        $discountBand = DiscountBand::find($discountBandId);
        $breadcum = [$title => route('item-centre.show', $discountBand->inventory_item_id), 'Create' => ''];
        try {
            $discountBand->discount_amount = $request->discount_amount;
            $discountBand->from_quantity = $request->from_quantity;
            $discountBand->to_quantity = $request->to_quantity;


            $discountBand->save();
            return redirect()->route("item-centre.show", $discountBand->inventory_item_id)->with('success', 'Discount Band Updated successfully');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors(['errors' => $e->getMessage()]);
        }
    }

    public function approve($discountBandId)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $basePath = $this->basePath;
        $discountBand = DiscountBand::find($discountBandId);
        $breadcum = [$title => route('item-centre.show', $discountBand->inventory_item_id), 'Create' => ''];
        try {
            $discountBand->status = 'APPROVED';
            $discountBand->approved_by = getLoggeduserProfile()->id;

            $discountBand->save();
            return redirect()->route("item-centre.show", $discountBand->inventory_item_id)->with('success', 'Discount Band Approved successfully');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors(['errors' => $e->getMessage()]);
        }
    }

    public function delete($discountBandId)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $basePath = $this->basePath;
        $discountBand = DiscountBand::find($discountBandId);
        $itemId = $discountBand->inventory_item_id;
        $breadcum = [$title => route('item-centre.show', $itemId), 'Create' => ''];
        try {
            $discountBand->delete();

            return redirect()->route("item-centre.show", $itemId)->with('success', 'Discount Band Deleted successfully');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors(['errors' => $e->getMessage()]);
        }
    }

    public function itemsWithDiscountsReport(Request $request)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = 'discount-items-report';
        $basePath = $this->basePath;
        $breadcum = [$title => route('items-with-discounts-reports'), 'Listing' => ''];
        $inventoryItems = WaInventoryItem::all();
        $query = "
        SELECT
            wa_inventory_items.stock_id_code as stock_id_code,
            wa_inventory_items.title as title,
            wa_inventory_items.selling_price as price,
            discount_bands.from_quantity,
            discount_bands.to_quantity,
            discount_bands.discount_amount,
            users.name as user_name
        FROM
            discount_bands
        LEFT JOIN
            wa_inventory_items ON discount_bands.inventory_item_id = wa_inventory_items.id  
        LEFT JOIN
            users ON discount_bands.initiated_by = users.id        
        ";
        if ($request->item) {
            $query .= " WHERE wa_inventory_items.id = $request->item ";
        }
        $data  = DB::select($query);

        if ($request->type && $request->type == 'Download') {
            $export = new ItemsWithDiscountsExport(collect($data));
            return Excel::download($export, 'ItemDiscounts.xlsx');
        }

        return view('admin.maintaininvetoryitems.discount.items_with_discount_report', compact('title', 'model', 'breadcum', 'data', 'inventoryItems'));
    }
    public function discountSalesReport(Request $request)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = 'discount-sales-report';
        $basePath = $this->basePath;
        $breadcum = [$title => route('discount-sales-report'), 'Listing' => ''];
        $inventoryItems = WaInventoryItem::all();
        $fromQuery  = $toQuery = null;
        if ($request->from && $request->to) {
            $fromDate = \Carbon\Carbon::parse($request->from)->toDateString();
            $toDate = \Carbon\Carbon::parse($request->to)->toDateString();
            $fromQuery = $request->from ? "AND wa_internal_requisition_items.created_at >= '$fromDate' " : null;
            $toQuery = $request->to ? "AND wa_internal_requisition_items.created_at <= '$toDate' " : null;
        }

        $query = DB::table('wa_inventory_items')
            ->select(
                'wa_inventory_items.stock_id_code as stock_id_code',
                'wa_inventory_items.title as title',
                'wa_inventory_items.selling_price as price',
                DB::raw("(SELECT SUM(quantity) FROM wa_internal_requisition_items WHERE wa_internal_requisition_items.wa_inventory_item_id = wa_inventory_items.id AND wa_internal_requisition_items.discount > 0  $fromQuery $toQuery) as sold_quantity"),
                DB::raw("(SELECT SUM(total_cost_with_vat) FROM wa_internal_requisition_items WHERE wa_internal_requisition_items.wa_inventory_item_id = wa_inventory_items.id AND wa_internal_requisition_items.discount > 0 $fromQuery $toQuery) as total_cost"),
                DB::raw("(SELECT SUM(discount) FROM wa_internal_requisition_items WHERE wa_internal_requisition_items.wa_inventory_item_id = wa_inventory_items.id AND wa_internal_requisition_items.discount > 0 $fromQuery $toQuery) as total_discount"),

            )
            ->leftJoin('wa_internal_requisition_items', 'wa_internal_requisition_items.wa_inventory_item_id', '=', 'wa_inventory_items.id')
            ->where('wa_internal_requisition_items.discount', '>', 0);


        if ($request->item) {
            $query->where('wa_inventory_items.id', '=', $request->item);
        }


        $data = $query->groupBy('stock_id_code')->get();

        if ($request->type && $request->type == 'Download') {
            $export = new GeneralExcelExport(collect($data), ['STOCK ID CODE', 'TITLE', 'PRICE', 'SOLD QTY', 'TOTAL SALES', 'TOTAL DISCOUNT']);
            return Excel::download($export, 'discount_sales_summary.xlsx');
        }

        return view('admin.maintaininvetoryitems.discount.discount_sales_summary_report', compact('title', 'model', 'breadcum', 'data', 'inventoryItems'));
    }
}
