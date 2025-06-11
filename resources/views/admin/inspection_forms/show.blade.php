
@extends('layouts.admin.admin')
@section('content')
<?php
// echo "<pre>";
// print_r($row->toArray());die();
?>
<head>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</head>

<section class="content">
    <div class="box box-primary">
        <div class="box-header"></div>
        @include('message')


        <div class="data-description">
            <div class="form-group">
                <div class="row">
                    <div class="box-body">
                    <div class="col-md-12">
                    <div class="dropdown" style="float:right;">
                    <a href="{{ route('service.user', ['id'=>$row->id]) }}""><button type="button" class="btn btn-primary">Add to Service Entry</button></a>
                    
                    <a href="{{ route('resolve.user', ['id'=>$row->id]) }}"><button type="button" class="btn btn-primary">Resolve With Note</button></a>
                      <!-- <a href="{!! route('resolve.user')!!}"><button type="button" class="btn btn-primary">Resolve With Note</button></a> -->

                    </div>
                    </div>
                    
                  <div class="col-md-12">
                    <ul class="dropdown-menu">
                      <li><a class="dropdown-item" href="#">Add to Service Entry &nbsp; <i class="fa fa-reorder"></i></a> </li>
                      <li><a class="dropdown-item" href="#">Resolve With Note &nbsp; <i class="fa fa-check-circle" aria-hidden="true"></i></a></li>
                    </ul>

                 </div>
                 </div>
                         <div class="box-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-md-4 control-label">Asset</label>
                                <div class="col-md-8">
                                    <!-- <input type="text" value="{{$row->vehicle}}"> -->
                                  <p><?= isset($row->Vehicle) && $row->Vehicle!=''?$row->Vehicle->license_plate:'N/A'; ?></p>
                                </div>
                            </div>
                        </div>


                        
                        <div class="box-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-md-4 control-label">Issues Id</label>
                                <div class="col-md-8">
                                  <p><?= isset($row->id) && $row->id!=''?$row->id:'N/A'; ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-md-4 control-label">Summary</label>
                                <div class="col-md-8">
                                    <p><?= isset($row->summary) && $row->summary!=''?$row->summary:'N/A'; ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-md-4 control-label">Reported Date</label>
                                <div class="col-md-8">
                                    <p><?= isset($row->reported_date) && $row->reported_date!=''?$row->reported_date:'N/A'; ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-md-4 control-label">Reported By</label>
                                <div class="col-md-8">
                                    <p><?= isset($row->reported_by) && $row->reported_by!=''?$row->reported_by:'N/A'; ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-md-4 control-label">Assigned To</label>
                                <div class="col-md-8">
                                    <p><?= isset($row->User) && $row->User!=''?$row->User->name:'N/A'; ?></p>
                                </div>
                            </div>
                        </div>
                         <div class="box-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-md-4 control-label">Due Date</label>
                                <div class="col-md-8">
                                    <p><?= isset($row->due_date) && $row->due_date!=''?$row->due_date:'N/A'; ?></p>
                                </div>
                            </div>
                        </div> 
                        <div class="box-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-md-4 control-label">Description</label>
                                <div class="col-md-8">
                                    <p><?= isset($row->description) && $row->description!=''?$row->description:'N/A'; ?></p>
                                </div>
                            </div>
                        </div> 

                        <div class="box-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-md-4 control-label">Resolve</label>
                                <div class="col-md-8">
                                    <p><?= isset($row->resolve) && $row->resolve!=''?$row->resolve:'N/A'; ?></p>
                                </div>
                            </div>
                        </div> 

                         <div class="box-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-md-4 control-label">Status</label>
                                <div class="col-md-8">
                                    <p><?= $row->status ?></p>
                                </div>
                            </div>
                        </div>
                     
                </div>
            </div>
        </div>
    </div>
</div>
</section>

@endsection

@section('uniquepagestyle')
<link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet" />
@endsection

@section('uniquepagescript')
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
<script type="text/javascript">
    $(function () {
        $('.select2').select2();
        $("#selector_selects2").select2();
    });
</script>

<style type="text/css">
.cont {
  text-align: justify;
  text-justify: inter-word;;
}
</style>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script>
    
  </script>


@endsection

