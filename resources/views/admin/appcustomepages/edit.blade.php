
@extends('layouts.admin.admin')
@section('content')
<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border"><h3 class="box-title"> {!! $title !!} </h3></div>
         @include('message')
         {!! Form::model($row, ['method' => 'PATCH','route' => [$model.'.update', $row->slug],'class'=>'validate','enctype'=>'multipart/form-data']) !!}
            {{ csrf_field() }}


            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Page Title</label>
                    <div class="col-sm-10">
                        {!! Form::text('title', null, ['maxlength'=>'255','placeholder' => 'Page Title', 'required'=>true, 'class'=>'form-control','readonly'=>true]) !!}  
                    </div>
                </div>
            </div>
             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Image</label>
                    <div class="col-sm-10">
                        <input type = "file" name = "image"  accept="image/*" >
                        @if($row->image)
                         <img width="100px" height="100px;"src="{{ asset('uploads/app_custom_pages/thumb/'.$row->image) }}">
                         @endif
                    </div>
                </div>
            </div>


            <?php 
            $description = $row->description;
            $description = json_decode($description);
            $counter = 1;
          
            ?>

            @foreach($description as $desc_data)

            @if($counter == 1)
                <div class="col-sm-12 withfieldset input_fields_container">
                 <fieldset style="border:1px solid red;" class="box-body">
                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Heading </label>
                        <div class="col-sm-10">
                           

                            <input type = "text" name = "heading[]" class= "form-control" required placeholder="Heading" maxlength = "255" value = "{!! $desc_data->heading!!}">
                        </div>
                    </div>
                </div>

                 <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Description</label>
                        <div class="col-sm-10">

                           <textarea  name = "description[]" class= "form-control" required placeholder="Description"   style = "height:150px;">{!! $desc_data->description!!}</textarea>
                        </div>
                    </div>
                </div>

                <a class="btn btn-sm btn-primary add_more_button" href = "javascript:void(0);">Add More Fields</a>

               
                </fieldset>
                </div>
            @endif

            @if($counter == 1)
            <div id = "addorefirel">
            @endif
                @if($counter > 1)
                <div class="col-sm-12 withfieldset ">
                <fieldset style="border:1px solid red;" class="box-body">
                <div class="box-body">
                <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Heading</label>
                <div class="col-sm-10">


                <input type = "text" name = "heading[]" class= "form-control" required placeholder="Heading" maxlength = "255" value = "{!! $desc_data->heading!!}">
                </div>
                </div>
                </div>

                <div class="box-body">
                <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Description</label>
                <div class="col-sm-10">

                <textarea  name = "description[]" class= "form-control" required placeholder="Description"   style = "height:150px;" >  {!! $desc_data->description!!}</textarea>
                </div>
                </div>
                </div>

                <a class="btn btn-sm btn-danger remove_field" href = "javascript:void(0);">Remove</a>


                </fieldset>
                </div>
                @endif
            @if(count($description) == $counter)
                </div>
            @endif
            <?php $counter++; ?>
            @endforeach




            
           
             


            <div class="box-footer">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
</section>

<div id = "addmore" style="display: none">
   <div class="col-sm-12 withfieldset ">
             <fieldset style="border:1px solid red;" class="box-body">
            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Heading</label>
                    <div class="col-sm-10">
                       

                        <input type = "text" name = "heading[]" class= "form-control" required placeholder="Heading" maxlength = "255">
                    </div>
                </div>
            </div>

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Description</label>
                    <div class="col-sm-10">

                       <textarea  name = "description[]" class= "form-control" required placeholder="Description"   style = "height:150px;"></textarea>
                    </div>
                </div>
            </div>

            <a class="btn btn-sm btn-danger remove_field" href = "javascript:void(0);">Remove</a>

           
            </fieldset>
            </div>
            </div>
@endsection

@section('uniquepagestyle')
<style type="text/css">
   .withfieldset{
    margin-bottom: 20px;
} 
</style>

@endsection

@section('uniquepagescript')

    <script>
        $(document).ready(function() {
        
        
        $('.add_more_button').click(function(e){ //click event on add more fields button having class add_more_button
            e.preventDefault();
          
               

                $('#addorefirel').append($("#addmore").html()); //add input field
           
        });  
        $('#addorefirel').on("click",".remove_field", function(e){ //user click on remove text links
            alert();
            e.preventDefault(); $(this).parent('fieldset').remove();
        })
    });
    </script>


@endsection


