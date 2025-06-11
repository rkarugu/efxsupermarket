<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Feedback;
use DB;
use Session;

class FeedbackController extends Controller
{

    protected $model;
    protected $title;
    protected $pmodule;
    
    public function __construct()
    {
        $this->model = 'feedback';
        $this->title = 'Feedback';
        $this->pmodule = 'feedback';
       
    } 

    public function order(Request $request)
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        if(isset($permission[$pmodule.'___view']) || $permission == 'superadmin')
        {

            $title = $this->title;
            $model = $this->model;
           $lists = Feedback::orderBy('id', 'DESC')
            		->where('status','Y')
            		->where('order_id','!=',null);

            if ($request->has('start-date'))
            {
                $lists = $lists->where('created_at','>=',$request->input('start-date'));
                
            }
            if ($request->has('end-date'))
            {
                $lists = $lists->where('created_at','<=',$request->input('end-date'));
               
            }

            $lists	=	$lists->get();
            $breadcum = [$title=>route($model.'.order'),'Orders'=>''];
            return view('admin.feedback.order',compact('title','lists','model','breadcum','pmodule','permission'));
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }   
    }

     public function restro(Request $request)
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        if(isset($permission[$pmodule.'___view']) || $permission == 'superadmin')
        {

            $title = $this->title;
            $model = $this->model;
            $lists = Feedback::orderBy('id', 'DESC')
            		->where('status','Y')
            		->where('restaurant_id','!=',null);
             if ($request->has('start-date'))
            {
                $lists = $lists->where('created_at','>=',$request->input('start-date'));
                
            }
            if ($request->has('end-date'))
            {
                $lists = $lists->where('created_at','<=',$request->input('end-date'));
               
            }

             if ($request->has('restaurant'))
            {
                $lists = $lists->where('restaurant_id',$request->input('restaurant'));
               
            }

            

            $restro = $this->getRestaurantList();

            $lists  =   $lists->get();
            $breadcum = [$title=>route($model.'.restro'),'Restaurants'=>''];
            return view('admin.feedback.restro',compact('title','lists','model','breadcum','pmodule','permission','restro'));
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }   
    }


 

    
}
