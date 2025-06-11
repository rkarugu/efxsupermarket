<?php

namespace App\Services\Inventory;

use App\Model\WaStockMove;

class TurnoverSales
{
    public function sales($supplier_id)
    {
        $stockMovesData = WaStockMove::whereHas('inventoryItem', function ($query) use ($supplier_id) {
            $query->whereHas('suppliers', function ($query) use ($supplier_id) {
                $query->where('wa_suppliers.id', $supplier_id);
            });
        })
            ->where(function ($query) {
                $query->where('document_no', 'LIKE', 'RTN%')
                    ->orWhere('document_no', 'LIKE', 'INV%')
                    ->orWhere('document_no', 'LIKE', 'CIV%');
            })
            ->whereYear('created_at', '>=', now()->subYears(2)->year)
            ->get()
            ->groupBy(function ($stockMove) {
                return $stockMove->created_at->format('Y');
            })
            ->map(function ($yearGroup) {
                return $yearGroup->groupBy(function ($stockMove) {
                    return $stockMove->created_at->format('m');
                })->map(function ($monthGroup) {
                    return $monthGroup->sum('total_cost');
                });
            });

        // For consistency
        $data = [];
        $loop = 0;
        foreach ($stockMovesData as $year => $stockMoveData) {
            array_push($data, [$year => []]);

            foreach ($stockMoveData as $month => $totalAmount) {
                array_push($data[$loop][$year], [
                    $month => round($totalAmount, 2)
                ]);
            }

            $loop++;
        }

        return $data;
    }
}
