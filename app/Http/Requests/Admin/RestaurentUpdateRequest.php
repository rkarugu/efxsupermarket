<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RestaurentUpdateRequest extends FormRequest
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
            'image_update' =>  'mimes:jpeg,jpg,png',
            'floor_image_update' =>  'mimes:jpeg,jpg,png',
            'opening_time'=>'required',
            'closing_time'=>'required',
            'branch_code' => 'required|unique:restaurants',

        ];      

        return $rules;
    }

    
}
