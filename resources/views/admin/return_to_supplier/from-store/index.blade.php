@extends('layouts.admin.admin')

@php
    $user = getLoggeduserProfile();
@endphp

@section('content')
    <section class="content" id="return-from-store">
        <div class="session-message-container">
            @include('message')
        </div>
        
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"> Return To Supplier From Store </h3>
            </div>

            <div class="box-body">
                <form>
                    <div class="row">
                        <div class="form-group col-sm-3">
                            <label>From</label>
                            <input type="date" class="form-control" name="from" value="{{ request()->from }}">
                        </div>

                        <div class="form-group col-sm-3">
                            <label>To</label>
                            <input type="date" class="form-control" name="to" value="{{ request()->to }}">
                        </div>

                        @if ($user->role_id == 1)
                            <div class="form-group col-sm-3">
                                <label>Store Location </label>
                                <select name="store" id="location-select" class="form-control mlselec6t">
                                    <option value="" selected disabled>Select Store Location</option>
                                    @foreach (getStoreLocationDropdown() as $index => $store)
                                        <option value="{{ $index }}" {{ request()->store == $index ? 'selected' : '' }}>
                                            {{ $store }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        
                        <div class="form-group col-sm-3">
                            <label>Supplier </label>
                            <select name="supplier" id="supplier-select" class="form-control mlselec6t">
                                <option value="" selected disabled>Select Supplier</option>
                                @foreach (getSupplierDropdown() as $index => $supplier)
                                    <option value="{{ $index }}" {{ request()->supplier == $index ? 'selected' : '' }}>
                                        {{ $supplier }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <br>

                        <div class="form-group col-sm-3" style="margin-top:5px">
                            <input type="submit" class="btn btn-danger" value="Filter">
                        </div>
                    </div>
                </form>

                @if ($page == 'pending')
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('return-to-supplier.from-store.create') }}" class="btn btn-success">Create Return</a>
                    </div>
                @endif
                
                <hr>

                <div class="table-responsive">
                    @if (session()->has('error'))
                        <div class="alert alert-danger">
                            {{ session()->get('error') }}

                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        </div>                        
                    @endif
                    
                    <table class="table table-bordered" id="create_datatable">
                        <thead>
                        <tr>
                            <th style="width: 3%;">#</th>
                            @if ($page == 'approved')
                                <th>Date Approved</th>
                            @elseif ($page == 'pending')
                                <th>Date Requested</th>
                            @endif
                            <th>RFS No</th>
                            <th>Store Location</th>
                            <th>Bin Location</th>
                            <th>Supplier</th>
                            <th>Items</th>
                            <th>Initiated By</th>
                            @if ($page == 'approved')
                                <th>Approved By</th>
                            @endif
                            <th class="noneedtoshort">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach ($returns as $i => $return)
                                <tr>
                                    <th style="width: 3%;"> {{ $i + 1 }}</th>
                                    @if ($page == 'approved')
                                        <td>{{ date_format(date_create($return->approved_date), 'Y-m-d H:i:s') }}</td>
                                    @elseif ($page == 'pending')
                                        <td>{{ date_format(date_create($return->created_at), 'Y-m-d H:i:s') }}</td>
                                    @endif
                                    <td>{{ $return->rfs_no }}</td>
                                    <td>{{ $return->location->location_name }}</td>
                                    <td>{{ $return->uom->title }}</td>
                                    <td>{{ $return->supplier?->name }}</td>
                                    <td>{{ $return->storeReturnItems->count() }}</td>
                                    <td>{{ $return->user?->name }}</td>
                                    @if ($page == 'approved')
                                        <td>{{ $return->approvedBy?->name }}</td>
                                    @endif
                                    <td>
                                        {{-- @if ($page == 'approved') --}}
                                            @if (isset($user->permissions['return-to-supplier-from-store___print']) || $user->role_id == '1')
                                                <a title="Print" href="{{ route('return-to-supplier.from-store.print', $return->id) }}" target="_blank" >
                                                    <i aria-hidden="true" class="fa fa-print" style="font-size: 20px;"></i>
                                                </a>
                                            @endif
                                        @if ($page == 'pending')                                            
                                            @if (isset($user->permissions['return-to-supplier-from-store___view-pending-details']) || $user->role_id == '1')
                                                <a title="Aprrove" href="{{ route('return-to-supplier.from-store.approve', $return->id) }}">
                                                    <i aria-hidden="true" class="fa fa-eye" style="font-size: 20px;"></i>
                                                </a> 
                                            @endif                                           
                                        @endif
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
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet"/>
@endsection

@section('uniquepagescript')
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('#location-select').select2({
                placeholder: 'Select store',
                allowClear: true
            });

            $('#supplier-select').select2({
                placeholder: 'Select supplier',
                allowClear: true
            });
        });
    </script>
@endsection
