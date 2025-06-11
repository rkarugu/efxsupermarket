@extends('layouts.admin.admin')

@section('content')

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border no-padding-h-b">
                <a href="{{ route('route-manager.create') }}" class="btn btn-primary btn-sm">Add Route</a>
                @include('message')
                <div class="col-md-12 no-padding-h">
                    <table class="table table-bordered table-hover" id="create_datatable">
                        <thead>
                        <tr>
                            <th width="5%">S.No.</th>

                            <th width="10%">Route</th>


                            <th width="15%" class="noneedtoshort">Action</th>

                            <!--th style = "width:10%" class = "noneedtoshort">Date</th-->

                        </tr>
                        </thead>
                        <tbody>
                        @if (isset($lists) && !empty($lists))
                                <?php $b = 1; ?>
                            @foreach ($lists as $list)
                                <tr>
                                    <td>{!! $b !!}</td>

                                    <td>{!! $list->route_name !!}</td>


                                    <td class="action_crud">


                                            <span>
                                                <a title="View" href="{{ route($model . '.edit', $list->id) }}"><i
                                                            class="fa fa-eye" aria-hidden="true"></i>
                                                </a>
                                            </span>

                                        <span>
                                                <a title="Add Delivery Centers"
                                                   href="{{ route('create-route-delivery-center', $list->id) }}"><i
                                                            class="fa fa-plus"></i>
                                                </a>
                                            </span>

                                        <span>
                                                <a title="Delivery Centers" href="{{ route('route-delivery-centers', $list->id) }}"><i
                                                            class="fa fa-list"></i>
                                                </a>
                                            </span>

                                        @if (isset($permission[$pmodule . '___route_customers']) || $permission == 'superadmin')
                                            <span class='span-action'> <a title='Route Customers'
                                                                          href='{{ route('maintain-customers.route_customer_by_route_id', $list->id) }}?start-date={{ date('Y-m-d') }}&end-date={{ date('Y-m-d') }}'><i
                                                            class='fa fa-users' aria-hidden='true'
                                                            style='font-size: 16px;'></i></a></span>
                                        @endif

                                        <span>
                                                <a title="Route Plan" href="{{ route('admin.routes.plan', $list->id) }}"><i class="fa fa-list"></i>
                                                </a>
                                            </span>

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
