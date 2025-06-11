@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header" style="border-bottom: 1px solid #eee">
                @include('message')
                <div class="d-flex justify-content-between">
                    <h4>Customer Information</h4>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-8">
                        <table class="table table-bordered">
                            <tr>
                                <th>Customer</th>
                                <td>{{ $customer->customer_name }}</td>
                                <th>
                                    KCB Till
                                </th>
                                <td>
                                    {{ $customer->kcb_till }}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    Route
                                </th>
                                <td>
                                    {{ $customer->route->route_name }}
                                </td>
                                <th>
                                    EQUITY Till
                                </th>
                                <td>
                                    {{ $customer->equity_till }}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    Telephone
                                </th>
                                <td>
                                    {{ $customer->telephone }}
                                </td>
                                <th>
                                    Return Limit
                                </th>
                                <td>
                                    {{ manageAmountFormat($customer->return_limit) }}
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-sm-4">
                        <div rowspan="3" class="text-center">
                            <h4>Balance</h4>
                            <h3 style="font-weight: bold">{{ manageAmountFormat($customer->balance) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="box box-primary">
            <ul class="nav nav-tabs">
                @if (can('customer-statement', 'customer-centre'))
                    <li class="active"><a href="#statement" data-toggle="tab">Statement</a></li>
                @endif
                @if (can('route-customers', 'customer-centre'))
                    <li><a href="#customers" data-toggle="tab">Route Customers</a></li>
                @endif
            </ul>
            <div class="tab-content">
                @if (can('customer-statement', 'customer-centre'))
                    <div class="tab-pane active" id="statement">
                        @include('admin.customer_centre.partials.statement')
                    </div>
                @endif
                @if (can('route-customers', 'customer-centre'))
                    <div class="tab-pane" id="customers">
                        @include('admin.customer_centre.partials.route_customers')
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection

@push('scripts')

    <script>

        $(document).ready(function() {
            let crc = @json(session('crc'));
            if (typeof crc !== 'undefined' && crc !== null) {
                printBill(crc)
            }
        });
        function printBill(slug) {
            jQuery.ajax({
                url:slug,
                type: 'GET',
                async: false,   //NOTE THIS
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    var divContents = response;
                    var printWindow = window.open('', '', 'width=600');
                    printWindow.document.write(divContents);
                    printWindow.document.close();
                    printWindow.print();
                    printWindow.close();
                    // location.reload();
                    {{--location.href = '{{ route($model.'.index') }}';--}}

                }
            });
        }
    </script>

@endpush
