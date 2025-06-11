@extends('layouts.admin.admin')
@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"> {!! $title !!} </h3>
            </div>
            @include('message')
            {!! Form::model($row, [
                'method' => 'PATCH',
                'route' => [$model . '.update', $row->slug],
                'class' => 'validate',
                'enctype' => 'multipart/form-data',
            ]) !!}
            {{ csrf_field() }}
            <?php
            $attr = 2;
            $column = 10;
            ?>

            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-{{ $attr }} control-label">Name (to appear on
                        reports)</label>
                    <div class="col-sm-{{ $column }}">
                        {!! Form::text('name', null, [
                            'maxlength' => '255',
                            'placeholder' => 'Name',
                            'required' => true,
                            'class' => 'form-control',
                        ]) !!}
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-{{ $attr }} control-label">Official Company
                        Number</label>
                    <div class="col-sm-{{ $column }}">
                        {!! Form::text('official_company_number', null, [
                            'maxlength' => '255',
                            'placeholder' => 'Company Number',
                            'required' => true,
                            'class' => 'form-control',
                        ]) !!}
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-{{ $attr }} control-label">Tax Authority
                        Reference</label>
                    <div class="col-sm-{{ $column }}">
                        {!! Form::text('tax_authority_reference', null, [
                            'maxlength' => '255',
                            'placeholder' => 'Tax Authority Reference',
                            'required' => false,
                            'class' => 'form-control',
                        ]) !!}
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-{{ $attr }} control-label">Company Address</label>
                    <div class="col-sm-{{ $column }}">
                        {!! Form::text('address', null, [
                            'maxlength' => '255',
                            'placeholder' => 'Address',
                            'required' => true,
                            'class' => 'form-control',
                            'id' => 'search_location',
                        ]) !!}
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-{{ $attr }} control-label">Telephone Number</label>
                    <div class="col-sm-{{ $column }}">
                        {!! Form::text('telephone_number', null, [
                            'maxlength' => '255',
                            'placeholder' => 'Telephone Number',
                            'required' => true,
                            'class' => 'form-control',
                        ]) !!}
                    </div>
                </div>
            </div>


            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-{{ $attr }} control-label">Facsimile Number</label>
                    <div class="col-sm-{{ $column }}">
                        {!! Form::text('facsimile_number', null, [
                            'maxlength' => '255',
                            'placeholder' => 'Facsimile Number',
                            'required' => false,
                            'class' => 'form-control',
                        ]) !!}
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-{{ $attr }} control-label">Email Address</label>
                    <div class="col-sm-{{ $column }}">
                        {!! Form::email('email_address', null, [
                            'maxlength' => '255',
                            'placeholder' => 'Email',
                            'required' => true,
                            'class' => 'form-control',
                        ]) !!}
                    </div>
                </div>
            </div>


            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-{{ $attr }} control-label">Home Currency</label>
                    <div class="col-sm-{{ $column }}">
                        {!! Form::select('home_currency', getCompanyPreferencesCurrency(), null, [
                            'maxlength' => '255',
                            'required' => true,
                            'class' => 'form-control mlselect',
                            'placeholder' => 'Please select',
                        ]) !!}
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-{{ $attr }} control-label">Debtors Control GL
                        Account</label>
                    <div class="col-sm-{{ $column }}">
                        {!! Form::select('debtors_control_gl_account', getChartOfAccountsDropdown(), null, [
                            'maxlength' => '255',
                            'required' => true,
                            'class' => 'form-control mlselect',
                            'placeholder' => 'Please select',
                        ]) !!}
                    </div>
                </div>
            </div>



            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-{{ $attr }} control-label">Creditors Control GL
                        Account</label>
                    <div class="col-sm-{{ $column }}">
                        {!! Form::select('creditors_control_gl_account', getChartOfAccountsDropdown(), null, [
                            'maxlength' => '255',
                            'required' => true,
                            'class' => 'form-control mlselect',
                            'placeholder' => 'Please select',
                        ]) !!}
                    </div>
                </div>
            </div>


            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-{{ $attr }} control-label">Payroll Net Pay Clearing GL
                        Account</label>
                    <div class="col-sm-{{ $column }}">
                        {!! Form::select('payroll_net_pay_clearing_gl_account', getChartOfAccountsDropdown(), null, [
                            'maxlength' => '255',
                            'required' => true,
                            'class' => 'form-control mlselect',
                            'placeholder' => 'Please select',
                        ]) !!}
                    </div>
                </div>
            </div>


            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-{{ $attr }} control-label">Goods Received Clearing GL
                        Account</label>
                    <div class="col-sm-{{ $column }}">
                        {!! Form::select('goods_received_clearing_gl_account', getChartOfAccountsDropdown(), null, [
                            'maxlength' => '255',
                            'required' => true,
                            'class' => 'form-control mlselect',
                            'placeholder' => 'Please select',
                        ]) !!}
                    </div>
                </div>
            </div>


            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-{{ $attr }} control-label">Retained Earning Clearing GL
                        Account</label>
                    <div class="col-sm-{{ $column }}">
                        {!! Form::select('retained_earning_clearing_gl_account', getChartOfAccountsDropdown(), null, [
                            'maxlength' => '255',
                            'required' => true,
                            'class' => 'form-control mlselect',
                            'placeholder' => 'Please select',
                        ]) !!}
                    </div>
                </div>
            </div>


            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-{{ $attr }} control-label">Freight Re-charged GL
                        Account</label>
                    <div class="col-sm-{{ $column }}">
                        {!! Form::select('freight_recharged_gl_account', getChartOfAccountsDropdown(), null, [
                            'maxlength' => '255',
                            'required' => true,
                            'class' => 'form-control mlselect',
                            'placeholder' => 'Please select',
                        ]) !!}
                    </div>
                </div>
            </div>


            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-{{ $attr }} control-label">Sales Exchange Variances GL
                        Account</label>
                    <div class="col-sm-{{ $column }}">
                        {!! Form::select('sales_exchange_variances_gl_account', getChartOfAccountsDropdown(), null, [
                            'maxlength' => '255',
                            'required' => true,
                            'class' => 'form-control mlselect',
                            'placeholder' => 'Please select',
                        ]) !!}
                    </div>
                </div>
            </div>


            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-{{ $attr }} control-label">Purchases Exchange Variances
                        GL Account</label>
                    <div class="col-sm-{{ $column }}">
                        {!! Form::select('purchases_exchange_variances_gl_account', getChartOfAccountsDropdown(), null, [
                            'maxlength' => '255',
                            'required' => true,
                            'class' => 'form-control mlselect',
                            'placeholder' => 'Please select',
                        ]) !!}
                    </div>
                </div>
            </div>


            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-{{ $attr }} control-label">Payment Discount GL
                        Account</label>
                    <div class="col-sm-{{ $column }}">
                        {!! Form::select('payment_discount_gl_account', getChartOfAccountsDropdown(), null, [
                            'maxlength' => '255',
                            'required' => true,
                            'class' => 'form-control mlselect',
                            'placeholder' => 'Please select',
                        ]) !!}
                    </div>
                </div>
            </div>


            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-{{ $attr }} control-label">Cash Sales Control GL
                        Account</label>
                    <div class="col-sm-{{ $column }}">
                        {!! Form::select('cash_sales_control_account', getChartOfAccountsDropdown(), null, [
                            'maxlength' => '255',
                            'required' => true,
                            'class' => 'form-control mlselect',
                            'placeholder' => 'Please select',
                        ]) !!}
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-{{ $attr }} control-label">Sales Control GL
                        Account</label>
                    <div class="col-sm-{{ $column }}">
                        {!! Form::select('sales_control_account', getChartOfAccountsDropdown(), null, [
                            'maxlength' => '255',
                            'required' => true,
                            'class' => 'form-control mlselect',
                            'placeholder' => 'Please select',
                        ]) !!}
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-{{ $attr }} control-label">VAT GL Account</label>
                    <div class="col-sm-{{ $column }}">
                        {!! Form::select('vat_control_account', getChartOfAccountsDropdown(), null, [
                            'maxlength' => '255',
                            'required' => true,
                            'class' => 'form-control mlselect',
                            'placeholder' => 'Please select',
                        ]) !!}
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="form-group">
                    <label for="withholding_vat_gl_account" class="col-sm-{{ $attr }} control-label">Withholding VAT GL Account</label>
                    <div class="col-sm-{{ $column }}">
                        {!! Form::select('withholding_vat_gl_account', getChartOfAccountsDropdown(), null, [
                            'maxlength' => '255',
                            'required' => true,
                            'class' => 'form-control mlselect',
                            'placeholder' => 'Please select',
                        ]) !!}
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="form-group">
                    <label for="discount_recieved_gl_account" class="col-sm-{{ $attr }} control-label">Discount Received GL Account</label>
                    <div class="col-sm-{{ $column }}">
                        {!! Form::select('discount_recieved_gl_account', getChartOfAccountsDropdown(), null, [
                            'maxlength' => '255',
                            'required' => true,
                            'class' => 'form-control mlselect',
                            'placeholder' => 'Please select',
                        ]) !!}
                    </div>
                </div>
            </div>            
            <div class="box-footer">
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
            </form>
        </div>
    </section>
@endsection

@section('uniquepagestyle')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
@endsection

@section('uniquepagescript')
    <script src="http://maps.googleapis.com/maps/api/js?libraries=places&key=AIzaSyAcu43Sc8RemuQGR4BUh9ZJiYTWF2EPlVk">
    </script>

    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>



    <script type="text/javascript">
        function initialize() {
            var input = document.getElementById('search_location');
            var options = {};

            var autocomplete = new google.maps.places.Autocomplete(input, options);
            google.maps.event.addListener(autocomplete, 'place_changed', function() {
                var place = autocomplete.getPlace();
                /* var lat = place.geometry.location.lat();
                 var lng = place.geometry.location.lng();
                 $("#latitude").val(lat);
                 $("#longitude").val(lng);*/
            });
        }

        google.maps.event.addDomListener(window, 'load', initialize);
    </script>

    <script type="text/javascript">
        $(function() {

            $(".mlselect").select2();
        });
    </script>
@endsection
