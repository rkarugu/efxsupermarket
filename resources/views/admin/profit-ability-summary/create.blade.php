
@extends('layouts.admin.admin')
@section('content')
<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border"><h3 class="box-title"> {!! $title !!} </h3></div>
         @include('message')
        <form class="validate form-horizontal"  role="form" method="POST" action="{{ route($model.'.store') }}" enctype = "multipart/form-data">
            {{ csrf_field() }}
            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Route</label>
                    <div class="col-sm-8">
                        <select class="form-control" name="route">
                            <option value="">Select Route</option>
                            <option value="charagita">Charagita</option>
                            <option value="lanel">Lanel</option>
                            <option value="free_area">Free Area</option>
                            <option value="kamwaura">Kamwaura</option>
                            <option value="thudua">Thudua</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Date</label>
                    <div class="col-sm-8">
                        <input type="date" name="date" class="form-control" placeholder="Enter Date">
                    </div>
                </div>

                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Tonnage</label>
                    <div class="col-sm-8">
                        <input type="text" name="tonnage" class="form-control" placeholder="Enter Toonage">
                    </div>
                </div>

                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Amount Ratio</label>
                    <div class="col-sm-8">
                        <input type="text" name="amount_ratio" class="form-control" placeholder="Enter Amount Ratio">
                    </div>
                </div>

                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">CTNS</label>
                    <div class="col-sm-8">
                        <input type="text" name="ctns" class="form-control" placeholder="Enter CTNS">  
                    </div>
                </div>

                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">LINES</label>
                    <div class="col-sm-8">
                        <input type="text" name="lines" class="form-control" placeholder="Enter LINES">  
                    </div>
                </div>

                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">UNMET</label>
                    <div class="col-sm-8">
                        <input type="text" name="unmet" class="form-control" placeholder="Enter UNMET">  
                    </div>
                </div>

                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">DD per Week</label>
                    <div class="col-sm-8">
                        <input type="text" name="dd_per_week" class="form-control" placeholder="Enter DD Per week">  
                    </div>
                </div>

                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Travel</label>
                    <div class="col-sm-8">
                        <input type="text" name="travel" class="form-control" placeholder="Enter Travel">  
                    </div>
                </div>
            </div>

              


            <div class="box-footer text-right">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
</section>
@endsection


