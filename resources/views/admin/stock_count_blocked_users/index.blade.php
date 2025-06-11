@extends('layouts.admin.admin')

@section('content')
    <section class="content">
    
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title">Blocked Stock Take Users  </h3>
                    <div class="btn-group">
                        @if ($users->count() > 0)
                            <a href="{{route('admin.stock-count-blocked-users.unblockAll')}}" class="btn btn-success btn-sm"> Unblock All</a> 
                            <form id="unblock_selected_form" action="{{ route('admin.stock-count-blocked-users.selected') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm">Unblock Selected</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <div class="box-body">
                {!! Form::open(['route' => 'admin.stock-count-blocked-users.index', 'method' => 'get']) !!}
                <div class="row">

                        <div class="col-md-2 form-group">
                            <select name="branch" id="branch" class="form-control mlselect"  data-url="{{ route('admin.get-branch-routes') }}">
                                <option value="" selected disabled>Select branch</option>
                                @foreach ($restaurants as $branch)
                                    <option value="{{$branch->id}}" 
                                        {{ request()->has('branch') ? ($branch->id == request()->branch ? 'selected' : '') : ($branch->id == $authuser->restaurant_id ? 'selected' : '') }}>
                                        {{$branch->name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    <div class="col-md-3 form-group">
                        <button type="submit" class="btn btn-success btn-sm" name="manage-request" value="filter"><i class="fas fa-filter"></i> Filter</button>
                    </div>
                </div>

                {!! Form::close(); !!}
                @include('message')
                <div class="col-md-12">
                        <table class="table table-bordered table-hover" id="create_datatable">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Employee</th>
                                <th>Bin</th>
                                <th>Bin Categories</th>
                                <th>Bin Items</th>
                                <th>Counted Categories</th>
                                <th>Items In Counted Categories</th>
                                <th>Counted Items</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                             
                                @foreach ($users as $user)
                                    <tr>
                                        <th>{{$loop->index+1}}</th>
                                        <td>{{$user->name}}</td>
                                        <td>{{$user->bin}}</td>
                                        <th style="text-align: center;">{{$user->total_categories}}</th>
                                        <th style="text-align: center;">{{$user->total_items}}</th>
                                        <th style="text-align: center;">{{$user->counted_categories}}</th>
                                        <th style="text-align: center;">{{$user->total_items_in_counted_categories}}</th>
                                        <th style="text-align: center;">{{$user->counted_items}}</th>
                                        <td>
                                            <input type="checkbox" name="selected_users[]" value="{{$user->id}}" form="unblock_selected_form">
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
    <link rel="stylesheet" href="{{ asset('assets/admin/dist/datepicker.css') }}">
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet"/>
    <style>
        .box-header-flex .btn-group {
            display: flex;
            gap: 15px;
        }
    </style>
@endsection
@section('uniquepagescript')
    <script src="{{ asset('assets/admin/dist/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript">
        $(function () {
            $(".mlselect").select2();
        });
    </script>
@endsection


