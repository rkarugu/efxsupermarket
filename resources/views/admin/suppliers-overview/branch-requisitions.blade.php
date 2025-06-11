@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Branch Requisitions </h3>
                </div>
            </div>

            <div class="box-body">
                <form action="" method="get">
                    <div class="form-group col-sm-3">
                        <label for="supplier">User</label>

                        <select name="user" class="form-control">
                            <option value="" selected disabled></option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" @if(request()->user == $user->id) selected @endif> {{ $user->name }} </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-sm-3">
                        <label for="supplier">branch</label>

                        <select name="branch" class="form-control">
                            <option value="" selected disabled></option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" @if(request()->branch == $branch->id) selected @endif> {{ $branch->name }} </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary" style="margin-top: 25px;">Filter</button>
                    <a href="{{route('suppliers-overview.branch-requisitions')}}" class="btn btn-primary" style="margin-top:25px;">Clear</a>

                </form>

                <div style="clear:both;">
                    <hr>
                </div>

                <table class="table table-bordered">
                    <thead>
                        <th>#</th>
                        <th>Purchase No</th>
                        <th>Date</th>
                        <th>User Name</th>
                        <th>Branch</th>
                        <th>Store Location</th>
                        <th>Bin Location</th>
                        <th>Supplier</th>
                        <th>Total Lists</th>
                        <th>Action</th>
                    </thead>
                    <tbody>
                        @foreach ($branchRequisitions as $i => $branchRequisition)
                            <tr>
                                <td>{{ ++$i }}</td>
                                <td>{{ $branchRequisition->purchase_no }}</td>
                                <td>{{ $branchRequisition->requisition_date }}</td>
                                <td>{{ $branchRequisition->user->name }}</td>
                                <td>{{ $branchRequisition->branch->name }}</td>
                                <td>{{ $branchRequisition->store_location->location_name }}</td>
                                <td>{{ $branchRequisition->bin->title }}</td>
                                <td>{{ $branchRequisition->supplier->name }}</td>
                                <td>{{ $branchRequisition->external_requisition_items_count }}</td>
                                <td>
                                    <a style="float:right" href="{{ route('resolve-requisition-to-lpo.edit', $branchRequisition->id) }}" target="_blank">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection

@section('uniquepagestyle')
    <link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet"/>
@endsection

@push('scripts')
    <script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>

    <script>
        $("body").addClass('sidebar-collapse');

        $("select").select2({
            placeholder: 'Select',
        });
    </script>
@endpush
