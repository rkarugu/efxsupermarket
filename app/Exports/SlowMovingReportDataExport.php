<?php

namespace App\Exports;use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SlowMovingReportDataExport implements FromView, ShouldAutoSize
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