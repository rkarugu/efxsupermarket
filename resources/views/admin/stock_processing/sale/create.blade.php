@extends('layouts.admin.admin')
@section('content')
<form class="validate form-horizontal" role="form" method="POST" action="{{ route('stock-processing.sales.store') }}"
                    enctype="multipart/form-data">
                @csrf
    <section class="content" style="padding-bottom:0px;">
        <div class="box box-primary" style="margin-bottom: 0px;">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h4> Add Sales (Short) </h4>
                    <a href="{{ route("stock-processing.sales") }}" role="button" class="btn btn-primary"> Back </a>
                </div>
            </div>
            <div class="session-message-container">
                @include('message')
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="box-body">
                        <div class="form-group" style="margin-bottom: 0px">
                            <label for="inputEmail3" class="col-sm-5 text-left">Employee Name</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control" class="form-control" value="{{ \Auth::user()->name }}" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="form-group" style="margin-bottom: 0px">
                            <label for="inputEmail3" class="col-sm-5 ">Date</label>
                            <div class="col-sm-7">
                                <input type="date" class="form-control" name="date" value="{{date('Y-m-d')}}" disabled>                                       
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="form-group" style="margin-bottom: 0px">
                            <label for="inputEmail3" class="col-sm-5 text-left">Location</label>
                            <div class="col-sm-7">
                                <input type="text" id="employee_location" class="form-control" disabled>                        
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="form-group" style="margin-bottom: 0px">
                            <label for="inputEmail3" class="col-sm-5 text-left">Bin Location</label>
                            <div class="col-sm-7">
                                <input type="text" id="employee_bin_location" class="form-control" disabled>  
                                <input type="hidden" id="employee_bin_location_id" name="employee_bin_location_id">                                     
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="box-body">
                        <div class="form-group" style="margin-bottom: 0px">
                            <label for="internal_debtor" class="col-sm-5 text-left">Internal Debtor</label>
                            <div class="col-sm-7">
                                <select name="internal_debtor" id="internal_debtor" class="select2 form-control" required>
                                    <option value="">Choose Employee</option>
                                    @foreach ($employees as $item)
                                        <option value="{{$item->id}}" 
                                            data-location_id="{{$item->employee->wa_location_and_store_id}}"
                                            data-phone="{{$item->employee->phone_number}}"
                                            data-bin_location ="{{$item->employee->uom ? $item->employee->uom->title : '-' }}"
                                            data-bin_location_id ="{{$item->employee->uom ? $item->employee->uom->id : 0 }}"
                                            data-location = "{{$item->employee->location_stores->location_name}}"
                                            data-balance = {{$item->getCurrentBalance()}}
                                            >
                                            {{$item->employee->name}}
                                        </option>
                                    @endforeach
                                </select>
                                
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="form-group" style="margin-bottom: 0px">
                            <label for="inputEmail3" class="col-sm-5 text-left">Phone</label>
                            <div class="col-sm-7">
                                <input type="text" id="employee_phone" class="form-control" disabled>                        
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="form-group" style="margin-bottom: 0px">
                            <label for="inputEmail3" class="col-sm-5 text-left">Current Balance</label>
                            <div class="col-sm-7">
                                <input type="text" id="current_balance" class="form-control" value="0.00" disabled>               
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="form-group" style="margin-bottom: 0px">
                            <label for="entry_date" class="col-sm-5 text-left">Stock Take Entry Date</label>
                            <div class="col-sm-5">
                                <input type="text" name="entry_date" id="entry_date" class="form-control">               
                            </div>
                            <div class="col-sm-2">
                                <button type="button" class="btn btn-success get_data">Process</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
                    


                {{-- <div class="box-footer">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div> --}}
            
        </div>
    </section>

    <section class="content">
        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                        <div class="box-header with-border no-padding-h-b">
                             
                            <div class="col-md-12 no-padding-h" >
                           <h3 class="box-title"> Invoice Line</h3>

                           <div id = "requisitionitemtable" name="item_id[0]">
                             
                                <table class="table table-bordered table-hover" id="mainItemTable">
                                    <thead>
                                    <tr>
                                      <th>Code</th>
                                      <th>Description</th>
                                      <th style="width: 90px;">QTY</th>
                                      <th>Selling Price</th>
                                      <th>VAT Type</th>
                                      <th>VAT</th>
                                      <th>Total</th>
                                      {{-- <th>Pend Entries</th> --}}
                                    </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot></tfoot>
                                </table>
                              </div>
                            </div>
                            <div class="col-md-12">
                            <div class="col-md-6 request_type">
                                <button type="submit" class="btn btn-success addExpense" value="save" disabled>Process</button>
                            </div>
                            <div class="col-md-3"></div>
                            <div class="col-md-3"></div>
                            </div>                               
                        </div>
                    </div>
    </section>
    <input type="hidden" id="store_location_id" name="store_location_id">
</form>
@endsection

@section('uniquepagestyle')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/material_red.css">
    <link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet"/>

    <style type="text/css">
            
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
position: fixed;
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
    <script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
    <script src="{{asset('js/sweetalert.js')}}"></script>
    <script src="{{asset('js/form.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script type="text/javascript">
        $(function () {
            $('body').addClass('sidebar-collapse');
            $(".select2").select2();
            $(".testIn").prop('disabled', true);
        });
    </script>

<script>
    var form = new Form();

    $(document).on('click','.addExpense',function(e){
        e.preventDefault();

        let getDataBtn = $('.get_data');
            let processDataBtn = $('.addExpense');

            let originalGetDataText = getDataBtn.text();
            let originalProcessDataText = processDataBtn.text();

            processDataBtn.prop('disabled', true).text('Processing...');
            getDataBtn.prop('disabled', true);
        
        var postData = new FormData($(this).parents('form')[0]);
        var url = $(this).parents('form').attr('action');
        postData.append('_token',$(document).find('input[name="_token"]').val());
        postData.append('request_type',$(this).val());
        
        $.ajax({
            url:url,
            data:postData,
            contentType: false,
            cache: false,
            processData: false,
            method:'POST',
            success:function(out){

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
                    if(out.id)
                    {
                        location.href = '/admin/stock-processing/sales/file/PDF/'+out.id;
                        setTimeout(
                        function() 
                        {                        
                            location.href = `{{route('stock-processing.sales')}}`;
                        }, 3000);                    
                    } else{
                        location.href = `{{route('stock-processing.sales')}}`;
                    }
                }
                if(out.result === -1) {
                    let errorMessage = '';
                        if (out.message) {
                            errorMessage = out.message
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            html: errorMessage,
                        });
                }
            },
            
            error:function(err)
            {
                $(".remove_error").remove();
                
                let errorMessage = '';
                    if (err?.responseJSON?.errors) {
                        for (let key in err.responseJSON.errors) {
                            if (err.responseJSON.errors.hasOwnProperty(key)) {
                                errorMessage += err.responseJSON.errors[key].join('<br>') + '<br>';
                            }
                        }
                    } else if (err?.responseJSON?.error) {
                        errorMessage = err.responseJSON.error
                    }else{
                        errorMessage = 'Something went wrong.'
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        html: errorMessage,
                    });
            },
                complete: function() {
                    getDataBtn.prop('disabled', false).text(originalGetDataText);
                    processDataBtn.prop('disabled', false).text(originalProcessDataText);
                }
        });
    });

    $(document).click(function(e){
        function fetchDisabledDates(id) {
            var url = "{{ route('stock-dates',[':id','type'=>'sale']) }}";
                url = url.replace(':id', id);
            return $.ajax({
                    url: url, // Adjust the URL to your endpoint
                    method: 'GET',
                    dataType: 'json'
                });
            }

        $('#internal_debtor').change(function(){
            $('.addExpense').prop('disabled', true);
            const $tableBody = $('#mainItemTable tbody');
            $tableBody.empty();

            const $tableFooter = $('#mainItemTable tfoot');
            $tableFooter.empty();
            $('#entry_date').val('');
            if ($(this).val()) {
                var bin_location_id = $(this).find(':selected').data('bin_location_id');

                $('#store_location_id').val($(this).find(':selected').data('location_id'));
                $('#employee_phone').val($(this).find(':selected').data('phone'));
                $('#employee_bin_location').val($(this).find(':selected').data('bin_location'));
                $('#employee_bin_location_id').val(bin_location_id);
                $('#employee_location').val($(this).find(':selected').data('location'));
                $('#current_balance').val($(this).find(':selected').data('balance'));
                $(".testIn").prop('disabled', false);

                fetchDisabledDates(bin_location_id).done(function(response) {
                    let availableDates = response;
                    
                    flatpickr("#entry_date", {
                        dateFormat: "Y-m-d",
                        enable: availableDates.map(date => {
                            return new Date(date);
                        })
                    });
                    
                }).fail(function() {
                    console.error('Failed to fetch dates.');
                });

            } else {
                $(".testIn").prop('disabled', true);
            }            
        });

      var container = $(".textData");
      // if the target of the click isn't the container nor a descendant of the container
      if (!container.is(e.target) && container.has(e.target).length === 0) 
      {
          container.hide();
      }
    });          

    $(document).on('click','.get_data',function(){
        var selectedDate = $('#entry_date').val();   

        let getDataBtn = $('.get_data');
            let processDataBtn = $('.addExpense');

            let originalGetDataText = getDataBtn.text();
            let originalProcessDataText = processDataBtn.text();

            getDataBtn.prop('disabled', true).text('Processing...');
            processDataBtn.prop('disabled', true);

        $.ajax({
                  url:"{{route('stock-dates-data')}}",
                  data:{
                    date: selectedDate,
                    type: 'sale',
                    bin_location: $('#employee_bin_location_id').val(),
                  },
                  method:'POST',
                  headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                  success:function(response){
                    form.successMessage('Stock Processing successful')
                    const $tableBody = $('#mainItemTable tbody');
                    $tableBody.empty();

                    const $tableFooter = $('#mainItemTable tfoot');
                    $tableFooter.empty();
                    if (response.data) {
                        $('.addExpense').prop('disabled', false);

                    response.data.forEach(item => {
                        const row = `
                            <tr>
                                <td>${item.code}</td>
                                <td>${item.title}</td>
                                <td>${item.quantity}</td>
                                <td>${item.price}</td>
                                <td>${item.vat_type}</td>
                                <td>${item.vat_amount}</td>
                                <td>${item.total_price}</td>
                            </tr>
                        `;
                        $tableBody.append(row);
                    });
                    
                    var totalFooter = `
                        
                            <tr>
                                <td colspan="6" style="text-align:right;"><b>Total Price</b></td>
                                <td><b>${response.total_amount}</b></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="6" style="text-align:right;"><b>Total Vat</b></td>
                                <td><b>${response.total_vat}</b></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="6" style="text-align:right;"><b>Total</b></td>
                                <td><b>${response.total}</b></td>
                                <td></td>
                            </tr>
                    `;
                    $tableFooter.append(totalFooter);
                    }
                    
                  },
                  
                  error:function(err)
                  {
                      $(".remove_error").remove();
                      let errorMessage = '';
                    if (err?.responseJSON?.errors) {
                        errorMessage = err.responseJSON.errors
                    } else if (err?.responseJSON?.error) {
                        errorMessage = err.responseJSON.error
                    }else {
                        errorMessage = 'Something went wrong. Please try again.'
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        html: errorMessage,
                    });                         
                  },
                complete: function() {
                    getDataBtn.prop('disabled', false).text(originalGetDataText);
                    processDataBtn.prop('disabled', false).text(originalProcessDataText);
                }
              }); 
    });
  
  function getDates(id){
    return $.ajax({
                    url: "/admin/stock-dates/"+id, 
                    method: 'GET',
                    dataType: 'json'
                });
  }
  
  
      </script>
@endsection
