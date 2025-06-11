<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OperationShift;
use App\Services\OperationShiftService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OperationShiftController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;
    public function __construct()
    {
        $this->model = 'operation-shift';
        $this->title = 'End Of Day Operation';
        $this->pmodule = 'operation-shift';
    }
    public function index(Request $request)
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;

        $date = $request->input('date', Carbon::yesterday()->toDateString());
        $operationalShifts = OperationShift::with('shiftChecks.checkDetails','branch')
//            ->whereDate('date', $date)
            ->orderByDesc('id')
            ->get();
        return view('admin.operation_shifts.index', compact('operationalShifts','permission','pmodule','title','model'));
    }

    public function show($id)
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $operationalShift = OperationShift::with('branch','shiftChecks','shiftChecks.checkDetails')->findOrFail($id);
        return view('admin.operation_shifts.show', compact('operationalShift','permission','pmodule','title','model'));
    }

    public function override($id)
    {
        $shift = OperationShift::findOrFail($id);
        $shift->manual_override = true;
        $shift->authorised_by = Auth::id();
        $shift->authorised_time = now();
        $shift->save();

        return redirect()->route('operation_shifts.index')->with('status', 'Shift manually overridden.');
    }

    public function rerun($id)
    {
        $shift = OperationShift::with('branch')->findOrFail($id);
        // Check if the shift is balanced
        if ($shift->balanced) {
            return redirect()->back()->with('warning', 'Shift is already balanced and cannot be re-run.');
        }

        // Check if the shift date is in the future
        if (Carbon::parse($shift->date)->isFuture()) {
            return redirect()->back()->with('warning', 'Cannot re-run a shift for a future date.');
        }

        // Call the service to re-run the shift logic
        $operationShiftService = new OperationShiftService($shift->branch, $shift);
        $operationShiftService->index();

        return redirect()->route('operation_shifts.show', $shift->id)->with('success', 'Shift re-run successfully.');
    }
}
