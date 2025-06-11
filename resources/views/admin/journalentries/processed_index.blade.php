
@extends('layouts.admin.admin')

@section('content')


    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                        <div class="box-header with-border no-padding-h-b">
                            <form action="" method="post">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                        <label for="">From</label>
                                        <input type="date" name="from" id="from" class="form-control" value="{{date('Y-m-d')}}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="">To</label>
                                            <input type="date" name="to" id="to" class="form-control" value="{{date('Y-m-d')}}">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-sm btn-biz-purplish" onclick="filterMe(this); return false;" value="filter" name="filter">Filter</button>
                                        {{-- <button type="submit" class="btn btn-sm btn-biz-greenish" onclick="printMe(this); return false;" value="print" name="filter">Print</button> --}}
                                    </div>
                                </div>
                            </form>
                            <br>
                            @include('message')
                            <div class="col-md-12 no-padding-h">
                                <table class="table table-bordered table-hover" id="thisTable">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Transaction No.</th>
                                            <th>GL Account</th>
                                            <th>Name</th>
                                            <th>Debit</th>
                                            <th>Credit</th>
                                            <th>Narration</th>
                                            <th>Posted By</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($grns as $item)
                                            <tr>
                                                <td>{{$item->date}}</td>
                                                <td>{{$item->transaction_no}}</td>
                                                <td>{{$item->account}}</td>
                                                <td>{{$item->name}}</td>
                                                <td>{{$item->debit}}</td>
                                                <td>{{$item->credit}}</td>
                                                <td>{{$item->narrative}}</td>
                                                <td>{{$item->posted_by}}</td>
                                                <td>{!! $item->action !!}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>


    </section>
   
@endsection

@section('uniquepagescript')
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
<script src="{{asset('assets/admin/dist/bootstrap-datepicker.js')}}"></script>
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
<script>
    function printMe(url){
        $('#loader-on').show();
        print_this(url);
        $('#loader-on').hide();
        
    }
    function filterMe(input) {
        $('#loader-on').show();
        var $this = $(input);
        var data = $this.parents('form').serialize();
        var url = "{{route('journal-entries.processed_index')}}?filter=filter&"+data;
        $.ajax({
            type: "GET",
            url: url,
            success: function (response) {
                // console.log(response);
                $('#thisTable tbody').html('');
                var d = '';
                $.each(response, function (indexInArray, valueOfElement) { 
                    d = d+'<tr>'+ 
                        '<td>'+valueOfElement.date+'</td>'+
                        '<td>'+valueOfElement.transaction_no+'</td>'+
                        '<td>'+valueOfElement.account+'</td>'+
                        '<td>'+valueOfElement.name+'</td>'+
                        '<td>'+valueOfElement.debit+'</td>'+
                        '<td>'+valueOfElement.credit+'</td>'+
                        '<td>'+valueOfElement.narrative+'</td>'+
                        '<td>'+valueOfElement.posted_by+'</td>'+
                        '<td>'+valueOfElement.action+'</td>'+
                        '</tr>';
                    });
                $('#thisTable tbody').append(d);
              
                $('#loader-on').hide();

            }
        });
    }
</script>

@endsection