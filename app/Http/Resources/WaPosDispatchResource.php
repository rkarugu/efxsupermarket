<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WaPosDispatchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=> $this->id,
            'cash_sale_no'=> $this->sales_no,
            'customer'=> $this->customer,
            'items_count'=> $this->items_count,
            'bin_locations'=> $this->bins_count,
            'Dispatched'=> $this->bins_count_dispatched,
            'status'=> 'Dispatching',
        ];
    }
}
