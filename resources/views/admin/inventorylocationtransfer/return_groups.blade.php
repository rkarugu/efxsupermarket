@extends('layouts.admin.admin')
@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Routes Over Limit Return Summary </h3>
                    {{-- <a href="{{ url()->previous() }}" class="btn btn-primary"> Back </a> --}}
                </div>
            </div>

            <div class="box-body" style="padding:15px">
                <div class="session-message-container">
                    @include('message')
                </div>

                <form action="" method="get">
                    <div class="row">
                       <div class="col-md-2">
                       <div class="form-group">
                        <label for="">From</label>
                        <input type="date" name="start_date" id="start_date" class="form-control" value="{{$d1}}">
                       </div>
                        </div>
                        <div class="col-md-2">
                        <div class="form-group">
                       <label for="">To</label>
                      <input type="date" name="end_date" id="end_date" class="form-control" value="{{$d2    }}">
                       </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="">Route</label>
                                <select name="route_id" id="route_id" class="form-control">
                                    <option value="" selected disabled> Select Route</option>
                                    @foreach($routes as $route)
                                        <option value="{{ $route->route_name }}" {{ $route->route_name == request()->route_id ? 'selected' : '' }}> {{ $route->route_name }} </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="">Type</label>
                                <select name="type" id="type" class="form-control mlselec6t" >
                                    <option value="" selected disabled>Select Type</option>
                                    <option value="1" {{request()->type == 1 ? 'selected' : ''}}>Over 10,000</option>
                                    <option value="2" {{request()->type == 2 ? 'selected' : ''}}>Over 100,000</option>
                                    <option value="3" {{request()->type == 3 ? 'selected' : ''}}>Past 48hrs</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group" style="margin-top: 25px; ">
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <a href="{{ route('transfers.return_groups') }}" class="btn btn-primary ml-12"> Clear </a>
                            </div>
                        </div>
                    </div>
                </form>

                <hr>

                <div class="table-responsive">
                    <table class="table table-hover table-bordered" id="create_datatable">
                        <thead>
                        <tr>
                            <th style="width: 3%;">#</th>
                            <th>Route</th>
                            <th>Amount Total</th>
                            {{-- <th>Action</th> --}}
                            
                        </tr>
                        </thead>
                        <tbody>
                            <?php $total = 0;?>
                        @foreach($returns as $key => $return)
                            <tr>
                                <th style="width: 3%;">{{ $loop->index + 1 }}</th>
                                <td> {{ $return->route }} </td>
                                 <td style="text-align: right;"> {{ number_format((float)$return->total_returns, 2) }} </td>
                                 {{-- <td><span><a href="{{route('transfers.return_groups.over_limit_returns_details', [$d1, $d2, $return->route_id, $type])}}"><i class="fa fa-eye" title="view details"></i></a></span></td> --}}
                                
                            </tr>
                             <?php $total += $return->total_returns;?>
                        @endforeach
                        </tbody>
                        <tfoot>
                            <th colspan="2">Total</th>
                             <th style="text-align: right;"> {{ number_format((float)$total, 2) }}</th>
                             {{-- <th></th> --}}
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    

    </section>
@endsection
@section('uniquepagestyle')
    <link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet"/>
@endsection


@section('uniquepagescript')
    <script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
    <script>
        function printgrn(transfer_no) {
            jQuery.ajax({
                url: '{{route('transfers.print-return')}}',
                async: false,   //NOTE THIS
                type: 'POST',
                data: {transfer_no: transfer_no},
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

        $(function () {
            $("#route_id").select2();
            $(".mlselec6t").select2();
        });

    $(document).on("click", ".open-confirmDialog", function () {
     var return_number = $(this).data('id');
     $(".modal-body #return_number").val(return_number );
     // As pointed out in comments, 
     // it is unnecessary to have to manually call the modal.
      $('#approve').modal('show');
    });
    </script>
@endsection