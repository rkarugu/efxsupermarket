<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Models\PromotionType;
use Illuminate\Http\Request;

class PromotionTypeController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;
    public function __construct()
    {
        $this->model = 'promotion-types';
        $this->title = 'Promotion Types';
        $this->pmodule = 'utility';
    }
    public function index()
    {
        $promotionTypes = PromotionType::latest()->get();
            return view('admin.inventory.item.promotion-type', [
            'model' => $this->model,
            'promotionTypes' => $promotionTypes,
            'title' => $this->title,
        ]);
    }

    public function store(Request $request)
    {
       $data =  $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
        ]);

        PromotionType::create($data);
        return response()->json(['success' => 'Promotion Type added successfully.']);
    }

    public function update(Request $request, $id)
    {
       $data =  $request->validate([
            'name' => 'required|string|max:255',
           'description' => 'required|string|max:255',
        ]);

        $promotionType = PromotionType::findOrFail($id);
        $promotionType->update($data);
        return response()->json(['success' => 'Promotion Type updated successfully.']);
    }

    public function destroy($id)
    {
        $promotionType = PromotionType::findOrFail($id);
        $promotionType->delete();

        return response()->json(['success' => 'Promotion Type deleted successfully.']);
    }

}
