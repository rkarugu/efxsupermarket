@extends('layouts.admin.admin')

@section('content')

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border ">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title"> Wallet Types</h3>

                    @if(isset($permission[$pmodule.'___add']) || $permission == 'superadmin')
                        <div align="right"><a href="{!! route('petty-cash-types.create')!!}" class="btn btn-success">Add Wallet Type</a></div>
                    @endif
                </div>
                <hr>
                <br>
                @include('message')
                <div class="col-md-12 no-padding-h">
                    <table class="table table-bordered table-hover" id="create_datatable">
                        <thead>
                        <tr>
                            <th>S.No.</th>
                            <th>Title</th>
                            <th>Active</th>
                            <th>GL Account Name</th>
                            <th>GL Account Code</th>
                            <th class="noneedtoshort">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(isset($lists) && !empty($lists))
                                <?php $b = 1; ?>
                            @foreach($lists as $list)

                                <tr>
                                    <td>{!! $b !!}</td>

                                    <td>{!! ucfirst($list->title) !!}</td>
                                    <td>{!! $list->active ? 'Yes' : 'No' !!}</td>
                                    <td>{!! $list->chart_of_account?$list->chart_of_account->account_name:'-' !!}</td>

                                    <td>{!! $list->chart_of_account?$list->chart_of_account->account_code:'-' !!}</td>
                                    <td class="action_crud">
                                        @if(isset($permission[$pmodule.'___edit']) || $permission == 'superadmin')
                                            <span>
                                                    <a title="Edit" href="{{ route('petty-cash-types.edit', $list->id) }}"><img src="{!! asset('assets/admin/images/edit.png') !!}" alt="">
                                                    </a>
                                                    </span>
                                        @endif
                                        @if(1==2)
                                            @if(isset($permission[$pmodule.'___delete']) || $permission == 'superadmin')
                                                <span>
                                                    <form title="Trash" action="{{ URL::route('petty-cash-types.destroy', $list->slug) }}" method="POST">
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
