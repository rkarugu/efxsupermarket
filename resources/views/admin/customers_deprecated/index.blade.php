
@extends('layouts.admin.admin')

@section('content')


    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                        <div class="box-header with-border no-padding-h-b">

                            
                            <br>
                            @include('message')
                            <div class="col-md-12 no-padding-h table-responsive">
                                <table class="table table-bordered table-hover" id="closed_orders_datatables">
                                    <thead>
                                    <tr>
                                        
                                        <th width="5%">S.No.</th>

                                        <th width="10%">Name</th>
                                        <th width="15%">Phone</th>
                                        <th width="15%">Nationality</th>
                                        <th width="15%">Created</th>
                                         <th width="15%">Wallet Balance</th>

                                        <th  width="8%" class="noneedtoshort" >Action</th>
                                     
                                        
                                    </tr>
                                    </thead>
                                  
                                </table>
                            </div>
                        </div>
                    </div>


    </section>
   <style type="text/css">
       .table td {
  font-size: 13px;
}

   </style>

 
@endsection


@section('uniquepagescript')

<script type="text/javascript">
    $(function() {

    var table = $('#closed_orders_datatables').DataTable({
        processing: true,
        serverSide: true,

        order: [[0, "desc" ]],
         "ajax":{
                     "url": '{!! route('users.show.datatables') !!}',
                     "dataType": "json",
                     "type": "POST",
                     "data":{ _token: "{{csrf_token()}}"}
                   },
       
        columns: [ 
                       
            { data: 'id', name: 'id', orderable:true },
           
          
            { data: 'name', name: 'name', orderable:true },
            { data: 'phone_number', name: 'phone_number',orderable:true },
            { data: 'nationality', name: 'nationality',orderable:true },
             { data: 'created_at', name: 'created_at', orderable:true  },
              { data: 'wallet_balance', name: 'wallet_balance', orderable:false, searchable:false  },
             { data: 'action', name: 'action', orderable:false, searchable:false  }
            
        ],
        "columnDefs": [
                { "searchable": false, "targets": 0 }
            ]
            
    });

    
});
</script>

@endsection
