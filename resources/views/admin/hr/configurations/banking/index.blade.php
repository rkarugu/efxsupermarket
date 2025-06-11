@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"> HR and Payroll Configurations - Banking </h3>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

            </div>
        </div>

        <div class="box box-primary">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#bank" data-toggle="tab">Bank</a></li>
                <li><a href="#bank-branch" data-toggle="tab">Bank Branch</a></li>
                <li><a href="#payment-modes" data-toggle="tab">Payment Modes</a></li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane active" id="bank">
                    @include('admin.hr.configurations.banking.partials.bank')
                </div>
                <div class="tab-pane" id="bank-branch">
                    @include('admin.hr.configurations.banking.partials.bank-branch')
                </div>
                <div class="tab-pane" id="payment-modes">
                    @include('admin.hr.configurations.banking.partials.payment-modes')
                </div>
            </div>
        </div>
    </section>
@endsection

@section('uniquepagestyle')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet"/>
@endsection

@section('uniquepagescript')
    <script src="{{ asset('js/sweetalert.js') }}"></script>
    <script src="{{ asset('js/form.js') }}"></script>
    <script src="{{ asset('js/utils.js') }}"></script>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>

    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>

    <script type="importmap">
        {
            "imports": {
                "vue": "{{ asset('js/vue.esm-browser.min.js') }}"
            }
        }
    </script>
@endsection
