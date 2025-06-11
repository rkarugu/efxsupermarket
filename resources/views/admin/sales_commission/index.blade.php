
@extends('layouts.admin.admin')

@section('content')


<!-- Main content -->
<section class="content">
    <!-- Small boxes (Stat box) -->
    <div class="box box-primary">
        <div class="box-header with-border no-padding-h-b">
                                   <div  style="height: 150px ! important;"> 
                            <div class="card-header">
                            <i class="fa fa-filter"></i> Form
                            </div><br>
                            {!! Form::model($editdata,['route' => $model.'.store','method'=>'post','autocomplete'=>"off"]) !!}
							{{csrf_field()}}
                            {!! Form::hidden('id', null, []) !!}
                            <div>
                            <div class="col-md-12 no-padding-h">	
                            <div class="col-sm-4">
                            <div class="form-group">
                            {!! Form::text('sales_from', null, [
                            'class'=>'form-control',
                            'placeholder'=>'Sales From','required'=>true]) !!}
                            </div>
                            </div>

                            <div class="col-sm-4">
                            <div class="form-group">
                            {!! Form::text('sales_to', null, [
                            'class'=>'form-control',
                            'placeholder'=>'Sales To','required'=>true]) !!}
                            </div>
                            </div>
                            <div class="col-sm-4">
                            <div class="form-group">
                            {!! Form::text('amount', null, [
                            'class'=>'form-control',
                            'placeholder'=>'Amount','required'=>true]) !!}
                            </div>
                            </div>


                           
                            </div>

                            <div class="col-md-12 no-padding-h">
                                <div class="col-sm-1"><button type="submit" class="btn btn-success" name="manage-request" value="filter"  >Save</button></div>


                            </div>
                            </div>

                            </form>
                        </div>
            <br>
            @include('message')
            <div class="col-md-12 no-padding-h" style="overflow-x: scroll;">
                <table class="table" id="create_datatable">
                    <thead>
                        <tr>
                            <th width="10%">S No.</th>
                            <th width="10%">Sales From</th>
                            <th width="10%">Sales To</th>
                            <th width="10%">Commission Amount</th>
                            <th width="10%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $b = 1; ?>
                        @foreach($lists as $list)
                        <tr>
                        <td>{{$b}}</td>
                        <td>{{$list->sales_from}}</td>
                        <td>{{$list->sales_to}}</td>
                        <td>{{$list->amount}}</td>
                        <td><a href="{{route($model.'.index').'?action=edit&id='.$list->id}}" class="btn btn-info">Edit</a> 
                        <a href="{{route($model.'.deleteRec',['id'=>$list->id])}}" onclick="return confirm('Are you sure?')" class="btn btn-danger"><i class="fa fa-trash"></a></td>
                        </tr>
                        <?php $b++; ?>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
@endsection
@section('uniquepagestyle')
 <link rel="stylesheet" href="{{asset('assets/admin/dist/bootstrap-datetimepicker.min.css')}}">
@endsection

@section('uniquepagescript')
<script src="{{asset('assets/admin/dist/bootstrap-datetimepicker.js')}}"></script>
<script>
               

                  $('.datepicker').datetimepicker({
                  format: 'yyyy-mm-dd hh:ii:00',
                  minuteStep:1,
                 });




            </script>

           


@endsection