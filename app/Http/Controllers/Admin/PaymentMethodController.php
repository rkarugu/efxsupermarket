<?php

namespace App\Http\Controllers\Admin;

use App\Model\WaInternalRequisition;
use App\PaymentProvider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\PaymentMethod;

use DB;
use Session;

class PaymentMethodController extends Controller
{

    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct()
    {
        $this->model = 'payment-methods';
        $this->title = 'Payment Methods';
        $this->pmodule = 'payment-methods';
    }

    public function index()
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $lists = PaymentMethod::with('provider','branch')->orderBy('id', 'DESC')->get();;
            $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
            return view('admin.paymentmethods.index', compact('title', 'lists', 'model', 'breadcum', 'pmodule', 'permission'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }

    }

    public function create()
    {
        $permission = $this->mypermissionsforAModule();
        if (isset($permission[$this->pmodule . '___add']) || $permission == 'superadmin') {
            $title = 'Add ' . $this->title;
            $model = $this->model;
            $breadcum = [$this->title => route($model . '.index'), 'Add' => ''];

            $branches =$this->getRestaurantList();
            $providers = PaymentProvider::select('id', 'name')->get();
            return view('admin.paymentmethods.create', compact('title', 'model', 'breadcum', 'providers','branches'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }

    }


    public function store(Request $request)
    {
        $this->validate($request, [
           'branch_id' => 'required',
        ]);
        if ($request->is_mpesa && $request->is_cash) {
            return back()->withErrors(['error' => 'A payment method cannot be both Mpesa and Cash.']);
        }

        try {
            $row = new PaymentMethod();
            $row->title = strtoupper($request->title);
            $row->gl_account_id = $request->gl_account_id;
            $row->payment_provider_id = $request->payment_provider_id;
            $row->use_for_payments = $request->use_for_payments;
            $row->use_for_receipts = $request->use_for_receipts;
            $row->use_in_pos = $request->use_in_pos;
            $row->is_mpesa = $request->is_mpesa;
            $row->is_cash = $request->is_cash;
            $row->use_as_channel = $request->use_as_channel;
            $row->branch_id = $request->branch_id;

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
            $permission = $this->mypermissionsforAModule();
            if (isset($permission[$this->pmodule . '___edit']) || $permission == 'superadmin') {
                $row = PaymentMethod::whereSlug($slug)->first();
//                dd($row);
                if ($row) {
                    $title = 'Edit ' . $this->title;
                    $breadcum = [$this->title => route($this->model . '.index'), 'Edit' => ''];
                    $model = $this->model;
                    $providers = PaymentProvider::select('id', 'name')->get();
                    $branches =$this->getRestaurantList();
                    return view('admin.paymentmethods.edit', compact('title', 'model', 'breadcum', 'row', 'providers','branches'));
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
        $this->validate($request, [
            'branch_id' => 'required',
        ]);
        if ($request->is_mpesa && $request->is_cash) {
            return back()->withErrors(['error' => 'A payment method cannot be both Mpesa and Cash.']);
        }

        try {

            $row = PaymentMethod::whereSlug($slug)->first();

            $row->title = strtoupper($request->title);
            $row->gl_account_id = $request->gl_account_id;
            $row->payment_provider_id = $request->payment_provider_id;
            $row->use_for_payments = $request->use_for_payments;
            $row->use_for_receipts = $request->use_for_receipts;
            $row->use_in_pos = $request->use_in_pos;
            $row->is_mpesa = $request->is_mpesa;
            $row->is_cash = $request->is_cash;
            $row->use_as_channel = $request->use_as_channel;
            $row->branch_id = $request->branch_id;


            $row->save();
            Session::flash('success', 'Record updated successfully.');
            return redirect()->route($this->model . '.index');


        } catch (\Exception $e) {

            $msg = $e->getMessage();
            Session::flash('warning', $msg);
            return redirect()->back()->withInput();
        }
    }


    public function destroy($slug)
    {
        try {

            PaymentMethod::whereSlug($slug)->delete();

            Session::flash('success', 'Deleted successfully.');
            return redirect()->back();
        } catch (\Exception $e) {

            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function getPaymentMethods(Request $request): JsonResponse
    {
        try {
            $paymentMethods = PaymentMethod::withWhereHas('provider')
                ->where('use_for_receipts', true)
                ->where('use_as_channel', false)
                ->get()
                ->map(function (PaymentMethod $paymentMethod) use ($request) {
                    $payload = [
                        'id' => $paymentMethod->id,
                        'title' => $paymentMethod->title,
                        'image' => env('APP_URL') . "/{$paymentMethod->provider->image}",
                        'require_phone_number' => false,
                        'phone_number_description' => "Phone Number",
                        'is_pay_later' => $paymentMethod->slug == 'pay-later',
                        'instructions' => [
                            'paybill_number' => 'N/A',
                            'account_number' => 'N/A',
                            'description' => 'Payment Instructions',
                        ],
                    ];

                    switch ($paymentMethod->provider->slug) {
                        case 'mpesa':
                            $payload['instructions']['paybill_number'] = '1002001';
                            if ($order = WaInternalRequisition::find($request->order_id)) {
                                $payload['instructions']['account_number'] = str_replace('INV-', '', $order->requisition_no);
                            }

                            break;
                        case 'kcb':
                            $payload['instructions']['paybill_number'] = '522533';
                            $payload['instructions']['account_number'] = '730435';

                            break;
                        default:
                            break;
                    }

                    return $payload;
                });

            return $this->jsonify(['data' => $paymentMethods], 200);
        } catch (\Throwable $e) {
            return $this->jsonify(['message' => $e->getMessage()], 500);
        }
    }
}
