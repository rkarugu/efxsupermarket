@extends('layouts.admin.admin')
@section('content')

<section class="content">    
   

        <!-- Small boxes (Stat box) -->
       
        <div class="box box-primary" id="invoices">
            <div class="box-header with-border no-padding-h-b">
                 
              
                <div class="col-md-12 no-padding-h ">
                    <div style="width: 100%;min-height: 50px;">
                        <h3 class="box-title"> Invoiced Orderes 
                        </h3>                           
                        @if(isset($permission['suppliers-invoice___add']) || $permission == 'superadmin')
                        <a href="{{route('pending-grns.index')}}" style="float: right !important" class=" btn btn-primary">Add Supplier Invoice</a>  
                        @endif  
                    </div>
                    <table class="table table-bordered table-hover" id="mainItemTable">
                        <tr>
                            <th width="2%">S.No.</th>
                            <th width="5%"  >Supplier</th>
                            <th width="5%"  >Order No</th>
                            <th width="10%"  >Order date</th>
                            <th width="10%"  >Initiated By</th>
                            <th width="10%"  >Branch</th>
                            <th width="10%"  >Department</th>
                            <th width="10%"  >Exclusive Amount</th>
                            <th width="10%"  >Vat</th>
                            <th width="10%"  >Total Amount</th>
                        </tr>
                        @if (count($invoices)>0)
                       @foreach ($invoices as $b => $list)
                        <tr>
                            <td>{!! $b+1 !!}</td>
                            <td>{!! $list->purchase_no !!}</td>
                            <td>{!! @$list->getSupplier->name !!} - {!! @$list->getSupplier->supplier_code !!}</td>
                            <td>{!! $list->purchase_date !!}</td>
                            <td>{!! $list->getrelatedEmployee->name !!}</td>
                            <td >{{ isset($list->getBranch)?$list->getBranch->name:'' }}</td>
                            <td >{{ isset($list->getDepartment)?$list->getDepartment->department_name:'' }}</td>
                            <td>{{ manageAmountFormat($list->getRelatedItem->sum('total_cost'))}}</td>
                            <td>{{ manageAmountFormat($list->getRelatedItem->sum('vat_amount'))}}</td>
                            <td>{{ manageAmountFormat($list->getRelatedItem->sum('total_cost_with_vat'))}}</td>                        
                        </tr>

                       @endforeach
                       @endif
                    </table>
                </div>
            </div>
        </div>
      


    </section>
 

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
    .textData table tr:hover{
      background:#000 !important;
      color:white !important;
      cursor: pointer !important;
    }


/* ALL LOADERS */

.loader{
  width: 100px;
  height: 100px;
  border-radius: 100%;
  position: relative;
  margin: 0 auto;
  top: 35%;
}

/* LOADER 1 */

#loader-1:before, #loader-1:after{
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  border-radius: 100%;
  border: 10px solid transparent;
  border-top-color: #3498db;
}

#loader-1:before{
  z-index: 100;
  animation: spin 1s infinite;
}

#loader-1:after{
  border: 10px solid #ccc;
}

@keyframes spin{
  0%{
    -webkit-transform: rotate(0deg);
    -ms-transform: rotate(0deg);
    -o-transform: rotate(0deg);
    transform: rotate(0deg);
  }

  100%{
    -webkit-transform: rotate(360deg);
    -ms-transform: rotate(360deg);
    -o-transform: rotate(360deg);
    transform: rotate(360deg);
  }
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

$(document).on('submit','.addExpense',function(e){
    e.preventDefault();
    $('#loader-on').show();
    var postData = new FormData($(this)[0]);
var url = $(this).attr('action');
postData.append('_token',$(document).find('input[name="_token"]').val());
    $.ajax({
        url:url,
        data:postData,
        contentType: false,
        cache: false,
        processData: false,
        method:'POST',
        success:function(out){
    $('#loader-on').hide();

            $(".remove_error").remove();
            if(out.result == 0) {
                for(let i in out.errors) {
                    var id = i.split(".");
                    if(id && id[1]){
                        $("[name='"+id[0]+"["+id[1]+"]']").parent().append('<label class="error d-block remove_error w-100" id="'+i+'_error">'+out.errors[i][0]+'</label>');
                    }else
                    {
                        $("[name='"+i+"']").parent().append('<label class="error d-block remove_error w-100" id="'+i+'_error">'+out.errors[i][0]+'</label>');
                        $("."+i).parent().append('<label class="error d-block remove_error w-100" id="'+i+'_error">'+out.errors[i][0]+'</label>');
                    }
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


