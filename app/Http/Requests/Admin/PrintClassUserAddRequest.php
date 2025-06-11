<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PrintClassUserAddRequest extends FormRequest
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
            'print_class_id' => 'required',
            'restaurant_id' => 'required',
            'username' => 'required|max:255|unique:print_class_users',
            'password' => 'required|max:30|min:6', 
        ];      
        return $rules;
    }

    
}
