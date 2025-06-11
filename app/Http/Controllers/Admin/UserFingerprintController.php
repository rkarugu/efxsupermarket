<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\UserFingerprint;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserFingerprintController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $response = [
            'status' => true,
            'message' => 'Fingerprints saved successfully.'
        ];

        try {
            $validator = Validator::make($request->all(), [
                'token' => 'required',
                'finger_number' => 'required|numeric',
                'fingerprint_file' => 'required'
            ]);

            if ($validator->fails()) {
                $response['status'] = false;
                $response['message'] = $this->validationHandle($validator->messages());
                return $this->jsonify($response, 422);
            }

            $user = JWTAuth::toUser($request->token);
            if (!$user) {
                $response['status'] = false;
                $response['message'] = 'Token mismatch';
                return response()->json($response, 422);
            }

            $uploadPath = 'uploads/fingerprints';
            $file = $request->file('fingerprint_file');
            $printForFinger = $user->fingerprints()->where('finger_number', $request->finger_number)->first();
            if ($printForFinger) {
                $fileName = $printForFinger->name;
                File::delete(public_path($uploadPath) . "/$fileName");

                $newName = $file->hashName();

                $file->move(public_path($uploadPath), $newName);
                $printForFinger->update([
                    'name' => "$newName",
                    'file_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getClientMimeType(),
                    'path' => "$uploadPath/$newName",
                ]);

                $response['message'] = 'Fingerprint updated successfully';
                $response['finger_number'] = $printForFinger->finger_number;
                $response['fingerprint_file'] = env('APP_URL') . "/$uploadPath/$newName";

                return $this->jsonify($response, 200);
            }

            if (!file_exists($uploadPath)) {
                File::makeDirectory($uploadPath, $mode = 0777, true, true);
            }

            $name = $file->hashName();
            $file->move(public_path($uploadPath), $name);
            $print = $user->fingerprints()->create([
                'finger_number' => $request->finger_number,
                'name' => "$name",
                'file_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getClientMimeType(),
                'path' => "$uploadPath/$name",
            ]);

            $response['finger_number'] = $print->finger_number;
            $response['fingerprint_file'] = env('APP_URL') . "/$print->path";

            return $this->jsonify($response, 200);
        } catch (\Throwable $e) {
            $response['status'] = false;
            $response['message'] = ' A server error was encountered.';

            return $this->jsonify($response, 500);
        }
    }

    public function getUserFingerPrints(Request $request): JsonResponse
    {
        $response = [
            'status' => true,
            'message' => 'Success',
            'data' => []
        ];

        try {
            $validator = Validator::make($request->all(), [
                'token' => 'required'
            ]);

            if ($validator->fails()) {
                $response['status'] = false;
                $response['message'] = $this->validationHandle($validator->messages());
                return $this->jsonify($response, 422);
            }

            $user = JWTAuth::toUser($request->token);
            if (!$user) {
                $response['status'] = false;
                $response['message'] = 'A user matching the provided token was not found.';
                return response()->json($response, 422);
            }

            $response['data'] = [
                'created_at' => $user->fingerprints()->first()->created_at,
                'updated_at' => $user->fingerprints()->latest('updated_at')->first()->updated_at
            ];

            $response['data']['fingerprints'] = $user->fingerprints->map(function (UserFingerprint $print) {
                return [
                    'finger_number' => $print->finger_number,
                    'fingerprint_file' => env('APP_URL') . "/$print->path",
                ];
            });

            return $this->jsonify($response, 200);
        } catch (\Throwable $e) {
            $response['status'] = false;
            $response['message'] = ' A server error was encountered.';

            return $this->jsonify($response, 500);
        }
    }
}
