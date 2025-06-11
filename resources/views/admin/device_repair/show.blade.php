@extends('layouts.admin.admin')

@section('content')
@php
    $settings = getAllSettings();
@endphp
<section class="content">
    <div class="box box-primary">
        <div class="box-header with-border">
            <div class="box-header-flex">
                <h3 class="box-title"> Details {{ $title }} </h3>
                <div>
                    <a href="{{ route($model.'.index') }}" class="btn btn-primary"><i class="fas fa-long-arrow-alt-left"></i> Back</a>
                </div>
            </div>
        </div>
        <div class="box-body">
            <div class="session-message-container">
                @include('message')
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <tbody>
                                <tr>
                                    <th>Device No.</th>
                                    <td><span>{{ $repair->device->device_no }}</span></td>
                                </tr>
                                <tr>
                                    <th>Repair Cost.</th>
                                    <td>{{ $repair->repair_cost }}</td>
                                </tr>
                                <tr>
                                    <th>Charge To.</th>
                                    <td>{{ $repair->charge_to=='Staff' ? $repair->chargeTo?->name :$repair->charge_to }}</td>
                                </tr>
                                <tr>
                                    <th>Created on</th>
                                    <td>{{ date('Y-m-d', strtotime($repair->created_at)) }}</td>
                                </tr>
                                <tr>
                                    <th>Created By</th>
                                    <td>{{ $repair->createdBy->name }}</td>
                                </tr>
                                <tr>
                                    <th>Comment</th>
                                    <td>{{ $repair->comment }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-sm-6">
                    <table class="table table-bordered"> 
                        <tbody>
                            <tr>
                            <th>Status</th>
                            <td>{{ $repair->status }}</td>
                        </tr>
                        <tr>
                            <th>Completed on</th>
                            <td>{{ $repair->complete_date ? date('Y-m-d', strtotime($repair->complete_date)):null }}</td>
                        </tr>
                        <tr>
                            <th>Completed By</th>
                            <td>{{ $repair->completedBy?->name }}</td>
                        </tr>
                        <tr>
                            <th>Completed Comment</th>
                            <td>{{ $repair->completed_comment }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

