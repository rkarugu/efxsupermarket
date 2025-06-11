<?php

namespace App\Http\Controllers\Admin;

use App\Model\WaInventoryItem;
use App\ProductionProcess;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class InventoryItemProcessController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct(Request $request)
    {
        $this->model = 'maintain-items';
        $this->title = 'Maintain Items';
        $this->pmodule = 'maintain-items';

    }

    public function index(int $itemId)
    {
        $pmodule = $this->pmodule;
        $model = $this->model;
        $title = $this->title;
        $breadcum = [$title => route($model . '.index'), 'Processes' => ''];

        $inventoryItem = WaInventoryItem::with('processes')->select(['id', 'title'])->find($itemId);
        if (!$inventoryItem) {
            return redirect()->back()->with('warning', 'Invalid Request');
        }

        $operationSteps = [];
        foreach ($inventoryItem->processes as $process) {
            $operationSteps[] = [
                'process_id' => $process->id,
                'step_number' => $process->pivot->step_number,
                'name' => $process->operation,
                'duration' => CarbonInterval::minutes($process->pivot->duration)->cascade()->forHumans(),
                'quality_control_check' => $process->pivot->quality_control_check ? 'Yes' : 'No',
            ];
        }

        $operationSteps = collect($operationSteps)->sortBy('step_number')->values()->all();

        $title = "$title Operation Steps";
        return view('admin.maintaininvetoryitems.operation-steps.index', compact('pmodule', 'model', 'title', 'breadcum', 'inventoryItem', 'operationSteps'));
    }

    public function create(int $itemId)
    {
        $pmodule = $this->pmodule;
        $model = $this->model;
        $title = $this->title;
        $breadcum = [$title => route($model . '.index'), 'Operation Steps' => route($model . '.operation-steps.index', $itemId), 'Add Step' => ''];

        $inventoryItem = WaInventoryItem::with('processes')->select(['id', 'title'])->find($itemId);
        if (!$inventoryItem) {
            return redirect()->back()->with('warning', 'Invalid Request');
        }

        $productionProcesses = ProductionProcess::active()->get();

        // TODO: Find a way to use collections to filter. More efficient and readable.
        $productionProcesses = $productionProcesses->filter(function (ProductionProcess $process) use ($inventoryItem) {
            $addedProcesses = $inventoryItem->processes;
            $processIsAlreadyAdded = false;
            foreach ($addedProcesses as $addedProcess) {
                if ($addedProcess->id == $process->id) {
                    $processIsAlreadyAdded = true;
                    break;
                }
            }

            return !$processIsAlreadyAdded;
        });

        $currentStep = 1;
        if (count($inventoryItem->processes) != 0) {
            $lastProcess = $inventoryItem->processes->last();
            $currentStep = $lastProcess->pivot->step_number + 1;
        }

        $title = "$title Processes";
        return view('admin.maintaininvetoryitems.operation-steps.create', compact('pmodule', 'model', 'title', 'breadcum', 'inventoryItem', 'productionProcesses', 'currentStep'));
    }

    public function store(int $itemId, Request $request)
    {
        $inventoryItem = WaInventoryItem::with('processes')->find($itemId);
        if (!$inventoryItem) {
            return redirect()->back()->with('warning', 'Invalid Request');
        }

        $validator = Validator::make($request->all(), [
            'operation_step_id' => 'required',
            'step_number' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator->errors());
        }

        $process = ProductionProcess::find($request->operation_step_id);
        if (!$process) {
            return redirect()->back()->with('warning', 'Invalid Request');
        }

        $inventoryItem->processes()->attach($process->id, [
            'step_number' => $request->step_number,
            'duration' => $request->duration ?? 0,
            'quality_control_check' => $request->quality_control_check ?? false,
        ]);

        return redirect()->route("$this->model.operation-steps.index", $inventoryItem->id)->with('success', 'Operation step added successfully');
    }

    public function edit(int $itemId, int $processId)
    {
        $pmodule = $this->pmodule;
        $model = $this->model;
        $title = $this->title;
        $breadcum = [$title => route($model . '.index'), 'Operation Steps' => route($model . '.operation-steps.index', $itemId), 'Update Step' => ''];

        $inventoryItem = WaInventoryItem::with('processes')->select(['id', 'title'])->find($itemId);
        if (!$inventoryItem) {
            return redirect()->back()->with('warning', 'Invalid Request');
        }

        $process = $inventoryItem->processes()->wherePivot('production_process_id', '=', $processId)->first();
        if (!$process) {
            return redirect()->back()->with('warning', 'Invalid Request');
        }

        $step = $process->pivot;
        $title = "Update Operation Step";
        return view('admin.maintaininvetoryitems.operation-steps.edit', compact('pmodule', 'model', 'title', 'breadcum', 'inventoryItem',
            'process', 'step'));
    }

    public function update(int $itemId, int $processId, Request $request)
    {
        $inventoryItem = WaInventoryItem::with('processes')->select(['id', 'title'])->find($itemId);
        if (!$inventoryItem) {
            return redirect()->back()->with('warning', 'Invalid Request');
        }

        $inventoryItem->processes()->updateExistingPivot($processId, [
            'duration' => $request->duration ?? 0,
            'quality_control_check' => $request->quality_control_check ?? false,
        ]);

        return redirect()->route("$this->model.operation-steps.index", $inventoryItem->id)->with('success', 'Operation step updated successfully');
    }

    public function delete(int $itemId, int $processId)
    {
        $inventoryItem = WaInventoryItem::with('processes')->select(['id', 'title'])->find($itemId);
        if (!$inventoryItem) {
            return redirect()->back()->with('warning', 'Invalid Request');
        }

        $inventoryItem->processes()->detach($processId);
        return redirect()->route("$this->model.operation-steps.index", $inventoryItem->id)->with('success', 'Operation step removed successfully');
    }
}
