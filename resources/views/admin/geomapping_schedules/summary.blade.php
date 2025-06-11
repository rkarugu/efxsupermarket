@extends('layouts.admin.admin')

@section('content')
    <?php
    $logged_user_info = getLoggeduserProfile();
    $my_permissions = $logged_user_info->permissions;
    ?>
    <!-- Main content -->
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title"> Geomapping Summary</h3>
                    <div>
                        <form action="{{route('geomapping-summary')}}" method="GET">
                        <input type="submit" name="download" value="Download" class="btn btn-success">   
                        </form>   
                    </div>
                </div>
             
            </div>
            <div class="box-body">

                @include('message')


                <div class="col-md-12">
                    <table class="table table-bordered table-hover" id="create_datatable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Branch</th>
                                <th>Routes</th>
                                <th>Centres</th>
                                <th>Existing Customers</th>
                                <th>New Customers</th>
                                <th>Total Customers</th>
                                <th>Geomapped Customers</th>
                                {{-- <th>New Centers</th> --}}
                                <th> % Completion</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $branch)
                                <tr>
                                    <th>{{ $loop->index + 1 }}</th>
                                    <td style="text-align: center;">{{ $branch->name }}</td>
                                    <td style="text-align: center;">{{ $branch->route_count }}</td>
                                    <td style="text-align: center;">{{ $branch->centre_count }}</td>
                                    <td style="text-align: center;">{{ $branch->customer_count - $branch->new_customers }}</td>
                                    <td style="text-align: center;">{{ $branch->new_customers }}</td>
                                    <td style="text-align: center;">{{ $branch->customer_count }}</td>
                                    <td style="text-align: center;">{{ $branch->geomapped_customer_count }}</td>
                                    {{-- <td style="text-align: center;">{{ $branch->new_centres }}</td> --}}
                                    <th style="text-align: center;">{{number_format(($branch->geomapped_customer_count / $branch->customer_count) * 100, 2)}}</th>
                                    <td style="text-align: center;"> 
                                        <div class="action-button-div">
                                            <a href="{{ route('geomapping-summary.show', $branch->id) }}" title="Detailed Report"><i class="fas fa-eye fa-lg text-primary"></i></a>
                                        </div>
                                     </td>

                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <th colspan="2" style="text-align: center;">Totals</th>
                            <td style="text-align: center;">{{ $data->sum('route_count') }}</td>
                            <td style="text-align: center;">{{ $data->sum('centre_count') }}</td>
                            <td style="text-align: center;">{{ $data->sum('customer_count') - $data->sum('new_customers') }}</td>
                            <td style="text-align: center;">{{ $data->sum('new_customers') }}</td>
                            <td style="text-align: center;">{{ $data->sum('customer_count') }}</td>
                            <td style="text-align: center;">{{ $data->sum('geomapped_customer_count') }}</td>
                            <th style="text-align: center;">{{ number_format(( ($data->sum('geomapped_customer_count') / $data->sum('customer_count')) * 100), 2)}}</th>
                            <th></th>


                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection


@section('uniquepagestyle')
    <link rel="stylesheet" href="{{ asset('assets/admin/dist/datepicker.css') }}">
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
@endsection

@section('uniquepagescript')
    <script src="{{ asset('assets/admin/dist/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript">
        $(function() {

            $(".mlselect").select2();
        });
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd'
        });
    </script>
@endsection
