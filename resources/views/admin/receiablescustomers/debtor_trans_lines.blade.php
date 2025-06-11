@extends('layouts.admin.admin')

@section('content')

    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
               <div class="box-header-flex">
                   <h3 class="box-title"> Debtor Trans Lines </h3>
                   <form method="get">
                       {{ @csrf_field() }}

                       <input type="submit" name="intent" value="EXCEL" class="btn btn-primary">
                   </form>
               </div>
            </div>

            <div class="box-body">
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
                            <th>Total</th>
                        </tr>
                        </thead>
                        <tbody>

                        <?php $b = 1;

                        $total_amount = [];
                        ?>
                        @foreach($lists as $list)
                            <tr>
                                <td>{!! $b !!}</td>
                                <td>{!! isset($number_series_list[$list->type_number])?$number_series_list[$list->type_number] : '' !!}</td>
                                <td>{!! \Carbon\Carbon::parse($list->trans_date)->toDateString() !!}</td>
                                <td>{!! $list->document_no !!}</td>
                                <td>{!! $list->reference !!}</td>
                                <td>{!! manageAmountFormat($list->amount) !!}</td>
                            </tr>
                                <?php
                                $b++;
                                $total_amount[] = $list->amount;
                                ?>
                        @endforeach

                        </tbody>
                        <tfoot>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style="font-weight: bold;">Total</td>
                            <td style="font-weight: bold;">{{ manageAmountFormat(array_sum($total_amount))}}</td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>


    </section>

@endsection
