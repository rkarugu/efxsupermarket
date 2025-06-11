<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\User;
use App\Model\Route;
use App\Models\RouteRepresentatives;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class GroupRepresentativeController extends Controller
{
    protected string $model = 'route-group-rep';

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!can('view', $this->model)) {
            return returnAccessDeniedPage();
        }

        
        $latestRoutes = RouteRepresentatives::select('user_id', 'route_id', DB::raw('MAX(id) as latest_id'))
            ->groupBy('user_id', 'route_id');

        // Join with the original table to get the full records of the latest routes per user
        $latestRouteRecords = RouteRepresentatives::with('user','route')
            ->joinSub($latestRoutes, 'latest', function ($join) {
                $join->on('route_representatives.id', '=', 'latest.latest_id');
            })
            ->select('route_representatives.*') // Get all columns of the latest records
            ->get();

        // Group the results by user id (and later map for name if needed)
        $groupedByUser = $latestRouteRecords->groupBy(function ($item) {
            return $item->user->id; // Group by user ID
        });

        // Optional: If you want to map both user.id and user.name together
        $groupedByUserAndName = $groupedByUser->map(function ($group) {
            return [
                'user_id' => $group->first()->user->id,
                'user_name' => $group->first()->user->name,
                'routes' => $group 
            ];
        });
        //    dd($groupedByUserAndName); 
        $title = 'Group Representatives';
        $model = $this->model;
        $breadcrum = ['Sales & Receivables' => '', $title => ''];

        return view('admin.group_representative.index', compact('title', 'model', 'groupedByUserAndName'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if (!can('view', $this->model)) {
            return returnAccessDeniedPage();
        }

        $rep = RouteRepresentatives::with('user','route')->where('user_id',$id)->first();
        $routes = RouteRepresentatives::with('user','route')->where('user_id',$id)->get();
        $groupReps = User::where('role_id', 187)
        ->where('restaurant_id',$rep->user->restaurant_id)
        ->whereNot('id',$id)
        ->orderBy('name')
        ->get()->map(function($user){
            return [
                'id' => $user->id,
                'name' => $user->name,
            ];
        });
        $title = 'Group Representatives View';
        $model = $this->model;
        $breadcrum = ['Sales & Receivables' => '', $title => ''];

        $selectRoutes = $routes->pluck('route_id');
        $allRoutes = Route::whereNotIn('id',$selectRoutes)->get(); 

        return view('admin.group_representative.view', compact('title', 'model', 'rep','routes','allRoutes','groupReps'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function add_route(Request $request)
    {
        if (!can('add', $this->model)) {
            return returnAccessDeniedPage();
        }

        DB::beginTransaction();
        try {
            RouteRepresentatives::where('route_id',$request->route)->delete();
            RouteRepresentatives::create([
                'route_id' => $request->route,
                'user_id' => $request->repId,
                'created_by' => Auth::user()->id
            ]);
            DB::commit();
            $request->session()->flash('success', 'Route Added Successfully');
        } catch (\Throwable $e) {
            DB::rollBack();
            $request->session()->flash('danger', $e->getMessage());
        }

        return redirect()->back();

    }

    /**
     * Update the specified resource in storage.
     */
    public function ressign_route(Request $request)
    {
        if (!can('reassign', $this->model)) {
            return returnAccessDeniedPage();
        }

        DB::beginTransaction();
        try {
            
            $route = RouteRepresentatives::find($request->routeId);
            
            RouteRepresentatives::create([
                'route_id' => $route->route_id,
                'user_id' => $request->new_rep,
                'created_by' => Auth::user()->id
            ]);
            $route->delete();
            
            DB::commit();
            $request->session()->flash('success', 'Route Ressigned Successfully');
            return redirect(route('group-rep.index'));
        } catch (\Throwable $e) {
            DB::rollBack();
            $request->session()->flash('danger', $e->getMessage());
            return redirect()->back();
        }
        
    }

    public function ressign_all_routes(Request $request)
    {
        if (!can('reassign', $this->model)) {
            return returnAccessDeniedPage();
        }

        DB::beginTransaction();
        try {
            
            $routes = RouteRepresentatives::where('user_id',$request->repId)->get()->pluck('route_id');
            
            RouteRepresentatives::where('user_id',$request->repId)->delete();
            foreach ($routes as $route) {
                RouteRepresentatives::create([
                    'route_id' => $route,
                    'user_id' => $request->new_rep,
                    'created_by' => Auth::user()->id
                ]);    
            }
            
            DB::commit();
            $request->session()->flash('success', 'Route Ressigned Successfully');
            return redirect(route('group-rep.index'));
        } catch (\Throwable $e) {
            DB::rollBack();
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
