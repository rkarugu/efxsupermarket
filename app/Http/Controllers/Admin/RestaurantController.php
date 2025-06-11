<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Restaurant;
use App\Http\Requests\Admin\RestaurentAddRequest;
use App\Http\Requests\Admin\RestaurentUpdateRequest;
use DB;
use Illuminate\Support\Facades\File;
use Session;
use Illuminate\Support\Facades\Validator;
use Throwable;
use App\Model\PaymentMethod;

class RestaurantController extends Controller
{

    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct()
    {
        $this->model = 'branches';
        $this->title = 'Branches';
        $this->pmodule = 'branches';
    }

    public function index()
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {

            $title = $this->title;
            $model = $this->model;
            $lists = Restaurant::orderBy('id', 'DESC')->get();
            $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
            return view('admin.restaurents.index', compact('title', 'lists', 'model', 'breadcum', 'pmodule', 'permission'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function create()
    {
        $permission = $this->mypermissionsforAModule();
        if (isset($permission[$this->pmodule . '___add']) || $permission == 'superadmin') {
            $title = 'Add Branch';
            $model = $this->model;
            $breadcum = [$this->title => route($model . '.index'), 'Add' => ''];
            $googleMapsApiKey = env('GOOGLE_MAPS_API_KEY');

            return view('admin.restaurents.create', compact('title', 'model', 'breadcum', 'googleMapsApiKey'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }


    public function store(RestaurentAddRequest $request)
    {
        try {

            $row = new Restaurant();
            $row->name = $request->name;
            $row->opening_time = $request->opening_time;
            $row->closing_time = $request->closing_time;
            $row->location = $request->location;
            $row->latitude = $request->latitude;
            $row->longitude = $request->longitude;
            $row->branch_code = $request->branch_code;
            $row->wa_company_preference_id = $request->wa_company_preference_id;


            $row->telephone = $request->telephone;
            $row->mpesa_till = $request->mpesa_till;
            $row->vat = $request->vat;
            $row->pin = $request->pin;
            $row->website_url = $request->website_url;
            $row->email = $request->email;
            $row->equity_account = $request->equity_account;
            $row->equity_paybill = $request->equity_paybill;
            $row->kcb_account = $request->kcb_account;
            $row->kcb_mpesa_paybill= $request->kcb_mpesa_paybill;
            $row->vooma_account = $request->vooma_account;
            $row->kcb_vooma_paybill= $request->kcb_vooma_paybill;  


//            try {
            $mainUploadPath = 'uploads/restaurants';
            if (!file_exists($mainUploadPath)) {
                File::makeDirectory($mainUploadPath, 0777, true, true);
            }

            $thumbsUploadPath = 'uploads/restaurants/thumb';
            if (!file_exists($thumbsUploadPath)) {
                File::makeDirectory($thumbsUploadPath, 0777, true, true);
            }

            if ($request->file('image')) {
                $file = $request->file('image');
                $image = uploadwithresize($file, 'restaurants', '341');
                $row->image = $image;
            }

            if ($request->file('floor_image')) {
                $file = $request->file('floor_image');
                $floor_image = uploadwithresize($file, 'restaurants', '341');
                $row->floor_image = $floor_image;
            }
//            } catch (Throwable $e) {
//                // pass
//            }

            $row->save();
            Session::flash('success', 'Record added successfully.');
            return redirect()->route($this->model . '.index');


        } catch (\Exception $e) {

            $msg = $e->getMessage();
            Session::flash('warning', $msg);
            return redirect()->back()->withInput();
        }
    }


    public function show($id)
    {

    }


    public function edit($slug)
    {
    
        try {
            $paymentMethods = PaymentMethod::where('use_as_channel', 1)->get();
            $permission = $this->mypermissionsforAModule();
            if (isset($permission[$this->pmodule . '___edit']) || $permission == 'superadmin') {
                $row = Restaurant::whereSlug($slug)->first();
                if ($row) {

                    $title = 'Edit Branch';
                    $breadcum = [$this->title => route($this->model . '.index'), 'Edit' => ''];
                    $model = 'branches';
                    $googleMapsApiKey = env('GOOGLE_MAPS_API_KEY');
                    return view('admin.restaurents.edit', compact('title', 'model', 'breadcum', 'row', 'googleMapsApiKey', 'paymentMethods'));
                } else {
                    Session::flash('warning', 'Invalid Request');
                    return redirect()->back();
                }
            } else {
                Session::flash('warning', 'Invalid Request');
                return redirect()->back();
            }


        } catch (\Exception $e) {

            $msg = $e->getMessage();
            Session::flash('warning', $msg);
            return redirect()->back();
        }
    }


    public function update(Request $request, $slug)
    {
        try {
            $row = Restaurant::whereSlug($slug)->first();
            $validator = Validator::make($request->all(), [
                'name' => 'required|max:255',
                'location' => 'required',
                'image_update' => 'mimes:jpeg,jpg,png',
                'floor_image_update' => 'mimes:jpeg,jpg,png',
                'opening_time' => 'required',
                'closing_time' => 'required',
                'branch_code' => 'required|unique:restaurants,branch_code,' . $row->id,

            ]);
            if ($validator->fails()) {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            } else {


                $previous_row = $row;
                $row->name = $request->name;
                $row->opening_time = $request->opening_time;
                $row->closing_time = $request->closing_time;
                $row->location = $request->location;
                $row->latitude = $request->latitude;
                $row->longitude = $request->longitude;
                $row->branch_code = $request->branch_code;
                $row->wa_company_preference_id = $request->wa_company_preference_id;
                $row->telephone = $request->telephone;
                $row->mpesa_till = $request->mpesa_till;

                $row->vat = $request->vat;
                $row->pin = $request->pin;

                $row->website_url = $request->website_url;
                $row->email = $request->email;
                $row->equity_account = $request->equity_account;
                $row->equity_paybill = $request->equity_paybill;
                $row->kcb_account = $request->kcb_account;
                $row->kcb_mpesa_paybill= $request->kcb_mpesa_paybill;
                $row->vooma_account = $request->vooma_account;
                $row->kcb_vooma_paybill = $request->kcb_vooma_paybill;

                $row->equity_payment_method_id = $request->equity_payment_method_id;
                $row->mpesa_payment_method_id = $request->mpesa_payment_method_id;
                $row->kcb_payment_method_id = $request->kcb_payment_method_id;

                $mainUploadPath = 'uploads/restaurants';
                if (!file_exists($mainUploadPath)) {
                    File::makeDirectory($mainUploadPath, 0777, true, true);
                }

                $thumbsUploadPath = 'uploads/restaurants/thumb';
                if (!file_exists($thumbsUploadPath)) {
                    File::makeDirectory($thumbsUploadPath, 0777, true, true);
                }

                if ($request->file('image_update')) {
                    $file = $request->file('image_update');
                    $image = uploadwithresize($file, 'restaurants', '341');

                    if ($previous_row->image) {
                        unlinkfile('restaurants', $previous_row->image);
                    }
                    $row->image = $image;

                }

                if ($request->file('floor_image_update')) {
                    $file = $request->file('floor_image_update');
                    $floor_image_update = uploadwithresize($file, 'restaurants', '341');

                    if ($previous_row->floor_image) {
                        unlinkfile('restaurants', $previous_row->floor_image);
                    }
                    $row->floor_image = $floor_image_update;

                }
                $row->save();
                Session::flash('success', 'Record updated successfully.');
                return redirect()->route($this->model . '.index');

            }


        } catch (\Exception $e) {

            $msg = $e->getMessage();
            Session::flash('warning', $msg);
            return redirect()->back()->withInput();
        }
    }


    public function destroy($slug)
    {
        try {
            $row = Restaurant::whereSlug($slug)->first();
            Restaurant::whereSlug($slug)->delete();
            if ($row->image) {
                unlinkfile('restaurants', $row->image);

            }
            Session::flash('success', 'Deleted successfully.');
            return redirect()->back();
        } catch (\Exception $e) {

            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function getBranches(): JsonResponse
    {
        try {
            $branches = Restaurant::select('id', 'name')->get();
            return $this->jsonify(['data' => $branches], 200);
        } catch (\Throwable $e) {
            return $this->jsonify([], 500);
        }
    }

    public function userBranches(Request $request)
    {
        $user = $request->user();

        $branches = Restaurant::select('id', 'name')
            ->unless($user->role_id == '1', function ($restaurants) use ($user) {
                $restaurants->whereIn('id', [$user->restaurant_id, ...$user->branches()->pluck('restaurants.id')]);
            })
            ->get();

        return response()->json($branches);
    }
}
