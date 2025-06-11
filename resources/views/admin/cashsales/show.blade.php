@extends('layouts.admin.admin')
@section('content')

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                        <div class="box-header with-border no-padding-h-b">
						<h3 class="box-title"> {!! $title !!} </h3>
                             <br>
                            @include('message')
                            <div class="col-md-12 no-padding-h table-responsive">
                                   <table class="table table-bordered table-hover" id="create_datatable">
                                    <thead>
                                    <tr>
                                      <th>Sr No.</th>
                                      <th>Description</th>
                                      <th>Item No</th>
                                     <th>QTY</th>
                                     <th>Standard Cost</th>
                                      <th>Unit Price</th>
                                       <th>Line Amount</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($row->getRelatedItem as $item)
                                       <tr>
                                          <td>{{ $item->id}}</td>
                                          <td>{{ $item->item_name}}</td>
                                          <td>{{ $item->item_no }}</td>
                                          <td><span id = "quantity_{{ $item->id }}">{{ manageAmountFormat($item->quantity) }}</span></td>
                                          <td>{{ $item->standard_cost }}</td>
                                          <td><span id = "unit_price_{{ $item->id }}">{{ manageAmountFormat($item->unit_price) }}</span>
                                          <span style="display:none;" id = "actual_unit_price_{{ $item->id }}">{{ $item->actual_unit_price }}</span>

                                          </td>
                                          <td><span id = "line_amount_{{ $item->id }}">{{ manageAmountFormat($item->unit_price*$item->quantity) }}</span></td>
                                     
                                      
                                    </tr>
                                    @endforeach
                        
                                   


                                    </tbody>
                                </table>
                              </div>


                               
                        </div>
                    </div>


    </section>


    <!-- Modal -->

@endsection

@section('uniquepagestyle')

<link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet" />
 <link rel="stylesheet" href="{{asset('assets/admin/dist/datepicker.css')}}">

 <style type="text/css">
   .select2{
    width: 100% !important;
   }
   #note{
    height: 60px !important;
   }
   .align_float_right
{
  text-align:  right;
}
 </style>
@endsection

@section('uniquepagescript')
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
  <script type="text/javascript">


  $("#wa_customer_id").change(function(){
      var customerid = $(this).val();
      $("#address").val('');
      $("#phone_number").val('');
       if(customerid != "")
       {
            $("#selected_customer_id").val(customerid);
            jQuery.ajax({
                url: '{{route('proforma-invoice.get.customer-detail')}}',
                type: 'POST',
                data:{customer_id:customerid},
                headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) 
                {
                  var obj = jQuery.parseJSON(response);
                  $("#address").val(obj.address);
                  $("#phone_number").val(obj.telephone);
                  
                }
            });
       }
       else
       {
       
        $("#selected_customer_id").val('');
       }
     });

  

   















   

   


  





  

</script>
<script src="{{asset('assets/admin/dist/bootstrap-datepicker.js')}}"></script>
    <script>
        $('.datepicker').datepicker({
        format: 'yyyy-mm-dd'
        });
    </script>
    
   
@endsection


