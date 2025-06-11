@extends('layouts.admin.admin')
@section('content')
<form method="GET" action="{{route('dispatched_items.report')}}" accept-charset="UTF-8" enctype="multipart/form-data" >

    <section class="content">    
        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title"> {!! $title !!} </h3></div>
            <div class="box-body">
                @include('message')     
                <?php             
                    $from_date = request()->from_date ?? date('Y-m-d');
                    $end_date = request()->end_date ?? date('Y-m-d');
                ?>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">From Date</label>
                            <input type="date" value="{{$from_date}}" class="form-control" name="from_date" required>
                        </div>
                    </div>   
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">End date</label>
                            <input class="form-control" name="end_date" type="date" value="{{$end_date}}" required>
                        </div>
                    </div> 
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">Type</label>
                            <select name="type" id="type" class="form-control" required>
                                <option value="Cash Sales"> Cash Sales </option>
                                <option value="Sales Invoice"> Sales Invoice </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">Inventory Item</label>
                            <select name="inventory" id="inventory" class="form-control" required>
                               
                                
                            </select>
                        </div>
                    </div>
                </div> 
                <div class="row">

                    <div class="col-md-12">
                        <input type="submit" class="btn btn-primary addExpense" name="request_type" value="Filter">
                        <button type="submit" class="btn btn-warning"  name="request_type" value="PDF"><i class="fa fa-file-pdf" aria-hidden="true"></i></button>
                        <button type="button" class="btn btn-success" onclick="printMe(this); return false;" name="request_type" value="Print"><i class="fa fa-print" aria-hidden="true"></i></button>
                    </div>
                </div>               
                <br> 
                <br> 
 
                <div class="row" >             
                    <div class="col-md-12" id="dispatch_items">
                    </div>       
                </div>
            </div>
        </div>
    </section>
</form>

@endsection

@section('uniquepagestyle')
<link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet" />
<link rel="stylesheet" href="{{asset('assets/admin/dist/datepicker.css')}}">

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
<div id="loader-on" style="position: absolute;top: 0;
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

$(document).on('click','.addExpense',function(e){
    e.preventDefault();
    $('#loader-on').show();
    $('#dispatch_items').html('');
    var postData = $(this).parents('form').serialize();
    postData = postData+'&request_type=Filter';
    var url = $(this).parents('form').attr('action');
    $.ajax({
        url:url,
        data:postData,
        contentType: false,
        cache: false,
        processData: false,
        method:'GET',
        success:function(out){
            $('#loader-on').hide();
            if(out.location)
            {
                $('#dispatch_items').html(out.location);
            }
        },        
        error:function(err)
        {
            $('#loader-on').hide();
            form.errorMessage('Something went wrong');							
        }
    });
});


</script>
<script src="{{asset('assets/admin/dist/bootstrap-datepicker.js')}}"></script>
    <script>
        $('.datepicker').datepicker({
        format: 'yyyy-mm-dd'
        });
        var inventory = function(){
            $("#inventory").select2(
            {
                minimumInputLength: 1,
                placeholder:'Select Inventory Item',
                ajax: {
                    url: '{{route("inventory_item_list")}}',
                    dataType: 'json',
                    type: "GET",
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function (data) {
                            var res = data.map(function (item) {
                                return {id: item.id, text: item.text};
                            });
                        return {
                            results: res
                        };
                    }
                },
            });
        };
        inventory();
        function printMe(input){
            var url = "{{route('dispatched_items.report')}}?"+$(input).parents('form').serialize()+'&request_type=Print';
            print_this(url);
        }
    </script>
@endsection


