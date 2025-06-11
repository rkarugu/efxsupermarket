@extends('layouts.admin.admin')

@section('content')

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border no-padding-h-b">
                @if(!env("USE_OTP"))
                    @if(isset($permission[$pmodule.'___invoices-create']) || $permission == 'superadmin')
                        @if(isset($permission[$pmodule.'___invoices-create']) || $permission == 'superadmin')
                            <div align="right"><a href="{!! route($model.'.create')!!}" class="btn btn-success">Add New Sales Invoice</a></div>
                        @endif
                    @endif
                @else
                    @if(isset($permission[$pmodule.'___invoices-create']) || $permission == 'superadmin')
                        <div align="right">
                            <button id="addNewInvoiceBtn" class="btn btn-success">Add New Sales Invoice</button>
                        </div>

                        <!-- OTP Modal -->
                        <div id="otpModal" class="modal" tabindex="-1" role="dialog">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">OTP Verification</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Please enter the OTP sent to the admin:</p>
                                        <input type="text" id="otpInput" class="form-control" placeholder="Enter OTP">
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-primary" id="verifyOtpBtn">Verify OTP</button>
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif




                    <br>
                @include('message')
                <div class="col-md-12 no-padding-h">
                    <form action="" method="get">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">From</label>
                                    <input type="date" name="from" id="start-date" class="form-control" value="{{request()->input('from') ?? date('Y-m-d')}}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">To</label>
                                    <input type="date" name="to" id="end-date" class="form-control"  value="{{request()->input('to') ?? date('Y-m-d')}}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <button type="submit" id="filter" class="btn btn-primary btn-sm" style="margin-top: 25px;">Filter</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <table class="table table-bordered table-hover" id="create_datatable">
                        <thead>
                        <tr>
                            <th width="5%">S.No.</th>

                            <th width="10%">Invoice No</th>
                            <th width="10%">Invoice Date</th>
                            {{-- <th width="13%"  >Vehicle Reg</th> --}}
                            <th width="15%">Route</th>
                            <th width="15%">Salesman Name</th>
                            <th width="15%">Customer</th>
                            <th width="10%">Status</th>


                            <th width="15%" class="noneedtoshort">Action</th>

                            <!--th style = "width:10%" class = "noneedtoshort">Date</th-->

                        </tr>
                        </thead>
                        <tbody>
                        @if(isset($lists) && !empty($lists))

                            @foreach($lists as $list)

                                <tr>
                                    <td>{{$loop ->iteration }}</td>

                                    <td>{!! $list->requisition_no !!}</td>
                                    <td>{!! $list->requisition_date !!}</td>
                                    <td>{!! $list->route !!}</td>
                                    <td>
                                        @if($list->getrelatedEmployee)
                                            {!! $list->getrelatedEmployee->name !!}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        {{ $list->name }}
                                    </td>
                                    <td>{!! $list->status !!}</td>
                                    <td class="action_crud">

                                        @if($list->status == 'UNAPPROVED')
                                            @if(isset($permission['sales-invoice___edit-invoice']) || $permission == 'superadmin')
                                                <span>
                                                    <a title="Edit" class="btn btn-primary btn-sm" href="{{ route($model.'.edit', $list->slug) }}"><i class="fa fa-pencil" aria-hidden="true"></i>
                                                    </a>
                                                    </span>
                                            @endif
                                            <span>
                                                    <form title="Trash" action="{{ URL::route($model.'.destroy', $list->slug) }}" method="POST">
                                                    <input type="hidden" name="_method" value="DELETE">
                                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                    <button class="btn btn-danger btn-sm" style="float:left"><i class="fa fa-trash" aria-hidden="true"></i>
                                                    </button>
                                                    </form>
                                                    </span>

                                        @else
                                            <span>
                                                    <a title="View" class="btn btn-warning btn-sm" href="{{ route($model.'.show', $list->slug) }}"><i class="fa fa-eye" aria-hidden="true"></i>
                                                    </a>
                                                    </span>
                                        @endif

                                        @if($list->status == 'APPROVED')

                                            @if( (!isset($user_permission['sales-invoice___confirm-invoice-r']) &&  isset($permission['sales-invoice___confirm-invoice'])) || $permission == 'superadmin')
                                                <span>
                                                          <a title="Confirm Invoice" class="btn btn-primary btn-sm" href="{{ route('confirm-invoice.show', $list->slug) }}"><i
                                                                      class="fa fa-check-circle"></i></i>
                                                          </a>
                                                        </span>
                                            @endif

                                        @endif

                                        @if($list -> status == 'COMPLETED')
                                                @if (isset($permission['print-invoice-delivery-note___pdf']) || $permission == 'superadmin')
                                                    @if (!$list->esd_details)
                                                        <button title="Not Signed successfully."
                                                                class="not-signed btn btn-sm btn-biz-purplish">
                                                            <i aria-hidden="true" class="fa fa-file-pdf"></i>
                                                        </button>
                                                    @else

                                                        <a title="Export To Pdf" class="btn btn-sm btn-biz-purplish"
                                                           href="{{ route($model . '.exportToPdf', $list->requisition_no) }}">
                                                            <i aria-hidden="true" class="fa fa-file-pdf"></i>
                                                        </a>
                                                    @endif
                                                @endif

                                                @if (isset($permission['print-invoice-delivery-note___print']) || $permission == 'superadmin')
                                                    <a title="Print" class="btn btn-sm btn-biz-greenish"
                                                       href="javascript:void(0)"
                                                       onClick="printgrn('{!! $list->requisition_no !!}')">
                                                        <i aria-hidden="true" class="fa fa-print"></i>
                                                    </a>
                                                @endif

                                                @if (isset($permission['print-invoice-delivery-note___return']) || $permission == 'superadmin')
                                                    <a title="Return" class="btn btn-sm btn-primary"
                                                       href="{{ route('transfers.return_show', $list->requisition_no) }}"
                                                       target="_blank">
                                                        <i class="fa fa-refresh" aria-hidden="true"></i>
                                                    </a>
                                                @endif
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>



@endsection

@push('scripts')
    <script type="text/javascript">
        function printMe(url, data, type) {
            let isConfirm = confirm('Do you want to print this Invoice?');
            if (isConfirm) {
                jQuery.ajax({
                    url: url,
                    async: false, //NOTE THIS
                    type: type,
                    data: data,
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        var divContents = response;
                        var printWindow = window.open('', '', 'width=600');
                        printWindow.document.write(divContents);
                        printWindow.document.close();
                        printWindow.print();
                        printWindow.close();
                    }
                });
            }
        }

        function printgrn(transfer_no) {
            printMe('{{ route('transfers.print') }}', {
                transfer_no: transfer_no
            }, 'POST');
        }

        function print_invoice(input) {
            var postData = $(input).parents('form').serialize() + '&request=PRINT';
            var url = $(input).parents('form').attr('action');
            // postData.append('request','PRINT');
            printMe(url, postData, 'GET');
        }
    </script>
    <script>
        $(document).ready(function() {
            $('#addNewInvoiceBtn').click(function() {
                $.post("{{ route('credit.sales.otp') }}", {
                    _token: '{{ csrf_token() }}'
                }, function(response) {
                    if (response.message) {
                        $('#otpModal').modal('show');
                    }
                });
            });

            $('#verifyOtpBtn').click(function() {
                const otp = $('#otpInput').val();

                $.post("{{ route('credit.sales.verify.otp') }}", {
                    _token: '{{ csrf_token() }}',
                    otp: otp
                }, function(response) {
                    if (response.success) {
                        window.location.href = "{{ route($model.'.create') }}";
                    } else {
                        alert(response.message);
                    }
                });
            });
        });

    </script>
@endpush
