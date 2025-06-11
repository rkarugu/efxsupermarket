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
                                <table class="table table-bordered table-hover" id="closed-order-table">
                                    <thead>
                                    <tr>
                                        
                                        <th >Receipt ID</th>
                                        <th class="noneedtoshort" >Date</th>
                                        <th  >Waiter Name</th>
                                         <th  >Cashier  Name</th>
                                        <th >Number of Orders</th>
                                         


                                        
                                        <th class="noneedtoshort" >Total Amount</th>

                                       
                                         <th class="noneedtoshort" >Action</th>
                                       

                                    </tr>
                                    </thead>
                                    
                                </table>
                            </div>
                        </div>
                    </div>

<input type = "hidden" id = "receipt_id_print" name="receipt_id_print" value="{{ isset($receipt_id)?$receipt_id:''}}">
    </section>
   <style type="text/css">
       .table td {
  font-size: 13px;
}

   </style>

   <script type="text/javascript">
       function printBill(receipt_id)
       {
          var confirm_text = 'receipt';
          
          var isconfirmed=confirm("Do you want to print "+confirm_text+"?");
          if (isconfirmed) 
          {
            jQuery.ajax({
                url: '{{route('admin.orders.multiplebillreceipt')}}',
                type: 'POST',
                async:false,   //NOTE THIS
                data:{receipt_id:receipt_id,printing_scrrent_type:'closedorder'},
                headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                  },
              success: function (response) {

                $("#print_receipt_id_"+receipt_id).remove();
                var divContents = response;
                for (i = 0; i < 2; i++) {
                  console.log(i);
                var printWindow = window.open('', '', 'width=400');
                printWindow.document.write('<html><head><title>Receipt</title>');
                printWindow.document.write('</head><body >');
                printWindow.document.write(divContents);
                printWindow.document.write('</body></html>');
                printWindow.document.close();
                printWindow.print();
              }
              }
            });
          }
       }
   </script>
@endsection

@section('uniquepagescript')

<script type="text/javascript">
    $(function() {
    var table = $('#closed-order-table').DataTable({
        processing: true,
        serverSide: true,

        order: [[1, "desc" ]],
         "ajax":{
                     "url": '{!! route('admin.datatables.closed.orders') !!}',
                     "dataType": "json",
                     "type": "POST",
                     "data":{ _token: "{{csrf_token()}}"}
                   },
       
        columns: [            
            { data: 'id', name: 'id', orderable:true },
            { data: 'created_at', name: 'created_at', orderable:true },
            { data: 'waiter_name', name: 'waiter_name', orderable:false, searchable:false  },
            { data: 'cashier_name', name: 'cashier_name', orderable:false, searchable:false  },
            { data: 'number_of_orders', name: 'number_of_orders', orderable:false, searchable:false  },
             { data: 'total_amount', name: 'total_amount', orderable:false, searchable:false  },
            { data: 'action', name: 'action', orderable:false, searchable:false }    
        ],
        "columnDefs": [
                { "searchable": false, "targets": 0 }
            ]
            
    });

    
});

     $(document).ready(function(){
      var receipt_id = $("#receipt_id_print").val();
      if(receipt_id != "")
      { 
       
           var confirm_text = 'receipt';
                       jQuery.ajax({
                url: '{{route('admin.orders.multiplebillreceipt')}}',
                type: 'POST',
                async:false,   //NOTE THIS
                data:{receipt_id:receipt_id,printing_scrrent_type:'closedorder'},
                headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                  },
              success: function (response) {

                $("#print_receipt_id_"+receipt_id).remove();
                var divContents = response;
                 for (i = 0; i < 2; i++) {
                  console.log(i);
                var printWindow = window.open('', '', 'width=400');
                printWindow.document.write('<html><head><title>Receipt</title>');
                printWindow.document.write('</head><body >');
                printWindow.document.write(divContents);
                printWindow.document.write('</body></html>');
                printWindow.document.close();
                printWindow.print();
                }
              }
            });
                    
       
      }

    });
</script>

@endsection
