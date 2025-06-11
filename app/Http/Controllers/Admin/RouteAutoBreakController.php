<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\WaInventoryItem;
use App\Model\WaLocationAndStore;
use App\Model\WaUnitOfMeasure;
use App\Models\RouteAutoBreak;
use App\Models\StockBreakDispatch;
use App\Models\StockBreakDispatchItem;
use Barryvdh\DomPDF\PDF as DomPDFPDF;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use PDF;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class RouteAutoBreakController extends Controller
{
    protected string $model = 'stock-auto-breaks';

    public function index(): View|RedirectResponse
    {
        if (!can('view', $this->model)) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }

        $model = $this->model;
        $title = "Stock Auto Breaks";
        $breadcum = [$title => route('stock-auto-breaks.index'), 'Listing' => ''];

        $user = getLoggeduserProfile();
        $records = DB::table('route_auto_breaks')->where('route_auto_breaks.status', 'pending');
        if ($user->role_id == 152) {
            $records = $records->where('route_auto_breaks.child_bin_id', $user->wa_unit_of_measures_id);
        }
        $records = $records->select(
            'route_auto_breaks.child_item_id',
            'route_auto_breaks.child_pack_size',
            'route_auto_breaks.mother_pack_size',
            'child.stock_id_code as child_code',
            'child.title as child_name',
            'mother.stock_id_code as mother_code',
            'mother.title as mother_name',
            'child_bin.title as child_bin',
            'mother_bin.title as mother_bin',
            DB::raw('COUNT(child_item_id) as item_count'),
            DB::raw('SUM(child_quantity) as child_qty'),
            DB::raw('SUM(mother_quantity) as mother_qty'),
        )
            ->join('wa_inventory_items as child', 'route_auto_breaks.child_item_id', '=', 'child.id')
            ->join('wa_inventory_items as mother', 'route_auto_breaks.mother_item_id', '=', 'mother.id')
            ->join('wa_unit_of_measures as child_bin', 'route_auto_breaks.child_bin_id', '=', 'child_bin.id')
            ->join('wa_unit_of_measures as mother_bin', 'route_auto_breaks.mother_bin_id', '=', 'mother_bin.id')
            ->groupBy('route_auto_breaks.child_item_id')
            ->orderBy('route_auto_breaks.child_bin_id')
            ->get();

        return view('admin.stock_auto_breaks.index', compact('title', 'model', 'breadcum', 'records', 'user'));
    }


    public function showAutoBreakLines($child_code): View|RedirectResponse
    {
        if (!can('view', $this->model)) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }

        $model = $this->model;
        $title = "$child_code - Auto Break Lines";
        $breadcum = ['Auto Breaks' => route('stock-auto-breaks.index'), $child_code => '', 'Lines' => ''];

        $item = DB::table('wa_inventory_items')->select('id', 'stock_id_code', 'title')->where('stock_id_code', $child_code)->first();
        $records = DB::table('route_auto_breaks')->where('route_auto_breaks.status', 'pending')->where('route_auto_breaks.child_item_id', $item->id);
        $records = $records->select(
            'route_auto_breaks.stb_number',
            'route_auto_breaks.created_at',
            'route_auto_breaks.child_item_id',
            'route_auto_breaks.child_quantity',
            'route_auto_breaks.child_pack_size',
            'route_auto_breaks.mother_quantity',
            'route_auto_breaks.mother_pack_size',
            'child.stock_id_code as child_code',
            'child.title as child_name',
            'mother.stock_id_code as mother_code',
            'mother.title as mother_name',
            'child_bin.title as child_bin',
            'mother_bin.title as mother_bin',
        )
            ->join('wa_inventory_items as child', 'route_auto_breaks.child_item_id', '=', 'child.id')
            ->join('wa_inventory_items as mother', 'route_auto_breaks.mother_item_id', '=', 'mother.id')
            ->join('wa_unit_of_measures as child_bin', 'route_auto_breaks.child_bin_id', '=', 'child_bin.id')
            ->join('wa_unit_of_measures as mother_bin', 'route_auto_breaks.mother_bin_id', '=', 'mother_bin.id')
            ->get();


        return view('admin.stock_auto_breaks.auto_break_lines', compact('title', 'model', 'breadcum', 'records'));
    }

    public function initiateDispatch(Request $request): RedirectResponse
    {
        DB::beginTransaction();
        try {
            $user = getLoggeduserProfile();

            // Create dispatches per mother bin
            $motherBins = RouteAutoBreak::where('child_bin_id', $user->wa_unit_of_measures_id)->where('status', 'pending')->distinct('mother_bin_id')->pluck('mother_bin_id')->toArray();
            foreach ($motherBins as $motherBin) {
                if ($motherBin) {
                    $dispatch = StockBreakDispatch::create([
                        'child_bin_id' => $user->wa_unit_of_measures_id,
                        'mother_bin_id' => $motherBin,
                        'initiated_by' => $user->id
                    ]);

                    $dispatchItems = RouteAutoBreak::where('child_bin_id', $user->wa_unit_of_measures_id)->where('status', 'pending')->where('mother_bin_id', $motherBin)->get();
                    foreach ($dispatchItems as $dispatchItem) {
                        StockBreakDispatchItem::create([
                            'dispatch_id' => $dispatch->id,
                            'child_item_id' => $dispatchItem->child_item_id,
                            'child_quantity' => $dispatchItem->child_quantity,
                            'child_pack_size' => $dispatchItem->child_pack_size,
                            'mother_item_id' => $dispatchItem->mother_item_id,
                            'mother_quantity' => $dispatchItem->mother_quantity,
                            'mother_pack_size' => $dispatchItem->mother_pack_size,
                        ]);

                        $dispatchItem->update(['status' => 'processed']);
                    }
                }
            }

            DB::commit();
            Session::flash('success', 'Stock break initiated successfully. The items are ready for dispatch at the mother bin.');
            return redirect()->route('stock-auto-breaks.index');
        } catch (\Throwable $e) {
            DB::rollBack();
            Session::flash('danger', $e->getMessage());
            return redirect()->route('stock-auto-breaks.index');
        }
    }

    public function showPendingDispatches(): View|RedirectResponse
    {

        $model = 'stock-break-dispatch';
        $title = "Pending Stock Break Dispatches";
        $breadcum = [$title => route('stock-auto-breaks.dispatch.list'), 'Pending' => ''];

        $user = getLoggeduserProfile();
        $permissions  = $this->mypermissionsforAModule();
        $records = StockBreakDispatch::where('stock_break_dispatches.dispatched', false);
        if ($permissions != 'superadmin') {
            $records = $records->where(function ($query) use ($user) {
                $query->where('stock_break_dispatches.mother_bin_id', $user->wa_unit_of_measures_id)->orWhere('stock_break_dispatches.initiated_by', $user->id);
            });
        }

        $records = $records->select(
            'stock_break_dispatches.id',
            'stock_break_dispatches.created_at',
            'child_bin.title as child_bin',
            'mother_bin.title as mother_bin',
            'initiator.name as initiator',
            DB::raw("(select COUNT(*) from stock_break_dispatch_items where stock_break_dispatch_items.dispatch_id = stock_break_dispatches.id) as item_count"),
        )
            ->join('wa_unit_of_measures as child_bin', 'stock_break_dispatches.child_bin_id', '=', 'child_bin.id')
            ->join('wa_unit_of_measures as mother_bin', 'stock_break_dispatches.mother_bin_id', '=', 'mother_bin.id')
            ->join('users as initiator', 'stock_break_dispatches.initiated_by', '=', 'initiator.id')
            ->orderBy('stock_break_dispatches.created_at', 'DESC')
            ->get();

        return view('admin.stock_auto_breaks.pending', compact('title', 'model', 'breadcum', 'records'));
    }

    public function showPendingDispatchLines($id): View|RedirectResponse
    {
        $dispatch = StockBreakDispatch::with(['items' => function ($query) {
            $query->select(
                'stock_break_dispatch_items.*',
                'child.stock_id_code as child_code',
                'child.title as child',
                'mother.stock_id_code as mother_code',
                'mother.title as mother',
                DB::raw('COUNT(child_item_id) as item_count'),
                DB::raw('SUM(child_quantity) as child_qty'),
                DB::raw('SUM(mother_quantity) as mother_qty'),
            )
                ->join('wa_inventory_items as child', 'stock_break_dispatch_items.child_item_id', '=', 'child.id')
                ->join('wa_inventory_items as mother', 'stock_break_dispatch_items.mother_item_id', '=', 'mother.id')->groupBy('stock_break_dispatch_items.child_item_id');
        }])->find($id);

        $model = 'stock-break-dispatch';
        $title = "Stock Break Dispatch Lines";
        $breadcum = [$title => route('stock-auto-breaks.dispatch.list'), $dispatch->dispatch_number => '', 'lines' => ''];

        return view('admin.stock_auto_breaks.pending_lines', compact('title', 'model', 'breadcum', 'dispatch'));
    }

    public function processDispatch(Request $request): RedirectResponse
    {
        DB::beginTransaction();
        try {
            $user = getLoggeduserProfile();
            $dispatch = StockBreakDispatch::find($request->dispatch_id);

            $dispatch->update([
                'dispatched_by' => $user->id,
                'dispatched' => true,
                'dispatch_time' => Carbon::now()
            ]);

            DB::commit();
            Session::flash('success', 'Stock break dispatched successfully.');
            return redirect()->route('stock-auto-breaks.dispatch.list');
        } catch (\Throwable $e) {
            DB::rollBack();
            Session::flash('danger', $e->getMessage());
            return redirect()->back();
        }
    }

    public function showDispatchedDispatches(): View|RedirectResponse
    {
        $model = 'stock-break-dispatch';
        $title = "Dispatched Stock Break Dispatches";
        $breadcum = [$title => route('stock-auto-breaks.dispatch.list'), 'Dispatched' => ''];

        $user = getLoggeduserProfile();
        $records = StockBreakDispatch::where('stock_break_dispatches.dispatched', true)->where('received', false);
        if ($user->role_id == 152) {
            $records = $records->where(function ($query) use ($user) {
                $query->where('stock_break_dispatches.child_bin_id', $user->wa_unit_of_measures_id)->orWhere('stock_break_dispatches.dispatched_by', $user->id);
            });
        }

        $records = $records->select(
            'stock_break_dispatches.id',
            'stock_break_dispatches.created_at',
            'child_bin.title as child_bin',
            'mother_bin.title as mother_bin',
            'initiator.name as initiator',
            DB::raw("(select COUNT(*) from stock_break_dispatch_items where stock_break_dispatch_items.dispatch_id = stock_break_dispatches.id) as item_count"),
        )
            ->join('wa_unit_of_measures as child_bin', 'stock_break_dispatches.child_bin_id', '=', 'child_bin.id')
            ->join('wa_unit_of_measures as mother_bin', 'stock_break_dispatches.mother_bin_id', '=', 'mother_bin.id')
            ->join('users as initiator', 'stock_break_dispatches.initiated_by', '=', 'initiator.id')
            ->orderBy('stock_break_dispatches.created_at', 'DESC')
            ->get();

        return view('admin.stock_auto_breaks.dispatched', compact('title', 'model', 'breadcum', 'records'));
    }


    public function showDispatchedLines($id): View|RedirectResponse
    {
        $dispatch = StockBreakDispatch::with(['items' => function ($query) {
            $query->select(
                'stock_break_dispatch_items.*',
                'child.stock_id_code as child_code',
                'child.title as child',
                'mother.stock_id_code as mother_code',
                'mother.title as mother',
                DB::raw('COUNT(child_item_id) as item_count'),
                DB::raw('SUM(child_quantity) as child_qty'),
                DB::raw('SUM(mother_quantity) as mother_qty'),
            )
                ->join('wa_inventory_items as child', 'stock_break_dispatch_items.child_item_id', '=', 'child.id')
                ->join('wa_inventory_items as mother', 'stock_break_dispatch_items.mother_item_id', '=', 'mother.id')->groupBy('stock_break_dispatch_items.child_item_id');
        }])->find($id);

        $model = 'stock-break-dispatch';
        $title = "Stock Break Dispatched Lines";
        $breadcum = [$title => route('stock-auto-breaks.dispatch.list'), $dispatch->dispatch_number => '', 'lines' => ''];

        return view('admin.stock_auto_breaks.dispatched_lines', compact('title', 'model', 'breadcum', 'dispatch'));
    }


    public function receive(Request $request): RedirectResponse
    {
        DB::beginTransaction();
        try {
            $user = getLoggeduserProfile();
            $dispatch = StockBreakDispatch::find($request->dispatch_id);

            $dispatch->update([
                'received_by' => $user->id,
                'received' => true,
                'receive_time' => Carbon::now()
            ]);

            DB::commit();
            Session::flash('success', 'Stock break received successfully.');
            return redirect()->route('stock-auto-breaks.dispatched.list');
        } catch (\Throwable $e) {
            DB::rollBack();
            Session::flash('danger', $e->getMessage());
            return redirect()->back();
        }
    }

    public function completed(): View|RedirectResponse
    {
        if (!can('dispatch', $this->model)) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }

        $model = 'stock-break-completed';
        $title = "Stock Break Completed";
        $breadcum = [$title => route('stock-auto-breaks.dispatched.list'), 'Listing' => ''];

        $user = getLoggeduserProfile();
        $records = DB::table('stock_break_dispatches')
            ->where('stock_break_dispatches.received', true);
        if ($user->role_id == 152) {
            $records = $records->where('stock_break_dispatches.child_bin_id', $user->wa_unit_of_measures_id)
                ->orWhere('stock_break_dispatches.mother_bin_id', $user->wa_unit_of_measures_id);
        }


        $records = $records->select(
            'stock_break_dispatches.id',
            'stock_break_dispatches.dispatched_by',
            'stock_break_dispatches.receive_time',
            'stock_break_dispatches.received_by',
            'received_users.name as received_by_name',
            'child_bin.title as child_bin',
            'mother_bin.title as mother_bin',
            'initiator.name as initiator',
            'users.name as dispatcheby',
            DB::raw("(select COUNT(*) from stock_break_dispatch_items where stock_break_dispatch_items.dispatch_id = stock_break_dispatches.id) as item_count"),
        )
            ->join('wa_unit_of_measures as child_bin', 'stock_break_dispatches.child_bin_id', '=', 'child_bin.id')
            ->join('wa_unit_of_measures as mother_bin', 'stock_break_dispatches.mother_bin_id', '=', 'mother_bin.id')
            ->leftJoin('users as initiator', 'stock_break_dispatches.initiated_by', '=', 'initiator.id')
            ->join('users as received_users', 'stock_break_dispatches.received_by', '=', 'received_users.id')
            ->leftJoin('users', 'stock_break_dispatches.dispatched_by', '=', 'users.id')
            ->orderBy('stock_break_dispatches.receive_time', 'DESC')
            ->get();

        return view('admin.stock_auto_breaks.received', compact('title', 'model', 'breadcum', 'records'));
    }

    public function printToPdf($id)
    {

        $user = getLoggeduserProfile();
        $records = DB::table('stock_break_dispatch_items')
            ->join('wa_inventory_items', 'stock_break_dispatch_items.child_item_id', '=', 'wa_inventory_items.id')
            ->select('stock_break_dispatch_items.*', 'wa_inventory_items.title')
            ->where('dispatch_id', $id)
            ->get();


        if (!$records) {
            Session::flash('warning', 'No Records Found');
            return redirect()->back();
        }

        $pdf = PDF::loadView('admin.stock_auto_breaks.print', compact('records', 'id'));
        return $pdf->download('stock_auto_break_' . date('Y_m_d_h_i_s') . '.pdf');
    }
    public function summary(Request $request)
    {
        if (!can('summary', 'stock-breaking')) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }
        $permission = $this->mypermissionsforAModule();
        if ($permission != 'superadmin') {
            $request->branch = $request->user()->wa_location_and_store_id;
        }

        $user = Auth::user();
        $branches = WaLocationAndStore::all();
        $uoms = WaUnitOfMeasure::all();
        if ($request->branch) {
            $uoms = WaUnitOfMeasure::select(
                'wa_unit_of_measures.id as id',
                'wa_unit_of_measures.title as title',
            )
                ->leftJoin('wa_location_store_uom', 'wa_location_store_uom.uom_id', '=', 'wa_unit_of_measures.id')
                ->where('wa_location_store_uom.location_id', $request->branch)
                ->get();
        }
        $branch = $request->branch ? WaLocationAndStore::find($request->branch)->location_name : '';
        $model = 'stock-breaking-summary';
        $title = "Stock Auto Breaks";
        $breadcum = [$title => route('stock-auto-breaks.index'), 'Listing' => ''];
        $user = getLoggeduserProfile();
        $start = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->startOfDay();
        $end = $request->start_date ? Carbon::parse($request->start_date)->endOfDay() : Carbon::now()->endOfDay();
        $records = DB::table('route_auto_breaks')->where('route_auto_breaks.status', 'processed');
        $records = $records->select(
            DB::raw('DATE(route_auto_breaks.created_at) as created_at'),
            'route_auto_breaks.stb_number as break_number',
            'route_auto_breaks.child_item_id',
            'route_auto_breaks.child_pack_size',
            'child.stock_id_code as child_code',
            'child.title as child_name',
            'child_bin.title as child_bin',
            'route_auto_breaks.mother_quantity',
            'route_auto_breaks.mother_pack_size',
            'mother.stock_id_code as mother_code',
            'route_auto_breaks.child_quantity',
            'mother.title as mother_name',
            'mother_bin.title as mother_bin',
            'wa_internal_requisitions.requisition_no as ref',
            DB::raw('SUM(route_auto_breaks.mother_quantity) as total_mother_quantity'),
            DB::raw('SUM(route_auto_breaks.child_quantity) as total_child_quantity')
        )
            ->join('wa_inventory_items as child', 'route_auto_breaks.child_item_id', '=', 'child.id')
            ->join('wa_inventory_items as mother', 'route_auto_breaks.mother_item_id', '=', 'mother.id')
            ->join('wa_unit_of_measures as child_bin', 'route_auto_breaks.child_bin_id', '=', 'child_bin.id')
            ->join('wa_unit_of_measures as mother_bin', 'route_auto_breaks.mother_bin_id', '=', 'mother_bin.id')
            ->join('wa_internal_requisitions', 'wa_internal_requisitions.id', '=', 'route_auto_breaks.invoice_id')
            ->whereBetween('route_auto_breaks.created_at', [$start, $end]);

        if ($request->uom) {
            $records = $records->where('mother_bin.id', $request->uom);
        }
        if ($request->branch) {

            $records = $records->where('wa_internal_requisitions.wa_location_and_store_id', $request->branch);
        }
        if ($user->role_id == 152) {
            $records = $records->where('mother_bin.id', $user->wa_unit_of_measures_id)
                ->orWhere('child_bin.id', $user->wa_unit_of_measures_id);
        }
        $records = $records->groupBy(DB::raw('DATE(route_auto_breaks.created_at)'), 'mother.stock_id_code')->get();
        $manualBreaks = DB::table('wa_stock_breaking_items')
            ->select(
                DB::raw('DATE(wa_stock_breaking_items.created_at) as created_at'),
                'wa_stock_breaking.breaking_code as break_number',
                'child.stock_id_code as child_code',
                'child.title as child_name',
                'wa_stock_breaking_items.destination_qty as child_quantity',
                'child_bin.title as child_bin_location',
                'mother.stock_id_code as mother_code',
                'mother.title as mother_name',
                'wa_stock_breaking_items.source_qty as mother_quantity',
                'mother_bin.title as mother_bin_location',
            )
            ->join('wa_stock_breaking', 'wa_stock_breaking.id', 'wa_stock_breaking_items.wa_stock_breaking_id')
            ->join('wa_inventory_items as child', 'child.id', 'wa_stock_breaking_items.destination_item_id')
            ->leftJoin('wa_inventory_location_uom  as child_location', 'child.id', 'child_location.inventory_id')
            ->leftJoin('wa_unit_of_measures as child_bin', 'child_bin.id', 'child_location.uom_id')
            ->join('wa_inventory_items as mother', 'mother.id', 'wa_stock_breaking_items.source_item_id')
            ->leftJoin('wa_inventory_location_uom as mother_location', 'mother.id', 'mother_location.inventory_id')
            ->leftJoin('wa_unit_of_measures as mother_bin', 'mother_bin.id', 'mother_location.uom_id')
            ->leftJoin('users', 'users.id', 'wa_stock_breaking.user_id')
            ->where('wa_stock_breaking.status', 'PROCESSED')
            ->whereBetween('wa_stock_breaking_items.created_at', [$start, $end])
            ->whereColumn('child_location.location_id', 'users.wa_location_and_store_id')
            ->whereColumn('mother_location.location_id', 'users.wa_location_and_store_id');
        if ($request->branch) {
            $manualBreaks = $manualBreaks->where('users.wa_location_and_store_id', $request->branch);
        }
        if ($request->uom) {
            $manualBreaks = $manualBreaks->where('mother_bin.id', $request->uom);
        }
        if ($user->role_id == 152) {
            $manualBreaks = $manualBreaks->where('mother_bin.id', $user->wa_unit_of_measures_id)
                ->orWhere('child_bin.id', $user->wa_unit_of_measures_id);
        }
        $manualBreaks  =  $manualBreaks->groupBy(DB::raw('DATE(wa_stock_breaking_items.created_at)'), 'mother.stock_id_code')->get();
        if ($request->type  &&  $request->type == 'Download') {
            $records = $records->sortBy('mother_bin');
            $manualBreaks = $manualBreaks->sortBy('mother_bin_location');
            $pdf = PDF::loadView('admin.stock_auto_breaks.summary_pdf', compact('records', 'user', 'branches', 'uoms', 'manualBreaks', 'start', 'end', 'branch'));
            return $pdf->download('stock_auto_breaks_summary_' . date('Y_m_d_h_i_s') . '.pdf');
        }
        return view('admin.stock_auto_breaks.summary', compact('title', 'model', 'breadcum', 'records', 'user', 'branches', 'uoms', 'manualBreaks', 'permission'));
    }
}
