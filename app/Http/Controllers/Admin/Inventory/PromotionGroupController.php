<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Models\PromotionGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PromotionGroupController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;
    public function __construct()
    {
        $this->model = 'promotion-groups';
        $this->title = 'Promotion Groups';
        $this->pmodule = 'utility';
    }
    public function index()
    {
        $promotionGroups = PromotionGroup::latest()->get();
        return view('admin.inventory.item.promotion-group', [
            'model' => $this->model,
            'promotionGroups' => $promotionGroups,
            'title' => $this->title,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'active' => 'required',
            'start_time' => 'required|date_format:Y-m-d',
            'end_time' => 'required|date_format:Y-m-d|after_or_equal:start_time',
        ]);
        $validated['end_time'] = $validated['end_time'] . ' 23:59:59';

        /*create promotion*/
        PromotionGroup::create($validated);
        return response()->json(['success' => 'Promotion Group added successfully.']);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'active' => 'required',
            'start_time' => 'required|date_format:Y-m-d',
            'end_time' => 'required|date_format:Y-m-d|after_or_equal:start_time',
        ]);

        $validated['end_time'] = $validated['end_time'] . ' 23:59:59';
        $validated['active'] = $validated['active'] ? 1 : 0;
        $validated['created_by'] = Auth::id();
        $promotionType = PromotionGroup::findOrFail($id);
        $promotionType->update($validated);
        return response()->json(['success' => 'Promotion Group updated successfully.']);
    }

    public function destroy($id)
    {
         PromotionGroup::destroy($id);

        return response()->json(['success' => 'Promotion Group deleted successfully.']);
    }
}
