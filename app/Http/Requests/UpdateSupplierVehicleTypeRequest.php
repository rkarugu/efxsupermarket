<?php

namespace App\Http\Requests;


class UpdateSupplierVehicleTypeRequest extends BaseJsonResponse
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
            'name' => 'required|string|max:255',
            'vehicle_type' => 'required|string|max:255',
            'tonnage' => 'required|numeric|min:0',
            'offloading_time' => 'required|date_format:H:i',
        ];
    }
}
