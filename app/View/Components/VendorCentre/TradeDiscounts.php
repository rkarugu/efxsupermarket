<?php

namespace App\View\Components\VendorCentre;

use App\Model\WaSupplier;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class TradeDiscounts extends Component
{
    protected $months = [
        "January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"
    ];

    public function __construct(
        protected int $supplierId
    ) {
    }

    public function render(): View|Closure|string
    {
        $year = date('Y');
        $years[] = $year;
        for ($i = 0; $i < 5; $i++) {
            $years[] = ($year -= 1);
        }

        return view('components.vendor-centre.trade-discounts.index', [
            'supplier' => WaSupplier::find($this->supplierId),
            'months' => $this->months,
            'years' => $years
        ]);
    }
}
