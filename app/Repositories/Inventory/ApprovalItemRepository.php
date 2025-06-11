<?php

namespace App\Repositories\Inventory;


use App\Interfaces\Inventory\ApprovalItemInterface;
use App\Models\WaInventoryItemApprovalStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ApprovalItemRepository implements ApprovalItemInterface
{
    
    public function getAllApprovalItem()
    {
        try {
            $items = WaInventoryItemApprovalStatus::all();
            return response($items, 200);
        } catch (\Exception $e) {
            return response('No Items Found', 400);
        }
    }

    public function getAllApprovalItemDatatable($data)
    {
        try {
            $items = WaInventoryItemApprovalStatus::with('approvalBy','inventoryItem','inventoryItem.category');
            if (request()->filled('search.value')) {
                $search = request()->input('search.value');
                $items->whereHas('inventoryItem', function ($query) use ($search) {
                    $query->where('stock_id_code', 'LIKE', "%{$search}%")
                          ->orWhere('title', 'LIKE', "%{$search}%");
                });
            }
            $items->when(request()->filled('start-date') && request()->filled('end-date'), function ($query) {
                $startDate = request('start-date');
                $endDate = request('end-date');
            
                if ($startDate === $endDate) {
                    return $query->whereDate('created_at', $startDate);
                } else {
                    return $query->whereDate('created_at', '>=', $startDate)
                                 ->whereDate('created_at', '<=', $endDate);
                }
            });
            $totalFiltered = $items->count();
            $data_query = $items->offset($data['start'])
                ->limit($data['limit'])
                ->orderBy($data['order'], $data['dir'])
                ->get();
            $return = ['data'=>$data_query,'totalFiltered'=>$totalFiltered];
            return response($return, 200);
        } catch (\Exception $e) {dd($e);
            return response('No Items Found', 400);
        }
    }

    public function getAllApprovalItemCount()
    {
        return WaInventoryItemApprovalStatus::count();
    }

    public function storeApprovalItem($data)
    {
        DB::beginTransaction();
        try {
            WaInventoryItemApprovalStatus::create([
                'wa_inventory_items_id' => $data['item'],
                'approval_by' => $data['user'],
                'status' => $data['status'],
                'changes' => $data['changes'],
                'new_data' => $data['new_data'],
                'approval_date' => Carbon::now(),
            ]);
            
            DB::commit();

            return response('Approval Item Stored Successfully', 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response('Something went wrong', 400);
        }
    }

    public function destroyApprovalItem($id)
    {
        DB::beginTransaction();
        try {
            $delete = WaInventoryItemApprovalStatus::find($id);
            if($delete){
                $delete->delete();
            }
            
            DB::commit();

            return response('Approval Item Deleted Successfully', 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response('Something went wrong', 400);
        }
    }

    public function getItemHistory()
    {
        
    }
}
