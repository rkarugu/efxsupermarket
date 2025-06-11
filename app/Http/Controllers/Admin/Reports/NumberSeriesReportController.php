<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Http\Controllers\Controller;
use App\Models\OperationShift;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use PhpParser\Node\Stmt\DeclareDeclare;

class NumberSeriesReportController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;
    public function __construct()
    {
        $this->model = 'number-series-utility';
        $this->title = 'Number Series Report';
        $this->pmodule = 'number-series-utility';
    }

    public function missingInvoices(Request $request)
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = 'Missing Invoice Series Numbers';
        $model = $this->model;

        if (!can('missing_invoice_series_numbers', $permission)) {
            Session::flash('warning', pageRestrictedMessage());
            return redirect()->back();
        }

        $date = $request->input('date', Carbon::today()->toDateString());

        $requisitionNumbers = DB::table('wa_internal_requisitions')
            ->whereDate('requisition_date', $date)
            ->pluck('requisition_no');

        $items = $requisitionNumbers->map(function ($number) {
            // Use regex to capture digits after "INV-"
            preg_match('/INV-(\d+)/', $number, $matches);
            return $matches[1]; // Return the captured digits
        });





        $last =(int) $items->last();
        $first = (int)$items->first();
        $count  = $items -> count();
        $expected = $last - $first;
        $variance = $expected - $count;


        if ($variance == 0) {
            $missingNumbers = [];
            return view('admin.numberseries.report.index', compact('missingNumbers','permission','pmodule','title','model'));;
        }

        $numbers = range($first, $last);


        $missingNumbers = array_diff($numbers, $items->toArray());


        return view('admin.numberseries.report.index', compact('missingNumbers','permission','pmodule','title','model'));
    }
}
