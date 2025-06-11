<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ExportViewToExcel implements FromView
{
    public function __construct(
        protected View $view
    ) {
    }

    public function view(): View
    {
        return $this->view;
    }
}
