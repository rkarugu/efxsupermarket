<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\PaymentProvider;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class PaymentProviderController extends Controller
{
    protected $model;
    protected $base_route;
    protected $resource_folder;
    protected $base_title;

    public function __construct()
    {
        $this->model = 'payment-providers';
        $this->base_route = 'payment-providers';
        $this->resource_folder = 'admin.payment_providers';
        $this->permissions_module = 'payment-providers';
        $this->base_title = 'Payment Providers';
    }

    public function index(): View | RedirectResponse
    {
        $title = $this->base_title;
        $model = $this->model;
        $permissions_module = $this->permissions_module;

        if (!can('view', $permissions_module)) {
            return redirect()->back()->withErrors(['message' => pageRestrictedMessage()]);
        }

        $breadcum = [$this->base_title => route("$this->base_route.index"), 'Listing' => ''];
        $base_route = $this->base_route;

        $providers = PaymentProvider::all();
        return view("$this->resource_folder.index", compact('title', 'model', 'breadcum', 'base_route', 'providers', 'permissions_module'));

    }

    public function create(): View | RedirectResponse
    {
        $title = "$this->base_title | Add New";
        $model = $this->model;

        if (!can('add', $this->permissions_module)) {
            return redirect()->back()->withErrors(['message' => pageRestrictedMessage()]);
        }

        $breadcum = [$this->base_title => route("$this->base_route.index"), 'Add' => ''];
        $base_route = $this->base_route;

        return view("$this->resource_folder.create", compact('title', 'model', 'breadcum', 'base_route'));

    }

    public function store(Request $request): RedirectResponse
    {
        try {
            $uploadPath = 'uploads/payment_providers';
            $file = $request->file('image');
            if (!file_exists($uploadPath)) {
                File::makeDirectory($uploadPath, $mode = 0777, true, true);
            }

            $name = $file->hashName();
            $file->move(public_path($uploadPath), $name);
            $provider = PaymentProvider::create(['name' => $request->name, 'image' => "$uploadPath/$name"]);

            return redirect()->route("$this->base_route.index")->with('success', 'Payment provider added successfully');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors(['message' => $e->getMessage()]);
        }
    }

    public function edit(PaymentProvider $paymentProvider): View | RedirectResponse
    {
        $title = "$this->base_title | Edit";
        $model = $this->model;

        if (!can('edit', $this->permissions_module)) {
            return redirect()->back()->withErrors(['message' => pageRestrictedMessage()]);
        }

        $breadcum = [$this->base_title => route("$this->base_route.index"), 'Edit' => ''];
        $base_route = $this->base_route;

        return view("$this->resource_folder.edit", compact('title', 'model', 'breadcum', 'base_route', 'paymentProvider'));
    }

    public function update(Request $request, PaymentProvider $paymentProvider): RedirectResponse
    {
        try {
            $data = ['name' => $request->name];
            
            if ($request->hasFile('image')) {
                $uploadPath = 'uploads/payment_providers';
                if (!file_exists($uploadPath)) {
                    File::makeDirectory($uploadPath, $mode = 0777, true, true);
                }

                // Delete old image if exists
                if ($paymentProvider->image && file_exists(public_path($paymentProvider->image))) {
                    File::delete(public_path($paymentProvider->image));
                }

                $file = $request->file('image');
                $name = $file->hashName();
                $file->move(public_path($uploadPath), $name);
                $data['image'] = "$uploadPath/$name";
            }

            $paymentProvider->update($data);

            return redirect()->route("$this->base_route.index")->with('success', 'Payment provider updated successfully');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors(['message' => $e->getMessage()]);
        }
    }
}
