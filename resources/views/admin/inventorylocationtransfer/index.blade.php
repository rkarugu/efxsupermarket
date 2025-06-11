@extends('layouts.admin.admin')

@section('content')

    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">
                    Print Invoice/Delivery Note
                </h3>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                {!! Form::open(['route' => $model . '.index', 'method' => 'get']) !!}

                <div class="row">
                    <div class="form-group col-md-2">
                        <label for="" class="control-label">Store</label>
                        {!! Form::select(
                            'salesman',
                            $branches->pluck('name', 'id')->toArray(),
                            request()->salesman ?? $user->wa_location_and_store_id,
                            ['class' => 'form-control mlselec6t', 'id' => 'salesman_id', 'required' => true, 'disabled' => $user->role_id == 4],
                        ) !!}

                    </div>
                    <div class="form-group col-md-2">
                        <label for="" class="control-label">From Date</label>
                        <input type="date" class="form-control" name="start-date" id="start_date"
                            value="{{ request()->get('start-date') }}">
                    </div>

                    <div class="form-group col-md-2">
                        <label for="" class="control-label">To Date</label>
                        <input type="date" class="form-control" name="end-date" id="end_date" value="{{ request()->get('end-date') }}">
                    </div>

                    <div class="form-group col-md-2">
                        <label for="" class="control-label">Select Route</label>
                        <select name="route" id="route_id" class="form-control">
                            <option value="" selected disabled>Select route</option>
                            @foreach ($routes as $route)
                                <option value="{{ $route->id }}" @if (request()->route == $route->route_name) selected @endif>
                                    {{ $route->route_name }} </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-2">
                        <label for="" class="control-label">Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="" selected >All</option>
                            <option value="paid" @if (request()->status == 'paid') selected
                            @endif>PAID</option>
                            <option value="unpaid" @if (request()->status == 'unpaid') selected @endif>UNPAID</option>
                        
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="" style="display: block; color: white;">Action</label>
                        <div class="form-group">
                            <button type="submit" class="btn btn-success btn-sm" name="manage-request"
                                value="filter">FILTER</button>
                            <button type="submit" class="btn btn-success btn-sm" name="manage-request"
                                value="PDF">PDF</button>
                            <button type="button" onclick="print_invoice(this); return false;"
                                class="btn btn-success btn-sm" name="manage-request" value="PRINT">PRINT</button>
                            <a class="btn btn-success btn-sm" href="{!! route('transfers.index') . getReportDefaultFilterForTrialBalance() !!}"> CLEAR </a>
                        </div>
                    </div>
                </div>
                {!! Form::close() !!}

                <hr>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="create_datatable">
                        <thead>
                            <tr>
                                <th style="width: 3%;">#</th>
                                <th>Delivery Note No.</th>
                                <th>Route</th>
                                <th>Salesman</th>
                                <th>Store</th>
                                <th>Business Name</th>
                                <th>Transfer Date</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th class="noneedtoshort">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $total = 0;
                            @endphp
                            @if (isset($lists) && !empty($lists))
                                <?php $b = 1; 
                                ?>
                                @foreach ($lists as $list)
                                    <tr>
                                        <td>{!! $list->id !!}</td>
                                        <td>{!! $list->transfer_no !!}</td>
                                        <td>{!! $list->route !!}</td>
                                        <td>{!! $list->salesman !!}</td>
                                        <td>{!! @$list->store !!}</td>
                                        <td>{{ $list->name ?? $list->credit_customer  }}</td>
                                        <td>{!! date('Y-m-d H:i:s', strtotime($list->created_at)) !!}</td>
                                        <td>{{ manageAmountFormat($list->transfer_total) }}</td>
                                        @php
                                            $total += $list->transfer_total;
                                        @endphp
                                        <td>{!! $list->req_status !!}</td>

                                        <td class="action_crud">
                                            @if ($list->status == 'PENDING')
                                                <a title="Edit" href="{{ route($model . '.edit', $list->slug) }}"><img
                                                        src="{!! asset('assets/admin/images/edit.png') !!}" alt="">
                                                </a>

                                                <form title="Trash"
                                                    action="{{ URL::route($model . '.destroy', $list->slug) }}"
                                                    method="POST">
                                                    <input type="hidden" name="_method" value="DELETE">
                                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                    <button style="float:left"><i class="fa fa-trash"
                                                            aria-hidden="true"></i>
                                                    </button>
                                                </form>
                                            @else
                                                @if (isset($permission['print-invoice-delivery-note___pdf']) || $permission == 'superadmin')
                                                    @if ($list->esd_status != 'Signed successfully.')
                                                        <button title="Signed successfully."
                                                            class="not-signed btn btn-sm btn-biz-purplish">
                                                            <i aria-hidden="true" class="fa fa-file-pdf"></i>
                                                        </button>
                                                    @else
                                                        <a title="Export To Pdf" class="btn btn-sm btn-biz-purplish"
                                                            href="{{ route($model . '.printToPdf', $list->slug) }}">
                                                            <i aria-hidden="true" class="fa fa-file-pdf"></i>
                                                        </a>
                                                    @endif
                                                @endif

                                                @if (isset($permission['print-invoice-delivery-note___print']) || $permission == 'superadmin')
                                                    <a title="Print" class="btn btn-sm btn-biz-greenish"
                                                        href="javascript:void(0)"
                                                        onClick="printgrn('{!! $list->transfer_no !!}')">
                                                        <i aria-hidden="true" class="fa fa-print"></i>
                                                    </a>
                                                @endif

                                                @if (isset($permission['print-invoice-delivery-note___return']) || $permission == 'superadmin')
                                                    @if(!str_starts_with($list->slug, 'cs'))
                                                            <a title="Return" class="btn btn-sm btn-primary"
                                                               href="{{ route('transfers.return_show', $list->slug) }}"
                                                               target="_blank">
                                                                <i class="fa fa-refresh" aria-hidden="true"></i>
                                                            </a>
                                                    @endif
                                                @endif
                                            @endif

                                            @if ($list->esd_status != 'Signed successfully.')
                                                @if (isset($permission['sales-invoice___confirm-invoice-r']) || $permission == 'superadmin')
                                                    <a style="margin: 2px; background:#063970; color:#fff;"
                                                        class="btn btn-sm"
                                                        href="{{ route('transfers.resign_esd', base64_encode($list->id)) }}'"
                                                        title="Re-Sign ESD">
                                                        <i class="fa fa-repeat" aria-hidden="true"></i>
                                                    </a>
                                                @endif
                                            @endif

                                        </td>

                                    </tr>

                                    <?php $b++; ?>
                                @endforeach
                            @endif


                        </tbody>
                        <tfoot>
                            <td colspan="7" style="text-align:right">Total:</td>
                            <td colspan="3" style="text-align:left">{{ manageAmountFormat($total) }}</td>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </section>
    <div class="modal fade" id="notSignedModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="box-title" id="notSignedModalTitle"> Unsigned Invoice</h3>

                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <form id="updateApproveForm" action="" method="POST">
                    @csrf
                    <div class="box-body">
                        The Invoice has not been Signed.
                    </div>

                    <div class="box-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <input type="hidden" name="" id="">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
            </div>
        </div>
    </div>
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

@endsection
@section('uniquepagestyle')
    <link rel="stylesheet" href="{{ asset('assets/admin/dist/datepicker.css') }}">

    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
@endsection

@section('uniquepagescript')
    <script src="{{ asset('assets/admin/dist/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script>
         $(function() {
            $(".mlselec6t").select2();
            $("#route_id").select2();
            $("#status").select2();
            $('body').addClass('sidebar-collapse');

        });

        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd'
        });
        $('#start_date').on('change', function(e) {
            e.preventDefault();
            const url = new URL(window.location.href);
            const endDate = $('#start_date').val();
            url.searchParams.set('start-date', endDate);
            history.pushState(null, '', url);
        })

        $('#end_date').on('change', function(e) {
            e.preventDefault();

            const url = new URL(window.location.href);
            const endDate = $('#end_date').val();
            url.searchParams.set('end-date', endDate);
            history.pushState(null, '', url);
        })
        $('#route_id').on('change', function(e) {
            e.preventDefault();

            const url = new URL(window.location.href);
            const endDate = $('#route_id').val();
            url.searchParams.set('route', endDate);
            history.pushState(null, '', url);
        })
        $('#salesman_id').on('change', function(e) {
            e.preventDefault();

            const url = new URL(window.location.href);
            const endDate = $('#salesman_id').val();
            url.searchParams.set('salesman', endDate);
            history.pushState(null, '', url);
        })

        $('.not-signed').on('click', function(e) {
            e.preventDefault();
            $('#notSignedModal').modal();
        })
    </script>
@endsection
