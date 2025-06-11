<?php

namespace App\Http\Controllers\Admin\pos;

use App\DiscountBand;
use App\Http\Controllers\Controller;
use App\ItemPromotion;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ActiveDiscountsPromotionsController extends Controller
{
    protected $model;
    protected $base_route;
    protected $resource_folder;
    protected $base_title;
    protected $permissions_module;

    public function __construct()
    {
        $this->model = 'pos-cash-sales';
        $this->base_route = 'active-discounts-promotions';
        $this->base_title = 'Active Promotions And Discounts';
        $this->permissions_module = 'pos-cash-sales';
    }

    public function index(Request $request)
    {
        $permission =  $this->mypermissionsforAModule();
        $title = $this->base_title;
        $model = $this->model;
        if (!can('promotions_discounts',$model)){
            returnAccessDeniedPage();
        }
        $today = Carbon::today();
        $search= $request->search ?? '';

        $promotions = ItemPromotion::where('status', 'active')
            ->where(function ($query) use ($today) {
                $query->where('from_date', '<=', $today)
                    ->where('to_date', '>=', $today);
            })
            ->whereHas('inventoryItem', function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%");
            })
            ->with('inventoryItem')
            ->with('promotionItem')
            ->get();
        $discounts = DiscountBand::with('inventoryItem:id,title')
            ->with('inventoryItem')
            ->where('status','APPROVED')
            ->whereHas('inventoryItem', function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%");
            })
            ->get();

        return view('admin.pos_cash_sales.discount_promotions', compact('promotions','discounts','title','permission','model'));
    }
}
