
@extends('layouts.admin.admin')

@section('content')


    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                        <div class="box-header with-border no-padding-h-b">

                            
                            <br>
                            @include('message')
                        <div class="col-sm-12" align="right">    
                        <a href="{{route('admin.master-bills')}}" class="btn btn-danger">Back</a>
                        </div>
                        <div class="col-md-12 no-padding-h table-responsive">
                                <table class="table table-bordered table-hover" id="create_datatable_desc">
                                    <thead>
                                    <tr>
                                        
                                        <th>Order ID</th>
                                        <th>Order Date</th>
                                        <th>Item Title</th>
                                        <th>Item Quantity</th>
                                        <th>Price</th>
                                        <th>Delivery Status</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(isset($lists) && !empty($lists))
                                        <?php $b = 1; $totalorderamnt = 0; ?>
                                          @foreach($lists as $list)
                                            <tr>
                                            <td>{!! $list->order_id !!}</td>
                                            <td>
                                              {!! date('Y-m-d',strtotime($list->created_at)) !!}
                                            </td>
                                            <td>{!! $list->item_title !!}</td>
                                            <td>{!! $list->item_quantity !!}</td>
                                            <td>{!! $list->price !!}</td>
                                            <td>{!! $list->item_delivery_status !!}</td>
                                            </tr>
                                          @endforeach 
                                              
                                          @endif
                                    

                                    </tbody>
 
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
       function printBill(bill_id)
       {
          var confirm_text = 'bill';
          
          var isconfirmed=confirm("Do you want to print "+confirm_text+"?");
          if (isconfirmed) 
          {
            jQuery.ajax({
                url: '{{route('admin.orders.printmultiplebillreceipt')}}',
                type: 'POST',
                async:false,   //NOTE THIS
                data:{bill_id:bill_id},
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
