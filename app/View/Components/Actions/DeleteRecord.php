<?php

namespace App\View\Components\Actions;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\Support\Str;

class DeleteRecord extends Component
{
    public function __construct(
        protected $action
    ) {}

    public function render(): View|Closure|string
    {
        return view('components.actions.delete-record', [
            'action' => $this->action,
            'identifier' => Str::random(6),
        ]);
    }
}
