<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TicketCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TicketCategoryController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct()
    {
        $this->model = 'ticket-category';
        $this->title = 'Ticket Category';
        $this->pmodule = 'ticket-category';
    }

    public function index()
    {
        if (!can('view', 'ticket-category')) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }

        $title = $this->title;
        $model= $this->model;
        

        $permissions = $this->mypermissionsforAModule();
        $breadcum = [ 'System Setup'=> '', $title => ''];

        $authuser = Auth::user();
        $isAdmin = $authuser->role_id == 1;
        $permission = $this->mypermissionsforAModule();

        if (request()->wantsJson()) {
            $query = TicketCategory::query();
            $tickets = $query->orderBy('created_at','DESC');
            return DataTables::of($tickets)
                    ->addIndexColumn()
                    ->editColumn('created_at',function($ticket){
                        return date('d-m-Y H:i',strtotime($ticket->created_at));
                    })
                    ->toJson();
        }

        return view('admin.ticket_category.index', compact('title', 'model', 'breadcum'));

    }

    public function create()
    {
        if (!can('add', 'ticket-category')) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }

        $title = $this->title;
        $model= $this->model;

        $breadcum = [ 'System Setup'=> '', 'New Ticket Category' => ''];

        return view('admin.ticket_category.create', compact('title', 'model', 'breadcum'));
    }

    public function store(Request $request)
    {
        if (!can('add', 'ticket-category')) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }
        
        DB::beginTransaction();
        try {
            TicketCategory::create([
                'created_by' => Auth::user()->id,
                'title' => $request->title
            ]);

            DB::commit();
            $request->session()->flash('success', 'Ticket Category Added Successfully');
            return redirect(route('ticket-category.index'));
        } catch (\Exception $e) {
            DB::rollBack();
            $request->session()->flash('danger', $e->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        if (!can('edit', 'ticket-category')) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }

        $title = $this->title;
        $model= $this->model;

        $breadcum = [ 'System Setup'=> '', 'Edit Ticket Category' => ''];

        $category = TicketCategory::find($id);

        return view('admin.ticket_category.edit', compact('title', 'model', 'breadcum','category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if (!can('edit', 'ticket-category')) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }
        
        DB::beginTransaction();
        try {
            $category = TicketCategory::find($id);
            $category->title = $request->title;
            $category->save();

            DB::commit();
            $request->session()->flash('success', 'Ticket Category Updated Successfully');
            return redirect(route('ticket-category.index'));
        } catch (\Exception $e) {
            DB::rollBack();
            // dd($e->getMessage());
            $request->session()->flash('danger', $e->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
