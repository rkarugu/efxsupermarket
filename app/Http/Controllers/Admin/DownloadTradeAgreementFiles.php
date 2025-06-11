<?php

namespace App\Http\Controllers\Admin;

use App\Exports\TradeAgreement\TradeAgreementExport;
use App\Http\Controllers\Controller;
use App\Model\User;
use App\Model\WaInventoryItem;
use App\Model\WaUserSupplier;
use App\Models\TradeAgreement;
use App\Models\WaSupplierDistributor;
use App\Services\ExcelDownloadService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class DownloadTradeAgreementFiles extends Controller
{

    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct()
    {
        $this->model = 'trade-agreement';
        $this->title = 'Trade Agreement';
        $this->pmodule = 'trade-agreement';
    }

    public function downloadItems(Request $request)
    {

        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        if (!isset($permission[$pmodule . '___view']) && $permission != 'superadmin') {
            throw new \Exception("Don't have enough permission to view this page");
        }
        $trade = TradeAgreement::with(['supplier'])->findOrFail($request->trade_agreement_id);
        $parent = WaSupplierDistributor::where('distributors', $trade->supplier->id)->first()?->supplier_id;

        $query = WaInventoryItem::select(
            [
                'wa_inventory_items.id',
                'wa_inventory_items.stock_id_code',
                'wa_inventory_items.title',
                'wa_inventory_items.price_list_cost',
                'wa_inventory_items.standard_cost',
                'wa_inventory_items.selling_price',
                'wa_inventory_items.margin_type',
                'wa_inventory_items.percentage_margin',
                'wa_inventory_items.created_at',
                "trade_product_offers.created_at as offer_date",
                "pack_sizes.title as pack_size",
            ]
        )
            ->leftJoin('pack_sizes',  'pack_sizes.id', 'wa_inventory_items.pack_size_id')
            ->join('trade_product_offers', function ($e) use ($trade) {
                $e->on('trade_product_offers.inventory_item_id', '=', 'wa_inventory_items.id')
                    ->where('trade_product_offers.trade_agreements_id', $trade->id);
            })
            ->where('pack_sizes.can_order', 1)
            ->where('wa_inventory_items.status', 1)
            ->whereHas('inventory_item_suppliers', function ($e) use ($trade, $parent) {
                $e->whereIn('wa_supplier_id', [$trade->supplier->id, $parent]);
            });

        $inventoryitems = $query->get();

        if ($request->intent == 'Download Pdf') {
            $pdf = \Pdf::loadView('pdfs.trade_agreements_items', [
                'inventoryitems' => $inventoryitems,
                'trade' => $trade
            ]);

            return $pdf->setPaper('a4', 'portrait')
                ->setWarnings(false)
                ->setOption('isPhpEnabled', true)
                ->download('TRADE-AGREEMENT-ITEMS' . '.pdf');
        } else if ($request->intent == 'Download Excel') {
            $excelData = [];
            foreach ($inventoryitems as $item) {
                $excelData[] = [
                    'item_code' => $item->stock_id_code ?? '',
                    'description' => $item->title ?? '',
                    'pack_size' => $item->pack_size ?? '',
                    'price_list_cost' => number_format($item->price_list_cost, 2) ?? '0.00',
                    'standard_cost' => number_format($item->standard_cost, 2) ?? '0.00',
                    'selling_price' => number_format($item->selling_price, 2) ?? '0.00',
                    'margin_type' => $item->margin_type == 0 ? 'Value' : 'Percentage',
                    'percentage_margin' => number_format($item->percentage_margin, 2) ?? '0.00',
                    'trade_agreement_date' => $item->created_at ? \Carbon\Carbon::parse($item->created_at)->format('d/m/Y') : '',
                ];
            }

            $headings = [
                'ITEM CODE',
                'DESCRIPTION',
                'PACK SIZE',
                'PRICE LIST COST',
                'STANDARD COST',
                'SELLING PRICE',
                'MARGIN TYPE',
                '% MARGIN',
                'TRADE AGREEMENT DATE'
            ];

            $filename = "TRADE-AGREEMENT-ITEMS";
            return ExcelDownloadService::download($filename, collect($excelData), $headings);
        } else {
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }

    public function downloadTradeAgreements(Request $request)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;

        if (!isset($permission[$pmodule . '___view']) && $permission != 'superadmin') {
            throw new \Exception("Don't have enough permission to view this page");
        }

        $user = getLoggeduserProfile();
        $assigned = WaUserSupplier::where('user_id', $user->id)->pluck('wa_supplier_id')->toArray();

        $trades = TradeAgreement::with(['supplier.users'])
            ->whereHas('supplier', function ($e) use ($assigned, $permission, $pmodule) {
                if (!isset($permission[$pmodule . '___view-all']) && $permission != 'superadmin') {
                    $e->whereIn('id', $assigned);
                }
            })
            ->where('status', ($request->status ?? 'Approved'))
            ->get();

        $trades = $trades->sort(function ($a, $b) {
            $userA = optional(optional($a->supplier)->users->first())->name;
            $userB = optional(optional($b->supplier)->users->first())->name;

            if ($userA === null && $userB === null) {
                return 0;
            }
            if ($userA === null) {
                return 1;
            }
            if ($userB === null) {
                return -1;
            }

            return strcmp($userA, $userB);
        });

        $grouped_trades = $trades->groupBy('is_locked');

        $locked_count = $trades->where('is_locked', true)->count();
        $unlocked_count = $trades->where('is_locked', false)->count();
        $signed_in_portal_count = $trades->where('linked_to_portal', 1)->count();
        $total_count = $trades->count();

        if ($request->intent == 'Download Pdf') {
            $pdf = \Pdf::loadView('pdfs.trade_agreements', [
                'grouped_trades' => $grouped_trades,
                'locked_count' => $locked_count,
                'unlocked_count' => $unlocked_count,
                'signed_in_portal_count' => $signed_in_portal_count,
                'total_count' => $total_count
            ]);

            return $pdf->setPaper('a4', 'portrait')
                ->setWarnings(false)
                ->setOption('isPhpEnabled', true)
                ->download('TRADE-AGREEMENTS' . '.pdf');
        } else if ($request->intent == 'Download Excel') {

            $excelData = [];

            $locked_suppliers = $grouped_trades->get(1, collect());
            $open_suppliers = $grouped_trades->get(0, collect());

            $all_suppliers = $locked_suppliers->merge($open_suppliers);

            foreach ($all_suppliers as $index => $trade) {
                $excelData[] = [
                    'reference' => $trade?->reference ?? '',
                    'supplier_code' => $trade?->supplier?->supplier_code ?? '',
                    'supplier_name' => $trade?->supplier?->name ?? '',
                    'user_name' => $trade?->supplier?->users?->first()?->name ?? '',
                    'subscription_amount' => $trade?->subscription_charge,
                    'date' => \Carbon\Carbon::parse($trade?->date)->format('d/m/Y') ?? '',
                    'signed_in_portal' => $trade?->linked_to_portal ? 'Yes' : 'No',
                    'status' => $trade?->is_locked ? 'Locked' : 'Open',
                ];
            }

            $export = new TradeAgreementExport($excelData);
            return Excel::download($export, 'TRADE-AGREEMENTS.xlsx');
        } else {
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }
}
