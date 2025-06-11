
@extends('layouts.admin.admin')
@section('content')
<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border"><h3 class="box-title"> Employee Name: {!! $row->name !!} </h3></div>
         @include('message')
         {!! Form::model($row, ['method' => 'PATCH','route' => [$model.'.store.change_password', $row->slug],'class'=>'validate','enctype'=>'multipart/form-data']) !!}
            {{ csrf_field() }}
           <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Password</label>
                    <div class="col-sm-10">
                        

                         <input type="password" class="form-control" name="new_password" placeholder="Enter New password" id="pass"  size="25" minlength="5" maxlength="30" class="validate[required]"required=""/>
                    </div>
                </div>
            </div>

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Confirm Password</label>
                    <div class="col-sm-10">
                        <input type="password" class="form-control"  name="confirm_password" id="confpass" equalTo="#pass" placeholder="Enter confirm password"  class="validate[required,equals[pass]]" required=""/>
                    </div>
                </div>
            </div>









             

           
            

           

            


           


            

      
            <div class="box-footer">
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</section>
@endsection



