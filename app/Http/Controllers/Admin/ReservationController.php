<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Reservation;
use DB;
use Session;
use App\Model\Order;

class ReservationController extends Controller
{

    protected $model;
    protected $title;
     protected $pmodule;
    public function __construct()
    {
        $this->model = 'reservations';
        $this->title = 'Reservations';
        $this->pmodule = 'reservations';
    } 

    public function index(Request $request)
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $start_date="";
        $end_date="";
        $restaurant="";


        if(isset($permission[$pmodule.'___view']) || $permission == 'superadmin')
        {
            $lists = Reservation::orderBy('id', 'DESC');
             if ($request->has('start-date'))
            {
               $start_date = $request->input('start-date');
                
            }
            if ($request->has('end-date'))
            {
                 $end_date = $request->input('end-date');
               
            }

             if ($request->has('restaurant'))
            {
                $restaurant = $request->input('restaurant');

               
            }

            $lists = $lists->get();
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];

             $restro = $this->getRestaurantList();
            return view('admin.reservations.index',compact('restro','title','lists','model','breadcum','pmodule','permission','start_date','end_date','restaurant'));
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        } 
            
    }

    public function create()
    {
       
        
    }


    public function store(Request $request)
    {
        
       
    }


    public function show($id)
    {
        
    }


    public function edit($id)
    {
       try
        {
            $permission =  $this->mypermissionsforAModule();
            if(isset($permission[$this->pmodule.'___edit']) || $permission == 'superadmin')
            {


                $row =  Reservation::where('id',$id)->first();
                if($row)
                {

                    $title = 'Edit '.$this->title;
                    $breadcum = [$this->title=>route($this->model.'.index'),'Edit'=>''];
                    $model = 'reservations'; 
                    return view('admin.reservations.edit',compact('title','model','breadcum','row')); 
                }
                else
                {
                    Session::flash('warning', 'Invalid Request');
                    return redirect()->back();
                }
            }
            else
            {
                Session::flash('warning', 'Invalid Request');
                return redirect()->back();
            }

           
        }
        catch(\Exception $e)
        {

           $msg = $e->getMessage();
           Session::flash('warning', $msg);
           return redirect()->back();
        }
    }


    public function update(Request $request, $id)
    {
       $row = Reservation::where('id',$id)->first();

       $row->status = $request->status;
       $row->save();
        Session::flash('success', 'Record updated successfully.');
        return redirect()->route('reservations.index');

    }


    public function destroy($id)
    {
        try
        {
            
            Reservation::whereSlug($id)->delete();
           
            Session::flash('success', 'Deleted successfully.');
            return redirect()->back();
        }
        catch(\Exception $e)
        {

            Session::flash('warning','Invalid Request');
            return redirect()->back();
        }
    }

    public function datatablesReservation(Request $request)
    {

        $pmodule = 'reservations';
        $permission =  $this->mypermissionsforAModule();
        $columns = array( 
                0 =>'id', 
                1 =>'user',
                2 =>'restro',
                3 =>'comment',
                4 =>'email', 
                5=> 'event_type',
                 5=> 'reservation_time',
                6=> 'status',
                7=> 'action',
            );
        $totalData = Reservation::where('status','!=','CANCLED');
          if ($request->has('start-date'))
            {
                $totalData = $totalData->where('created_at','>=',$request->input('start-date'));
                
            }
            if ($request->has('end-date'))
            {
                $totalData = $totalData->where('created_at','<=',$request->input('end-date'));
               
            }

             if ($request->has('restaurant'))
            {
                $totalData = $totalData->where('restaurant_id',$request->input('restaurant'));
               
            }


        $totalData= $totalData->count();
        $totalFiltered = $totalData; 
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        if(empty($request->input('search.value')))
        {            
            $posts = Reservation::where('status','!=','CANCLED');
            if ($request->has('start-date'))
            {
                $posts = $posts->where('created_at','>=',$request->input('start-date'));
                
            }
            if ($request->has('end-date'))
            {
                $posts = $posts->where('created_at','<=',$request->input('end-date'));
               
            }

             if ($request->has('restaurant'))
            {
                $posts = $posts->where('restaurant_id',$request->input('restaurant'));
               
            }

            $posts =     $posts->offset($start)
                ->limit($limit)
                ->orderBy($order,$dir)
                ->get();
        }
        else
        {
            $search = $request->input('search.value'); 
            $posts = Reservation::where('status','!=','CANCLED')
                        ->where(function($query) use ($search){
                        $query->where('id','LIKE',"%{$search}%")
                        ->orWhere('created_at','LIKE',"%{$search}%")
                         ->orWhere('email','LIKE',"%{$search}%")
                         ->orWhere('event_type','LIKE',"%{$search}%")
                        
                        ;
                        });

            if ($request->has('start-date'))
            {
                $posts = $posts->where('created_at','>=',$request->input('start-date'));
                
            }
            if ($request->has('end-date'))
            {
                $posts = $posts->where('created_at','<=',$request->input('end-date'));
               
            }

             if ($request->has('restaurant'))
            {
                $posts = $posts->where('restaurant_id',$request->input('restaurant'));
               
            }

                  $posts =   $posts->offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();



            $totalFiltered =Reservation::where('status','!=','CANCLED')
                                ->where(function($query) use ($search){
                                            $query->where('id','LIKE',"%{$search}%")
                                            ->orWhere('created_at','LIKE',"%{$search}%")
                                             ->orWhere('email','LIKE',"%{$search}%")
                         ->orWhere('event_type','LIKE',"%{$search}%")
                                            ;
                                            });

             if ($request->has('start-date'))
            {
                $totalFiltered = $totalFiltered->where('created_at','>=',$request->input('start-date'));
                
            }
            if ($request->has('end-date'))
            {
                $totalFiltered = $totalFiltered->where('created_at','<=',$request->input('end-date'));
               
            }

             if ($request->has('restaurant'))
            {
                $totalFiltered = $totalFiltered->where('restaurant_id',$request->input('restaurant'));
               
            }


                       $totalFiltered =          $totalFiltered->count();
            
        }
        $data = array();
        if(!empty($posts))
        {
            foreach ($posts as $list)
            {
                
                $nestedData['id'] = $list->id;
                $nestedData['user'] = $list->getAssociateUser->name;
                $nestedData['restro'] = $list->getAssociateRestro->name;
                $nestedData['reservation_time'] = date('Y-m-d h:i A',strtotime($list->reservation_time));
               // $nestedData['table'] = $list->table_name;
                $nestedData['comment'] = $list->comment;
               // $nestedData['phone_number'] = $list->phone_number;
                $nestedData['email'] = $list->email;
                //$nestedData['no_of_person'] = $list->no_of_person;
                $nestedData['event_type'] = $list->event_type;
                $status = $list->status;
              

                $nestedData['status'] = $status;
                
                $nestedData['action'] = '';

                 if(isset($permission[$pmodule.'___edit']) || $permission == 'superadmin')
                {
                    $nestedData['action'] = $nestedData['action'].'
                    <span>
                    <a title="Edit"  href="'.route('reservations.edit',$list->id).'" ><i aria-hidden="true" class="fa fa-pencil" style="font-size: 20px;" id = "trash'.$list->id .'"></i>
                    </a>
                    </span>
                    ';

                }

                if(isset($permission[$pmodule.'___delete']) || $permission == 'superadmin')
                {
                    $nestedData['action'] = $nestedData['action'].'
                    <span>
                    <a title="Delete" href="'.route('admin.delete.reservation.request',$list->id).'" ><i aria-hidden="true" class="fa fa-trash" style="font-size: 20px;" id = "trash'.$list->id .'"></i>
                    </a>
                    </span>
                    ';
                }
               

                
                             


                
              
               
                $data[] = $nestedData;
            }
        }

      $json_data = array(
                "draw"            => intval($request->input('draw')),  
                "recordsTotal"    => intval($totalData),  
                "recordsFiltered" => intval($totalFiltered), 
                "data"            => $data   
                );
        echo json_encode($json_data); 
    }

    public function deletereservation(Request $request,$id)
    {
        $id = $request->id;
        Reservation::where('id',$id)->delete();
        Session::flash('success','Record deleted successfully');
        return redirect()->back();
       
           
    }

    

    

    
}
