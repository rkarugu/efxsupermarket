@extends('layouts.admin.admin')
@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> {{"$title ( > 10,000)"}} </h3>
                    <a href="{{ url()->previous() }}" class="btn btn-primary"> Back </a>
                </div>
            </div>

            <div class="box-body" style="padding:15px">
                <div class="session-message-container">
                    @include('message')
                </div>

                <form action="" method="get">
                    <div class="row">
                    </div>
                </form>
                <hr>
                <form action="{{ route('transfers.returns.process_group_return') }}" method="post">
                    {{ @csrf_field() }}
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="create_datatable">
                            <thead>
                            <tr>
                                <th style="width: 3%;">#</th>
                                <th>Return No</th>
                                <th>Return Date</th>
                                <th>Initiated By</th>
                                <th>Invoice No</th>
                                <th>Invoice Date</th>
                                <th>Customer</th>
                                <th>Route</th>
                                <th>Amt Total</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($returns as $key => $return)
                            @if ($return->return_status == 1 )
                            <tr>
                                <th style="width: 3%; background-color: #31a5ec; color:white; font-weight:50px;">{{ $loop->index + 1 }}</th>
                                <td style="background-color: #31a5ec; color:white; font-weight:50px;"> {{ $return->return_number }} </td>
                                <td style="background-color: #31a5ec; color:white; font-weight:50px;"> {{ $return->return_date }} </td>
                                <td style="background-color: #31a5ec; color:white; font-weight:50px;"> {{ $return->initiated_by }} </td>
                                <td style="background-color: #31a5ec; color:white; font-weight:50px;"> {{ $return->invoice_number }} </td>
                                <td style="background-color: #31a5ec; color:white; font-weight:50px;"> {{ $return->invoice_date }} </td>
                                <td style="background-color: #31a5ec; color:white; font-weight:50px;"> {{ $return->customer }} </td>
                                <td style="background-color: #31a5ec; color:white; font-weight:50px;"> {{ $return->route }} </td>
                                <td style="text-align: right; background-color: #31a5ec; color:white; font-weight:50px;"> {{ number_format((float)$return->total_returns, 2) }} </td>
                                <td style="background-color: #31a5ec; color:white; font-weight:50px;">
                                    <div class="action-button-div">
                                        <a href="{{ route('transfers.return_list_items_pending', $return->return_number) }}" title="Approve Return"><i class="fa fa-eye"></i></a>
                                        @if ($return->status == 'pending')
                                            <span>{{"(Pending Reception)"}}</span>
                                        @else
                                            <span>{{"(Received)"}}</span>
                                        @endif

                                        {{-- <input type="checkbox" value="{{ $return->return_number }}" name="checkbox[]"> --}}
                                    </div>
                                </td>
                            </tr>
                            @else                                
                                <tr>
                                    <th style="width: 3%;">{{ $loop->index + 1 }}</th>
                                    <td> {{ $return->return_number }} </td>
                                    <td>{{ $return->return_date }}</td>
                                    <td>{{$return->initiated_by}}</td>
                                    <td> {{ $return->invoice_number }} </td>
                                    <td> {{ $return->invoice_date }} </td>
                                    <td> {{ $return->customer }} </td>
                                    <td> {{ $return->route }} </td>
                                    <td style="text-align: right;"> {{ number_format((float)$return->total_returns, 2) }} </td>
                                    <td>
                                        <div class="action-button-div">
                                            <a href="{{ route('transfers.return_list_items_pending', $return->return_number) }}" title="Approve Return"><i class="fa fa-eye"></i></a>

                                            <input type="checkbox" value="{{ $return->return_number }}" name="checkbox[]">
                                        </div>
                                    </td>
                                </tr>
                                @endif

                            @endforeach
                            </tbody>
                        </table>

                    </div>
                    <div class="d-flex justify-content-end">
                        <div class="otp-send  ">
                            <button type="submit" class="btn btn-primary " name="approve" value="approve"> Approve </button>

                            <button type="submit" class="btn btn-primary btn-sn opt" name="reject" value="reject" >
                                Reject
                            </button>
                        </div>
                    </div>
                </form>

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