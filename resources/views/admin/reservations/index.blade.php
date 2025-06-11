
@extends('layouts.admin.admin')

@section('content')


    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                        <div class="box-header with-border no-padding-h-b ">

                         <div  style="height: 150px ! important;"> 
                            <div class="card-header">
                            <i class="fa fa-filter"></i> Filter
                            </div><br>
                            {!! Form::open(['route' => 'reservations.index','method'=>'get']) !!}

                            <div>
                            <div class="col-md-12 no-padding-h">
                            <div class="col-sm-3">
                            <div class="form-group">
                            {!! Form::text('start-date', null, [
                            'class'=>'datepicker form-control',
                            'placeholder'=>'Start Date' ,'readonly'=>true]) !!}
                            </div>
                            </div>

                            <div class="col-sm-3">
                            <div class="form-group">
                            {!! Form::text('end-date', null, [
                            'class'=>'datepicker form-control',
                            'placeholder'=>'End Date','readonly'=>true]) !!}
                            </div>
                            </div>

                            <div class="col-sm-3">
                            <div class="form-group">
                            {!!Form::select('restaurant', $restro, null, ['placeholder'=>'Branch', 'class' => 'form-control'  ])!!}
                            </div>
                            </div>
                            </div>

                            <div class="col-md-12 no-padding-h">
                                <div class="col-sm-1"><button type="submit" class="btn btn-success" name="manage-request" value="filter"  >Filter</button></div>

                               

                                
                                <div class="col-sm-1">
                                <a class="btn btn-info" href="{!! route('reservations.index') !!}"  >Clear</a>
                                </div>
                            </div>
                            </div>

                            </form>
                        </div>
                            
                            <br>
                            @include('message')
                            <div class="col-md-12 no-padding-h table-responsive">
                                <table class="table table-bordered table-hover" id="closed_orders_datatables">
                                    <thead>
                                    <tr>
                                    <th >S.No.</th>

                                    <th >User</th>
                                    <th  >Branch</th>
                                    
                                    <th >Comment</th>
                                    
                                    <th  >Email</th>
                                    
                                    <th   >Event Type</th>
                                     <th   >Reservation Time</th>
                                    <th  >Status</th>

                                    <th   class="noneedtoshort" >Action</th>
                                       
                                        <!--th style = "width:10%" class = "noneedtoshort">Date</th-->
                                        
                                    </tr>
                                    </thead>
                                    <tbody>
                                  

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

<script type="text/javascript">
    $(function() {

    var table = $('#closed_orders_datatables').DataTable({
        processing: true,
        serverSide: true,

        order: [[0, "desc" ]],
         "ajax":{
                     "url": '{!! route('admin.reservations.datatables') !!}',
                     "dataType": "json",
                     "type": "POST",
                     "data":{ _token: "{{csrf_token()}}",'start_date':'{!! $start_date !!}','end_date':'{!! $end_date !!}','restaurant':'{!! $restaurant !!}'}
                   },
       
        columns: [ 
                       
            { data: 'id', name: 'id', orderable:true },
           
          
            { data: 'user', name: 'user', orderable:false },
            { data: 'restro', name: 'restro', orderable:false, searchable:false  },
           
             { data: 'comment', name: 'comment', orderable:false, searchable:false  },
            
               { data: 'email', name: 'email', orderable:false, searchable:false  },

               
                { data: 'event_type', name: 'event_type', orderable:false, searchable:false  },
                { data: 'reservation_time', name: 'reservation_time', orderable:false, searchable:false  },
                { data: 'status', name: 'status', orderable:true  },
                { data: 'action', name: 'action', orderable:false, searchable:false  },
        ],
        "columnDefs": [
                { "searchable": false, "targets": 0 }
            ]
            
    });

    
});

 
</script>

<script src="{{asset('assets/admin/dist/bootstrap-datetimepicker.js')}}"></script>
<script>
               

                  $('.datepicker').datetimepicker({
                  format: 'yyyy-mm-dd hh:ii:00',
                  minuteStep:1,
                 });




            </script>

@endsection
