<?php

namespace App\Http\Controllers\Admin;

use App\ProductionProcess;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Throwable;

class ProductionProcessController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct(Request $request)
    {
        $this->model = 'processes';
        $this->title = 'Production Processes';
        $this->pmodule = 'processes';
    }

    /**
     * Display a listing of processes
     *
     * @return Factory|Application|View
     */
    public function index()
    {
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $breadcum = [$title => route($model . '.index'), 'Listing' => ''];

        return view('admin.processes.index', compact('title', 'model', 'breadcum', 'pmodule'));
    }

    /**
     * Fetches stored processes
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function datatable(Request $request): JsonResponse
    {
        $permission = $this->mypermissionsforAModule();
        $limit = $request->input('length');
        $start = $request->input('start');
        $pmodule = $this->pmodule;

        $totalData = ProductionProcess::count();
        $processes = ProductionProcess::select(['id', 'operation', 'description', 'notes', 'status'])->offset($start)->limit($limit)->get();
        $records = [];
        foreach ($processes as $process) {
            $processPayload = [
                'operation' => $process->operation,
                'description' => $process->description ?? '-',
                'notes' => $process->notes ?? '-',
                'status' => ucfirst($process->status),
            ];

            $actionContent = "";
            if (isset($permission[$pmodule . '___edit']) || $permission == 'superadmin') {
                $actionContent .= buttonHtmlCustom('edit_process', route($this->model . '.edit', $process->id));
            }

            if (isset($permission[$pmodule . '___delete']) || $permission == 'superadmin') {
                $actionContent .= buttonHtmlCustom('remove_process', route($this->model . '.destroy', $process->id));
            }

            $processPayload['actions'] = $actionContent;
            $records[] = $processPayload;
        }

        $responsePayload = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => $totalData,
            "recordsFiltered" => $totalData,
            "data" => $records
        );

        return response()->json($responsePayload);
    }

    /**
     * Show the form for creating a new process.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        $pmodule = $this->pmodule;
        $title = 'Add Process';
        $model = $this->model;
        $breadcum = ['Processes' => route($model . '.index'), 'Add' => ''];

        return view('admin.processes.create', compact('title', 'model', 'breadcum', 'pmodule'));
    }

    /**
     * Store a newly created process in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $this->validate($request, [
            'operation' => 'required',
        ]);

        ProductionProcess::create([
            'operation' => $request->operation,
            'description' => $request->description,
            'notes' => $request->notes,
            'status' => $request->status,
        ]);

        $request->session()->flash('success', 'Process added successfully.');
        return redirect()->route($this->model . '.index');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified process.
     *
     * @param int $id
     * @return Factory|Application|RedirectResponse|View
     */
    public function edit(int $id)
    {
        $permission = $this->mypermissionsforAModule();
        if (isset($permission[$this->pmodule . '___edit']) || $permission == 'superadmin') {
            $process = ProductionProcess::find($id);
            if (!$process) {
                Session::flash('warning', 'Invalid Request');
                return redirect()->back();
            }

            $title = 'Edit Process';
            $breadcum = [$this->title => route($this->model . '.index'), 'Edit' => ''];
            $model = $this->model;

            return view('admin.processes.edit', compact('title', 'model', 'breadcum', 'process'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    /**
     * Update the specified process in storage.
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $this->validate($request, [
            'operation' => 'required',
        ]);

        try {
            $process = ProductionProcess::find($id);
            $process->update([
                'operation' => $request->operation,
                'description' => $request->description,
                'notes' => $request->notes,
                'status' => $request->status,
            ]);

            $request->session()->flash('success', 'Process updated successfully.');
            return redirect()->route($this->model . '.index');
        } catch (Throwable $e) {
            // TODO: Report error
            Session::flash('danger', "An error was encountered. Please try again.");
            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified process from storage.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            $process = ProductionProcess::find($id);
            $process->delete();

            Session::flash('success', 'Process deleted successfully.');
            return redirect()->route($this->model . '.index');
        } catch (Throwable $e) {
            Session::flash('danger', "An error was encountered. Please try again.");
            return redirect()->back()->withInput();
        }
    }
}
