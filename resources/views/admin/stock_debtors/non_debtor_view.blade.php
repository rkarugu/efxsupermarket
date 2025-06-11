@extends('layouts.admin.admin')
@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title" style="font-weight:500 !important;"> Non Stock Debtor Information </h3>
                    <a href="{{ route("stock-non-debtors.index") }}" role="button" class="btn btn-primary"> Back </a>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-8">
                        <table class="table table-bordered">
                            <tr>
                                <th>Name</th>
                                <td>{{$debtor->name}}</td>
                                <th>
                                    Phone
                                </th>
                                <td>
                                    {{$debtor->phone_number}}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    Role
                                </th>
                                <td>
                                    {{$debtor->userRole->title}}
                                </td>
                                <th>
                                    Branch
                                </th>
                                <td>
                                    {{$debtor->userRestaurent->name}}
                                </td>
                            </tr>
                            
                        </table>
                    </div>
                    <div class="col-sm-4">
                        <div rowspan="3" class="text-center">
                            <h4>Balance</h4>
                            <h3 style="font-weight: bold">{{ manageAmountFormat($total) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
                <div class="session-message-container">
                    @include('message')
                </div>
                
        </div>
        <div class="box box-primary">
            <div class="box-body">
                <div style="padding:10px">
                    <form action="{{ route('stock-debtors.view',$debtor->id) }}" method="get">
                        <div class="row">
                            <div class="col-sm-5">
                                <input type="hidden" id="startDate" name="from">
                                <input type="hidden" id="endDate" name="to">
                                <input type="hidden" name="debtor_id" value="{{ $debtor->id }}">
                                <div class="row">
                                    <label class="col-sm-4">Select Dates</label>
                                    <div class="reportRange col-sm-8">
                                        <i class="fa fa-calendar" style="padding:8px"></i>
                                        <span class="flex-grow-1" style="padding:8px"></span>
                                        <i class="fa fa-caret-down" style="padding:8px"></i>
                                    </div>
                                </div>
                            </div>
                            {{-- @if (can('print', 'stock-non-debtors'))
                                <div class="col-sm-4">
                                    <button type="submit" class="btn btn-primary" name="manage-request" value="pdf">Print
                                        Pdf</button>
                                </div>
                            @endif --}}
                        </div>
                    </form>
                </div>
                <table class="table table-bordered" id="datatable">
                    <thead>
                        <tr>
                            <th class="text-right" colspan="5">Opening Balance</th>
                            <th id="openingBalance"
                                style="text-align: right; border-top: 1px solid #000;border-bottom: 1px solid #000;"></th>
                        </tr>
                        <tr>
                            <th>Date</th>
                            <th>Document No</th>
                            <th>Transaction Type</th>
                            <th>Description</th>
                            <th style="text-align: right;">Debit</th>
                            <th style="text-align: right;">Credit</th>
                            <th style="text-align: right;">Running Balance</th>
                        </tr>
                        
                    </thead>
                    <tbody>
                        {{-- @foreach ($debtor->stockDebtorTrans as $item)
                        <tr>
                            <td>{{date('d/m/Y',strtotime($item->created_at))}}</td>
                            <td>{{$item->document_no}}</td>
                            <td>
                                @php
                                    $total=0;
                                @endphp
                                @foreach ($item->items as $tot)
                                @php
                                    $total=$total + $tot->price;
                                @endphp
                                
                                @endforeach
                                {{manageAmountFormat($total)}}
                            </td>
                        </tr>
                    @endforeach --}}
                    </tbody>
                    <tfoot>
                        <tr>
                            <th style="text-align: right;" colspan="5">Closing Balance:</th>
                            <th id="total" style="text-align: right; border-top: 1px solid #000;border-bottom: 1px solid #000;">
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </section>
@endsection

@section('uniquepagestyle')
    <link rel="stylesheet" href="{{ asset('assets/admin/dist/datepicker.css') }}">
    <link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" rel="stylesheet" />
    <style>
        .reportRange {
            display: flex;
            align-content: center;
            justify-content: stretch;
            border: 1px solid #eee;
            cursor: pointer;
            height: 35px;
        }
    </style>
@endsection

@section('uniquepagescript')
    <script src="{{ asset('assets/admin/dist/bootstrap-datepicker.js') }}"></script>
    <script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script type="text/javascript">
        $(function () {
            $(".select2").select2();
        });
    </script>

    <script>
        $(document).ready(function () {
            let start = moment().subtract(30, 'days');
            let end = moment();

            $('.reportRange span').html(start.format('MMM D, YYYY') + ' - ' + end.format('MMM D, YYYY'));
            $("#startDate").val(start.format('YYYY-MM-DD'));
            $("#endDate").val(end.format('YYYY-MM-DD'));

            $('.reportRange').daterangepicker({
                startDate: start,
                endDate: end,
                alwaysShowCalendars: true,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(7, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(30, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                        'month').endOf('month')]
                }
            });
            
            $('.reportRange').on('apply.daterangepicker', function(ev, picker) {
                $("#startDate").val(picker.startDate.format('YYYY-MM-DD'));
                $("#endDate").val(picker.endDate.format('YYYY-MM-DD'));

                $('.reportRange span').html(picker.startDate.format('MMM D, YYYY') + ' - ' + picker.endDate
                    .format('MMM D, YYYY'));

                    $("#datatable").DataTable().ajax.reload();
            });

            $("#datatable").DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [0, "asc"]
                ],
                autoWidth: false,
                pageLength: '<?= Config::get('params.list_limit_admin') ?>',
                ajax: {
                    url: '{!! route('stock-non-debtors.view', $debtor->id) !!}',
                    data: function(data) {
                        data.from = $("#startDate").val();
                        data.to = $("#endDate").val();
                    }
                },
                columns: [{
                        data: 'created_at',
                        name: 'created_at',
                    },
                    {
                        data: 'document_no',
                        name: 'document_no',
                        orderable: false,
                    },
                    {
                        data: 'transaction_type',
                        name: 'transaction_type',
                        orderable: false
                    },
                    {
                        data: 'description',
                        name: 'description',
                        orderable: false
                    },
                    {
                        data: 'debit',
                        name: 'debit',
                        orderable: false,
                        searchable: false,
                        className: "text-right"
                    },
                    {
                        data: 'credit',
                        name: 'credit',
                        orderable: false,
                        searchable: false,
                        className: "text-right"
                    },
                    {
                        data: 'total',
                        name: 'total',
                        orderable: false,
                        searchable: false,
                        className: "text-right"
                    },
                ],
                columnDefs: [
                    {
                            targets: 2,
                            render: function (data, type, row, meta) {
                                if (type === 'display') {
                                    let documentNo = row.document_no;
                                    
                                }
                                return data;
                            }
                    },
                ],
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api();
                    var json = api.ajax.json();

                    $("#total").html(json.total);
                    $("#openingBalance").html(json.opening_balance);
                }
            });
            

        });
        
    </script>
@endsection
