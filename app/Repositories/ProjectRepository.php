<?php

namespace App\Repositories;


use App\Interfaces\ProjectInterface;
use App\Model\Projects;
use Illuminate\Support\Facades\DB;

class ProjectRepository implements ProjectInterface
{
    
    public function getProjects()
    {
        try {
            $projects = Projects::all();
            return response($projects, 200);
        } catch (\Exception $e) {
            return response('No Account Found', 400);
        }
    }

    public function storeProject($data)
    {
        DB::beginTransaction();
        try {
            $new = new Projects;
            $new->title = $data['title'];
            $new->description = $data['description'];
            $new->save();
            
            DB::commit();

            return response('Project Stored Successfully', 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response('Something went wrong', 400);
        }
    }

    public function updateProject($id, $data)
    {
        DB::beginTransaction();
        try {
            $new = Projects::find($data['id']);
            $new->title = $data['title'];
            $new->description = $data['description'];
            $new->save();
            
            DB::commit();

            return response('Project Updated Successfully', 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response('Something went wrong', 400);
        }
    }

    public function destroyProject($id)
    {
        DB::beginTransaction();
        try {
            $delete = Projects::find($id);
            if($delete){
                $delete->delete();
            }
            
            DB::commit();

            return response('Project Deleted Successfully', 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response('Something went wrong', 400);
        }
    }
}
