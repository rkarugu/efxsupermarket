
@extends('layouts.admin.admin')

@section('content')


<!-- Main content -->
<section class="content">
    <!-- Small boxes (Stat box) -->
    <div class="box box-primary">
        <div class="box-header with-border no-padding-h-b">
        <div style="float:right;"><a class="btn btn-danger" href="{{route($model.'.stock-movements',$stock_id_code)}}">Back</a></div>

            <br>
            @include('message')

            <div class="col-md-12 no-padding-h">
                <table class="table table-bordered table-hover" id="create_datatable">
                    <thead>
                        <tr>
                            <th width="10%">S.No.</th>
                            <th width="10%"  >Period</th>
                            <th width="10%"  >Date</th>
                            <th width="10%"  >GL Account</th>
                            <th width="10%"  >GL Account Name</th>
                             
                            <th width="10%"  >Description</th>

                             <th width="10%"  >Transaction Type</th>
                            <th width="10%"  >Transaction No</th>
                            <th width="10%"  >Debit</th>
                             <th width="10%"  >Credit</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $b = 1;
                          $account_codes =  getChartOfAccountsList();
                         ?>
                        @foreach($data as $row)

                        <tr>
                             <td>{!! $b !!}</td>

                            <td>{!! $row->period_number !!}</td>
                            <td>{!! getDateFormatted($row->trans_date) !!}</td>
                            <td>{!! $row->account !!}</td>
                             <td>{!! $account_codes[$row->account] !!}</td>

                           
                            <td>{!! $row->narrative !!}</td>
                             <td>{!! $row->transaction_type !!}</td>
                            <td>{!! $row->transaction_no !!}</td>
                            <td>{!! $row->amount>'0'?$row->amount:'' !!}</td>
                            <td>{!! $row->amount<'0'?$row->amount:'' !!}</td>

                        </tr>
                        <?php $b++; ?>
                        @endforeach
                    </tbody>
                </table>
                 <table class="table table-bordered table-hover removeborder" >
                                  <tr> 
                                    <td width="10%"></td>
                                    <td width="10%"></td>
                                    <td width="10%"></td>
                                    <td width="10%"></td>
                                    <td width="10%"></td>
                                    <td width="10%"></td>
                                    <td width="10%"></td>
                                    <td width="10%">Total</td>
                                    <td width="10%">{!! $positiveAMount !!}</td>
                                    <td width="10%">{!! $negativeAMount !!}</td>
                                               
                                  </tr>
                                </table>
            </div>
        </div>
    </div>


</section>
<style type="text/css">
    .removeborder td{
border: none !important;
}
.table td {
  font-size: 13px;
}
</style>
@endsection
