<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ValidationFailedResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'result' => 0,
            'errors' => $this->errors,
        ];
    }

    public function toResponse($request)
    {
        return response()->json($this->toArray($request));
    }
}
