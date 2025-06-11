<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmailTemplate;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class EmailTemplateController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct() {
        $this->model = 'email-templates';
        $this->title = 'Email Templates';
        $this->pmodule = 'email-templates';
    }

    public function index()
    {
        try {
            $permission = $this->mypermissionsforAModule();
            $pmodule = $this->pmodule;
            throw_if((!isset($permission[$pmodule . '___view']) && $permission != 'superadmin'), "Not Enough Permissions");
            $title = $this->title;
            $model = $this->model;
            $templates = EmailTemplate::get();
            $templateList = EmailTemplate::templateList();
            $data = [];
            foreach ($templateList as $key => $email_template) {
                $template = $templates->where('name',$email_template->name)->first();
                $data[$key] = (object)[
                    'name'=>$email_template->name,
                    'subject'=>$email_template->subject
                ];
                if($template){
                    $data[$key]->subject = $template->subject;
                }
            }
            return view('admin.email_templates.index', compact('data', 'title', 'model', 'pmodule', 'permission'));
        } catch (\Throwable $th) {
            Session::flash('warning', $th->getMessage());
            return redirect()->back();
        }
    }

    public function edit($key)
    {
        try {
            $permission = $this->mypermissionsforAModule();
            $pmodule = $this->pmodule;
            throw_if((!isset($permission[$pmodule . '___view']) && $permission != 'superadmin'), "Not Enough Permissions");
            $title = $this->title;
            $model = $this->model;
            $email_template = EmailTemplate::templateList()[$key] ?? "";
            throw_if($email_template == "", "Invalid Request");
            $template = EmailTemplate::where('name',$email_template->name)->first();
            return view('admin.email_templates.edit', compact('template', 'title', 'model', 'pmodule', 'permission','email_template','key'));
        } catch (\Throwable $th) {
            Session::flash('warning', $th->getMessage());
            return redirect()->back();
        }
    }

    public function update(Request $request, $key)
    {
        $validations = Validator::make($request->all(),[
            'subscribers'=>'nullable|string',
            'subject'=>'required|string|max:255',
            'body'=>'required|string'
        ]);
        if($validations->fails()){
            return response()->json([
                'result'=>0,
                'message'=>$validations->errors(),
                'data'=>$validations->errors()
            ]);
        }
        try {
            $permission = $this->mypermissionsforAModule();
            $pmodule = $this->pmodule;
            throw_if((!isset($permission[$pmodule . '___view']) && $permission != 'superadmin'), "Not Enough Permissions");
            $title = $this->title;
            $model = $this->model;
            $email_template = EmailTemplate::templateList()[$key] ?? "";
            throw_if($email_template == "", "Invalid Request");
            $template = EmailTemplate::where('name',$email_template->name)->first();
            if(!$template){
                $template = new EmailTemplate();
                $template->name=$email_template->name;
            }
            $template->subscribers = $request->subscribers;
            $template->subject = $request->subject;
            $template->body = $request->body;
            $template->save();
            return response()->json([
                'result'=>1,
                'message'=>'Template saved successfully',
                'location'=>route('admin.email_templates.index')
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'result'=>-1,
                'message'=>$th->getMessage()
            ]);
        }
    }
}
