@extends('layouts.admin.admin')

@section('content')

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border no-padding-h-b">
                <div class="col-md-12">
                    <form action="{{route('maintain-customers.debtors-inquiry',['slug'=>$row->slug])}}">
                        @if (request()->posted)
                            <input type="hidden" name="posted" value="{{request()->posted}}">
                        @endif
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="date_from">From</label>
                                    <input type="date" name="date_from" value="{{request()->date_from ?? NULL}}" id="date_from" class="form-control" placeholder="" aria-describedby="helpId">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="date_to">To</label>
                                    <input type="date" name="date_to" value="{{request()->date_to ?? NULL}}" id="date_to" class="form-control" placeholder="" aria-describedby="helpId">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label style="display: block;">&nbsp</label>

                                    <button type="submit" class="btn btn-primary">Filter</button>
                                    <input type="submit" value="EXCEL" name="intent" class="btn btn-primary ml-12">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <br>
                @include('message')
                <div class="col-md-12 no-padding-h">
                    <table class="table table-bordered table-hover" id="create_datatable">
                        <thead>
                        <tr>
                            <th>S.No.</th>
                            <th>Type</th>
                            <th>Date</th>
                            <th>Document No</th>
                            <th>Reference</th>
                            <th>Channel</th>
                            <th>User</th>
                            <th>Transaction Count</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>

                        <?php $b = 1;

                        $total_amount = [];
                        ?>
                        @if(count($row->getAllDebtorsTrans)>0)
                            @foreach($row->getAllDebtorsTrans as $list)
                                <tr>
                                    <td>{!! $b !!}</td>
                                    <td>{!! isset($number_series_list[$list->type_number])?$number_series_list[$list->type_number] : '' !!}</td>
                                    <td>{!! \Carbon\Carbon::parse($list->trans_date)->toDateString() !!}</td>
                                    <td>{!! $list->document_no !!}</td>
                                    <td>{!! $list->reference !!}</td>
                                    <td>{!! $list->channel !!}</td>
                                    <td>{!! \App\User::find($list->user_id)?->name ?? 'System' !!}</td>
                                    <td>{!! $list->count !!}</td>
                                    <td>{!! manageAmountFormat($list->total) !!}</td>
                                    <td>
                                        <div class="action-button-div">
                                            <a href="{{ route('maintain-customers.debtors-inquiry-lines', $list->document_no) }}" target="_blank">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            <a href="javascript:void(0);" class="text-danger">
                                                <i class="fa fa-flag"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                    <?php
                                    $b++;
                                    $total_amount[] = $list->total;
                                    ?>
                            @endforeach
                        @endif

                        </tbody>
                        <tfoot>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style="font-weight: bold;">Total</td>
                            <td style="font-weight: bold;">{{ manageAmountFormat(array_sum($total_amount))}}</td>
                            <td></td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>


    </section>

@endsection
