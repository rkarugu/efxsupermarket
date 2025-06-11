<?php

namespace App\Livewire\Inventory\Promotions;

use App\Model\PackSize;
use App\Model\WaInventoryCategory;
use App\Model\WaInventoryItem;
use App\View\Components\ItemCentre\InventoryItems;
use Livewire\Component;
use function Laravel\Prompts\alert;

class HamperComponent extends Component
{
    public $step = 1;
    public $hamper = [
        'name' => '',
        'code' => '',
        'category' => '',
        'packsize' => '',
        'maximum_order_quantity' => '',
        'tax_category' => '',
        'image' => '',
        'is_blocked' => false,
    ];
    public $inventoryItems = [];
    public $items = [];
    public $hamper_items = 1;
    public $branches = [];
    public $promotion_duration = '';
    public $search = '';

    public $categories;
    public $packsizes;
    protected $rules = [
        'hamper.name' => 'required|string|max:255',
        'hamper.code' => 'required|string|max:255',
        'hamper.category' => 'required|string',
        'hamper.packsize' => 'required|string',
        'hamper.maximum_order_quantity' => 'required|integer',
        'hamper.tax_category' => 'required|string',
        'hamper.image' => 'nullable|image|max:1024',
        'hamper.is_blocked' => 'boolean',
        'items.*.name' => 'required|string|max:255',
        'items.*.selling_price' => 'required|numeric',
        'items.*.cost' => 'required|numeric',
        'items.*.qty' => 'required|integer',
        'branches' => 'required|string',
        'promotion_duration' => 'required|integer|min:1',
    ];

    public function mount()
    {
        $this->categories = WaInventoryCategory::pluck('category_description','id');
        $this->packsizes = PackSize::pluck('title','id');
        $this->inventoryItems = WaInventoryItem::all();
    }


    public function addItem()
    {
        $this->items[] = ['name' => '', 'selling_price' => '', 'cost' => '', 'qty' => ''];
    }

    public function valid()
    {

        if ($this->step == 1)
        {
            $this->validate([
                'hamper.name' => 'required|string|max:255',
                'hamper.code' => 'required|string|max:255',
                'hamper.category' => 'required|string',
                'hamper.packsize' => 'required|string',
                'hamper.maximum_order_quantity' => 'required|integer',
                'hamper.image' => 'nullable|image|max:1024',
                'hamper.is_blocked' => 'boolean',
            ]);
            if ($this->hamper_items != $this->items)
            {
                $k = $this->hamper_items - $this->items;
                for ($k; $k <= $this->hamper_items; $k++)
                {
                    $this->items[] = ['name' => '', 'selling_price' => '', 'cost' => '', 'qty' => ''];
                }
            }

        }
        if ($this->step == 2)
        {


            $this->validate([
                'items.*.id' => 'required',
                'items.*.selling_price' => 'required|numeric',
                'items.*.cost' => 'required|numeric',
                'items.*.qty' => 'required|integer',
            ],
                [
                    'items.*.id.required' => 'The item name is required.',
                    'items.*.selling_price.required' => 'The selling price is required.',
                    'items.*.selling_price.numeric' => 'The selling price must be a number.',
                    'items.*.cost.required' => 'The cost is required.',
                    'items.*.cost.numeric' => 'The cost must be a number.',
                    'items.*.qty.required' => 'The quantity is required.',
                    'items.*.qty.integer' => 'The quantity must be an integer.',
                ]);
        }
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function nextStep()
    {
//        $this->valid();
        $this->step++;
    }

    public function prevStep()
    {
        $this->step--;
    }

    public function submit()
    {
        // Handle form submission
    }
    public function render()
    {

        return view('livewire.inventory.promotions.hamper-component');
    }
}
