<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RestaurentAddRequest extends FormRequest
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
    public function rules()
    {
       
        $rules = [
            'name' => 'required|max:255',
            'location' => 'required',
            'image' =>  'required|mimes:jpeg,jpg,png',
            'floor_image' =>  'required|mimes:jpeg,jpg,png',
            'branch_code' => 'required|unique:restaurants',
            'opening_time'=>'required',
            'closing_time'=>'required',
        ];      
        return $rules;
    }

    
}
