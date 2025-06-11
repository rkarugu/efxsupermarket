@extends('layouts.admin.admin')

@section('content')
    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> {{ $route->route_name }} Route Customers </h3>

                   <div class"d-flex">
                    <a href="/api/route-customer-export-all?route_id={{ $route->id }}" class="btn btn-success">
                            Export
                        </a>

                        <a href="{!! route($model . '.route_customer_add', $customer->id) !!}" class="btn btn-success ml-12">
                            Add Route Customer
                        </a>
                   </div>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover tablest" id="create_datatable_50">
                        <thead>
                        <tr>
                            <th>Center</th>
                            <th>Shop Owner</th>
                            <th>Phone No.</th>
                            <th>Business Name</th>
                            <th>Mapped</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $totalCashSales= 0;@endphp
                        @foreach ($lists as $key => $item)
                            @php $totalCashSales+=$item->total_sales; @endphp
                            <tr>
                                <td>{{ $item->center['name'] ?? '-' }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->phone }}</td>
                                <td>{{ $item->bussiness_name }}</td>
                                <td>{{ $item->image_url ? 'Yes' : 'No' }}</td>
                                <td>
                                  <div class="action-button-div">
                                  <a href="{!! route($model . '.route_customer_edit', $item->id) !!}" class="text-primary"><i class="fas fa-edit fa-lg"></i> </a>
                                    <a href="javascript:void(0);" data-toggle="modal" data-target="#delete-customer-modal" data-backdrop="static"
                                              data-id="{{ $item->id }}" data-name="{{ $item->bussiness_name }}"><i class="fas fa-user-times text-danger fa-lg"></i></a>
                                  </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


        <div class="modal fade" id="delete-customer-modal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="box-title"> Remove Customer </h3>

                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>

                    <div class="box-body">
                        <p style="font-size: 16px;"> Are you sure you want to remove <span id="shop-name"></span> from the system? </p>
                        <form action="{!! route("route-customers.remove")  !!} " method="post" id="delete-customet-form">
                            {{ csrf_field() }}

                            <input type="hidden" id="shop-id" name="shop_id">
                        </form>
                    </div>

                    <div class="box-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" onclick="confirmDeleteCustomer();">Yes, Remove</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('uniquepagescript')
    <style type="text/css">
        .select2 {
            width: 100% !important;
        }

        #note {
            height: 60px !important;
        }

        .align_float_right {
            text-align: right;
        }

        .textData table tr:hover {
            background: #000 !important;
            color: white !important;
            cursor: pointer !important;
        }


        /* ALL LOADERS */

        .loader {
            width: 100px;
            height: 100px;
            border-radius: 100%;
            position: relative;
            margin: 0 auto;
            top: 35%;
        }

        /* LOADER 1 */

        #loader-1:before,
        #loader-1:after {
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

        #loader-1:before {
            z-index: 100;
            animation: spin 1s infinite;
        }

        #loader-1:after {
            border: 10px solid #ccc;
        }

        @keyframes spin {
            0% {
                -webkit-transform: rotate(0deg);
                -ms-transform: rotate(0deg);
                -o-transform: rotate(0deg);
                transform: rotate(0deg);
            }

            100% {
                -webkit-transform: rotate(360deg);
                -ms-transform: rotate(360deg);
                -o-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }
    </style>
    <div id="loader-on" class="loder"
         style="
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
        <div class="loader " id="loader-1"></div>
    </div>
    <script src="{{ asset('js/sweetalert.js') }}"></script>
    <script src="{{ asset('js/form.js') }}"></script>
    <script type="text/javascript">
        function filterReport(input) {
            alert();
            $('#loader-on').show();
            // $('#invoice_body').html('');
            // $('#cash_sales_body').html('');
            var values = $(input).parents('form').serialize();
            console.log('valuesvaluesvaluesvalues', values);
            // values = values + $('#filter').data('id');
            // alert(values);
            $.ajax({
                type: "GET",
                url: "{!! route('maintain-customers.route_customer_list.date_time') !!}",
                success: function (response) {
                    alert();
                    // $('.tablest').show();
                    // $.each(response.invoiceData, function (indexInArray, valueOfElement) { 
                    //     var invoiceTableBody = '<tr>'+
                    //             '<td> Invoice </td>'+
                    //             '<td>'+valueOfElement.description+'</td>'+
                    //             '<td>'+(valueOfElement.vat_percentage)+'</td>'+
                    //             '<td>'+(valueOfElement.vat_amount)+'</td>'+
                    //             '<td>'+(valueOfElement.tax_manager_title)+'</td>'+
                    //             '<td>'+(valueOfElement.total_cost_with_vat)+'</td>'+
                    //     '</tr>';
                    //     $('#invoice_body').append(invoiceTableBody);
                    // });

                    // $.each(response.cashSalesData, function (indexInArray2, valueOfElement2) { 
                    //     var cashSalesTableBody = '<tr>'+
                    //             '<td> Cash Sales </td>'+
                    //             '<td>'+valueOfElement2.description+'</td>'+
                    //             '<td>'+(valueOfElement2.vat_rate)+'</td>'+
                    //             '<td>'+(valueOfElement2.vat_amount)+'</td>'+
                    //             '<td>'+(valueOfElement2.tax_manager_title)+'</td>'+
                    //             '<td>'+(valueOfElement2.total)+'</td>'+
                    //     '</tr>';
                    //     $('#cash_sales_body').append(cashSalesTableBody);
                    // });


                    // $('#grand_total_sales_vat_invoice').html(response.total_sales_with_vat_invoice);
                    // $('#grand_total_sales_vat_cash_sales').html(response.total_sales_with_vat_cash_sales);
                    // $('#grand_total_vat').html('Total VAT : '+response.grand_total_vat);

                    // console.log(response.total_sales_with_vat_invoice);
                    // console.log(response.total_sales_with_vat_cash_sales);

                    // $('#loader-on').hide();


                },
                error: function (response) {
                    alert();
                    $('#loader-on').hide();
                }
            });

        }
    </script>

    <script type="text/javascript">
        function confirmDeleteCustomer() {
            $("#delete-customet-form").submit();
        }

        $('#delete-customer-modal').on('show.bs.modal', function (event) {
            let triggeringButton = $(event.relatedTarget);
            let idValue = triggeringButton.data('id');
            let nameValue = triggeringButton.data('name');

            $("#shop-id").val(idValue);
            $("#shop-name").text(nameValue);
        })
    </script>
@endsection
