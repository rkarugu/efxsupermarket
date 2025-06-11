@extends('layouts.admin.admin')

@section('content')

    <!-- Main content -->
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border no-padding-h-b">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title"> Departments </h3>

                    @if(isset($permission[$pmodule.'___add']) || $permission == 'superadmin')
                        <a href="{!! route($model.'.create')!!}" class="btn btn-primary">Add Department</a>
                    @endif
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <div class="col-md-12 no-padding-h">
                    <table class="table table-bordered table-hover" id="create_datatable">
                        <thead>
                        <tr>
                            <th width="10%">S.No.</th>

                            <th width="20%">Department Name</th>
                            <th width="20%">Department Code</th>
                            <th width="30%">Branch</th>

                            <th width="20%" class="noneedtoshort">Action</th>

                            <!--th style = "width:10%" class = "noneedtoshort">Date</th-->

                        </tr>
                        </thead>
                        <tbody>
                        @if(isset($lists) && !empty($lists))
                                <?php $b = 1; ?>
                            @foreach($lists as $list)

                                <tr>
                                    <td>{!! $b !!}</td>

                                    <td>{!! @$list->department_name !!}</td>
                                    <td>{!! @$list->department_code !!}</td>
                                    <td>{!! @$list->getAssociateBranch->name !!}</td>

                                    <td class="action_crud">
                                        @if($list->slug != 'mpesa')
                                            @if(isset($permission[$pmodule.'___edit']) || $permission == 'superadmin')
                                                <span>
                                                    <a title="Edit" href="{{ route($model.'.edit', $list->slug) }}"><img src="{!! asset('assets/admin/images/edit.png') !!}" alt="">
                                                    </a>
                                                    </span>

                                            @endif

                                            @if(isset($permission[$pmodule.'___delete']) || $permission == 'superadmin')

                                                <span>
                                                    <form title="Trash" action="{{ URL::route($model.'.destroy', $list->slug) }}" method="POST">
                                                    <input type="hidden" name="_method" value="DELETE">
                                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                    <button style="float:left"><i class="fa fa-trash" aria-hidden="true"></i>
                                                    </button>
                                                    </form>
                                                    </span>
                                            @endif

                                        @endif


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
