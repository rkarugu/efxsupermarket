
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
                                <table class="table table-bordered table-hover" id="create_datatable">
                                    <thead>
                                    <tr>





                                        <th >S.No.</th>
                                       
                                        <th >Receipt No</th>
                                        <th>Date</th>


                                         <th >Amount</th>
                                        
                                       
                                      
                                       
                                      
                                          <th   class="noneedtoshort" >Action</th>
                                       
                                        <!--th style = "width:10%" class = "noneedtoshort">Date</th-->
                                        
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(isset($lists) && !empty($lists))
                                        <?php $b = 1;

                                        $total_amount = [];
                                        ?>
                                        @foreach($lists as $list)
                                         
                                            <tr>
                                                <td>{!! $b !!}</td>
                                                
                                                <td>{{ $list->document_no }}</td>
                                                <td>{{ $list->trans_date }}</td>
                                                 <td>{{ manageAmountFormat(abs($list->amount)) }}</td>
                                               
                    
                                                <td class = "action_crud">
                                               



                                                       @if(isset($permission[$pmodule.'___print-receipts']) || $permission == 'superadmin')
                                                       

                                                      
                                                <a class="printing" id = "print_{{ $list->id }}" title="Print Receipt" href="javascript:void(0)" onclick="printBill({{$list->id}})"><i aria-hidden="true" class="fa fa-print" style="font-size: 20px;" id = "print_receipt_id_{{$list->id}}"></i>
                                                </a>
                                                </span>
                                                        
                                                      @endif

                                                  








                                                </td>
                                                
											
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

    <span id="is_admin" data="{{ $is_admin }}">
   
@endsection

@section('uniquepagescript')

 <script type="text/javascript">
       function printBill(receipt_id)
       {
          var confirm_text = 'receipt';
          
          var isconfirmed=confirm("Do you want to print "+confirm_text+"?");
          if (isconfirmed) 
          {
            //alert(receipt_id);
            jQuery.ajax({
                url: '{{route('maintain-customers.print-receipt-by-id')}}',
                type: 'POST',
                async:false,   //NOTE THIS
                data:{receipt_id:receipt_id},
                headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                  },
              success: function (response) {
                  var is_admin = $("#is_admin").attr('data');

                 if(is_admin =='no')
                    {
                       $("#print_"+receipt_id).remove();
                    }
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
<!--script type="text/javascript">
  $(".printing").click(function(){
    var my_id = $(this).attr('id');
    var is_admin = $("#is_admin").attr('data');
   

    if(is_admin =='no')
    {
       $("#"+my_id).remove();
    }
   
    window.location.href = $(this).attr('data-href');
    
  });
</script-->

@endsection
