<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
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
            'role_id' => 'required',
            'restaurant_id' => 'required',
            'phone_number' => "required|numeric|unique:users,id,$id",
            'badge_number' => 'required',
            'id_number' => 'required',
            'image_update' =>  'mimes:jpeg,jpg,png',
            'email'=>"required|unique:users,id,$id",
            
        ];      
        return $rules;
    }

    
}
