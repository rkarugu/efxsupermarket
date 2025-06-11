<?php

namespace App\View\Components;

use App\Models\ReportedNewItem;
use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class NewItemsTab extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $today = Carbon::now()->toDateString();
        $user = Auth::user();
        $reportedItems = ReportedNewItem::select('reported_new_items.*', 'users.name')
            ->leftJoin('users', 'users.id', 'reported_new_items.reported_by')
            ->whereDate('reported_new_items.created_at', $today)
            ->where('reported_new_items.reported_by', $user->id)
            ->get();
        return view('components.new-items-tab',['reportedItems' => $reportedItems]);
    }
}
