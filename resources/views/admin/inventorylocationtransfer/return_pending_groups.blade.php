@extends('layouts.admin.admin')
@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> {{"$title ( > 10,000)"}} </h3>
                </div>
            </div>

            <div class="box-body" style="padding:15px">
                <div class="session-message-container">
                    @include('message')
                </div>

                <form action="" method="get">
                    <div class="row">

                        <div class="col-sm-3">
                            <label for="branch">Branch</label>
                            <select name="branch" id="branch" class="form-control">
                                <option value="">Select Branch</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}" @if (request()->branch == $branch->id) selected @endif>{{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Route</label>
                                <select name="route_id" id="route_id" class="form-control"></select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group" style="margin-top: 25px; ">
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <a href="{{ route('transfers.return_list_groups') }}" class="btn btn-primary ml-12"> Clear </a>
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
                            <th>Return Date</th>
                            <th>Route</th>
                            <th>Amt Total</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($returns as $key => $return)
                            <tr>
                                <th style="width: 3%;">{{ $loop->index + 1 }}</th>

                                <td> {{ \Carbon\Carbon::parse($return->invoice_date)->toDateString()  }} </td>
                                <td> {{ $return->route }} </td>
                                <td style="text-align: right;"> {{ number_format((float)$return->total_returns, 2) }} </td>
                                <td>
                                    <div class="action-button-div">
                                        <a href="{{ route('transfers.return_list_route', [$return->route, $return->invoice_date]) }}" title="Approve Return"><i class="fa fa-eye"></i></a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
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

        const routes = @json($routes);
        const selectedRoute = @json(request()->route_id);

        function filterRoutes() {
            let selectedBranch = $('#branch').val();

            let routeSelect = $('#route_id');

            routeSelect.empty();
            routeSelect.append('<option value="">Select Route</option>');

            filteredRoutes = routes.filter(route => route.restaurant_id == selectedBranch);

            filteredRoutes.forEach(route => {
                let option = $('<option></option>', {
                    value: route.route_name,
                    text: route.route_name,
                    selected: selectedRoute == route.route_name
                });
                
                routeSelect.append(option)
            });
        }
        
        $(function() {
            filterRoutes()
        })
        
        $('#branch').on('change', function () {
            filterRoutes()
        })
    </script>
@endsection