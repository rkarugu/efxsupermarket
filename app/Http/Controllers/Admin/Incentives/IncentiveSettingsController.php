<?php

namespace App\Http\Controllers\Admin\Incentives;

use App\Http\Controllers\Controller;
use App\Models\IncentiveSettings;
use Illuminate\Http\Request;

class IncentiveSettingsController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;
    public function __construct()
    {
        $this->model = 'incentive-settings';
        $this->title = 'Incentive Settings';
        $this->pmodule = 'incentive-settings';
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $user = getLoggeduserProfile();

        $incentiveSettings = IncentiveSettings::latest()->get();
        return view('admin.incentives.incentive-settings.index', [
            'incentiveSettings'=>$incentiveSettings,
            'model' => $this->model,
            'title' => $this->title,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:incentive_settings,name',
            'reward' => 'required|array',
            'group' => 'sometimes',
            'target.*' => 'required|string',
            'reward.*' => 'required|numeric',
        ],[
            'name.unique' => 'The Incentive has already been set',
        ]);


        $valueReward = array_map(function($target,$reward) use ($validated) {

            return [
                'target' => $target,
                'reward' => $reward,
                'title' => $target,  // A
            ];
        }, $validated['target'], $validated['reward']);


        IncentiveSettings::create([
            'name' => $validated['name'],
            'target' => $validated['target'],
            'type' => $validated['type'],
            'target_reward' => json_encode($valueReward),
        ]);

        return response()->json(['success' => 'Incentive Setting created successfully!']);
    }

    public function getTitle($operation, $target)
    {
        return match ($operation) {
            'equal' =>  'equals ' . $target,
            'greater_than' => 'greater than ' . $target,
            'less_than' =>  'Less than ' . $target,
            'share' => 'Shared ' . $target,
            default => '',
        };
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $incentiveSetting = IncentiveSettings::findOrFail($id);

        // Decode the JSON value_reward into an array
        $valueReward = json_decode($incentiveSetting->target_reward, true);

        // Return the incentive setting data as JSON
        return response()->json([
            'id' => $incentiveSetting->id,
            'name' => $incentiveSetting->name,
            'target_reward' => $valueReward,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Find the incentive setting by ID
        $incentiveSetting = IncentiveSettings::findOrFail($id);

        // Decode the JSON value_reward into an array
        $valueReward = json_decode($incentiveSetting->target_reward, true);

        // Return the incentive setting data as JSON
        return response()->json([
            'id' => $incentiveSetting->id,
            'name' => $incentiveSetting->incentive_name,
            'target' => $incentiveSetting->target,
            'type' => $incentiveSetting->type,
            'target_reward' => $valueReward,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,  IncentiveSettings $incentiveSetting)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'reward' => 'required|array',
            'group' => 'sometimes',
            'target.*' => 'required|string',
            'reward.*' => 'required|numeric',
        ],[
            'name.unique' => 'The Incentive has already been set',
        ]);


        $valueReward = array_map(function($target,$reward) use ($validated) {

            return [
                'target' => $target,
                'reward' => $reward,
                'title' => $target,  // A
            ];
        }, $validated['target'], $validated['reward']);



        $incentiveSetting->update([
            'name' => $validated['name'],
            'target_reward' => json_encode($valueReward),
        ]);

        return response()->json(['success' => 'Incentive Setting updated successfully!']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(IncentiveSettings $incentiveSetting)
    {
        $incentiveSetting->delete();
        return response()->json(['success' => 'Incentive Setting deleted successfully!']);
    }
}
