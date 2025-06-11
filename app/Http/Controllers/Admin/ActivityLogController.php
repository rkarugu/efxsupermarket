<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\User;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ActivityLogController extends Controller
{
    protected $model;
    protected $title;

    public function __construct()
    {
        $this->model = 'activity-logs';
        $this->title = 'Activity logs';
    }

    /**
     * Display a listing of the activity logs.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Auth::user()->role_id != 1) {
            return returnAccessDeniedPage();
        } 

        $title = $this->title;
        $model = $this->model;
        $ids = Activity::with('causer')->select('causer_id')->distinct()->get()->pluck('causer_id');
        $models = Activity::select('subject_type')
        ->whereNotNull('subject_type')
        ->orderBy('subject_type')
        ->distinct()
        ->get()
        ->mapWithKeys(function ($model) {
            $subject = explode('\\', $model->subject_type);
            return [$model->subject_type => end($subject)];
        });
        $descriptions = Activity::select('description')->orderBy('description')->distinct()->get()->pluck('description');

        $users = User::whereIn('id',$ids)->get();

        return view('admin.activity_log.index', compact('users','title','model','models','descriptions'));
    }

    public function datatable()
    {
        $from = request()->filled('from') ? request()->from . ' 00:00:00' : now()->subDays(30)->format('Y-m-d 00:00:00');
        $to = request()->filled('to') ? request()->to . ' 23:59:59' : now()->format('Y-m-d 23:59:59');
        
        $activities = Activity::query()
                        ->with('subject','causer')
                        ->whereBetween('created_at', [$from, $to])
                        ->orderBy('created_at','DESC');

        if(request()->causer != 'all'){
            $activities->where('causer_id',request()->causer);
        }

        if(request()->model != 'all'){
            $activities->where('subject_type',request()->model);
        }

        if(request()->description != 'all'){
            $activities->where('description',request()->description);
        }

        

    return DataTables::eloquent($activities)
        ->editColumn('subject_type', function ($subject) {
            $subject = explode('\\', $subject->subject_type);
            if (isset($subject[2])) {
                return $subject[2];
            }
            return '-';
        })
        ->editColumn('created_at', function ($date) {
            return date('Y-m-d H:i',strtotime($date->created_at));
        })
        ->editColumn('causer.name', function ($causer) {
            return $causer->causer ? $causer->causer->name : '-';
        })
        ->addColumn('subject_name', function($subject) {
            if($subject->subject){
                if($subject->subject->title){
                    return $subject->subject->title;
                } elseif($subject->subject->name){
                    return $subject->subject->name;
                }
                elseif($subject->subject->customer_name){
                    return $subject->subject->customer_name;
                }
                elseif($subject->subject->stock_id_code){
                    return $subject->subject->stock_id_code;
                } 
                elseif($subject->subject->document_no){
                    return $subject->subject->document_no;
                }  
                elseif($subject->subject->transaction_no){
                    return $subject->subject->transaction_no;
                }   
                elseif($subject->subject->grn_number){
                    return $subject->subject->grn_number;
                } else{
                    return '-';
                }
            }
            return '-';
        })
        ->toJson();
    }

    /**
     * Display the specified activity log.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (Auth::user()->role_id != 1) {
            return returnAccessDeniedPage();
        } 

        $title = $this->title;
        $model = $this->model;
        $activity = Activity::findOrFail($id);

        return view('admin.activity_log.show', compact('activity','title','model'));
    }

    /**
     * Remove the specified activity log from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (Auth::user()->role_id != 1) {
            return returnAccessDeniedPage();
        } 

        $activity = Activity::findOrFail($id);
        $activity->delete();

        return redirect()->route('activity-logs.index')
                         ->with('success', 'Activity log deleted successfully');
    }

    public function user_activity($id)
    {
        if (Auth::user()->role_id != 1) {
            return returnAccessDeniedPage();
        } 
        
        $title = $this->title;
        $model = $this->model;

        
            $activities = Activity::with('subject')->select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('DAY(created_at) as day'),
                'created_at',
                'subject_id',
                'subject_type',
                'description',
                'properties',
                'id'
            )
            ->where('causer_id',$id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function($date) {
                return Carbon::parse($date->created_at)->format('Y');
            })
            ->map(function($year) {
                return $year->groupBy(function($date) {
                    return Carbon::parse($date->created_at)->format('m');
                })->map(function($month) {
                    return $month->groupBy(function($date) {
                        return Carbon::parse($date->created_at)->format('d');
                    });
                });
            });
        
        $query = Activity::query()
                        ->with('subject','causer');

        $query->where('causer_id',$id);
        
        if(request()->date){
            $query->whereBetween('created_at',[request()->date.' 00:00:00',request()->date.' 23:59:59']);
        }
        // dd($query->toRawSql());
        // Group By dates
        // Get data Based on the dates
        // Use Accordion to grouped.
        // $activities = $query->get();

        $user = User::find($id);
        return view('admin.activity_log.user_activity', compact('title','model','activities','user'));
    }
}
