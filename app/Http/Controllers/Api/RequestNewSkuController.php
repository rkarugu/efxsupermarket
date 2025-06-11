<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RequestNewSku;
use App\Models\RequestNewSkuImage;
use App\Models\TradeAgreement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class RequestNewSkuController extends Controller
{

    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct()
    {
        $this->model = 'request-new-sku';
        $this->title = 'New SKU Requests';
        $this->pmodule = 'request-new-sku';
    }

    public function index()
    {
        if (!can('view', $this->pmodule)) {
            Session::flash('warning', pageRestrictedMessage());
            return redirect()->back();
        }

        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;

        $sku_requests = RequestNewSku::where('status', 'Pending')->with(['requestnewskuimages', 'packsize', 'subcategory', 'subcategory.category', 'supplier', 'approvedby'])->get();

        return view('admin.suppliers-overview.new_sku_requests', compact(
            'title',
            'model',
            'pmodule',
            'permission',
            'sku_requests'
        ));
    }

    public function showRequestedNewSku($reference, Request $request)
    {
        $trade = TradeAgreement::where('reference', $reference)->with('supplier')->first();
        $requested_skus = RequestNewSku::where('trade_agreement_id', $trade->id)
            ->with(['requestnewskuimages', 'packsize', 'subcategory', 'subcategory.category', 'supplier', 'approvedby'])
            ->get();

        $baseUrl = asset('');

        $requested_skus->each(function ($sku) use ($baseUrl) {
            $sku->requestnewskuimages->each(function ($image) use ($baseUrl) {
                $image->image_path = $baseUrl . $image->image_path;
            });
        });

        return response()->json(['requested_skus' => $requested_skus]);
    }




    public function requestNewSku($reference, Request $request)
    {
        $validated = $request->validate([
            'itemCode' => 'required|string|max:255',
            'itemName' => 'required|string|max:255',
            'unitOfMeasure' => 'required|exists:pack_sizes,id',
            'subCategories' => 'required|exists:wa_item_sub_categories,id',
            'tradeAgreementDiscounts' => 'required',
            'grossWeight' => 'required|numeric',
            'priceListCost' => 'required|numeric',
        ]);

        $trade = TradeAgreement::where('reference', $reference)->with('supplier')->first();

        try {
            $trade_agreement_discounts = is_array($request->tradeAgreementDiscounts)
                ? json_encode($request->tradeAgreementDiscounts)
                : json_encode([$request->tradeAgreementDiscounts]);

            $requestNewSku = RequestNewSku::create([
                'trade_agreement_id' => $trade->id,
                'wa_supplier_id' => $trade->supplier->id,
                'trade_agreement_reference' => $reference,
                'supplier_sku_code' => $request->itemCode,
                'supplier_sku_name' => $request->itemName,
                'pack_size_id' => $request->unitOfMeasure,
                'sub_category_id' => $request->subCategories,
                'trade_agreement_discount' => $trade_agreement_discounts,
                'gross_weight' => $request->grossWeight,
                'price_list_cost' => $request->priceListCost,
            ]);

            $imageRecords = [];

            foreach ($request->images as $fileUrl) {
                $fileName = basename($fileUrl);
                $fileContents = file_get_contents($fileUrl);

                $stored = Storage::put("public/request_new_skus/{$fileName}", $fileContents);

                $path = Storage::url("public/request_new_skus/{$fileName}");

                $fileRecord = RequestNewSkuImage::create([
                    'file_name' => $fileName,
                    'file_path' => $path,
                    'request_new_sku_id' => $requestNewSku->id
                ]);

                $imageRecords[] = $fileRecord;
            }


            return response()->json([
                'message' => 'SKU request created successfully!',
                'data' => $requestNewSku
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while creating the SKU request.',
                'error' => $e->getMessage()
            ], 500);
        }
    }





    public function approve(Request $request)
    {

        if (!can('approve', $this->pmodule)) {
            Session::flash('warning', pageRestrictedMessage());
            return redirect()->back();
        }

        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:request_new_skus,id',
        ]);

        try {
            RequestNewSku::whereIn('id', $request->ids)->update([
                'status' => 'Approved',
                'approved_by' => auth()->id(),
            ]);

            return response()->json([
                'message' => 'SKU approved successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function reject(Request $request)
    {

        if (!can('reject', $this->pmodule)) {
            Session::flash('warning', pageRestrictedMessage());
            return redirect()->back();
        }

        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:request_new_skus,id',
        ]);

        try {
            RequestNewSku::whereIn('id', $request->ids)->update([
                'status' => 'Rejected',
                'approved_by' => auth()->id(),
            ]);

            return response()->json([
                'message' => 'SKU rejected successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
