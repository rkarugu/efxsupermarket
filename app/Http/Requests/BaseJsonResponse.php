<?php

namespace App\Http\Requests;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use App\Http\Resources\ValidationFailedResource;

class BaseJsonResponse extends FormRequest
{
    protected function failedValidation(Validator $validator)
    {
        $response = new ValidationFailedResource((object)['errors'=>$validator->errors()]);

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
