<?php

namespace App\Exports;use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class SalesItemsDataExport implements FromView
{
    protected $view;

    /**
     * Purchases constructor.
     * @param $view
     */
    public function __construct($view)
    {
        $this->view = $view;
    }

    public function view(): View
    {
        return $this->view;
    }
}