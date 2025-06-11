@extends('layouts.admin.admin')
@section('content')
    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border no-padding-h-b">
                @include('message')
                <div class="col-md-12 no-padding-h">
                    <h4>
                        <i class="fa fa-filter" aria-hidden="true"></i> Filter
                        <hr>
                    </h4>
                    <form action="{{ route('admin.account-inquiry.search') }}" method="GET">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Account</label>
                                    {{-- <select class="form-control" name="account" id="paymentAccount">
                                    </select> --}}
                                    <select class="form-control" name="account" id="paymentAccount">
                                        @if ($accountdata)
                                            <option value="{{ $accountdata->id }}" selected>{{ $accountdata->account_name }}
                                                ( {{ $accountdata->account_code }} )</option>
                                        @endif
                                    </select>

                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Branch</label>
                                    <select class="form-control branches" name="branch" id="branches">
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">From</label>
                                    <input type="date" class="form-control" name="start-date" id="payment_date">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">To</label>
                                    <input type="date" class="form-control" name="end-date" id="payment_date">
                                </div>
                            </div>
                            <div class="col-md-12 text-center">
                                <button type="submit" class="btn btn-primary" value="filter" name="manage">Filter</button>
                                <button type="submit" class="btn btn-primary" value="export" name="manage">Export</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('uniquepagestyle')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <style>
        .select2.select2-container.select2-container--default {
            width: 100% !important;
        }
    </style>
@endsection
@section('uniquepagescript')
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>

    <script>
        var paymentAccount = function() {
            $("#paymentAccount").select2({
                placeholder: 'Select Account',
                ajax: {
                    url: '{{ route('expense.category_list') }}',
                    dataType: 'json',
                    type: "GET",
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function(data) {
                        var res = data.map(function(item) {
                            return {
                                id: item.id,
                                text: item.text
                            };
                        });
                        return {
                            results: res
                        };
                    }
                },
            });
        }
        paymentAccount();
        var branches = function() {
            $(".branches").select2({
                placeholder: 'Select Branch',
                ajax: {
                    url: '{{ route('expense.branches') }}',
                    dataType: 'json',
                    type: "GET",
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function(data) {
                        var res = data.map(function(item) {
                            return {
                                id: item.id,
                                text: item.text
                            };
                        });
                        return {
                            results: res
                        };
                    }
                },
            });
        };
        branches();
        $(document).ready(function() {
            paymentAccount()
            branches()
        })
    </script>
@endsection
