@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header">
                <div class="box-header-flex">
                    <div class="d-flex flex-column">
                        <h3 class="box-title"> Portal Supplier Information </h3>
                    </div>

                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>
                <h4><b>Business Details</b></h4>
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <label for="">
                                Business Name
                            </label>
                            <p class="form-control">{{@$portal_supplier['business_name'] ?? $supplier->name}}</p>
                        </div>
                        <div class="col-sm-12">
                            <label for="">
                                Business Registration Number
                            </label>
                            <p class="form-control">{{@$portal_supplier['business_reg_no']}}</p>
                        </div>

                        <div class="col-sm-6">
                            <label for="">
                                Company Phone Number
                            </label>
                            <p class="form-control">{{@$portal_supplier['phone_number']}}</p>
                        </div>

                        <div class="col-sm-6">
                            <label for="">
                                Company Email Address
                            </label>
                            <p class="form-control">{{@$portal_supplier['email_address'] ?? $supplier->email}}</p>
                        </div>

                        <div class="col-sm-12">
                            <label for="">
                                Physical Location
                            </label>
                            <p class="form-control">{{@$portal_supplier['physical_location']}}</p>
                        </div>

                        <div class="col-sm-4">
                            <label for="" style="display: block">
                                Registration Certificate
                            </label>
                            <a href="{{env('SUPPLIER_PORTAL_URI')}}/{{@$portal['registration_certificate']}}" target="_blank" class="btn btn-primary">Download</a>
                        </div>
                        <div class="col-sm-4">
                            <label for="" style="display: block">
                                Trade License
                            </label>
                            <a href="{{env('SUPPLIER_PORTAL_URI')}}/{{@$portal['trade_license']}}" target="_blank" class="btn btn-primary">Download</a>
                        </div>
                        <div class="col-sm-4">
                            <label for="" style="display: block">
                                KRA PIN Certificate
                            </label>
                            <a href="{{env('SUPPLIER_PORTAL_URI')}}/{{@$portal['kra_pin_certificate']}}" target="_blank" class="btn btn-primary">Download</a>
                        </div>

                    </div>
                </div>
                <hr>
                
               
                <h4><b>Contact Person Details (System Administrator)</b></h4>
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <label for="">
                                First Name
                            </label>
                            <p class="form-control">{{@$portal_supplier['contact_person_first_name']}}</p>
                        </div>

                        <div class="col-sm-6">
                            <label for="">
                                Last Name
                            </label>
                            <p class="form-control">{{@$portal_supplier['contact_person_last_name']}}</p>
                        </div>
                        <div class="col-sm-6">
                            <label for="">
                                Phone Number
                            </label>
                            <p class="form-control">{{@$portal_supplier['contact_person_phone_number']}}</p>
                        </div>

                        <div class="col-sm-6">
                            <label for="">
                                Email Address
                            </label>
                            <p class="form-control">{{@$portal_supplier['contact_person_email']}}</p>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>

       
    </section>
@endsection
