@extends('layouts.admin.admin')

@section('content')
<section class="content">
    <div class="box box-primary">
        <div class="box-header with-border">
            <div class="box-header-flex">
                <h3 class="box-title"> Device Listings </h3>
                <div class="d-flex">
                    <div>
                        @if (Auth::user()->is_hq_user || Auth::user()->role_id==1)
                            <select name="branch" id="branch" class="form-control select2">
                                <option value="">Select Branch</option>
                                    @foreach (getBranchesDropdown() as $key => $branch)
                                        <option value="{{ $key }}" {{ request()->branch == $key ? 'selected' : '' }}>{{ $branch }}</option>
                                    @endforeach
                            </select>
                        @endif
                    </div>
                    <a title="Print" href="{{ route($model.'.index',['print'=>'pdf']) }}" type="button" class="btn btn-primary" style="margin-left:10px;">
                        <i class="fa fa-file-pdf" aria-hidden="true"></i>
                    </a>
                    
                    @if (can('add', $model))
                        <a href="{{ route($model.'.create') }}" class="btn btn-primary" style="margin-left:10px;"><i class="fas fa-plus"></i> Device</a>
                    @endif
                    @if (can('bulk-allocate', $model))
                        <a href="{{ route('device-center.bulk-allocate') }}" class="btn btn-primary" style="margin-left:10px;"><i class="fa-solid fa-upload"></i> Allocate</a>
                    @endif
                    @if (Auth::user()->role_id == 1)
                        <a href="{{ route('device-center.bulk-upload') }}" class="btn btn-primary" style="margin-left:10px;"><i class="fa-solid fa-upload"></i> Upload</a>
                    @endif
                </div>
            </div>
        </div>
        <div class="box-body">
            <div class="session-message-container">
                @include('message')
            </div>
            <div class="table-responsive">
                <table class="table table-bordered" id="deviceTypesDataTable">
                    <thead>
                        <tr>
                            <th style="width: 3%;">#</th>
                            <th>Device No.</th>
                            <th>Serial No.</th>
                            <th>Device Type.</th>
                            <th>IMEI</th>
                            <th>Current Holder</th>
                            <th>Branch</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($devices as $device)
                            <tr>
                                <th style="width: 3%;" scope="row"> {{ $loop->index + 1 }} </th>
                                <td> {{ $device['device_no'] }} </td>
                                <td> {{ $device['serial_no'] }} </td>
                                <td> {{ $device['deviceType'] }} </td>
                                <td> {{ $device['simcard'] }} </td>
                                <td> 
                                    {{ $device['current_holder'] }}
                                 </td>
                                 <td>{{ $device['branch']}}</td>
                                <td class="text-center">
                                    <a href="{{ route($model .'.edit',$device['id']) }}" class="" style="margin-left: 10px;"><i class="fas fa-pen"></i></a>
                                    <a title="Device centre" href="{{ route($model .'.show',$device['device_no']) }}"><i class="fa fa-store"></i></a>
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
<link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet" />
@endsection
@push('scripts')
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
    <script>
        $(document).ready(function () {
        $('.select2').select2();
    
        $('#branch').on('change', function () {
            window.location.href = `${window.location.pathname}?branch=${encodeURIComponent($(this).val())}`;
        });

            $('#deviceTypesDataTable').DataTable({
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
@endpush