@extends('layouts.admin.admin')

@section('content')
    <section class="content">

        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title"> {!! $title !!}</h3>
            </div>
            @include('message')
            <div class="box-body">
                <div class="row pb-4">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="">From</label>
                            <input type="date" name="start_date" id="start_date" class="form-control" value="{{request()->input('start_date') ?? date('Y-m-d')}}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="">To</label>
                            <input type="date" name="end_date" id="end_date" class="form-control"  value="{{request()->input('end_date') ?? date('Y-m-d')}}">
                        </div>
                    </div>
                   
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="restaurant_id">Branch</label>
                            {!!Form::select('branch', $branches, $branch, ['placeholder'=>'Select Branch ', 'class' => 'form-control mlselec6t','required'=>true,'title'=>'Please select Branch','id'=>'branch'  ])!!}
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="">Route</label>
                            <select id="route_id" style="width: 200px;"></select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <button type="submit" id="filter" class="btn btn-primary btn-sm" style="margin-top: 25px;">Filter</button>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <a href="{{ url('admin/route-customers-download-customers?'). http_build_query(Request::query()) }}" id="downloadExcel"  class="btn btn-primary btn-sm" style="margin-top: 25px;">Excel</a>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table" id="customerTable">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th> Date Onboarded</th>
                            <th> Branch</th>
                            <th> Route</th>
                            <th> Center</th>
                            <th> Business Name</th>
                            <th> Customer Name</th>
                            <th> Phone Number</th>
                            <th> Status</th>
                            <th> Comment</th>
                            <th> Actions</th>
                        </tr>
                        </thead>
                        <tbody>

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
        // var VForm = new Form();

        $(document).ready(function() {
            $('body').addClass('sidebar-collapse');

            let excel = false;
            var table = $("#customerTable").DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                pageLength: '<?= Config::get('params.list_limit_admin') ?>',
                ajax: {
                    url: '{!! route('route-customers.comments') !!}',
                    data: function(data) {
                        var route_id = $('#route_id').val();
                        var from = $('#start_date').val();
                        var to = $('#end_date').val();
                        data.start_date = from;
                        data.end_date = to;
                        data.route_id = route_id;
                        data.branch = $('#branch').val();
                        data.excel = excel;
                    }
                },
                columns: [{
                    data: 'DT_RowIndex',
                    searchable: false,
                    sortable: false,
                    width: "70px"
                },
                    {
                        data: "created_at",
                        name: "created_at",
                        searchable: false
                    },
                    {
                        data: "route.branch.name",
                        name: "route.branch.name",
                        sortable: false
                    },
                    {
                        data: "route.route_name",
                        name: "route.route_name",
                        sortable: false
                    },
                    {
                        data: "center.name",
                        name: "center.name",
                        sortable: false
                    },
                    {
                        data: "name",
                        name: "name"
                    },
                    {
                        data: "bussiness_name",
                        name: "bussiness_name"
                    },
                    {
                        data: "phone",
                        name: "phone"
                    },
                    {
                        data: "status",
                        name: "status"
                    },
                    {
                        data: "comment",
                        name: "comment"
                    },
                    {
                        data: "action",
                        name: "action",
                        searchable: false
                    }
                ],

            });

            $('#filter').click(function(e){
                e.preventDefault();
                table.draw();
                updateUrlFilter()
            });
            $('#downloadExcel').click(function(e){
                e.preventDefault();
                var queryParams = window.location.search.substring(1);

                // Append the query parameters to another URL
                var anotherUrl = 'route-customers-download-customers?' + queryParams;

                // Redirect to the new URL
                window.location.href = anotherUrl;
            });
            $("#route_id").select2();
            $(".mlselec6t").select2();
            function updateUrlFilter() {
                var route_id = $('#route_id').val();
                var start_date = $('#start_date').val();
                var end_date = $('#end_date').val();
                var branch = $('#branch').val();

                const url = new URL(window.location.href);
                url.search = '';
                if (start_date) {
                    url.searchParams.set('start_date', start_date);
                } else {
                    url.searchParams.delete('start_date');
                }
                if (end_date) {
                    url.searchParams.set('end_date', end_date);
                } else {
                    url.searchParams.delete('end_date');
                }
                if (route_id) {
                    url.searchParams.set('route_id', route_id);
                } else {
                    url.searchParams.delete('route_id');
                }
                if (branch) {
                    url.searchParams.set('branch', branch);
                } else {
                    url.searchParams.delete('branch');
                }
                history.pushState(null, '', url);
            }

            function initializeSelect2() {
                $('#route_id').select2({
                    placeholder: 'Select Route',
                    ajax: {
                        url: '{{ url('admin/route-customers-get-routes') }}/'+ $('#branch').val(),
                        dataType: 'json',
                        delay: 250,
                        processResults: function (data) {
                            return {
                                results: data.map(function(item) {
                                    return {
                                        id: item.id,
                                        text: item.route_name
                                    };
                                })
                            };
                        },
                        cache: true
                    }
                });
            }
            $('#branch').on('change', function() {
                // Reinitialize Select2 (or perform any action needed)
                $('#route_id').val(null).trigger('change'); // Reset the value of route_id
                initializeSelect2();
            });
            $('#branch').trigger('change');
            function autoSelectOption(route_id) {
                // Select the option with the given ID
                $('#route_id').val(route_id).trigger('change');
            }
            const urlParams = new URLSearchParams(window.location.search);
            const route_id = urlParams.get('route_Id');
            if (route_id) {
                // Auto-select the option if routeId is present in URL params
                autoSelectOption(route_id);
            }
        })

    </script>
    <script>
        $(document).ready(function() {
            $('#show-export-excel-modal').click(function() {
                $('#export-customer-modal').modal('show');
            });
        });
    </script>
    <script>
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd'
        });
    </script>
    <script>
        function submitExportForm() {
            // Submit the form
            document.getElementById('export-customer-form').submit();
            $('#export-customer-modal').modal('hide');
            form.reset();


        }
    </script>
@endsection