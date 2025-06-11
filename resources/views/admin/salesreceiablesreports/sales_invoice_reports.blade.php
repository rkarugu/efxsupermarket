@extends('layouts.admin.admin')

@section('content')
    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">

            <div class="box-header with-border">
                <div class="d-flex justify-content-between">
                    <h3 class="box-title">Customer Invoices Report</h3>
                    {{-- <a href="{{ url()->previous() }}" class="btn btn-primary" role="button">
                        << Back to Sales and Receivables Reports </a> --}}
                </div>
            </div>

            <div class="box-header with-border no-padding-h-b">
                <div style="height: 150px ! important;">
                    <div class="card-header">
                        <i class="fa fa-filter"></i> Filter
                    </div><br>
                    {!! Form::open(['route' => 'sales-and-receivables-reports.customer_invoices', 'method' => 'get']) !!}

                    <div>
                        <div class="col-md-12 no-padding-h">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    {!! Form::text('start-date', null, [
                                        'class' => 'datepicker form-control',
                                        'placeholder' => 'Start Date',
                                        'readonly' => true,
                                    ]) !!}
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <div class="form-group">
                                    {!! Form::text('end-date', null, [
                                        'class' => 'datepicker form-control',
                                        'placeholder' => 'End Date',
                                        'readonly' => true,
                                    ]) !!}
                                </div>
                            </div>



                            <div class="col-sm-1">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-success" name="manage-request"
                                        value="filter">Filter</button>
                                </div>
                            </div>

                            <div class="col-sm-1">
                                <button title="Export In Excel" type="submit" class="btn btn-warning" name="manage-request"
                                    value="xls"><i class="fa fa-file-excel" aria-hidden="true"></i>
                                </button>
                            </div>


                        </div>


                    </div>

                    </form>
                </div>

                <br>
                @include('message')
                <div class="col-md-12 no-padding-h">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th width="10%">S.No.</th>
                                <th width="10%">Invoice Number</th>
                                <th width="20%">Customer Name</th>
                                <th width="15%">Date</th>
                                <th width="15%">Due Date</th>
                                <th width="10%">Invoice Total</th>
                                <th width="10%">Paid</th>
                                <th width="10%">Due</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1;
                            $final_amount = [];
                            $paidtotal = 0;
                            $deutotal = 0;
                            
                            ?>
                            @foreach ($all_item as $row)
                                <tr>
                                    <td>{{ $i }}</td>
                                    <td>{{ $row->sales_invoice_number }}</td>
                                    <td>{{ ucfirst(@$row->getRelatedCustomer->customer_name) }}</td>
                                    <td>{{ $row->order_date }}</td>
                                    <td>{{ $row->order_date }}</td>


                                    <?php
                                    //echo "<pre>"; print_r($row->getRelatedCustomerAllocatedAmnt->sum('allocated_amount')); die;
                                    $total_amount = $row->getRelatedItem->sum('total_cost_with_vat');
                                    $final_amount[] = $total_amount;
                                    ?>
                                    <td>{{ manageAmountFormat($total_amount) }}</td>
                                    <td>{{ number_format($row->getRelatedCustomerAllocatedAmnt->sum('allocated_amount'), 2) }}
                                    </td>
                                    <td>{{ number_format($total_amount - $row->getRelatedCustomerAllocatedAmnt->sum('allocated_amount'), 2) }}
                                    </td>
                                    @php
                                        $paidtotal += $row->getRelatedCustomerAllocatedAmnt->sum('allocated_amount');
                                        $deutotal +=
                                            $total_amount -
                                            $row->getRelatedCustomerAllocatedAmnt->sum('allocated_amount');
                                    @endphp

                                </tr>
                                <?php $i++; ?>
                            @endforeach




                        </tbody>

                        <tfoot style="font-weight: bold;">
                            <td></td>
                            <td>Total</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>{{ manageAmountFormat(array_sum($final_amount)) }}</td>
                            <td>{{ number_format($paidtotal, 2) }}</td>
                            <td>{{ number_format($deutotal, 2) }}</td>

                        </tfoot>
                    </table>
                </div>


            </div>
        </div>
    </section>
@endsection


@section('uniquepagestyle')
    <link rel="stylesheet" href="{{ asset('assets/admin/dist/datepicker.css') }}">
@endsection

@section('uniquepagescript')
    <script src="{{ asset('assets/admin/dist/bootstrap-datepicker.js') }}"></script>
    <script>
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd'
        });
    </script>
@endsection
