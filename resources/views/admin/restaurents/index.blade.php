@extends('layouts.admin.admin')

@section('content')

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title"> Branches </h3>

                    @if(isset($permission[$pmodule.'___add']) || $permission == 'superadmin')
                        <a href="{!! route($model.'.create')!!}" class="btn btn-primary"> Add Branch </a>
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
                            <th width="10%" class="noneedtoshort">Image</th>
                            <th width="15%">Name</th>
                            <th width="20%">Location</th>
                            <th width="15%">Branch Code</th>
                            <th width="20%">Company</th>
                            <th>KCB MPESA Paybill</th>
                            <th>KCB Vooma Paybill</th>
                            <th>Equity Paybill</th>
                            <th width="10%" class="noneedtoshort">Action</th>

                            <!--th style = "width:10%" class = "noneedtoshort">Date</th-->

                        </tr>
                        </thead>
                        <tbody>
                        @if(isset($lists) && !empty($lists))
                                <?php $b = 1; ?>
                            @foreach($lists as $list)

                                <tr>
                                    <td>{!! $b !!}</td>
                                    <td><img width="100%" height="70px;" src="{{ asset('uploads/restaurants/thumb/'.$list->image) }}"></td>
                                    <td>{!! @$list->name !!}</td>
                                    <td>{!! @$list->location !!}</td>
                                    <td>{!! @$list->branch_code !!}</td>

                                    <td>{!! @$list->getAssociateCompany->name !!}</td>
                                    <td>{{ $list->kcb_mpesa_paybill }}</td>
                                    <td>{{ $list->kcb_vooma_paybill}}</td>
                                    <td>{{ $list->equity_paybill }}</td>
                                    <td class="action_crud">
                                        @if(isset($permission[$pmodule.'___edit']) || $permission == 'superadmin')
                                            <span>
                                                    <a title="Edit" href="{{ route($model.'.edit', $list->slug) }}"><img src="{!! asset('assets/admin/images/edit.png') !!}" alt="">
                                                    </a>
                                                    </span>

                                        @endif


                                        @if(canDeleteParentData() == true)

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
