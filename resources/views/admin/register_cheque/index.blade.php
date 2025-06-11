@extends('layouts.admin.admin')
@section('content')
    <section class="content">

        <div class="box box-primary">
            <div class="box-header with-border  text-center">
                <h3 class="box-title">
                    <img src="{{ asset('images/icons8-cheque-64.png') }}" alt="cheque" height="60">
                    Cheque Management
                </h3>
                @if ((isset($permission[$pmodule . '___add']) || $permission == 'superadmin'))
                    <a class="btn btn-danger remove-btn mr-xs pull-right ml-2 btn-sm" style="margin-left:5px" href="{{route($model.'.create')}}?source=register-cheque">

                        <i class="fa fa-plus"></i>
                        Register Cheque
                    </a>
                @endif
            </div>
            <div class="box-body">

                <ul class="nav nav-tabs nav-justified">
                    <ul class="nav nav-tabs nav-justified">
                        <li class="active">
                            <a href="#registered" data-toggle="tab">
                                <div class="badge-count">{{ $registered->count() }}</div>
                                <div class="tab-title">Registered Cheques</div>
                            </a>
                        </li>
                        <li>
                            <a href="#ready" data-toggle="tab">
                                <div class="badge-count">{{ $ready -> count() }}</div>
                                <div class="tab-title">Ready For Deposit</div>
                            </a>
                        </li>
                        <li>
                            <a href="#deposited" data-toggle="tab">
                                <div class="badge-count">{{ $deposited->count() }}</div>
                                <div class="tab-title">Deposited</div>
                            </a>
                        </li>
                        <li>
                            <a href="#cleared" data-toggle="tab">
                                <div class="badge-count">{{ $cleared->count() }}</div>
                                <div class="tab-title">Cleared</div>
                            </a>
                        </li>
                        <li>
                            <a href="#bounced" data-toggle="tab">
                                <div class="badge-count">{{ $bounced->count() }}</div>
                                <div class="tab-title">Bounced</div>
                            </a>
                        </li>
                    </ul>
                </ul>
                <div class="tab-content">
                    <div id="registered" class="tab-pane fade in active">
                        <table class="table table-bordered table-hover" id="registered">
                            <thead>
                            <tr>
                                <th>S.No.</th>
                                <th>Customer</th>
                                <th>Cheque no</th>
                                <th>Drawers name</th>
                                <th>Drawers bank</th>
                                <th>Cheque date</th>
                                <th>Bank deposited</th>
                                @if (request()->source == 'register-cheque')
                                    <th>Date received</th>
                                @else
                                    <th>Date Deposited</th>
                                    <th>Deposited By</th>
                                @endif
                                <th>Amount</th>
                                <th>##</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php
                                $total = 0;
                            @endphp
                            @foreach($registered as $key => $item)
                                <tr>
                                    <td>{{$key+1}}</td>
                                    <td>{{@$item->customer->customer_name}}</td>
                                    <td>{{$item->cheque_no}}</td>
                                    <td>{{$item->drawers_name}}</td>
                                    <td>{{$item->drawers_bank}}</td>

                                    <td>{{$item->cheque_date}}</td>
                                    <td>{{$item->bank_deposited}}</td>
                                    @if (request()->source == 'register-cheque')
                                        <td>{{$item->date_received}}</td>
                                    @else
                                        <td>{{$item->deposited_date}}</td>
                                        <td>{{@$item->depositer->name}}</td>
                                    @endif
                                    <td>{{manageAmountFormat($item->amount)}}</td>
                                    @php
                                        $total += $item->amount;
                                    @endphp
                                    <td>
                                        <div style="display: flex;">
                                            @if ($item->status == 'Registered')
                                                @if(isset($permission[$pmodule . '___edit']) || $permission == 'superadmin')
                                                    <a href="{{route($model.'.edit',$item->id)}}?source=register-cheque" style="margin:5px"><i class="fa fa-pencil"></i></a>
                                                @endif
                                                @if(isset($permission[$pmodule . '___delete']) || $permission == 'superadmin')
                                                    <form title="Trash"  style="margin:5px" action="{{ URL::route($model.'.destroy', $item->id) }}" class="deleteMe" method="POST">
                                                        <input type="hidden" name="_method" value="DELETE">
                                                        <input type="hidden" name="source" value="register-cheque">
                                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                        <button  style="float:left" type="submit"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                                    </form>
                                                @endif
                                                @if(isset($permission[$pmodule . '___deposit-cheque']) || $permission == 'superadmin')
                                                    <a href="{{route('register-cheque.deposit_cheque',$item->id)}}?source=register-cheque" style="margin:5px" title="Deposit Cheque">
                                                        <i class="fa fa-money-bill"></i>
                                                    </a>
                                                @endif
                                            @endif
                                            @if ($item->status == 'Deposited')
                                                <!-- Button trigger modal -->
                                                @if(isset($permission[$pmodule . '___update-status']) || $permission == 'superadmin')
                                                    <a href="#"  title="Update Status"  style="margin:5px" data-toggle="modal" data-target="#modelId{{$key+1}}">
                                                        <i class="fa fa-cogs" aria-hidden="true"></i>
                                                    </a>

                                                    <!-- Modal -->
                                                    <div class="modal fade" id="modelId{{$key+1}}" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
                                                        <form action="{{route('register-cheque.deposit_cheque_update_status',$item->id)}}" method="post" class="submitMe">
                                                            <input type="hidden" name="id" value="{{$item->id}}">
                                                            <input type="hidden" name="source" value="deposit-cheque">
                                                            {{method_field('PUT')}}
                                                            {{csrf_field()}}
                                                            <div class="modal-dialog" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">Cheque No: {{$item->cheque_no}}</h5>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div class="form-group">
                                                                            <label for="">Clearance date</label>
                                                                            <input type="date" name="clearance_date" id="clearance_date" class="form-control">
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label for="">Select Status</label>
                                                                            <select name="status" class="form-control">
                                                                                <option value="" selected disabled>Select Status</option>
                                                                                <option value="Cleared">Cleared</option>
                                                                                <option value="Bounced">Bounced</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                        <button type="submit" class="btn btn-primary">Save</button>
                                                                    </div>
                                                                </div>

                                                            </div>
                                                        </form>
                                                    </div>

                                                @endif
                                            @endif
                                            @if ($item->status == "Bounced" && $item->is_bounced_transfer == 0)
                                                @if(isset($permission[$pmodule . '___transfer']) || $permission == 'superadmin')
                                                    <form action="{{route('register-cheque.bounced_cheque_transfer',$item->id)}}" method="post" class="reverseMe">
                                                        <input type="hidden" name="_method" value="PUT">
                                                        <input type="hidden" name="id" value="{{$item->id}}">
                                                        <input type="hidden" name="source" value="bounced-cheque">
                                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                        <button title="Transfer Bounced Cheque" style="float:left" type="submit"><i class="fa fa-repeat" aria-hidden="true"></i></button>
                                                    </form>
                                                @endif
                                            @endif
                                            {{--                                    <a href="#" title="View Cheque Image" style="margin:5px" onclick="openImageModel('{{asset($item->cheque_image)}}'); return false;"><i class="fa fa-eye"></i></a>--}}
                                        </div>
                                    </td>
                                </tr>
                            @endforeach

                            </tbody>
                            <tfoot>
                            <tr>

                                @if (request()->source == 'register-cheque')
                                    <th colspan="9" style="text-align: right">Grand Total : {{manageAmountFormat($total)}}</th>

                                @else
                                    <th colspan="10" style="text-align: right">Grand Total : {{manageAmountFormat($total)}}</th>

                                @endif
                                <th></th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div id="ready" class="tab-pane fade">
                        <table class="table table-bordered table-hover" id="ready_cheques" >
                            <thead>
                            <tr>
                                <th>S.No.</th>
                                <th>Customer</th>
                                <th>Cheque no</th>
                                <th>Drawers name</th>
                                <th>Drawers bank</th>
                                <th>Cheque date</th>
                                <th>Bank deposited</th>
                                @if (request()->source == 'register-cheque')
                                    <th>Date received</th>
                                @else
                                    <th>Date Deposited</th>
                                    <th>Deposited By</th>
                                @endif
                                <th>Amount</th>
                                <th>##</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php
                                $total = 0;
                            @endphp
                            @foreach($ready as $key => $item)
                                <tr>
                                    <td>{{$key+1}}</td>
                                    <td>{{@$item->customer->customer_name}}</td>
                                    <td>{{$item->cheque_no}}</td>
                                    <td>{{$item->drawers_name}}</td>
                                    <td>{{$item->drawers_bank}}</td>

                                    <td>{{$item->cheque_date}}</td>
                                    <td>{{$item->bank->bank}}</td>
                                    @if (request()->source == 'register-cheque')
                                        <td>{{$item->date_received}}</td>
                                    @else
                                        <td>{{$item->deposited_date}}</td>
                                        <td>{{@$item->depositer->name}}</td>
                                    @endif
                                    <td class="text-right">{{manageAmountFormat($item->amount)}}</td>
                                    @php
                                        $total += $item->amount;
                                    @endphp
                                    <td>
                                        <div style="display: flex;">
                                            @if ($item->status == 'Registered')
                                                @if(isset($permission[$pmodule . '___edit']) || $permission == 'superadmin')
                                                    <a href="{{route($model.'.edit',$item->id)}}?source=register-cheque" style="margin:5px"><i class="fa fa-pencil"></i></a>
                                                @endif
                                                @if(isset($permission[$pmodule . '___delete']) || $permission == 'superadmin')
                                                    <form title="Trash"  style="margin:5px" action="{{ URL::route($model.'.destroy', $item->id) }}" class="deleteMe" method="POST">
                                                        <input type="hidden" name="_method" value="DELETE">
                                                        <input type="hidden" name="source" value="register-cheque">
                                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                        <button  style="float:left" type="submit"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                                    </form>
                                                @endif
                                                @if(isset($permission[$pmodule . '___deposit-cheque']) || $permission == 'superadmin')
                                                    <a href="{{route('register-cheque.deposit_cheque',$item->id)}}?source=register-cheque" style="margin:5px" title="Deposit Cheque">
                                                        <i class="fa fa-money-bill"></i>
                                                    </a>
                                                @endif
                                            @endif
                                            @if ($item->status == 'Deposited')
                                                <!-- Button trigger modal -->
                                                @if(isset($permission[$pmodule . '___update-status']) || $permission == 'superadmin')
                                                    <a href="#"  title="Update Status"  style="margin:5px" data-toggle="modal" data-target="#modelId{{$key+1}}">
                                                        <i class="fa fa-cogs" aria-hidden="true"></i>
                                                    </a>

                                                    <!-- Modal -->
                                                    <div class="modal fade" id="modelId{{$key+1}}" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
                                                        <form action="{{route('register-cheque.deposit_cheque_update_status',$item->id)}}" method="post" class="submitMe">
                                                            <input type="hidden" name="id" value="{{$item->id}}">
                                                            <input type="hidden" name="source" value="deposit-cheque">
                                                            {{method_field('PUT')}}
                                                            {{csrf_field()}}
                                                            <div class="modal-dialog" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">Cheque No: {{$item->cheque_no}}</h5>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div class="form-group">
                                                                            <label for="">Clearance date</label>
                                                                            <input type="date" name="clearance_date" id="clearance_date" class="form-control">
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label for="">Select Status</label>
                                                                            <select name="status" class="form-control">
                                                                                <option value="" selected disabled>Select Status</option>
                                                                                <option value="Cleared">Cleared</option>
                                                                                <option value="Bounced">Bounced</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                        <button type="submit" class="btn btn-primary">Save</button>
                                                                    </div>
                                                                </div>

                                                            </div>
                                                        </form>
                                                    </div>

                                                @endif
                                            @endif
                                            @if ($item->status == "Bounced" && $item->is_bounced_transfer == 0)
                                                @if(isset($permission[$pmodule . '___transfer']) || $permission == 'superadmin')
                                                    <form action="{{route('register-cheque.bounced_cheque_transfer',$item->id)}}" method="post" class="reverseMe">
                                                        <input type="hidden" name="_method" value="PUT">
                                                        <input type="hidden" name="id" value="{{$item->id}}">
                                                        <input type="hidden" name="source" value="bounced-cheque">
                                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                        <button title="Transfer Bounced Cheque" style="float:left" type="submit"><i class="fa fa-repeat" aria-hidden="true"></i></button>
                                                    </form>
                                                @endif
                                            @endif
{{--                                                <a href="{{route($model.'.show',$item->id)}}?source=register-cheque" style="margin:5px"><i class="fa fa-eye"></i></a>--}}
                                               <a href="#" title="View Cheque Image" style="margin:5px" onclick="openImageModel('{{asset($item->cheque_image)}}'); return false;"><i class="fa fa-eye"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach

                            </tbody>
                            <tfoot>
                            <tr>

                                @if (request()->source == 'register-cheque')
                                    <th colspan="9" style="text-align: right">Grand Total : {{manageAmountFormat($total)}}</th>

                                @else
                                    <th colspan="10" style="text-align: right">Grand Total : {{manageAmountFormat($total)}}</th>

                                @endif
                                <th></th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div id="deposited" class="tab-pane fade">
                        <table class="table table-bordered table-hover" id="deposited_cheque">
                            <thead>
                            <tr>
                                <th>S.No.</th>
                                <th>Customer</th>
                                <th>Cheque no</th>
                                <th>Drawers name</th>
                                <th>Drawers bank</th>
                                <th>Cheque date</th>
                                <th>Bank deposited</th>
                                @if (request()->source == 'register-cheque')
                                    <th>Date received</th>
                                @else
                                    <th>Date Deposited</th>
                                    <th>Deposited By</th>
                                @endif
                                <th>Amount</th>
                                <th>##</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php
                                $total = 0;
                            @endphp
                            @foreach($deposited as $key => $item)
                                <tr>
                                    <td>{{$key+1}}</td>
                                    <td>{{@$item->customer->customer_name}}</td>
                                    <td>{{$item->cheque_no}}</td>
                                    <td>{{$item->drawers_name}}</td>
                                    <td>{{$item->drawers_bank}}</td>

                                    <td>{{$item->cheque_date}}</td>
                                    <td>{{ $item->bank->bank  }}</td>
                                    @if (request()->source == 'register-cheque')
                                        <td>{{$item->date_received}}</td>
                                    @else
                                        <td>{{$item->deposited_date}}</td>
                                        <td>{{@$item->depositer->name}}</td>
                                    @endif
                                    <td class="text-right">{{manageAmountFormat($item->amount)}}</td>
                                    @php
                                        $total += $item->amount;
                                    @endphp
                                    <td>
                                        <div style="display: flex;">

                                            @if ($item->status == 'Deposited')
                                                <!-- Button trigger modal -->
                                                @if(isset($permission[$pmodule . '___update-status']) || $permission == 'superadmin')

                                                    <a href="#" class="modo" title="Update Status"  style="margin:5px" data-toggle="modal" data-target="#modelId" data-item-id="{{ $item->id }}"  data-cheque-no="{{ $item->cheque_no }}">
                                                        <i class="fa fa-cogs" aria-hidden="true"></i>
                                                    </a>

                                                @endif
                                            @endif
                                                <a href="#" title="View Cheque Image" style="margin:5px" onclick="openImageModel('{{asset($item->cheque_image)}}'); return false;"><i class="fa fa-eye"></i></a>

                                        </div>
                                    </td>
                                </tr>
                            @endforeach

                            </tbody>
                            <tfoot>
                            <tr>

                                @if (request()->source == 'register-cheque')
                                    <th colspan="9" style="text-align: right">Grand Total : {{manageAmountFormat($total)}}</th>

                                @else
                                    <th colspan="10" style="text-align: right">Grand Total : {{manageAmountFormat($total)}}</th>

                                @endif
                                <th></th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div id="cleared" class="tab-pane fade">
                        <table class="table table-bordered table-hover" id="cleared_cheque" >
                            <thead>
                            <tr>
                                <th>S.No.</th>
                                <th>Customer</th>
                                <th>Cheque no</th>
                                <th>Drawers name</th>
                                <th>Drawers bank</th>
                                <th>Cheque date</th>
                                <th>Bank deposited</th>
                                @if (request()->source == 'register-cheque')
                                    <th>Date received</th>
                                @else
                                    <th>Date Deposited</th>
                                    <th>Deposited By</th>
                                @endif
                                <th>Amount</th>
                                <th>##</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php
                                $total = 0;
                            @endphp
                            @foreach($cleared as $key => $item)
                                <tr>
                                    <td>{{$key+1}}</td>
                                    <td>{{@$item->customer->customer_name}}</td>
                                    <td>{{$item->cheque_no}}</td>
                                    <td>{{$item->drawers_name}}</td>
                                    <td>{{$item->drawers_bank}}</td>

                                    <td>{{$item->cheque_date}}</td>
                                    <td>{{$item->bank->bank}}</td>
                                    @if (request()->source == 'register-cheque')
                                        <td>{{$item->date_received}}</td>
                                    @else
                                        <td>{{$item->deposited_date}}</td>
                                        <td>{{@$item->depositer->name}}</td>
                                    @endif
                                    <td>{{manageAmountFormat($item->amount)}}</td>
                                    @php
                                        $total += $item->amount;
                                    @endphp
                                    <td>
                                        <div style="display: flex;">

                                        </div>
                                    </td>
                                </tr>
                            @endforeach

                            </tbody>
                            <tfoot>
                            <tr>

                                @if (request()->source == 'register-cheque')
                                    <th colspan="9" style="text-align: right">Grand Total : {{manageAmountFormat($total)}}</th>

                                @else
                                    <th colspan="10" style="text-align: right">Grand Total : {{manageAmountFormat($total)}}</th>

                                @endif
                                <th></th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div id="bounced" class="tab-pane fade">
                        <table class="table table-bordered table-hover" id="bounced_cheque" >
                            <thead>
                            <tr>
                                <th>S.No.</th>
                                <th>Customer</th>
                                <th>Cheque no</th>
                                <th>Drawers name</th>
                                <th>Drawers bank</th>
                                <th>Cheque date</th>
                                <th>Bank deposited</th>
                                @if (request()->source == 'register-cheque')
                                    <th>Date received</th>
                                @else
                                    <th>Date Deposited</th>
                                    <th>Deposited By</th>
                                @endif
                                <th>Amount</th>
                                <th>##</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php
                                $total = 0;
                            @endphp
                            @foreach($bounced as $key => $item)
                                <tr>
                                    <td>{{$key+1}}</td>
                                    <td>{{@$item->customer->customer_name}}</td>
                                    <td>{{$item->cheque_no}}</td>
                                    <td>{{$item->drawers_name}}</td>
                                    <td>{{$item->drawers_bank}}</td>

                                    <td>{{$item->cheque_date}}</td>
                                    <td>{{$item->bank->bank}}</td>
                                    @if (request()->source == 'register-cheque')
                                        <td>{{$item->date_received}}</td>
                                    @else
                                        <td>{{$item->deposited_date}}</td>
                                        <td>{{@$item->depositer->name}}</td>
                                    @endif
                                    <td class="text-right">{{manageAmountFormat($item->amount)}}</td>
                                    @php
                                        $total += $item->amount;
                                    @endphp
                                    <td>
                                        <div style="display: flex;">
                                            <a href="#" class="replace" title="Replace Cheque"  style="margin:5px" data-toggle="modal" data-target="#replace" data-item-id="{{ $item->id }}"  data-cheque-no="{{ $item->cheque_no }}">
                                                <i class="fa fa-sync-alt" aria-hidden="true"></i>
                                            </a>
                                            <a href="#" title="View Cheque Image" style="margin:5px" onclick="openImageModel('{{asset($item->cheque_image)}}'); return false;"><i class="fa fa-eye"></i></a>

                                        </div>
                                    </td>
                                </tr>
                            @endforeach

                            </tbody>
                            <tfoot>
                            <tr>

                                @if (request()->source == 'register-cheque')
                                    <th colspan="9" style="text-align: right">Grand Total : {{manageAmountFormat($total)}}</th>

                                @else
                                    <th colspan="10" style="text-align: right">Grand Total : {{manageAmountFormat($total)}}</th>

                                @endif
                                <th></th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </section>


    <!-- Modal -->

    <div class="modal fade" id="modelId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <form action="{{route('register-cheque.deposit_cheque_update_status', 0)}}" method="post" class="submitMe">
            <input type="hidden" name="id" id="modal-item-id" value="">
            <input type="hidden" name="source" value="deposit-cheque">
            {{ method_field('PUT') }}
            {{ csrf_field() }}
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Cheque No: <span id="cheque-no"></span></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="">Clearance date</label>
                            <input type="date" name="clearance_date" id="clearance_date" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="">Select Status</label>
                            <select name="status" class="form-control">
                                <option value="" selected disabled>Select Status</option>
                                <option value="Cleared">Cleared</option>
                                <option value="Bounced">Bounced</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="modal fade" id="replace" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <form action="#" method="post" class="submitMe">
            {{ method_field('PUT') }}
            {{ csrf_field() }}
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Cheque No: <span id="cheque-reference"></span></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="modal fade" id="openImageModel" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">

                <div class="modal-body" style="padding:0">

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="    background: #000;
                    opacity: 1;
                    height: 30px;
                    color: #fff;
                    width: 30px;
                    position: absolute;
                    right: -5px;
                    top: -6px;
                    border-radius: 50%;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <img src="" id="openImageModelImg" style="width:100%">
                </div>

            </div>
        </div>
    </div>
@endsection
@section('uniquepagescript')
    <style>
        .nav-tabs {
            margin-bottom: 20px;
        }

        /* Style for making tabs full width */
        .nav-tabs.nav-justified > li {
            display: table-cell;
            width: 1%;
            float: none;
        }

        .nav-tabs.nav-justified > li > a {
            text-align: center;
            vertical-align: middle;
            padding: 15px 0;
            font-weight: bold;
        }

        .nav-tabs.nav-justified > li.active > a {
            background-color: #ecf0f5 !important;
        }

        /* Custom badge style */
        .badge-count {
            background-color: orangered;
            color: white;
            border-radius: 50%;
            padding: 10px 15px;
            font-size: 14px;
            display: block;
            margin: 0 auto 5px;
            width: 40px;
        }

        /* Adjust spacing between counter and text */
        .tab-title {
            margin-top: 5px;
            font-size: 16px;
        }

        .tab-content {
            border: 1px solid #ddd;
            padding: 15px;
            background-color: #fff;
        }
    </style>

    <style type="text/css">
        .select2{
            width: 100% !important;
        }
        #note{
            height: 60px !important;
        }
        .align_float_right
        {
            text-align:  right;
        }
        .textData table tr:hover{
            background:#000 !important;
            color:white !important;
            cursor: pointer !important;
        }


        /* ALL LOADERS */

        .loader{
            width: 100px;
            height: 100px;
            border-radius: 100%;
            position: relative;
            margin: 0 auto;
            top: 35%;
        }

        /* LOADER 1 */

        #loader-1:before, #loader-1:after{
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-radius: 100%;
            border: 10px solid transparent;
            border-top-color: #3498db;
        }

        #loader-1:before{
            z-index: 100;
            animation: spin 1s infinite;
        }

        #loader-1:after{
            border: 10px solid #ccc;
        }

        @keyframes spin{
            0%{
                -webkit-transform: rotate(0deg);
                -ms-transform: rotate(0deg);
                -o-transform: rotate(0deg);
                transform: rotate(0deg);
            }

            100%{
                -webkit-transform: rotate(360deg);
                -ms-transform: rotate(360deg);
                -o-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }

    </style>

    <div id="loader-on" class="loder" style="
position: fixed;
top: 0;
text-align: center;
display: block;
z-index: 999999;
width: 100%;
height: 100%;
background: #000000b8;
display:none;
">
        <div class="loader" id="loader-1"></div>
    </div>
    <script src="{{asset('js/sweetalert.js')}}"></script>
    <script src="{{asset('js/form.js')}}"></script>
    <script>
        async function openImageModel(image){
            await $('#openImageModelImg').attr('src',image);
            await $('#openImageModel').modal('show');
            return true;
        }
        $(document).on('submit','.reverseMe',function(e){
            e.preventDefault();
            var $this = this;
            Swal.fire({
                title: 'Do you want to reverse this?',
                showCancelButton: true,
                confirmButtonText: `Procced`,
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    form.delete(this);
                }
            })

  });
</script>
    <script>
        $(document).ready(function() {

            $('.modo').on('click', function() {

                var itemId = $(this).data('item-id');
                var chequeNo = $(this).data('cheque-no');

                var url = '{{route("register-cheque.deposit_cheque_update_status", ":id")}}';
                url = url.replace(':id', itemId);

                $('#modelId form').attr('action', url);
                $('#modal-item-id').val(itemId);

                $('#cheque-no').text(chequeNo);
            });
            $('.replace').on('click', function() {

                var itemId = $(this).data('item-id');
                var chequeNo = $(this).data('cheque-no');

                var url = '{{route("register-cheque.deposit_cheque_update_status", ":id")}}';
                url = url.replace(':id', itemId);

                $('#modelId form').attr('action', url);
                $('#modal-item-id').val(itemId);

                $('#cheque-reference').text(chequeNo);
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#bounced_cheque').DataTable({
                "pageLength": 30,
                "lengthMenu": [[10, 30, 50, 100], [10, 30, 50, 100]],
            });
            $('#deposited_cheque').DataTable({
                "pageLength": 30,
                "lengthMenu": [[10, 30, 50, 100], [10, 30, 50, 100]],
            });
            $('#cleared_cheque').DataTable({
                "pageLength": 30,
                "lengthMenu": [[10, 30, 50, 100], [10, 30, 50, 100]],
            });
            $('#ready_cheques').DataTable({
                "pageLength": 30,
                "lengthMenu": [[10, 30, 50, 100], [10, 30, 50, 100]],
            });
            $('#registred').DataTable({
                "pageLength": 30,
                "lengthMenu": [[10, 30, 50, 100], [10, 30, 50, 100]],
            });
        });

    </script>
@endsection
