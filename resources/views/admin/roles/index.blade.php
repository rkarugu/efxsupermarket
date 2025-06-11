@extends('layouts.admin.admin')

@section('content')

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border no-padding-h-b">
                @if(isset($permission[$pmodule.'___add']) || $permission == 'superadmin')
                    <div align="right"><a href="{!! route($model.'.create')!!}" class="btn btn-success">Add {!! $title !!}</a></div>
                @endif
                <br>
                @include('message')
                <div class="col-md-12 no-padding-h">
                    <table class="table table-bordered table-hover" id="create_datatable">
                        <thead>
                        <tr>
                            <th width="10%">S.No.</th>
                            <th width="70%">Name</th>
                            <th width="20%" class="noneedtoshort">Action</th>
                        </tr>
                        </thead>

                        <tbody>
                        @if(isset($lists) && !empty($lists))
                                <?php $b = 1; ?>
                            @foreach($lists as $list)
                                <tr>
                                    <td>{!! $b !!}</td>
                                    <td>{!! $list->title !!}</td>
                                    <td class="action_crud">
                                        @if(isset($permission[$pmodule.'___edit']) || $permission == 'superadmin')
                                            <span>
                                                <a title="Edit" href="{{ route($model.'.edit', $list->slug) }}">
                                                    <img src="{!! asset('assets/admin/images/edit.png') !!}" alt="">
                                                </a>
                                            </span>

                                            <span>
                                                <a style="font-size: 15px" title="Permissions" href="{{ route('users.permissions.form', $list->slug) }}">
                                                    <i class="fa fa-lock" aria-hidden="true"></i>
                                                </a>
                                            </span>
                                        @endif

{{--                                        @if((isset($permission[$pmodule.'___delete']) || $permission == 'superadmin') && $list->permissioned == null)--}}
{{--                                            <span>--}}
{{--                                                <form title="Trash" action="{{ URL::route($model.'.destroy', $list->slug) }}" method="POST">--}}
{{--                                                <input type="hidden" name="_method" value="DELETE">--}}
{{--                                                <input type="hidden" name="_token" value="{{ csrf_token() }}">--}}
{{--                                                <button style="float:left"><i class="fa fa-trash" aria-hidden="true"></i>--}}
{{--                                                </button>--}}
{{--                                                </form>--}}
{{--                                            </span>--}}
{{--                                        @endif--}}
                                    </td>
                                </tr>
                                    <?php $b++; ?>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection
