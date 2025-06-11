@extends('layouts.admin.admin')

@section('content')
    <style>
        .span-action {

            display: inline-block;
            margin: 0 3px;

        }
    </style>
    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">

            <div class="box-header with-border">
                <div class="d-flex justify-content-between">
                    <h3 class="box-title">Sales Vs Stocks Ledger Report</h3>
                    {{-- <a href="{{ url()->previous() }}" class="btn btn-primary" role="button">
                        << Back to Sales and Receivables Reports </a> --}}
                </div>
            </div>

            <div class="box-header with-border no-padding-h-b">

                @include('message')
                <div class="col-md-12 no-padding-h table-responsive">
                    <form action="{{ route('summary_report.sales_vs_stocks_ledger') }}" method="GET">
                        <div class="row">
                            <div class="col-md-3 form-group">
                                <label for="">Choose From Date</label>
                                <input type="date" name="date" id="date" class="form-control">
                            </div>
                            <div class="col-md-3 form-group">
                                <label for="">Choose To Date</label>
                                <input type="date" name="todate" id="todate" class="form-control">
                            </div>
                            <div class="col-md-3 form-group">
                                <label for="">Branch</label>
                                <select name="branch" id="mlselec6t" class="form-control mlselec6t">
                                    <option value="" selected disabled>--Select Branch--</option>
                                    @foreach (getBranchesDropdown() as $key => $branch)
                                        <option value="{{ $key }}">{{ $branch }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 ">
                                <br>
                                <input type="submit" name="download" value="Download" class="btn btn-primary">
                                <button type="button" class="btn btn-danger" onclick="printgrn();return false;">Print
                                    Report</button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>


    </section>
@endsection
@section('uniquepagescript')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <style>
        .select2 {
            width: 100% !important;
        }
    </style>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript">
        function printgrn() {
            jQuery.ajax({
                url: '{{ route('summary_report.sales_vs_stocks_ledger') }}',
                async: false, //NOTE THIS
                type: 'GET',
                data: {
                    'date': $('#date').val(),
                    'todate': $('#todate').val(),
                    'request_type': 'print'
                },
                success: function(response) {

                    var divContents = response;
                    //alert(divContents);
                    var printWindow = window.open('', '', 'width=600');
                    printWindow.document.write(divContents);
                    printWindow.document.close();
                    printWindow.print();
                    printWindow.close();
                }
            });
        }
        $(function() {
            $(".mlselec6t").select2();
        });
    </script>
@endsection
