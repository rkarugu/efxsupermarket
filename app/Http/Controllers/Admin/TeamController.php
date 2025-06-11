<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\Restaurant;
use App\Model\User;
use App\Team;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Loader;
use App\Model\Route;
use App\TeamMember;
use App\TeamRoute;

class TeamController extends Controller
{
    protected $model;
    protected $base_route;
    protected $resource_folder;
    protected $base_title;
    protected $permissions_module;

    public function __construct()
    {
        $this->model = 'teams';
        $this->base_route = 'teams';
        $this->resource_folder = 'admin.teams';
        $this->base_title = 'Teams';
        $this->permissions_module = 'teams';
    }

    public function index(): View|RedirectResponse
    {
        $title = $this->base_title;
        $model = $this->model;

        if (!can('view', $this->permissions_module)) {
            return redirect()->back()->withErrors(['message' => pageRestrictedMessage()]);
        }

        $breadcum = [$this->base_title => route("$this->base_route.index"), 'Listing' => ''];
        $base_route = $this->base_route;
        $permissions_module = $this->permissions_module;

        $teams = Team::with(['members', 'routes'])->get();
        $data = [];
        foreach ($teams as $team) {
            $teamData=[];
            $teamData['id'] = $team->id;
            $teamData['team_name'] = $team->team_name;
            $teamData['branch_name'] = Restaurant::find($team->restaurant_id)->name ??  '-';
            $teamData['team_leader'] = User::find($team->team_leader_id)->name;
            $members=[];
            foreach ($team->members as $member) {
                $members[] = Loader::find($member->loader_id)->name;
            }
            $teamData['members'] = $members;
            $routes = [];
            foreach ($team->routes as $route) {
                $routes[] = Route::find($route->route_id)->route_name;
            }
            $teamData['routes'] = $routes;
            $data[] = $teamData;
        }
        

        return view("$this->resource_folder.index", compact(
            'title',
            'model',
            'breadcum',
            'base_route',
            'teams',
            'data',
            'permissions_module'
        ));
    }

    public function create(): View|RedirectResponse
    {
        $title = $this->base_title;
        $model = $this->model;

        if (!can('add', $this->permissions_module)) {
            return redirect()->back()->withErrors(['message' => pageRestrictedMessage()]);
        }

        $breadcum = [$this->base_title => route("$this->base_route.index"), 'Add' => ''];
        $base_route = $this->base_route;

        $users = User::select('id', 'name')->get();

        $loaders = Loader::leftJoin('team_members', 'loaders.id', '=', 'team_members.loader_id')
        ->whereNull('team_members.team_id')
        ->select('loaders.*')
        ->get();

        $routes = Route::select('id', 'route_name')->get();
        $branches = Restaurant::select('id', 'name')->get();

        return view("$this->resource_folder.create", compact(
            'title',
            'model',
            'breadcum',
            'base_route',
            'loaders',
            'users',
            'routes',
            'branches'
        ));
    }
    public function store(Request $request){

        try{
            $team = Team::create(
                ['team_name'=>$request->team_name,
                'team_leader_id'=>$request->team_leader_id,
                'restaurant_id'=>$request->branch_id
                ]
            );
            foreach ($request->team_member_id as $teamMember) {
                TeamMember::create([
                    'team_id'=>$team->id,
                    'loader_id'=>$teamMember
                ]);

            }
            foreach ($request->team_route_id as $route) {
                TeamRoute::create([
                    'team_id'=>$team->id,
                    'route_id'=>$route

                ]);
            }
            return redirect()->route("teams.index")->with('success', 'Team Created successfully' );
    
            }catch(\Throwable $e){
                return redirect()->back()->withErrors(['errors' => $e->getMessage()]);
    
            }

    }
    public function edit($id): View|RedirectResponse
    {
        $title = $this->base_title;
        $model = $this->model;

        if (!can('edit', $this->permissions_module)) {
            return redirect()->back()->withErrors(['message' => pageRestrictedMessage()]);
        }

        $breadcum = [$this->base_title => route("$this->base_route.index"), 'Add' => ''];
        $base_route = $this->base_route;
        $team = Team::find($id);
        $teamLeader = User::find($team->team_leader_id);
        // $teamMembers = TeamMember::where('team_id', $team->id)->get();
        $users = User::select('id', 'name')->get();


        $loaders = Loader::all();
        $selectedLoaders = TeamMember::select('loader_id')->where('team_id',  $team->team_id)->get();
        $routes = Route::select('id', 'route_name')->get();
        $branches = Restaurant::select('id', 'name')->get();
        $selectedBranch = Restaurant::find($team->restaurant_id);


        return view("$this->resource_folder.edit", compact(
            'title',
            'model',
            'breadcum',
            'base_route',
            'loaders',
            'users',
            'routes',
            'team',
            'teamLeader',
            'branches',
            'selectedBranch',
            'selectedLoaders'

            // 'teamMembers'
        ));
    }
}
