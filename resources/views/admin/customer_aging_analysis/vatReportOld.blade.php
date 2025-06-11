@extends('layouts.admin.admin')
@section('content')
<section class="content">
    <!-- Small boxes (Stat box) -->
    <div class="box box-primary">
        <div class="box-header with-border no-padding-h-b">                        
        @include('message')
        <div class="col-md-12 no-padding-h">
            <form action="" method="get">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                          <label for="">Date From</label>
                          <input type="date" name="from" id="from" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                          <label for="">Date To</label>
                          <input type="date" name="to" id="to" class="form-control" required>
                        </div>
                    </div>
                    @if(isset($permission['sales-and-receivables-reports___vat-report-dropdown-type']) || $permission == 'superadmin')
                    <div class="col-md-4">
                        <div class="form-group">
                          <label for="">Type</label>
                          <select name="type" id="type" class="form-control" required>
                              <option disabled selected>--Select Type--</option>
                              <option value="All">All User</option>
                              <option value="true">User with Upload Rights</option>
                              <option value="false">User without Upload Rights</option>
                          </select>
                        </div>
                    </div>
                    @else
                    <div class="col-md-4">
                        <input type="hidden" name="type" value="true">
                    </div>
                    @endif
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-biz-pinkish" value="filter" name="manage" onclick="filterReport(this); return false;">Filter</button>
                        <button type="submit" class="btn btn-biz-purplish" value="filter" name="manage" onclick="printPdf(this); return false;"><i class="fa fa-print" aria-hidden="true"></i></button>
                        <button type="submit" class="btn btn-biz-greenish" value="pdf" name="manage"><i class="fa fa-file-pdf"></i></button>
                    </div>
                </div>
            </form>
        </div>
            <div class="col-md-12 no-padding-h">
                <table class="table table-bordered table-hover tablest" id="create_datatable1">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Rate</th>
                            <th style="text-align: right">Goods</th>
                            <th style="text-align: right">Vat</th>
                            <th style="text-align: right">Sell Value</th>
                        </tr>
                    </thead>
                    <tbody>
                                              
                    </tbody>  
                    <tfoot>
                        <tr class="item">
                            <td></td>
                            <td></td>
                            <td style="text-align: right" id="posto">{{(0.00)}}</td>
                            <td style="text-align: right" id="saleso">{{(0.00)}}</td>
                            <td style="text-align: right" id="toto">{{(0.00)}}</td>
                        </tr>
                    </tfoot>                              
                </table>
            </div>                       
        </div>
    </div>
</section>
@endsection
@section('uniquepagestyle')
<div id="loader-on" style="
position: fixed;
top: 0;
text-align: center;
z-index: 999999;
width: 100%;
height: 100%;
background: #000000b8;
display:none;
">
  <div class="loader" id="loader-1"></div>
</div>
@endsection
@section('uniquepagescript')
    <script>
        function printPdf(input){
            var values = $(input).parents('form').serialize();
            values = values + "&manage=print";
            var url = "{!! route('customer-aging-analysis.vatReport') !!}?"+values;
            print_this(url);
        }
        function filterReport(input){
            $('#loader-on').show();
            $('.tablest tbody').html('');
            var values = $(input).parents('form').serialize();
            values = values + "&manage=filter";
            $.ajax({
                type: "GET",
                url: "{!! route('customer-aging-analysis.vatReport') !!}?"+values,
                success: function (response) {

                    $.each(response.data, function (indexInArray, valueOfElement) { 
                     
                         var va = '<tr>'+
            '<td>'+valueOfElement.title+'</td>'+
            '<td>'+valueOfElement.tax_value+'</td>'+
            '<td style="text-align: right">'+(valueOfElement.posto)+'</td>'+
            '<td style="text-align: right">'+(valueOfElement.saleso)+'</td>'+
            '<td style="text-align: right">'+(valueOfElement.toto)+'</td>'+
                                '</tr>';
                        $('.tablest tbody').append(va);
                    });
                    $('#posto').html(response.posto);
                    $('#saleso').html(response.saleso);
                    $('#toto').html(response.toto);
                    $('#loader-on').hide();
                },
                error: function (response) {
                    $('#loader-on').hide();
                    }
            });

        }
    </script>
@endsection