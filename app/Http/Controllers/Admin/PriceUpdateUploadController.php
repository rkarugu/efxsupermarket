<?php

namespace App\Http\Controllers\Admin;

use App\Exports\PriceUploads;

use App\Http\Controllers\Controller;
use App\Imports\PriceUploads as ImportsPriceUploads;
use App\Model\TaxManager;
use App\Model\WaInventoryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Session;



class PriceUpdateUploadController extends Controller
{
    public function showUploadPage(Request $request)
    {
        $title = 'Upload Prices';
        $model = 'price-update-upload';
        $breadcum = ['Maintain Items' => '', 'Price Upload' => ''];

        $categories = DB::table('wa_inventory_categories')->get();
        return view('admin.maintaininvetoryitems.price_update_upload.download', compact('title', 'model', 'breadcum', 'categories'));
    }
    public function download(Request $request)
    {
        $title = 'Upload Prices';
        $model = 'price-update-upload';
        $breadcum = ['Maintain Items' => '', 'Price Upload' => ''];

        $categories = DB::table('wa_inventory_categories')->get();
        $inventoryItems = WaInventoryItem::all();
        if($request->category){
            $inventoryItems = WaInventoryItem::where('wa_inventory_category_id', $request->category)->get();
        }
        $data = $inventoryItems
            ->map(function (WaInventoryItem $item) {
                $taxManager = TaxManager::find($item->tax_manager_id)->title ?? '-';
                $payload = [
                    'item_id' => $item->id,
                    'stock_id_code' => $item->stock_id_code,
                    'description' => $item->description,
                    'category' => $item->wa_inventory_category_id,
                    'selling_price' => $item->selling_price,
                    'tax' => $taxManager,
                    'tax_manager_id' => $item->tax_manager_id,
                    'hs_code' => $item->hs_code
                ];

                return $payload;
            })->sortBy('center');

        $export = new PriceUploads($data);

        return Excel::download($export, "priceUploads.xlsx");
    }
    public function upload(Request $request) 
    {
        try {
            Excel::import(new ImportsPriceUploads, $request->file('upload_file'));
            Session::flash('success', 'File Uploaded  Successfully.');
            return redirect()->route('price-update.upload-page');
        } catch (\Throwable $e) {
            Session::flash('warning', $e->getMessage());
            return redirect()->route('price-update.upload-page');
        }
      
    }
}
