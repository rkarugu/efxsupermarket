@extends('layouts.admin.admin')
@push('styles')
    <style>
        th, td {
            text-align: right;
        }
    </style>
@endpush
@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title"> Cashier Summary</h3>
            </div>
            @include('message')
            <div class="box-body">
                    <form action="" method="get">
                        <div class="row pb-4">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="restaurant_id">Branch</label>
                                        {!!Form::select('restaurant_id', $branches, request()->input('restaurant_id') ?? $user->restaurant_id, ['placeholder'=>'Select Branch ', 'class' => 'form-control mlselec6t','title'=>'Please select Branch','id'=>'restaurant_id'  ])!!}
                                    </div>
                                </div>


                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">Date</label>
                                    <input type="date" name="date" id="date" class="form-control"  value="{{request()->input('date') ?? date('Y-m-d')}}">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <input type="submit" name="intent" value="FILTER" class="btn btn-primary btn-sm" style="margin-top: 25px;"/>
                                    <input type="submit" name="intent" value="PDF" class="btn btn-primary btn-sm" style="margin-top: 25px;"/>
                                </div>
                            </div>
                        </div>
                    </form>
                @php
                    $total = [];
                @endphp
                <table class="table table-striped mt-3" id="cashiersTable">
                    <thead>
                    <tr class="text-right">
                        <th>#</th>
                        <th>Name</th>
                        <th>Branch</th>
                        <th>Drop Limit</th>
                        <th>Total Sales</th>
                        <th>Returns</th>
                        <th>Net Sales</th>
                        @foreach($payMethods as $payMethod)
                            @if(!$payMethod->is_cash)
                                <th class="text-right">{{ $payMethod->title }}</th>
                            @endif
                        @endforeach
                        <th>Total Drops</th>
                        <th>Cash At Hand</th>

                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($groupedData as $index => $data)
                        <tr class="parent-row">
                            <td>{{ $loop -> iteration }}</td>
                            <td>{{ $data['user_name'] }}</td>
                            <td>{{ $data['branch'] }}</td>
                            <td>{{number_format( $data['user']->drop_limit, 2) ?? 0.00 }}</td>
                            <td>{{ number_format($data['total_sales'], 2) }}</td>
                            <td>{{ number_format($data['total_returns'], 2) }}</td>
                            <td>{{number_format(($data['net_cash']) , 2) }}</td>
                            @php
                                $amounts = [];
                                $cash = 0 - $data['total_change'];
                                foreach ($data['payment_methods'] as $payment_method) {
                                    $amounts[$payment_method['method_id']] = $payment_method['amount'];
                                    if ($payment_method['is_cash']) {
                                        $cash += $payment_method['amount'];
                                    }
                                }
                            @endphp

                            @foreach($payMethods as $payMethod)
                                @if(!$payMethod->is_cash)
                                    <th class="text-right">
                                        {{ isset($amounts[$payMethod->id]) ? number_format($amounts[$payMethod->id], 2) : '-' }}
                                    </th>
                                @endif

                            @endforeach
                            <td>{{ number_format($data['total_drops'], 2)}}</td>
                            <td>{{ number_format(\App\User::find($data['user']->id)->cashAtHand() , 2) }}</td>
                            <td><a href="{{ route('cashier-management.cashier', base64_encode($data['user_id'])) }}"><i class="fa fa-eye"></i></a> </td>
                        </tr>
                    @endforeach

                    </tbody>
                    <tfoot>
                      <tr class="text-bold">
                          <td colspan="4">Grand Totals</td>
                          @php
                              $grouped = collect($groupedData);
                          @endphp
                          <td  class="text-right">{{number_format( $grouped->sum('total_sales'), 2) }}</td>
                          <td  class="text-right">{{number_format( $grouped->sum('total_returns'), 2) }}</td>
                          <td  class="text-right">{{number_format( $grouped->sum('net_cash'), 2) }}</td>

                          @php
                           $methods = [];
                            foreach ($grandTotals as $key=>$value) {
                                    $methods[$key] = $value;
                                }
                          @endphp

                          @foreach($payMethods as $payMethod)
                              @if(!$payMethod->is_cash)
                                  <th class="text-right">
                                      {{ isset($methods[$payMethod->title]) ? number_format($methods[$payMethod->title], 2) : '-' }}
                                  </th>
                              @endif
                          @endforeach
                          <td  class="text-right">{{ number_format($grouped->sum('total_drops'), 2) }}</td>
                          <td  class="text-right">{{ number_format(ceil($grouped->sum('total_cash') - $grouped->sum('total_drops') - $grouped->sum('total_returns')) , 2) }}</td>
                          <td></td>
                      </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </section>


@endsection
@push('scripts')
    <script src="{{asset('js/sweetalert.js')}}"></script>
    <script src="{{asset('js/form.js')}}"></script>
    <link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet"/>
    <script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
    <script>
        var VForm = new Form();
        $(document).ready(function() {
            var table = $("#cashiersTable").DataTable();

            $('#filter').click(function(e){
                e.preventDefault();
                table.draw();
            });
            $(".mlselec6t").select2();
        })

    </script>

    <script>
        $(document).ready(function() {
            $('.parent-cell').on('click', function() {
                var parentRow = $(this).closest('tr');
                var childRow = parentRow.next('.child-row');
                var toggleIcon = parentRow.find('.toggle-icon i');

                if (childRow.is(':visible')) {

                    childRow.hide();
                    toggleIcon.toggleClass('fa-circle-plus fa-circle-minus');
                } else {

                    $('.child-row').hide();
                    $('.toggle-icon i').removeClass('fa-circle-minus').addClass('fa-circle-plus');

                    childRow.show();
                    toggleIcon.toggleClass('fa-circle-plus fa-circle-minus');
                }

                var childTableId = childRow.find('.child-table').attr('id');
                var childRowIndex = childRow.attr('id').split('-')[2]; // Extract the index from the child-row id

                childRow.toggleClass('collapse');


                if (!$.fn.DataTable.isDataTable('#' + childTableId)) {
                    $('#' + childTableId).DataTable({
                        "processing": true,
                        "serverSide": true,
                        "ajax": {
                            url: '{!! route('cashier-management.cashier-sales') !!}',
                            data: function(data) {
                                data.cashier = childRowIndex;
                            }
                        },

                        columns: [{
                            data: 'DT_RowIndex',
                            searchable: false,
                            sortable: false,
                            width: "70px"
                        },
                            {
                                data: "date",
                                name: "date"
                            },
                            {
                                data: "time",
                                name: "time"
                            },
                            {
                                data: "sales_no",
                                name: "sales_no"
                            },
                            {
                                data: "customer",
                                name: "customer"
                            },
                            {
                                data: "customer_phone_number",
                                name: "customer_phone_number"
                            },
                            {
                                data: "provider_name",
                                name: "payment_providers.name"
                            },

                            {
                                data: "payment_reference",
                                name: "wa_tender_entries.reference"
                            },
                            {
                                data: "payment_amount",
                                name: "wa_pos_cash_sales_payments.amount"
                            },
                            {
                                data: "total_sales",
                                name: "total_sales",
                                searchable: false
                            },

                        ],
                    });
                }
            });
        });

    </script>
@endpush