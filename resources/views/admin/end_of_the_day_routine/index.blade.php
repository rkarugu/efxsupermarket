
@extends('layouts.admin.admin')

@section('content')


    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                        <div class="box-header with-border no-padding-h-b">
                            <!-- Button trigger modal -->
                            <!-- Modal -->
                            @if(isset($permission[$pmodule.'___add']) || $permission == 'superadmin')
                            <div class="modal fade" id="modelId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
                                <form action="{!! route($model.'.create')!!}" method="get">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Add Routine</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                  <label for="">Select Routine Date</label>
                                                  <input type="date"
                                                    class="form-control" name="date" id="date" aria-describedby="helpId" placeholder="">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary">Save</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div align = "right"> <a data-toggle="modal" data-target="#modelId" href = "#" class = "btn btn-success">Add {!! $title !!}</a></div>
                             @endif
                            <br>
                            @include('message')
                            <div class="col-md-12 no-padding-h">
                                <table class="table table-bordered table-hover" id="create_datatable">
                                    <thead>
                                    <tr>
                                        <th width="10%">S.No.</th>
                                         <th width="25%"  >Routine No</th>
                                       
                                        <th width="15%"  >Routine Date</th>
                                        <th width="15%"  >Open Time</th>
                                        <th width="20%"  >Close Time</th>
                                           
                                           <th width="20%"  >Status</th>
                                           
                                                                              
                                       
                                        
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(isset($lists) && !empty($lists))
                                        <?php $b = 1;?>
                                        @foreach($lists as $list)
                                         
                                            <tr>
                                                <td>{!! $b !!}</td>
                                                 <td>{!! $list->routine_no !!}</td>
                                                <td>{!! $list->start_date !!}</td>
                                                <td>{!! $list->open_time !!}</td>
                                                <td>{!! $list->close_time !!}</td>
                                                 <td>{!! $list->status !!}</td>
                                            </tr>
                                           <?php $b++; ?>
                                        @endforeach
                                    @endif


                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>


    </section>
   
@endsection
