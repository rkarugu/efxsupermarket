<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\Role;
use App\Model\UserPermission;
use App\User;
use App\Models\Ticket;
use App\Models\TicketStatus;
use App\Model\WaNumerSeriesCode;
use App\Models\SupportTeam;
use App\Models\TicketAssignee;
use App\Models\TicketCategory;
use App\Models\TicketResponse;
use App\Notifications\HelpDesk\TicketAssignNotification;
use App\Notifications\HelpDesk\TicketResponseNotification;
use App\Notifications\HelpDesk\TicketCreationAdminNotification;
use App\Notifications\HelpDesk\TicketCreationNotification;
use App\Notifications\HelpDesk\TicketStatusUpdateNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TicketController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct()
    {
        $this->model = 'help-desk';
        $this->title = 'Tickets';
        $this->pmodule = 'tickets';
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!can('status-tickets', 'help-desk-tickets')) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }

        $title = $this->title;
        $model= 'tickets';
        if (request()->filled('status') && request()->status !='all') {
            if (request()->status == 'Open') {
                $title = "Open Tickets";
                $model= 'open-tickets';
            }
            if (request()->status == 'Development') {
                $title = "Development Tickets";
                $model= 'development-tickets';
            }
            if (request()->status == 'Completed') {
                $title = "Completed Tickets";
                $model= 'completed-tickets';
            }
            if (request()->status == 'Closed') {
                $title = "Closed Tickets";
                $model= 'closed-tickets';
            }
        }

        $permissions = $this->mypermissionsforAModule();
        $breadcum = [ 'Help Desk'=> '', $title => ''];

        $authuser = Auth::user();
        $isAdmin = $authuser->role_id == 1;
        $permission = $this->mypermissionsforAModule();

        $query = Ticket::with('category','creator','branch','current_assignee','current_assignee.assignee','current_status');
        $categoryQuery = $query->clone()->select('category_id')->distinct()->get()->pluck('category_id')->toArray();
        $categoryIds = array_filter($categoryQuery);

        $categories = TicketCategory::whereIn('id',$categoryIds)->get();

        if (request()->wantsJson()) {
            
            if (!$isAdmin && !isset($permission['view___help-desk-tickets'])) {
                $query->Where(function ($query) use ($authuser) {
                    $query->where('tickets.created_by', $authuser->id);
                });
                $query->orWhereHas('current_assignee', function ($q) use ($authuser) {
                    $q->where('assignee_id', $authuser->id);
                }); 
            }
            if (request()->filled('status') && request()->status !='all') {
                $query->whereHas('current_status', function ($q) {
                    $q->where('id', function($subQuery) {
                        $subQuery->select(DB::raw('MAX(id)'))
                            ->from('ticket_statuses as tr2')
                            ->whereColumn('tr2.ticket_id', 'ticket_statuses.ticket_id');
                    })->where('status', request()->status); 
                }); 
            }

            if (request()->filled('priority') && request()->priority !='all') {
                $query->where('priority', request()->priority);
            }

            if (request()->filled('category') && request()->category !='all') {
                $query->where('category_id', request()->category);
            }

            if(request()->start_date && request()->end_date){
                $query->whereBetween('created_at',[request()->start_date.' 00:00:00',request()->end_date.' 23:59:59']);
            }
            $tickets = $query->orderBy('created_at','DESC');
            return DataTables::of($tickets)
                    ->addIndexColumn()
                    ->editColumn('created_at',function($ticket){
                        return date('d-m-Y H:i',strtotime($ticket->created_at));
                    })
                    ->editColumn('current_assignee.assignee.name', function($ticket){
                        $assignee = '-';
                        if($ticket->current_assignee){
                            $assignee = $ticket->current_assignee->assignee->name;
                        }
                        return $assignee;
                    })
                    ->editColumn('category.title',function($ticket){
                        $category = '-';
                        if($ticket->category){
                            $category= $ticket->category->title;
                        }
                        return $category;
                    })
                    ->toJson();
        }

        return view('admin.help_desk.index', compact('title', 'model', 'breadcum','categories'));

    }

    public function my_tickets()
    {
        if (!can('my-tickets', 'help-desk-tickets')) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }

        $title = 'My Tickets';
        $model= 'my-tickets';

        $permissions = $this->mypermissionsforAModule();
        $breadcum = [ 'Help Desk'=> '', 'My Tickets' => ''];

        $query = Ticket::with('category','branch','current_assignee','current_assignee.assignee','current_status');
        $categoryQuery = $query->clone()->select('category_id')->distinct()->get()->pluck('category_id')->toArray();
        $categoryIds = array_filter($categoryQuery);

        $categories = TicketCategory::whereIn('id',$categoryIds)->get();
        if (request()->wantsJson()) {
            
            $query->where('tickets.created_by', Auth::user()->id);
            if (request()->filled('status') && request()->status !='all') {
                $query->whereHas('current_status', function ($q) {
                    $q->where('id', function($subQuery) {
                        $subQuery->select(DB::raw('MAX(id)'))
                            ->from('ticket_statuses as tr2')
                            ->whereColumn('tr2.ticket_id', 'ticket_statuses.ticket_id');
                    })->where('status', request()->status); 
                }); 
            }

            if (request()->filled('priority') && request()->priority !='all') {
                $query->where('priority', request()->priority);
            }

            if (request()->filled('category') && request()->category !='all') {
                $query->where('category_id', request()->category);
            }

            if(request()->start_date && request()->end_date){
                $query->whereBetween('created_at',[request()->start_date.' 00:00:00',request()->end_date.' 23:59:59']);
            }

            $tickets = $query->orderBy('created_at','DESC');
            return DataTables::of($tickets)
                    ->addIndexColumn()
                    ->editColumn('created_at',function($ticket){
                        return date('d-m-Y H:i',strtotime($ticket->created_at));
                    })
                    ->editColumn('current_assignee.assignee.name', function($ticket){
                        $assignee = '-';
                        if($ticket->current_assignee){
                            $assignee = $ticket->current_assignee->assignee->name;
                        }
                        return $assignee;
                    })
                    ->editColumn('category.title',function($ticket){
                        $category = '-';
                        if($ticket->category){
                            $category= $ticket->category->title;
                        }
                        return $category;
                    })
                    ->setRowAttr([
                        'color' => function($user) {
                            return 'bg-white';
                        },
                    ])
                    ->setRowClass(function ($ticket) {
                        if ($ticket->current_status->status == 'Open') {
                            return 'alert-danger';
                        }
                        if ($ticket->current_status->status == 'Development') {
                            return 'alert-info';
                        }
                        if ($ticket->current_status->status == 'Completed') {
                            return 'alert-success';
                        }
                    })
                    ->toJson();
        }

        return view('admin.help_desk.my_tickets', compact('title', 'model', 'breadcum','categories'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!can('add', 'help-desk-tickets')) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }

        $permissions = $this->mypermissionsforAModule();
        $title = $this->title;
        $model= 'new-tickets';
        
        $row = Role::find(Auth::user()->role_id);
        $permissions = UserPermission::where('role_id', $row->id)->get();
        // dd($row);
        $breadcum = [ 'Help Desk'=> '', 'New Tickets' => ''];
        $categories = TicketCategory::all();

        return view('admin.help_desk.create', compact('title', 'model', 'breadcum','categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!can('add', 'help-desk-tickets')) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }
        $validator = Validator::make($request->all(),[
            'module'=>'required',
            'subject'=>'required',
            'message' => 'required',
            'priority' => 'required',
            'attachment' => 'nullable',
            'category' => 'required'
        ]);
        if($validator->fails()){
            return response()->json([
                'result'=>0,
                'errors'=>$validator->errors()
            ]);
        }

        try {
            $check = DB::transaction(function () use ($request){
                $series_module = WaNumerSeriesCode::where('module', 'TICKET')->first();
                $lastNumberUsed = $series_module->last_number_used;
                $newNumber = (int)$lastNumberUsed + 1;
                $newCode = $series_module->code."-".str_pad($newNumber,5,"0",STR_PAD_LEFT);
                $series_module->update(['last_number_used' => $newNumber]);
                $attachData ='';
                if (!empty($request->attachment)) {
                    $attachData = $request->file('attachment')->store('uploads/Tickets', 'public');
                }
                
                $ticket = Ticket::create([
                    'created_by'=>Auth::user()->id,
                    'subject' => $request->subject,
                    'message' => $request->message,
                    'priority' => $request->priority,
                    'branch_id' => Auth::user()->restaurant_id,
                    'module' => $request->module,
                    'code' => $newCode,
                    'attachment' => $attachData,
                    'category_id' => $request->category
                ]);

                TicketStatus::create([
                    'ticket_id' => $ticket->id,
                    'status' => 'Open',
                    'created_by'=>Auth::user()->id,
                ]);

                $supports = SupportTeam::with('user')->where('get_notifications',1)->get()->map(function($team){
                    if ($team->user) {
                        return [
                            'id' => $team->user->id,
                        ];
                    }
                
                    return null;
                });

                foreach ($supports as $support) {
                    if (isset($support['id'])) {
                        $user = User::find($support['id']);
                        if ($user) {
                            $user->notify(new TicketCreationAdminNotification($ticket));
                        }
                    }
                }
                
                $user = User::find(Auth::user()->id);
                $user->notify(new TicketCreationNotification($ticket));

                return true;
            });
        
            if($check){            

                return response()->json([
                    'result'=>1,
                    'message'=>'Ticket Created Successfully.',
                ]);         
            }
        } catch (\Exception $e) {
            return response()->json(['result'=>-1,'message'=>$e->getMessage()]);
        }
        
        
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if (!can('show', 'help-desk-tickets')) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }

        $permissions = $this->mypermissionsforAModule();
        $title = $this->title;
        $model= 'tickets';

        $breadcum = [ 'Help Desk'=> '', 'Tickets' => ''];

        $ticket = Ticket::with(['creator','branch','responses' => function($query) {
            $query->orderBy('created_at', 'asc');
        }])->find($id);
        
        $groupedResponses=[];
        if (isset($ticket->responses)) {
            $groupedResponses = $ticket->responses->map(function ($response) use($ticket) {
                return [
                    'date' => $response->created_at->toDateString(),
                    'time' => $response->created_at->format('H:i'),
                    'message' => $response->message,
                    'created_by' => $response->created_by,
                    'creator' => $response->creator->name
                ];
            })->groupBy('date');
        }
        
        $notUsers = [Auth::user()->id];
        if (isset($ticket->current_assignee)) {
            $notUsers[]=$ticket->current_assignee->assignee->id;
        }
        $assignees = SupportTeam::whereHas('user', function ($q) use($notUsers) {
            $q->whereNotIn('id',$notUsers); 
        })->get()->map(function($team){
            return [
                'id' => $team->user->id,
                'name' => $team->user->name,
            ];
        });        

        return view('admin.help_desk.ticket_view', compact('title', 'model', 'breadcum', 'ticket','assignees','groupedResponses'));
    }

    public function assign(Request $request)
    {
        if (!can('assign', 'help-desk-tickets')) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }
        $validator = Validator::make($request->all(),[
            'assignee'=>'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'result'=>0,
                'errors'=>$validator->errors()
            ]);
        }

        $check = DB::transaction(function () use ($request){
            TicketAssignee::create([
                'created_by'=>Auth::user()->id,
                'ticket_id' => $request->ticket,
                'assignee_id' => $request->assignee,
            ]);
            
            $ticket = Ticket::find($request->ticket);
            $user = User::find($request->assignee);
            $user->notify(new TicketAssignNotification($ticket));
            return true;
        });
        
        if($check){            
            return response()->json([
                'result'=>1,
                'message'=>'Ticket Assigned Successfully.',
            ]);         
        }
        return response()->json(['result'=>-1,'message'=>'Something went wrong']);
    }

    public function respond(Request $request)
    {
        if (!can('respond', 'help-desk-tickets')) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }

        $validator = Validator::make($request->all(),[
            'message'=>'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'result'=>0,
                'errors'=>$validator->errors()
            ]);
        }

        $check = DB::transaction(function () use ($request){
            $assignee = Ticket::with('current_assignee')->find($request->ticket);
            if (isset($assignee->current_assignee)) {
                $assigneeId = $assignee->current_assignee->id; 
            } else {
                $assigneeNew = TicketAssignee::create([
                    'created_by'=>Auth::user()->id,
                    'ticket_id' => $request->ticket,
                    'assignee_id' => Auth::user()->id,
                ]);
                
                $assigneeId = $assigneeNew->id; 
                
            }
            
            TicketResponse::create([
                'created_by'=>Auth::user()->id,
                'ticket_id' => $request->ticket,
                'assignee_id' => $assigneeId,
                'message' => $request->message
            ]);

            $user = User::find($assignee->created_by);
            $user->notify(new TicketResponseNotification($assignee));
            
            return true;
        });
        
        if($check){            
            return response()->json([
                'result'=>1,
                'message'=>'Ticket Responded Successfully.',
            ]);         
        }
        return response()->json(['result'=>-1,'message'=>'Something went wrong']);
    }

    public function status(Request $request)
    {
        if (!can('update-status', 'help-desk-tickets')) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }

        $validator = Validator::make($request->all(),[
            'status'=>'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'result'=>0,
                'errors'=>$validator->errors()
            ]);
        }

        $check = DB::transaction(function () use ($request){
            TicketStatus::create([
                'ticket_id' => $request->ticket,
                'status' => $request->status,
                'created_by'=>Auth::user()->id,
            ]);
            $ticket = Ticket::find($request->ticket);
            $user = User::find($ticket->created_by);
            $user->notify(new TicketStatusUpdateNotification($ticket));
            return true;
        });
        
        if($check){            
            return response()->json([
                'result'=>1,
                'message'=>'Ticket Status Updated Successfully.',
            ]);         
        }
        return response()->json(['result'=>-1,'message'=>'Something went wrong']);
    }
}
