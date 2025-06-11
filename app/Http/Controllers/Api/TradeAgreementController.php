<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Model\PackSize;
use App\Model\WaInventoryItem;
use Illuminate\Http\Request;
use App\Model\WaSupplier;
use App\Models\TradeAgreement;
use App\Models\TradeProductOffer;
use App\Models\TradeAgreementDiscount;
use App\Model\WaLocationAndStore;
use App\Models\ApprovePriceListCost;
use App\Models\PriceChangeHistoryLogSupplier;
use DB;
use Illuminate\Support\Facades\Validator;

class TradeAgreementController extends Controller
{
    public function get_locations()
    {
        try {
            return response()->json([
                'result' => 1,
                'message' => 'Ok',
                'data' => WaLocationAndStore::where('is_physical_store', '1')->where('location_name', '<>', 'THIKA')->get(),
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'result' => -1,
                'message' => $th->getMessage(),
                'data' => [],
            ]);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required',
                'date' => 'required',
                'reference' => 'required',
                'supplier_code' => 'required',
                'supplier_email' => 'required'
            ]
        );
        if ($validator->fails()) {
            return response()->json([
                'result' => -1,
                'message' => 'validation failed',
                'error' => $validator->errors()
            ]);
        }
        $supplier = WaSupplier::where([
            'supplier_code' => $request->supplier_code,
            'email' => $request->supplier_email,
        ])->first();
        if (!$supplier) {
            return response()->json([
                'result' => -1,
                'message' => 'Supplier Not found',
                'error' => ['supplier_code' => [
                    'Supplier Not found'
                ]]
            ]);
        }
        $trade = TradeAgreement::where([
            'wa_supplier_id' => $supplier->id,
        ])->where('status', '!=', 'Rejected')->with('discounts')->first();
        $check = DB::transaction(function () use ($request, $supplier, $trade) {
            if (!$trade) {
                $trade = new TradeAgreement();
                $trade->reference = $request->reference;
                $trade->name = $request->name;
                $trade->date = $request->date;
                $trade->wa_supplier_id = $supplier->id;
                $trade->save();
                $trade->reference = 'KH-TA-' . str_pad($trade->id, 5, '0', STR_PAD_LEFT);
            }
            $trade->linked_to_portal = 1;
            $trade->linked_at = date('Y-m-d H:i:s');
            $trade->save();
            $trade = TradeAgreement::where([
                'id' => $trade->id,
            ])->with('discounts', 'offers', 'billing_charges.currency')->first();
            return $trade;
        });
        if ($check) {
            return response()->json([
                'result' => 1,
                'message' => 'Agreement Added Succesfully',
                'data' => $check
            ]);
        }
        return response()->json([
            'result' => 0,
            'message' => 'Something went wrong',
            'data' => []
        ]);
    }

    public function store_offer_amount($reference, Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'stock_id_code' => 'required|string|max:255',
                'inventory_item_id' => 'required|numeric|min:0',
                'offer_amount' => 'min:0|required|numeric',
                'target_quantity' => 'min:0|required|numeric'
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors(), 'result' => 0]);
            }

            DB::transaction(function () use ($reference, $request) {
                $trade = TradeAgreement::where('reference', $reference)->first();
                $offer = TradeProductOffer::where('trade_agreements_id', $trade->id)->where(
                    'stock_id_code',
                    $request->stock_id_code
                )->first();
                if (!$offer) {
                    $offer = new TradeProductOffer();
                    $offer->trade_agreements_id = $trade->id;
                    $offer->stock_id_code = $request->stock_id_code;
                }
                $offer->inventory_item_id = $request->inventory_item_id;
                $offer->offer_amount = $request->offer_amount;
                $offer->target_quantity = $request->target_quantity;
                $offer->save();
            });

            return response()->json([
                'result' => 1,
                'message' => 'Offer Stored and Sent to portal Successfully',
                'data' => [],
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'result' => -1,
                'message' => $th->getMessage()
            ]);
        }
    }

    public function save_supplier_data($reference, Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'newPrice' => 'required|string|max:255',
                'id' => 'required|numeric|min:0',
                'newPrice' => 'min:0|required|numeric'
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors(), 'result' => 0]);
            }

            DB::transaction(function () use ($reference, $request) {
                $trade = TradeAgreement::with('supplier')->where('reference', $reference)->first();
                $suplier = \App\Model\WaInventoryItemSupplierData::where('wa_supplier_id', $trade->wa_supplier_id)
                    ->where('wa_inventory_item_id', $request->id)
                    ->first();
                if (!$suplier) {
                    $suplier = new \App\Model\WaInventoryItemSupplierData;
                    $suplier->wa_supplier_id = $trade->wa_supplier_id;
                    $suplier->wa_inventory_item_id = $request->id;
                }
                $suplier->currency = 'KES';
                $suplier->price = $request->newPrice;
                $suplier->price_effective_from = "";
                $suplier->our_unit_of_measure = 'Each';
                $suplier->supplier_unit_of_measure = 'Each';
                $suplier->conversion_factor = NULL;
                $suplier->supplier_stock_code = $request->supplier_stock_code;
                $suplier->minimum_order_quantity = NULL;
                $suplier->supplier_stock_description = "";
                $suplier->lead_time_days = $request->lead_time ?? NULL;
                $suplier->preferred_supplier = "";
                $suplier->save();
                \App\Model\WaInventoryItemSupplierPrices::where('wa_inventory_item_supplier_id', $suplier->id)->update(['status' => 'Old']);
                $price = new \App\Model\WaInventoryItemSupplierPrices;
                $price->wa_inventory_item_supplier_id = $suplier->id;
                $price->price = $suplier->price;
                $price->status = 'Current';
                $price->save();
            });

            return response()->json([
                'result' => 1,
                'message' => 'Offer Stored and Sent to portal Successfully',
                'data' => [],
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'result' => -1,
                'message' => $th->getMessage()
            ]);
        }
    }

    public function save_bulk_supplier_data($reference, Request $request)
    {
        $groupedData = json_decode($request->grouped_data, true);

        array_shift($groupedData);

        try {
            DB::transaction(function () use ($reference, $groupedData) {
                $trade = TradeAgreement::with('supplier')->where('reference', $reference)->first();

                foreach ($groupedData as $data) {

                    $item_code = $data[0];
                    $supplier_stock_code = $data[1];
                    $stock_name = $data[2];
                    $price_list_cost = $data[4];

                    $inventory_item = WaInventoryItem::where('stock_id_code', $item_code)->first();
                    $inventory_item_updating_cost = ApprovePriceListCost::where('inventory_item_id', $inventory_item->id)
                        ->where('trade_agreement_id', $trade->id)
                        ->where('price_list_cost', $price_list_cost)
                        ->where('status', 'Pending')->first();
                    $supplier = \App\Model\WaInventoryItemSupplierData::where('wa_supplier_id', $trade->wa_supplier_id)
                        ->where('wa_inventory_item_id', $inventory_item->id)
                        ->first();

                    if (
                        (float)$inventory_item->price_list_cost != (float)$price_list_cost && $price_list_cost != 0 && $price_list_cost != ''
                        && $price_list_cost != null
                    ) {

                        if (!$inventory_item_updating_cost || (float)$inventory_item_updating_cost->price_list_cost != (float)$price_list_cost) {

                            if (!$supplier) {
                                $supplier = new \App\Model\WaInventoryItemSupplierData;
                                $supplier->wa_supplier_id = $trade->wa_supplier_id;
                                $supplier->wa_inventory_item_id = $inventory_item->id;
                                $supplier->currency = 'KES';
                                $supplier->price_list_cost = $price_list_cost;
                                $supplier->price_list_cost_effective_from = "";
                                $supplier->our_unit_of_measure = 'Each';
                                $supplier->supplier_unit_of_measure = 'Each';
                                $supplier->conversion_factor = NULL;
                                $supplier->supplier_stock_code = $supplier_stock_code;
                                $supplier->minimum_order_quantity = NULL;
                                $supplier->supplier_stock_description = $stock_name;
                                $supplier->lead_time_days = $data['lead_time'] ?? NULL;
                                $supplier->preferred_supplier = "";
                                $supplier->save();
                            } else {
                                $supplier = new \App\Model\WaInventoryItemSupplierData;
                                $supplier->wa_supplier_id = $trade->wa_supplier_id;
                                $supplier->wa_inventory_item_id = $inventory_item->id;
                                $supplier->currency = 'KES';
                                $supplier->price_list_cost = $price_list_cost;
                                $supplier->price_list_cost_effective_from = "";
                                $supplier->our_unit_of_measure = 'Each';
                                $supplier->supplier_unit_of_measure = 'Each';
                                $supplier->conversion_factor = NULL;
                                $supplier->supplier_stock_code = $supplier_stock_code;
                                $supplier->minimum_order_quantity = NULL;
                                $supplier->supplier_stock_description = $stock_name;
                                $supplier->lead_time_days = $data['lead_time'] ?? NULL;
                                $supplier->preferred_supplier = "";
                                $supplier->save();
                            }

                            \App\Model\WaInventoryItemSupplierPrices::where('wa_inventory_item_supplier_id', $supplier->id)
                                ->update(['status' => 'Old']);

                            $price = new \App\Model\WaInventoryItemSupplierPrices;
                            $price->wa_inventory_item_supplier_id = $supplier->id;
                            $price->price_list_cost = $supplier->price_list_cost;
                            $price->status = 'Current';
                            $price->save();

                            $approve_price_list = new ApprovePriceListCost();
                            $approve_price_list->inventory_item_id = $inventory_item->id;
                            $approve_price_list->supplier_id = $trade->wa_supplier_id;
                            $approve_price_list->trade_agreement_id = $trade->id;
                            $approve_price_list->price_list_cost = $supplier->price_list_cost;
                            $approve_price_list->status = 'Pending';
                            $approve_price_list->save();

                            $price_change_history_log = new PriceChangeHistoryLogSupplier();
                            $price_change_history_log->wa_supplier_id = $trade->wa_supplier_id;
                            $price_change_history_log->wa_inventory_item_id = $inventory_item->id;
                            $price_change_history_log->save();
                        }
                    }
                }

            });

            return response()->json([
                'result' => 1,
                'message' => 'All items saved successfully!',
                'data' => [],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'result' => -1,
                'message' => $e->getMessage()
            ]);
        }
    }



    public function store_discount($reference, Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'discount_type' => 'required|in:' . implode(",", array_keys(TradeAgreementDiscount::typeList())),
                'discount_value' => 'required_if:discount_type,No Goods Return Discount,Target discount on total value|numeric',
                'applies_to_all_item' => 'nullable|in:1',
                'selected_product_discount' => 'required_if:discount_type,End month Discount,Quarterly Discount,Invoice Discount,Base Discount|array',
                'selected_product_discount.*' => 'required_if:discount_type,End month Discount,Quarterly Discount,Invoice Discount,Base Discount',
                'selected_products' => 'array|required_if:discount_type,End month Discount,Quarterly Discount,Purchase Quantity Offer,Target discount on value,Target discount on quantity,Invoice Discount,Base Discount',
                'selected_products.*' => 'required_if:discount_type,End month Discount,Quarterly Discount,Purchase Quantity Offer,Target discount on value,Target discount on quantity,Invoice Discount,Base Discount',
                'selected_product_offer' => 'required_if:discount_type,Purchase Quantity Offer,Target discount on value|array',
                'selected_product_offer.*' => 'required_if:discount_type,Purchase Quantity Offer,Target discount on value',
                'discount_value_type' => 'required_if:discount_type,Quarterly Discount,End month Discount,Target discount on total value',
                'payment_period_discount' => 'required_if:discount_type,Payment Discount|array',
                'payment_period_discount.*' => 'required_if:discount_type,Payment Discount|numeric',
                'target_quantity' => 'required_if:discount_type,Target discount on quantity|array',
                'target_quantity.*' => 'required_if:discount_type,Target discount on quantity|numeric',
                'target_discount' => 'required_if:discount_type,Target discount on quantity|array',
                'target_discount.*' => 'required_if:discount_type,Target discount on quantity|numeric',
                'discount_target_type.*' => 'required_if:discount_type,Transport rebate|in:Invoice,Product,All Location',
                'store_location.*' => 'required_if:discount_type,Transport rebate',
                'per_unit_discount.*' => 'required_if:discount_type,Transport rebate|numeric',
                'percentage_of_invoice.*' => 'required_if:discount_type,Transport rebate|numeric',
                'per_tonnage_discount_value.*' => 'required_if:discount_type,Transport rebate|numeric',
                'application_stage.*' => 'required_if:discount_type,Transport rebate',

                'from' => 'required_if:discount_type,Performance Discount|array',
                'from.*' => 'required_if:discount_type,Performance Discount',

                'to' => 'required_if:discount_type,Performance Discount|array',
                'to.*' => 'required_if:discount_type,Performance Discount',

                'value' => 'required_if:discount_type,Performance Discount|array',
                'value.*' => 'required_if:discount_type,Performance Discount',
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors(), 'result' => 0]);
            }

            DB::transaction(function () use ($reference, $request) {
                $trade = TradeAgreement::where('reference', $reference)->first();
                $discount = TradeAgreementDiscount::where('trade_agreements_id', $trade->id)->where(
                    'discount_type',
                    $request->discount_type
                )->first();
                if (!$discount) {
                    $discount = new TradeAgreementDiscount();
                    $discount->trade_agreements_id = $trade->id;
                    $discount->discount_type = $request->discount_type;
                }
                $discount->discount_value = $request->discount_value;
                $discount->applies_to_all_item = $request->applies_to_all_item;
                $discount->discount_value_type = $request->discount_value_type;
                $discount->purchased_product_quantity = $request->purchased_product_quantity;
                $discount->free_product_quantity = $request->free_product_quantity;
                $other_options = "";
                if ($request->discount_type == 'End month Discount' || $request->discount_type == 'Quarterly Discount' || $request->discount_type == 'Base Discount' || $request->discount_type == 'Invoice Discount') {
                    $other_options = [];
                    if ($request->selected_products && count($request->selected_products) > 0) {
                        foreach ($request->selected_products as $key => $selected_product) {
                            $other_options[$key] = (object)['stock_id' => $request->selected_products[$key]];
                        }
                    }
                }
                if ($request->discount_type == 'Purchase Quantity Offer' || $request->discount_type == 'Target discount on value') {
                    $other_options = [];
                    if ($request->selected_product_quantity && count($request->selected_product_quantity) > 0) {
                        foreach ($request->selected_product_quantity as $key => $selected_product) {
                            $other_options[$key] = (object)['stock_id' => $request->selected_products[$key], 'free_stock' => (float)$request->selected_product_offer[$key], 'purchase_quantity' => (float)$selected_product];
                        }
                        $discount->purchased_product_quantity = 0;
                        $discount->free_product_quantity = 0;
                    }
                }
                if ($request->discount_type == 'Payment Discount') {
                    $other_options = array_map(function ($item) {
                        return (float)$item;
                    }, $request->payment_period_discount);
                }
                if ($request->discount_type == 'Performance Discount') {
                    $other_options = [];
                    foreach ($request->from as $key => $value) {
                        $other_options[] = (object)[
                            'from' => $value,
                            'to' => $request->to[$key],
                            'value' => $request->value[$key],
                        ];
                    }
                }
                if ($request->discount_type == 'Target discount on quantity') {
                    $other_options = [];
                    foreach ($request->target_quantity as $key => $quantity) {
                        $other_options[$key] = (object)[
                            'stock_id' => $request->selected_products[$key],
                            'quantity' => $quantity,
                            'discount' => (float)$request->target_discount[$key]
                        ];
                    }
                }
                if ($request->discount_type == 'Transport rebate') {
                    $other_options = [
                        'discount_target_type' => $request->discount_target_type
                    ];
                    foreach ($request->store_location as $key => $store_location) {
                        $other_options['location_discounts'][] = [
                            'location' => $store_location,
                            'per_unit_discount' => (float)$request->per_unit_discount[$key],
                            'percentage_of_invoice' => (float)$request->percentage_of_invoice[$key],
                            'per_tonnage_discount_value' => (float)$request->per_tonnage_discount_value[$key],
                            'application_stage' => $request->application_stage[$key]
                        ];
                    }
                }
                $discount->other_options = json_encode($other_options);
                $discount->save();
            });
            return response()->json([
                'result' => 1,
                'message' => 'Discount Stored Successfully',
                'data' => []
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'result' => -1,
                'message' => $th->getMessage()
            ]);
        }
    }
}
