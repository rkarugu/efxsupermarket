@extends('layouts.admin.admin')
@section('content')
    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> {{ $title }}</h3>
                    <div class="d-flex">
                        <a href="{{ route('small-packs.dispatched') }}" class="btn btn-primary" style="margin-top:0px;"><i class="fas fa-long-arrow-alt-left"></i> Back</a>
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>
               
                <div class="col-md-12 no-padding-h" id="getintervalview">
                    <table class="table table-bordered" id="dataTable">
                        <thead>
                            <tr>
                                <th>Stock Id Code</th>
                                <th>Title</th>
                                <th>Total Quantity</th>
                                <th>Dispatched Quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                                @foreach ($dispatch as $item)
                                    <tr>
                                        <td>{{ $item->stock_id_code }}</td>
                                        <td>{{ $item->title }}</td>
                                        <td>{{ $item->total_quantity }}</td>
                                        <td>{{ $item->dispatched_quantity }}</td>
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

<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
                'paging': true,
                'lengthChange': true,
                'searching': true,
                'ordering': true,
                'info': true,
                'autoWidth': false,
                'pageLength': 100,
                'aoColumnDefs': [{
                    'bSortable': false,
                    'aTargets': 'noneedtoshort'
                }],
            });
    });
            </script>
@endsection