<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Request;
use Illuminate\Validation\Rule;

class UserAddRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(Request $request)
    {
       
        $rules = [
            'name' => 'required|max:255',
            'role_id' => 'required',
            'restaurant_id' => 'required',
            'phone_number' => 'required|numeric|unique:users',
            'id_number' => 'required',
            'image' =>  'nullable|mimes:jpeg,jpg,png',
            'password' => 'required|max:30|min:6',
            'drop_limit' => 'required_if:role_id,170',
        ];
        return $rules;
    }

    public function messages(): array
    {
        return [
            'drop_limit.required_if' => 'The drop limit field is required when the role ID is Pos Cashier.',
        ];
    }

    
}
