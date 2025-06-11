@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border no-padding-h-b">
                <div class="box-header-flex">
                    <h3 class="box-title"> Customer Accounts </h3>

                    <div>
                        @if(isset($permission[$pmodule.'___add']) || $permission == 'superadmin')
                            <a href="{!! route($model.'.create')!!}" class="btn btn-success">Add {!! $title !!}</a>
                        @endif
                        <a href="{!! route($model.'.index',['download'=>true])!!}" class="btn btn-primary">Excel</a>
                        <a href="{!! route($model.'.index',['enable_all'=>true])!!}" class="btn btn-primary">Enable All {!! $title !!}</a>
                        <a href="{!! route($model.'.index',['disable_all'=>true])!!}" class="btn btn-primary">Disable All {!! $title !!}</a>
{{--                            <a title='Bulk Upload' class="btn btn-primary ml-20" href='{{ route("maintain-customers.real_recon.index")}}' target="_blank">--}}
{{--                               Bulk Upload--}}
{{--                            </a>--}}
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <div class="col-md-12 no-padding-h table-responsive">
                    <table class="table table-bordered table-hover" id="create_datatable">
                        <thead>
                        <tr>
                            <th>Customer Code</th>
                            <th>Customer Name</th>
                            <th>Route</th>
                            <th>Equity Till</th>
                            <th>KCB Till</th>
                            <th>Is-Blocked</th>
                            <th>Amount</th>
                            <th class="noneedtoshort">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                            $total_amount = [];
                        @endphp

                        @foreach($lists as $list)
                            @php
                                $total_amount[] = $list->getAllDebtorsTrans->sum('amount');
                            @endphp

                            <tr>
                                <td>{!! $list->customer_code !!}</td>
                                <td>{!! ucwords($list->customer_name) !!}</td>
                                <td>{!! $list->getRoute() ? $list->getRoute()->route_name : '-' !!}</td>
                                <td>{{ $list->equity_till }}</td>
                                <td>{{ $list->kcb_till }}</td>
                                <td>{!! $list->is_blocked == 1 ? "Yes" : "No" !!}</td>
                                <td>{!! manageAmountFormat(@$list->getAllDebtorsTrans->sum('amount')) !!}</td>
                                <td class="action_crud">
                                    @if(isset($permission[$pmodule.'___edit']) || $permission == 'superadmin')
                                        <span>
                                            <a title="Edit" href="{{ route($model.'.edit', $list->slug) }}">
                                                <img src="{!! asset('assets/admin/images/edit.png') !!}" alt="">
                                            </a>
                                        </span>

{{--                                        <span>--}}
{{--                                            <a title="Debtor Trans" href="{{ route($model.'.debtors-inquiry', $list->slug) }}">--}}
{{--                                                <i class="fa fa-list"></i>--}}
{{--                                            </a>--}}
{{--                                        </span>--}}

                                        <span>
                                            <a title="Mini Statement" href="{{ route($model.'.debtors-inquiry-2', $list->slug) }}">
                                                <i class="fa fa-list"></i>
                                            </a>
                                        </span>
                                    @endif

                                    {{--                                    <span class='span-action'>--}}
                                    {{--                                        <a title='Enter Customer Payment' href='{{ route("maintain-customers.enter-customer-payment",$list->slug)}}'>--}}
                                    {{--                                            <i class='fa fa-money' aria-hidden='true' style='font-size: 16px;'></i>--}}
                                    {{--                                        </a>--}}
                                    {{--                                    </span>--}}

                                    {{--                                    <span class='span-action'>--}}
                                    {{--                                        <a title='Bulk Customer Payment' href='{{ route("maintain-customers.enter-customer-payment-uploads",$list->slug)}}'>--}}
                                    {{--                                            <i class='fa fa-money' aria-hidden='true' style='font-size: 16px;'></i>--}}
                                    {{--                                        </a>--}}
                                    {{--                                    </span>--}}

                                    @if(isset($permission[$pmodule.'___print-receipts']) || $permission == 'superadmin')
                                        <span class='span-action'>
                                            <a title='Print Receipts' href='{{ route("maintain-customers.print-receipts",$list->slug)}}'>
                                                <i class='fa fa-print' aria-hidden='true' style='font-size: 16px;'></i>
                                            </a>
                                        </span>
                                    @endif

                                    @if(isset($permission[$pmodule.'___allocate-receipts']) || $permission == 'superadmin')
                                        <span class='span-action'>
                                            <a title='Allocate Receipts' href='{{ route("maintain-customers.allocate-receipts",$list->slug)}}'>
                                                <i class='fa fa-tasks' aria-hidden='true' style='font-size: 16px;'></i>
                                            </a>
                                        </span>
                                    @endif

                                    <!-- @if((isset($permission[$pmodule.'___delete']) || $permission == 'superadmin') && count($list->getAllDebtorsTrans) == 0)
                                        <span>
                                            <form title="Trash" action="{{ URL::route($model.'.destroy', $list->slug) }}" method="POST">
                                                <input type="hidden" name="_method" value="DELETE">
                                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                <button style="float:left"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                            </form>
                                        </span>



                                    @endif -->


                                    <span class='span-action'>
                                            <a title='Route Customers' href='{{ route("maintain-customers.route_customer_list",$list->id)}}?start-date={{date("Y-m-d")}}&end-date={{date("Y-m-d")}}'>
                                                <i class='fa fa-users' aria-hidden='true' style='font-size: 16px;'></i>
                                            </a>
                                        </span>

                                    {{--                                    <span class='span-action'>--}}
                                    {{--                                        <a title='Reconcile' href='{{ route("maintain-customers.recon.index",$list->slug)}}' target="_blank">--}}
                                    {{--                                            <i class='fa fa-handshake text-success' aria-hidden='true' style='font-size: 16px;'></i>--}}
                                    {{--                                        </a>--}}
                                    {{--                                    </span>--}}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>

                        <tfoot>
                        <tr>
                            <td style="font-weight: bold;" colspan="6">Total</td>
                            <td style="font-weight: bold;" colspan="2">{{ manageAmountFormat(array_sum($total_amount))}}</td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection
