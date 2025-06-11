@extends('layouts.admin.admin')

@section('content')

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Location and Stores </h3>

                    @if(isset($permission[$pmodule.'___add']) || $permission == 'superadmin')
                        <a href="{!! route($model.'.create') !!}" class="btn btn-primary">Add Location and Store </a>
                    @endif
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <div class="col-md-12">
                    <table class="table table-bordered table-hover" id="create_datatable">
                        <thead>
                        <tr>
                            <th>Location Code</th>
                            <th>Location Name</th>
                            <th>Branch</th>
                            <th>Route</th>
                            <th>Account</th>
                            <th>Biller</th>
                            <th>Physical Store?</th>
                            <th class="nonedtoshort">Action</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($lists as $list)
                            <tr>
                                <td>{!! $list->location_code !!}</td>
                                <td>{!! $list->location_name!!}</td>
                                <td>{!! $list->getBranchDetail ? $list->getBranchDetail->name : '-' !!}</td>
                                <td>{!! $list->getRoute() ? $list->getRoute()->route_name : '-' !!}</td>
                                <td>{!! $list->account_no ?? '-' !!}</td>
                                <td>{!! $list->biller_no ?? '-' !!}</td>
                                <td>{!! $list->is_physical_store ? "Yes" : "No"!!}</td>

                                <td class="action_crud">
                                    @if($list->slug != 'mpesa')
                                        @if(isset($permission[$pmodule.'___edit']) || $permission == 'superadmin')
                                            <span>
                                                <a title="Edit" href="{{ route($model.'.edit', $list->slug) }}">
                                                    <img src="{!! asset('assets/admin/images/edit.png') !!}" alt="">
                                                </a>
                                            </span>
                                        @endif

{{--                                        @if((isset($permission[$pmodule.'___delete']) || $permission == 'superadmin') && $list->stock_count == 0)--}}
{{--                                            <span>--}}
{{--                                                <form title="Trash" action="{{ URL::route($model.'.destroy', $list->slug) }}" method="POST">--}}
{{--                                                <input type="hidden" name="_method" value="DELETE">--}}
{{--                                                <input type="hidden" name="_token" value="{{ csrf_token() }}">--}}
{{--                                                <button style="float:left"><i class="fa fa-trash" aria-hidden="true"></i>--}}
{{--                                                </button>--}}
{{--                                                </form>--}}
{{--                                            </span>--}}
{{--                                        @endif--}}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    {{$lists->links()}}
                </div>
            </div>
        </div>

    </section>

@endsection
