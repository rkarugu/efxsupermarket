<?php

namespace App\Http\Requests;


class StoreSuggestedOrderRequest extends BaseJsonResponse
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'order_number'=>'required|string|unique:suggested_orders,order_number',
            'order_date'=>'required|date',
            'status'=>'required|string',
            'code'=>'required|array',
            'code.*'=>'required|exists:wa_inventory_items,stock_id_code',
            'quantity'=>'required|array',
            'quantity.*'=>'required|numeric',
            'supplier_code' => 'required|exists:wa_suppliers,supplier_code',
            'supplier_email' => 'required|exists:wa_suppliers,email'
        ];
    }
}
