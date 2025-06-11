<?php

namespace App\Repositories;


use App\Interfaces\GLTagInterface;
use App\Model\GlTags;
use Illuminate\Support\Facades\DB;

class GLTagRepository implements GLTagInterface
{
    
    public function getGLTags()
    {
        try {
            $tags = GlTags::all();
            return response($tags, 200);
        } catch (\Exception $e) {
            return response('No GL Tags Found', 400);
        }
    }

    public function storeGLTags($data)
    {
        DB::beginTransaction();
        try {
            $new = new GlTags;
            $new->title = $data['title'];
            $new->description = $data['description'];
            $new->save();
            
            DB::commit();

            return response('Gl Tag Stored Successfully', 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response('Something went wrong', 400);
        }
    }

    public function updateGLTags($id, $data)
    {
        DB::beginTransaction();
        try {
            $new = GlTags::find($data['id']);
            $new->title = $data['title'];
            $new->description = $data['description'];
            $new->save();
            
            DB::commit();

            return response('Gl Tag Updated Successfully', 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response('Something went wrong', 400);
        }
    }

    public function destroyGLTags($id)
    {
        DB::beginTransaction();
        try {
            $delete = GlTags::find($id);
            if($delete){
                $delete->delete();
            }
            
            DB::commit();

            return response('Gl Tag Deleted Successfully', 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response('Something went wrong', 400);
        }
    }
}
