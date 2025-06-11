
@extends('layouts.admin.admin')

@section('content')


    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                        <div class="box-header with-border no-padding-h-b">
                            
                            <br>
                            @include('message')
                            <div class="col-md-12 no-padding-h">
                                <table class="table table-bordered table-hover" id="create_datatable">
                                    <thead>
                                    <tr>
                                        <th width="20%">Date</th>
                                        <th width="20%">Branch</th>
                                        <th width="20%">Payment Method</th>
                                        <th width="20%">No of Entries</th>
                                        <th width="20%">GL Account No</th>
                                        <th width="20%">Amount</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                     <?php 
                                        $amount = [];
                                        ?>
                                    @if(!empty($data))
                                        <?php $b = 1;
                                        
                                        ?>
                                        @foreach($data as $list)
                                            <tr>
                                                <td>{!! $list['date'] !!}</td>
                                                <td>{!! $list['restaurant_name'] !!}</td>
                                                <td>{!!  $list['payment_method'] !!}</td>
                                                <td>{!! $list['no_of_entry'] !!}</td>
                                                <td>{!! $list['gl_account_no'] !!}</td>
                                                <td>{!! manageAmountFormat($list['amount']) !!}</td>
                                            </tr>
                                           <?php $b++;
                                           $amount[] = $list['amount'];

                                            ?>
                                        @endforeach
                                    @endif


                                    </tbody>

                                      <tfoot>
                                        <tr >
                                           
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            
                                           
                                            <td><?= manageAmountFormat(array_sum($amount))?></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>


    </section>

       <style type="text/css">
        tfoot tr td {
            font-weight: bold;
        }
    </style>
   
@endsection
