
@extends('layouts.admin.admin')
@section('content')
<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border"><h3 class="box-title"> {!! $title !!} </h3></div>
         @include('message')
         {!! Form::model($row, ['method' => 'PATCH','route' => [$model.'.update', $row->id],'class'=>'validate  form-horizontal','enctype'=>'multipart/form-data']) !!}
            {{ csrf_field() }}
             <div class="box-body">

                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Route</label>
                    <div class="col-sm-8">
                        <select class="form-control" name="route">
                            <option value="">Select Route</option>
                            <option {{($row->route=="charagita")?'selected':''}} value="charagita">Charagita</option>
                            <option {{($row->route=="charagita")?'selected':''}} value="lanel">Lanel</option>
                            <option {{($row->route=="free_area")?'selected':''}} value="free_area">Free Area</option>
                            <option {{($row->route=="kamwaura")?'selected':''}} value="kamwaura">Kamwaura</option>
                            <option {{($row->route=="thudua")?'selected':''}} value="thudua">Thudua</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Date</label>
                    <div class="col-sm-8">
                        <input type="date" value="{{$row->date}}" name="date" class="form-control" placeholder="Enter Date">
                    </div>
                </div>

                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Toonage</label>
                    <div class="col-sm-8">
                        <input type="text" value="{{$row->tonnage}}" name="tonnage" class="form-control" placeholder="Enter Tonnage">
                    </div>
                </div>

                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Amount Ratio</label>
                    <div class="col-sm-8">
                        <input type="text" value="{{$row->amount_ratio}}" name="amount_ratio" class="form-control" placeholder="Enter Amount Ratio">
                    </div>
                </div>

                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">CTNS</label>
                    <div class="col-sm-8">
                        <input type="text" value="{{$row->ctns}}" name="ctns" class="form-control" placeholder="Enter CTNS">  
                    </div>
                </div>

                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">LINES</label>
                    <div class="col-sm-8">
                        <input type="text" value="{{$row->lines}}" name="lines" class="form-control" placeholder="Enter LINES">  
                    </div>
                </div>

                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">UNMET</label>
                    <div class="col-sm-8">
                        <input type="text"  value="{{$row->unmet}}" name="unmet" class="form-control" placeholder="Enter UNMET">  
                    </div>
                </div>

                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">DD per Week</label>
                    <div class="col-sm-8">
                        <input type="text"  value="{{$row->dd_per_week}}" name="dd_per_week" class="form-control" placeholder="Enter DD Per week">  
                    </div>
                </div>

                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Travel</label>
                    <div class="col-sm-8">
                        <input type="text" value="{{$row->travel}}" name="travel" class="form-control" placeholder="Enter Travel">  
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



