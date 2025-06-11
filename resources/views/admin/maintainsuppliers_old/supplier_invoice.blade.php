@extends('layouts.admin.admin')
@section('content')

<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border  no-padding-h-b"><h3 class="box-title"> {!! $title !!} </h3>
            @include('message')
            <form method="GET" action="" accept-charset="UTF-8"  enctype="multipart/form-data" >
                <div class="row">
                    <div class="col-md-6 ">
                        <div class="form-group">
                            <select class="form-control mlselec6t supplier" name="supplier" id="supplier">
                                <option selected disabled> - Select Supplier -</option>
                                @foreach ($supplierList as $item)
                                <option value="{{$item->id}}" @if(isset(request()->supplier) && request()->supplier==$item->id) selected @endif>{{$item->name}}({{$item->supplier_code}})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6 ">
                        <div class="form-group">
                            <label for=""></label>
                            <button type="submit" class="btn btn-biz-pinkish">Get Orders</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

        <!-- Small boxes (Stat box) -->
        @if (count($invoices)>0)
        <div class="box box-primary" id="invoices">
            <div class="box-header with-border no-padding-h-b">
                 
              
                <div class="col-md-12 no-padding-h ">
                <h3 class="box-title"> Invoices </h3>                           
                    <table class="table table-bordered table-hover" id="mainItemTable">
                        <tr>
                            <th width="2%">S.No.</th>
                            <th width="5%"  >Order No</th>
                            <th width="10%"  >Order date</th>
                            <th width="10%"  >Initiated By</th>
                            <th width="10%"  >Branch</th>
                            <th width="10%"  >Department</th>
                            <th width="10%"  >Exclusive Amount</th>
                            <th width="10%"  >Vat</th>
                            <th width="10%"  >Total Amount</th>
                            <th  width="15%" class="noneedtoshort" >Action</th>
                        </tr>
                       @foreach ($invoices as $b => $list)
                        <tr>
                            <td>{!! $b+1 !!}</td>
                            <td>{!! $list->purchase_no !!}</td>
                            <td>{!! $list->purchase_date !!}</td>
                            <td>{!! $list->getrelatedEmployee->name !!}</td>
                            <td >{{ isset($list->getBranch)?$list->getBranch->name:'' }}</td>
                            <td >{{ isset($list->getDepartment)?$list->getDepartment->department_name:'' }}</td>
                            <td>{{ manageAmountFormat($list->getRelatedItem->sum('total_cost'))}}</td>
                            <td>{{ manageAmountFormat($list->getRelatedItem->sum('vat_amount'))}}</td>
                            <td>{{ manageAmountFormat($list->getRelatedItem->sum('total_cost_with_vat'))}}</td>
                            <td class = "action_crud">
                               <a class="btn btn-biz-purplish btn-sm" href="{{route('maintain-suppliers.supplier_invoice_order_details',['order_id'=>$list->id,'supplier'=>request()->supplier])}}">Process Invoice</a>
                               <a class="btn btn-biz-greenish btn-sm supplier_archived supplier_archived_id_{{$list->id}} id_{{$list->id}}" data-purchase_no="{{$list->purchase_no}}" data-id="{{$list->id}}" href="{{route('maintain-suppliers.supplier_invoice_order_details')}}">Archive</a>
                            </td>
                        </tr>

                       @endforeach
                    </table>
                </div>
            </div>
        </div>
        @endif


    </section>
 

@endsection

@section('uniquepagestyle')
<link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet" />
<style type="text/css">
   .select2{
     width: 100% !important;
    }
    </style>
@endsection

@section('uniquepagescript')
<div id="loader-on" style="
position: absolute;
top: 0;
text-align: center;
display: block;
z-index: 999999;
width: 100%;
height: 100%;
background: #000000b8;
display:none;
">
  <div class="loader" id="loader-1"></div>
</div>
<script src="{{asset('js/sweetalert.js')}}"></script>
<script src="{{asset('js/form.js')}}"></script>
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
  <script type="text/javascript">
var form = new Form();
$(document).on('click','.supplier_archived',function(e){
    e.preventDefault();
   
    var id = $(this).data('id');
    var url = "{{route('maintain-suppliers.supplier_invoice_make_archive')}}";
    var supplier = $('#supplier').val();
    let isConfirm = confirm("Confirm to archive this invoice - "+$(this).data('purchase_no'));
    
            if (isConfirm) {
                $('#loader-on').show();
        $.ajax({
            url:url,
            data:{
                'id':id ,
                'supplier':supplier ,
                '_token':"{{csrf_token()}}"
            },
            method:'POST',
            success:function(out){
                $('#loader-on').hide();
                $(".remove_error").remove();
                if(out.result == 0) {
                    for(let i in out.errors) {
                        $("."+i).parent().append('<label class="error d-block remove_error w-100" id="'+i+'_error">'+out.errors[i][0]+'</label>');
                        $("."+i+"_"+id).parent().append('<label class="error d-block remove_error w-100" id="'+i+'_error">'+out.errors[i][0]+'</label>');
                    }
                }
                if(out.result === 1) {
                    form.successMessage(out.message);
                    if(out.location)
                    {
                        setTimeout(() => {
                            location.href = out.location;
                        }, 1000);
                    }
                }
                if(out.result === -1) {
                    form.errorMessage(out.message);
                }
            },
            
            error:function(err)
            {
                $('#loader-on').hide();
                $(".remove_error").remove();
                form.errorMessage('Something went wrong');							
            }
        });
    }else{

        $('#loader-on').hide();
          
        }
      
    
});


    $(function () {
        $(".mlselec6t").select2();
    });
  
</script>
<script src="{{asset('assets/admin/dist/bootstrap-datepicker.js')}}"></script>
    <script>
        $('.datepicker').datepicker({
        format: 'yyyy-mm-dd'
        });
    </script>
    
@endsection


