
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
                                        
                                        <th >Order No</th>
                                        <th class="noneedtoshort">Date And Time</th>
                                        <th class="noneedtoshort" >Table No</th>
                                        <th width="5%">No Of Guest</th>
                                        <th class="noneedtoshort" >Item Description</th>
                                        <th class="noneedtoshort" >Condiments</th>
                                        <th class="noneedtoshort">Waiter</th>
                                         <th >Branch</th>
                                         <th >Total Amount</th>
                                         <th class="noneedtoshort">Status</th>
                                         <th >Order By</th>
                                     
                                        
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

   <script type="text/javascript">
       function printBill(order_id,user_id,print_type)
       {
          var confirm_text = 'bill';
          if(print_type == 'D')
          {
            confirm_text = 'docket';
          }
          var isconfirmed=confirm("Do you want to print "+confirm_text+"?");
          if (isconfirmed) 
          {
            jQuery.ajax({
                url: '{{route('admin.orders.receipt')}}',
                type: 'POST',
                async:false,   //NOTE THIS
                data:{order_id:order_id,user_id:user_id,print_type:print_type},
                headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                  },
              success: function (response) {
                var divContents = response;
                var printWindow = window.open('', '', 'width=400');
                printWindow.document.write('<html><head><title>Bill</title>');
                printWindow.document.write('</head><body >');
                printWindow.document.write(divContents);
                printWindow.document.write('</body></html>');
                printWindow.document.close();
                printWindow.print();
              }
            });
          }
       }
   </script>
@endsection


@section('uniquepagescript')

<script type="text/javascript">
    $(function() {

    var table = $('#closed_orders_datatables').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 100,

        order: [[0, "desc" ]],
         "ajax":{
                     "url": '{!! route('admin.completed.orders.datatables') !!}?order_id={{$_GET["order-id"]}}',
                     "dataType": "json",
                     "type": "POST",
                     "data":{ _token: "{{csrf_token()}}"}
                   },
       
        columns: [ 
                       
            { data: 'id', name: 'id', orderable:true },
           
          
            { data: 'created_at', name: 'created_at', orderable:true },
            { data: 'table_no', name: 'table_no', orderable:false, searchable:false  },
            { data: 'total_guests', name: 'total_guests', orderable:false, searchable:false  },
             { data: 'item_description', name: 'item_description', orderable:false, searchable:false  },
             { data: 'condiments', name: 'condiments', orderable:false, searchable:false  },
             { data: 'waiter_name', name: 'waiter_name', orderable:false, searchable:false  },
               { data: 'restro_name', name: 'restro_name', orderable:false, searchable:false  },

                { data: 'order_final_price', name: 'restro_name', orderable:false, searchable:false  },
                { data: 'status', name: 'status', orderable:false, searchable:false  },
                { data: 'order_by', name: 'order_by', orderable:false, searchable:false  }  
        ],
        "columnDefs": [
                { "searchable": false, "targets": 0 }
            ]
            
    });

    
});
</script>

@endsection
