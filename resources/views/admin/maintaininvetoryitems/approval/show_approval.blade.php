@extends('layouts.admin.admin')

@section('content')
    <style>
        .span-action {

            display: inline-block;
            margin: 0 3px;
        }
    </style>
    @include('message')
    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <div>
                        <h3 class="box-title"> Item Data to Approve </h3> <br>
                        <div><b>Item: </b>{{$row->title}}</div>
                        <div><b>Initiated By: </b>{{count($row->approvalStatus) ? $row->approvalStatus[0]->approvalBy?->name : ''}}</div>
                        <div><b>Date: </b>{{ count($row->approvalStatus) ? date("F j, Y, g:i a", strtotime($row->approvalStatus[0]->created_at)) : ''}}</div>
                    </div>
                    <div>
                        @if(isset($permission[$pmodule.'___edit']) || $permission == 'superadmin')
                            <a href = "{!! route($model.'.edit',$row->slug)!!}" class = "btn btn-success">Edit Item</a>
                        @endif
                        @if ($row->approval_status != App\Enums\Status\ApprovalStatus::Approved->value)
                            @if(isset($permission[$pmodule.'___item-approval']) || $permission == 'superadmin')
                                <a href = "{!! route($model.'.update_approval',[$row->id,App\Enums\Status\ApprovalStatus::Approved->value])!!}" class = "btn btn-success">Approve</a>
                            @endif
                            @if ($row->approval_status != App\Enums\Status\ApprovalStatus::Rejected->value)
                                @if(isset($permission[$pmodule.'___item-approval']) || $permission == 'superadmin')
                                    <a href = "{!! route($model.'.update_approval',[$row->id,App\Enums\Status\ApprovalStatus::Rejected->value])!!}" class = "btn btn-success">Reject</a>
                                @endif
                            @endif
                        @endif
                    </div>                       
                </div>
            </div>
            
            <div class="box-body">
                <table class="table">
                    <thead>
                        <th></th>
                        <th>Edited Information</th>
                        <th>Original Information</th>
                    </thead>
                    <tbody>
                        @if (count($row->approvalStatus))
                            @php
                                $changes = json_decode($row->approvalStatus[0]->changes);
                            @endphp
                            @if ($changes)
                                @foreach ($changes as $change)
                                    @php
                                        $key = key((array)$change);
                                    @endphp
                                    <tr>
                                        <td><b>{{$key}}</b></td>
                                        @foreach ($change as $item)
                                            <td>{{$item[1]}}</td>
                                            <td> {{$item[0]}}</td>
                                        @endforeach                                            
                                    </tr>
                                @endforeach
                            @endif 
                        @endif                        
                    </tbody>
                </table>
                               
            </div>
        </div>
        
    </section>
@endsection

@section('uniquepagestyle')
    <link rel="stylesheet" href="{{ asset('css/multistep-form.css') }}">
    <div id="loader-on"
        style="
            position: fixed;
            top: 0;
            text-align: center;
            display: block;
            z-index: 999999;
            width: 100%;
            height: 100%;
            background: #000000b8;
            display:none;
        "
        class="loder">
        <div class="loader" id="loader-1"></div>
    </div>
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
@endsection

@section('uniquepagescript')
    <script src="{{ asset('js/sweetalert.js') }}"></script>
    {{-- <script src="{{ asset('js/multistep-form.js') }}"></script> --}}
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            
            $('.wa_inventory_category_id').select2();
            $('#supplier-id').select2();
        });
    </script>
    <script type="text/javascript">
        
    </script>
@endsection
