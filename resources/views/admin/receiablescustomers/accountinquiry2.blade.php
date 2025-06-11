@extends('layouts.admin.admin')

@section('content')

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border no-padding-h-b">
                <div class="col-md-12">
                    <form action="{{route('maintain-customers.debtors-inquiry-2',['slug'=>$row->slug])}}">
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
                                    <label for="date_to">&nbsp</label>

                                    <button type="submit" class="btn btn-primary btn-sm " style="display:block">Filter</button>
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
                            <th style="width: 3%;">S.No.</th>
                            <th>Type</th>
                            <th>TXN No</th>
                            <th>Date</th>
                            <th>Reference</th>
                            <th>Name</th>
{{--                            @if (!request()->posted)--}}
{{--                                <th>Allocated Amount</th>--}}
{{--                                <th>Settled Amount</th>--}}
{{--                            @endif--}}

                            <th>Document No</th>
                            <th>Total</th>
                            @if (request()->posted)
                                <th>Action</th>
                            @endif
                        </tr>
                        </thead>
                        <tbody>

                        <?php
                        $b = 1;
                        $total_amount = 0;
                        ?>
                        @foreach($row->getAllDebtorsTrans as $list)
                            <tr>
                                <td style="width: 3%;">{!! $b !!}</td>
                                <td>{!! isset($number_series_list[$list->type_number])?$number_series_list[$list->type_number] : '' !!}</td>
                                <td>{!! manageOrderidWithPad($list->id) !!}</td>
                                <td>{!! \Carbon\Carbon::parse($list->trans_date)->toDateString() !!}</td>
                                <td>{!! $list->reference !!}</td>
                                <td>{!! $list->invoice_customer_name !!}</td>

{{--                                @if (!request()->posted)--}}
{{--                                    @if ($list->amount < 0)--}}
{{--                                        <td>----</td>--}}
{{--                                        <td>----</td>--}}
{{--                                    @else--}}
{{--                                        <td>{!! $list->allocated_amount !!}</td>--}}
{{--                                        <td>{!! manageAmountFormat($list->amount - $list->allocated_amount) !!}</td>--}}
{{--                                    @endif--}}
{{--                                @endif--}}

                                <td>{!! $list->document_no !!}</td>
                                <td>{!! manageAmountFormat($list->amount) !!}</td>
                                @if (request()->posted)
                                    <td>
                                        @if ($list->type_number == 12)
                                            <form action="{{route('maintain-customers.debtors-inquiry.reverse_receipt',['slug'=>'showroom-stock','document_no'=>$list->document_no])}}"
                                                  method="post">
                                                {{csrf_field()}}
                                                <button type="submit" title="Reverse Receipt - {!! $list->document_no !!}">
                                                    <i class="fa fa-undo" aria-hidden="true"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                                <?php $b++;

                                $total_amount += $list->amount;

                                ?>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <td style="width: 3%;"></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
{{--                            @if (!request()->posted)--}}
{{--                                <td></td>--}}
{{--                                <td></td>--}}
{{--                            @endif--}}
                            <td style="font-weight: bold;">Total</td>
                            <td style="font-weight: bold;">{{ manageAmountFormat($total_amount) }}</td>
                            @if (request()->posted)
                                <td></td>
                            @endif
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>


    </section>

@endsection
