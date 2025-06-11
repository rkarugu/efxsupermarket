<?php

namespace App\Services\Inventory;

use App\Model\WaGrn;
use App\Models\WaReturnDemand;

class TurnoverPurchases
{
    public function purchases($supplier_id = null)
    {
        $grnsData = WaGrn::query()
            ->when(is_array($supplier_id), function ($query) use ($supplier_id) {
                $query->whereIn('wa_supplier_id', $supplier_id);
            })
            ->when(!is_array($supplier_id) && !is_null($supplier_id), function ($query) use ($supplier_id) {
                $query->where('wa_supplier_id', $supplier_id);
            })
            ->whereHas('stockMoves')
            ->whereYear('created_at', '>=', now()->subYears(2)->year)
            ->latest()
            ->get()
            ->groupBy(function ($grn) {
                return $grn->created_at->format('Y');
            })
            ->map(function ($yearGroup) {
                return $yearGroup->groupBy(function ($grn) {
                    return $grn->created_at->format('m');
                })->map(function ($monthGroup) {
                    return $monthGroup->sum(function ($grn) {
                        return (float)$grn->qty_received * (float)json_decode($grn->invoice_info)->order_price;
                    });
                });
            });

        $returnsData = WaReturnDemand::query()
            ->when(is_array($supplier_id), function ($query) use ($supplier_id) {
                $query->whereIn('wa_supplier_id', $supplier_id);
            })
            ->when(!is_array($supplier_id) && !is_null($supplier_id), function ($query) use ($supplier_id) {
                $query->where('wa_supplier_id', $supplier_id);
            })
            ->where('processed', true)
            ->whereYear('created_at', '>=', now()->subYears(2)->year)
            ->latest()
            ->get()
            ->groupBy(function ($return) {
                return $return->created_at->format('Y');
            })
            ->map(function ($yearGroup) {
                return $yearGroup->groupBy(function ($return) {
                    return $return->created_at->format('m');
                })->map(function ($monthGroup) {
                    return $monthGroup->sum('demand_amount');
                });
            });

        $data = [];
        $loop = 0;
        foreach ($grnsData as $year => $grnData) {
            array_push($data, [$year => []]);

            foreach ($grnData as $month => $totalAmount) {
                $demandAmount = $returnsData[$year][$month] ?? 0;

                array_push($data[$loop][$year], [
                    $month => round($totalAmount - $demandAmount, 2)
                ]);
            }

            $loop++;
        }

        return $data;
    }
}
