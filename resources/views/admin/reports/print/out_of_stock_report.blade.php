@extends('layouts.report')

@section('title', 'OUT OF STOCK REPORT')

@section('content')
    <table class="table table-striped" id="outOfStockDataTable">
        <thead>
            <tr>
                <th style="font-size: 10px !important" class="text-left">ITEM CODE</th>
                <th style="font-size: 10px !important" class="text-left">ITEM NAME</th>
                <th style="font-size: 10px !important" class="text-left">CATEGORY</th>
                <th style="font-size: 10px !important" class="text-left">BIN</th>
                <th style="font-size: 10px !important" class="text-left">MAX STOCK</th>
                <th style="font-size: 10px !important" class="text-left">RE-ORDER LEVEL</th>
                <th style="font-size: 10px !important" class="text-left">QoH</th>
                <th style="font-size: 10px !important" class="text-left">QOO</th>
                <th style="font-size: 10px !important" class="text-left">Qty to Order</th>
                <th style="font-size: 10px !important" class="text-left">Sales Qty(7 Days)</th>
                <th style="font-size: 10px !important" class="text-left">Sales Qty(30 Days)</th>
                <th style="font-size: 10px !important" class="text-left">Sales Qty(30 - 180 Days)</th>
                <th style="font-size: 10px !important" class="text-left">SUPPLIERS</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $item)
                <tr>
                    <td style="font-size: 10px !important">{{ $item->stock_id_code }}</td>
                    <td style="font-size: 10px !important">{{ $item->title }}</td>
                    <td style="font-size: 10px !important">{{ $item->category->category_description }}</td>
                    <td style="font-size: 10px !important">{{ $item->bin_title }}</td>
                    <td style="font-size: 10px !important" class="text-right">{{ $item->max_stock }}</td>
                    <td style="font-size: 10px !important" class="text-right">{{ $item->re_order_level }}</td>
                    <td style="font-size: 10px !important" class="text-right">{{ $item->qty_on_hand }}</td>
                    <td style="font-size: 10px !important" class="text-right">{{ $item->qty_on_order }}</td>
                    <td style="font-size: 10px !important" class="text-right">
                        {{ $item->qty_to_order > 0 ? $item->qty_to_order : 0 }}</td>
                    <td style="font-size: 10px !important" class="text-right">{{ abs($item->sales_7_days) }}</td>
                    <td style="font-size: 10px !important" class="text-right">{{ abs($item->sales_30_days) }}</td>
                    <td style="font-size: 10px !important" class="text-right">{{ abs($item->sales_180_days) }}</td>
                    <td style="font-size: 10px !important" style="width: 200px">{{ $item->suppliers }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10">No items found</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
