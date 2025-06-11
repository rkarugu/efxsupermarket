@extends('layouts.admin.admin')
@section('content')
    <!-- Main content -->
    <section class="">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border no-padding-h-b">
                <div class="row">
                    <div class="col-md-9">
                        <h3 class="box-title"> General Ledger Journal Inquiry </h3>
                    </div>
                    <div class="col-sm-3">
                        <div align="right">
                            <i class="fa fa-filter" aria-hidden="true"></i> Filter
                        </div>
                    </div>
                </div>
                @include('message')
                <div class="no-padding-h">
                    
                    <form action="{{route('admin.journal-inquiry.search')}}" method="GET" id="subme">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                <label for="">Transaction Type</label>
                                    <select class="form-control" name="account" id="paymentAccount">
                                        @foreach ($number_series as $item)
                                        <option value="{{$item->type_number}}">{{$item->description}} - {{$item->code}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                        <label for="">From</label>
                                        <input type="date" class="form-control" name="start-date" id="payment_date">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                        <label for="">To</label>
                                        <input type="date" class="form-control" name="end-date" id="payment_date">
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-center" style="margin-top: 24px;">
                                        <button type="submit" class="btn btn-primary subme" value="filter" name="manage">Filter</button>
                                        <button type="submit" class="btn btn-primary subme" value="export" name="manage">Export</button>
                                    </div>
                                </div>
                            </div>
                           
                            
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border no-padding-h-b">
                @include('message')
                <div class="col-md-12 no-padding-h" id="getintervalview">
                    
                    
                </div>
            </div>
        </div>
    </section>
@endsection
@section('uniquepagestyle')
<link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet" />
<style>
    .select2.select2-container.select2-container--default
    {
        width: 100% !important;
    }

</style>
@endsection
@section('uniquepagescript')
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>

<script>
    var paymentAccount = function(){
            $("#paymentAccount").select2();
        }
        paymentAccount();
    $('.subme').click(function(e){
        if($(this).attr('value') != 'export')
        {
            // console.log($(this));                  
            $('.loder').css('display','block');
            var data = $(this).parents('form').serialize();
            data = data+'&manage=filter';
            e.preventDefault();  
            $.ajax({
                type: "GET",
                url: "{{route('admin.journal-inquiry.search')}}",
                data:data,
                contentType: false,
                cache: false,
                processData: false,
                success: function (response) {
                    $('#getintervalview').html(response);
                    $('.loder').css('display','none');
                }
            });
        }
    });
</script>
@endsection
