@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                @include('message')
                <div class="d-flex align-items-center justify-content-between">
                    <h4 class="box-title flex-grow-1">Procurement Dashboard</h4>
                    <form class="d-flex">
                        <x-filters.store />
                        <button class="btn btn-primary btn-sm" style="margin-left: 10px">
                            <i class="fa fa-filter"></i> filter
                        </button>
                    </form>
                </div>
            </div>
            <div class="box-body">
                <x-dashboard.stock-stats />
                <hr>
                <x-dashboard.lpo-stats />
                <hr>
                <x-dashboard.supplier-stats />
                <hr>
                <x-dashboard.supplier-invoice-aging />
                <hr>
                <div class="d-flex align-items-center" style="border-bottom: 1px solid #eee">
                    <h4 class="flex-grow-1">Supplier Information</h4>
                    <a id="printSupplierInformation" href="javascript: printSection('supplierInformation')"
                        style="display: none">
                        <i class="fa fa-print fa-xl"></i>
                    </a>
                </div>
                <div style="max-height: 600px; overflow-y:auto; position:relative">
                    <x-dashboard.supplier-information />
                </div>
                <hr>
                <div class="d-flex align-items-center" style="border-bottom: 1px solid #eee">
                    <h4 class="flex-grow-1">Supplier Balances</h4>
                    <a id="printSupplierBalances" href="javascript: printSection('supplierBalances')" style="display: none">
                        <i class="fa fa-print fa-xl"></i>
                    </a>
                </div>
                <div style="max-height: 600px; overflow-y:auto; position:relative">
                    <x-dashboard.supplier-balances />
                </div>
                <hr>                
                <div class="row">
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center" style="border-bottom: 1px solid #eee">
                            <h4 class="flex-grow-1">Primary Sales</h4>
                            <a id="printPrimarySales" href="javascript: printSection('primarySales')" style="display: none">
                                <i class="fa fa-print fa-xl"></i>
                            </a>
                        </div>
                        <div style="max-height: 600px; overflow-y:auto;">
                            <x-dashboard.turn-over-purchases />
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center" style="border-bottom: 1px solid #eee">
                            <h4 class="flex-grow-1">Secondary Sales</h4>
                            <a id="printSecondarySales" href="javascript: printSection('secondarySales')"
                                style="display: none">
                                <i class="fa fa-print fa-xl"></i>
                            </a>
                        </div>
                        <div style="max-height: 600px; overflow-y:auto;">
                            <x-dashboard.turn-over-sales />
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row" style="margin-bottom: 15px">
                    <div class="col-sm-6">
                        <h4>Pending Demands</h4>
                        <div style="max-height: 450px; overflow-y:auto">
                            <x-dashboard.pending-returns />
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <h4>Target Discounts</h4>
                        <div style="max-height: 450px; overflow-y:auto">
                            <x-dashboard.pending-discounts />
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-6">
                        <h4>Purchases vs Sales</h4>
                        <x-dashboard.purchases-vs-sales />
                    </div>
                    <div class="col-sm-6">
                        <h4>Top Stock Value</h4>
                        <x-dashboard.top-stock-value />
                    </div>
                </div>
                <hr>
                <div class="box-header with-border">
                    <h4 class="box-title">Delivery Schedule</h4>
                </div>
                <x-dashboard.delivery-schedule />
            </div>
        </div>
    </section>
@endsection
@push('styles')
    <style>
        .row-sticky {
            position: sticky;
            top: 0;
            background-color: white;
            z-index: 1;
            box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
        }
    </style>
@endpush
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        function printSection(sectionId) {
            // Get the content of the section
            var sectionContent = document.getElementById(sectionId).innerHTML;

            // Create a new window
            var newWindow = window.open('', '', 'height=600,width=800');

            // Write the content and include the styles from the main document
            newWindow.document.write('<html><head><title></title>');

            // Copy external stylesheets (if any)
            var stylesheets = document.querySelectorAll('link[rel="stylesheet"], style');
            stylesheets.forEach(function(stylesheet) {
                newWindow.document.write(stylesheet.outerHTML);
            });

            // Close the head and open the body
            newWindow.document.write('</head><body>');

            // Insert the content of the section
            newWindow.document.write(sectionContent);

            // Close the body and the document
            newWindow.document.write('</body></html>');

            // Close the document to apply the styles
            newWindow.document.close();

            // Wait for the window to load the styles, then print and close
            newWindow.onload = function() {
                newWindow.print();
                newWindow.close();
            };
        }
    </script>
@endpush
