
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
                                <table class="table table-bordered table-hover" id="create_datatable_desc_closed_order_payments">
                                    <thead>
                                    <tr>
                                        
                                        <th >Receipt ID</th>
                                         <th  >Waiter Name</th>
                                        <th >Date</th>
                                        
                                        <th >Number of Orders</th>
                                         


                                        
                                        <th class="noneedtoshort" >Total Amount</th>

                                       
                                         <th class="noneedtoshort" >Action</th>
                                       

                                    </tr>
                                    </thead>
                               
                                </table>
                            </div>
                        </div>
                    </div>


    </section>

     <div class="modal fade new-m tnc send-lesson-popup" id="application-approval" role="dialog" tabindex="-1"  aria-hidden="true" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      
    </div>
  </div>
</div> 
   <style type="text/css">
       .table td {
  font-size: 13px;
}

   </style>

   <script type="text/javascript">
    function uploadOfferletter(receipt_id)
    {
      var url = '{{ route("admin.get.payment.summary", ":receipt_id") }}';
      url = url.replace(':receipt_id', receipt_id);
      $('#application-approval').find(".modal-content").load(url);
    }
   </script>

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
                data:{receipt_id:receipt_id},
                headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                  },
              success: function (response) {

                
                var divContents = response;
                var printWindow = window.open('', '', 'width=400');
                printWindow.document.write('<html><head><title>Receipt</title>');
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

    var table = $('#create_datatable_desc_closed_order_payments').DataTable({
        processing: true,
        serverSide: true,

        order: [[0, "desc" ]],
         "ajax":{
                     "url": '{!! route('admin.datatables.closed.orders.payments.with.datatables') !!}',
                     "dataType": "json",
                     "type": "POST",
                     "data":{ _token: "{{csrf_token()}}"}
                   },
       
        columns: [            
            { data: 'id', name: 'id', orderable:true },
           
            { data: 'waiter_name', name: 'waiter_name', orderable:false, searchable:false  },
            { data: 'created_at', name: 'created_at', orderable:true },
            { data: 'number_of_orders', name: 'number_of_orders', orderable:false, searchable:false  },
             { data: 'total_amount', name: 'total_amount', orderable:false, searchable:false  },
            { data: 'action', name: 'action', orderable:false, searchable:false }    
        ],
        "columnDefs": [
                { "searchable": false, "targets": 0 }
            ]
            
    });

    
});
</script>

@endsection