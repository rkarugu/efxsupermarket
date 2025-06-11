@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between">
                        <h3 class="box-title"> Unassigned Invoices Report </h3>
                        <a href="{{ route('sales-and-receivables-reports.unassigned_invoices')}}" class="btn btn-primary"> << Go Back</a>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <form id="filterForm" action="{{ route('sales-and-receivables-reports.filter-unassigned-invoices-items', ['route_id' => request()->route_id, 'start_date' => request()->start_date, 'end_date' => request()->end_date]) }}" method="get">

                    <input type="hidden" name="ctns_dzns" value="">

                    <input type="hidden" id="route_id" name="route_id" value="">

                    <input type="hidden" id="invoice_number" name="invoice_number" value="{{request()->invoice_number}}">

                    <div class="row">
                        <div class="form-group col-md-2">
                            <label for="" class="control-label"> Start Date </label>
                            <input type="date" name="start_date" value="{{ request()->start_date ?? \Carbon\Carbon::now()->toDateString() }}" class="form-control"/>
                        </div>

                        <div class="form-group col-md-2">
                            <label for="" class="control-label"> End Date </label>
                            <input type="date" name="end_date" value="{{ request()->end_date ?? \Carbon\Carbon::now()->toDateString() }}" class="form-control"/>
                        </div>

                        <div class="form-group col-md-3">
                            <label class="text-white" style="display: block; color: white!important;"> Action </label>
                            <input type="submit" name="intent" value="FILTER" class="btn btn-primary"/>
                            <input type="submit" name="intent" value="EXCEL" class="btn btn-primary ml-12"/>
                            <input type="submit" name="intent" value="CLEAR" class="btn btn-primary ml-12" onclick="clearForm()"/>
                            
                        </div>
                    </div>
                </form>

                <hr>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="unassigned-invoices-items-table">
                        <thead>
                            <tr>
                                <th>ITEM ID</th>
                                <th>ITEM CODE</th>
                                <th>ITEM DESCRIPTION</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (isset($wainternalrequisitionitems['getRelatedItem']))
                                @foreach ($wainternalrequisitionitems['getRelatedItem'] as $wainternalrequisitionitem)
                                    <tr>
                                        <td>{{$wainternalrequisitionitem['getInventoryItemDetail']['id']}}</td>
                                        <td>{{$wainternalrequisitionitem['getInventoryItemDetail']['stock_id_code']}}</td>
                                        <td>{{$wainternalrequisitionitem['getInventoryItemDetail']['description']}}</td>
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

@section('uniquepagestyle')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet"/>
@endsection

@section('uniquepagescript')
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>

    <script type="text/javascript">
        $(function () {
            $('body').addClass('sidebar-collapse');
            $("#route").select2();
            $("#filter").select2();
            $("#group").select2();
        });

        $(document).ready(function() {
            var ctnsDznsValue = '{{ request()->input('ctns_dzns') }}';
            $('input[name="ctns_dzns"]').val(ctnsDznsValue);
            var routeId = '{{ request()->input('route_id') }}';
            $('input[name="route_id"]').val(routeId);
        });

        function clearForm() {
        document.getElementsByName('start_date')[0].valueAsDate = new Date();
        document.getElementsByName('end_date')[0].valueAsDate = new Date();

        document.getElementById('route').value = '';

        document.getElementsByName('ctns_dzns')[0].value = ctnsDznsValue;
        document.getElementsByName('route_id')[0].value = routeId;

        document.getElementById('filterForm').submit();
    }
        
        $(document).ready(function () {
            $('#unassigned-invoices-items-table').DataTable({
                "paging": true,
                "pageLength": 10,
                "searching": true,
                "lengthChange": true,
                "lengthMenu": [10, 20, 50, 100],
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "order": [[0, "asc"]]
            });
        });
    </script>
    
@endsection

