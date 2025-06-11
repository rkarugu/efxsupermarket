
@extends('layouts.admin.admin')

@section('content')
  

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
                <div class="box box-primary">
                    <div class="box-header with-border no-padding-h-b">
                        <div  style="height: 150px ! important;"> 
                            <div class="card-header">
                                <i class="fa fa-filter"></i> Filter
                            </div><br>

                            {!! Form::open(['route' => 'confirm-invoice.invoice_dispatch_report','method'=>'POST']) !!}
                                {{ csrf_field() }}
                                <input type="hidden" name="type" value="{{@$type}}">
                                <div>
                                    <div class="col-md-12 no-padding-h">
        
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                            {!!Form::select('salesman_id', getAllsalesmanList(), null, ['placeholder'=>'Select Salesman', 'class' => 'form-control mlselect getshiftdata' ,'required'=>true ])!!}
                                            </div>
                                        </div>
            
                                        <div class="col-sm-5">
                                            <div class="form-group">
                                            {!!Form::select('shift_id[]', getAllShiftList(), null, ['placeholder'=>'Select Shift', 'class' => 'form-control  mlselec6t shiftList', 'multiple'=>'multiple','required'=>true  ])!!}
                                            </div>
                                        </div> 
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                            {!!Form::select('store', $storeLocation, null, ['placeholder'=>'Select Store', 'class' => 'form-control mlselect','required'=>true  ])!!}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12 no-padding-h">
                                       <div class="col-sm-1"><button type="submit" class="btn btn-warning" name="manage-request" value="pdf"><i class="fa fa-file-pdf"></i></button></div>
                                       <div class="col-sm-1"><button type="submit" class="btn btn-danger printMe" name="manage-request" value="print">Print</button></div>
                                    </div>
                                </div>

                            </form>
							<div class="reportList"></div>
                        </div>

                    </div>
                </div>

    </section>


  
@endsection


@section('uniquepagestyle')
  <link rel="stylesheet" href="{{asset('assets/admin/dist/datepicker.css')}}">
<link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet" />
@endsection

@section('uniquepagescript')
<script src="{{asset('assets/admin/dist/bootstrap-datepicker.js')}}"></script>
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
<script type="text/javascript">
    $(function () {
   
    $(".mlselect").select2();
});
$(document).ready(function(){
  $(".getshiftdata").change(function(){
	  var salesmanId = $(this).val();
    $.ajax({
	    url: "{{route('sales-and-receivables-reports.getShiftBySalesman')}}",
	    dataType:"JSON", 
	    data:{'_token':"{{csrf_token()}}",salesman_id:salesmanId,'shift_summary':'1'},
	    success: function(result){
		    $('.shiftList').html('');
			$.each(result, function (key, val) {
		    $('.shiftList').append('<option value="'+key+'">'+val+'</option>');
			});
//			$("#div1").html(result);
    	}});
  });
});

    $(function () {
	    		$(".mlselec6t").select2({
			closeOnSelect : false,
		});
//        $(".mlselec6t").select2();
    });

</script>

<script>
            

      $('.datepicker').datepicker({
        format: 'yyyy-mm-dd'
        });
        $('.printMe').click(function(e){   
            e.preventDefault();
            var postData = $(this).parents('form').serialize()+'&print=PRINT&manage-request=print';
            var url = $(this).parents('form').attr('action');
            // postData.append('request','PRINT');
            printMe(url,postData,'POST');
        });
    function printMe(url,data,type){
        jQuery.ajax({
            url: url,
            async:false,   //NOTE THIS               
            type: type,
            data:data,
            headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {               
                var divContents = response;
                var printWindow = window.open('', '', 'width=600');
                printWindow.document.write(divContents);
                printWindow.document.close();
                printWindow.print();
                printWindow.close();
            }
        }); 
    }
</script>


@endsection