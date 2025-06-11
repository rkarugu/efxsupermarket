<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\User;
use App\Models\SupportTeam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class HelpDeskSupportController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct()
    {
        $this->model = 'support-team';
        $this->title = 'Support Team';
        $this->pmodule = 'support-team';
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!can('view', 'support-team')) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }

        $title = $this->title;
        $model= $this->model;
        

        $permissions = $this->mypermissionsforAModule();
        $breadcum = [ 'Help Desk Setup'=> '', $title => ''];

        $authuser = Auth::user();
        $isAdmin = $authuser->role_id == 1;
        $permission = $this->mypermissionsforAModule();

        if (request()->wantsJson()) {
            $query = SupportTeam::with('user');
            $tickets = $query->orderBy('created_at','DESC');
            return DataTables::of($tickets)
                    ->addIndexColumn()
                    ->editColumn('get_notifications',function($support){
                       return $support->get_notifications==1? 'Yes': 'No';
                    })
                    ->toJson();
        }

        return view('admin.support_team.index', compact('title', 'model', 'breadcum'));
    }

    public function create()
    {
        if (!can('add', 'support-team')) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }

        $title = $this->title;
        $model= $this->model;

        $breadcum = [ 'System Setup'=> '', 'New Support Team' => ''];
        $users = User::all();

        return view('admin.support_team.create', compact('title', 'model', 'breadcum','users'));
    }

    public function store(Request $request)
    {
        if (!can('add', 'support-team')) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }
        
        DB::beginTransaction();
        try {
            SupportTeam::create([
                'created_by' => Auth::user()->id,
                'user_id' => $request->user,
                'get_notifications' => $request->notification
            ]);

            DB::commit();
            $request->session()->flash('success', 'Support Team Added Successfully');
            return redirect(route('support-team.index'));
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
        if (!can('edit', 'support-team')) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }

        $title = $this->title;
        $model= $this->model;

        $breadcum = [ 'Help Desk Setup'=> '', 'Edit Support Team' => ''];

        $support = SupportTeam::find($id);

        return view('admin.support_team.edit', compact('title', 'model', 'breadcum','support'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if (!can('edit', 'support-team')) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }
        
        DB::beginTransaction();
        try {
            $support = SupportTeam::find($id);
            $support->get_notifications = $request->notification;
            $support->save();

            DB::commit();
            $request->session()->flash('success', 'Support Team Updated Successfully');
            return redirect(route('support-team.index'));
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
    public function delete(string $id)
    {
        if (!can('delete', 'support-team')) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }
        
        DB::beginTransaction();
        try {
            $support = SupportTeam::find($id);
            $support->delete();

            DB::commit();
            request()->session()->flash('success', 'Support Team Deleted Successfully');
            return redirect(route('support-team.index'));
        } catch (\Exception $e) {
            DB::rollBack();
            request()->session()->flash('danger', $e->getMessage());
            return redirect()->back();
        }
        
    }
}
