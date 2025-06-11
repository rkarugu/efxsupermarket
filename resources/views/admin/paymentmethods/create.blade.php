@extends('layouts.admin.admin')
@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> {!! $title !!} </h3>
                    <a href="{{route("$model.index")}}" class="btn btn-primary"> Back</a>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <form class="validate form-horizontal" role="form" method="POST" action="{{ route($model.'.store') }}" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="box-body">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-2 control-label">Title</label>
                            <div class="col-sm-10">
                                {!! Form::text('title', null, ['maxlength'=>'255','placeholder' => 'Title', 'required'=>true, 'class'=>'form-control']) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-2 control-label">Payment Provider</label>
                            <div class="col-sm-10">
                                <select name="payment_provider_id" id="payment_provider_id" class="form-control mlselect">
                                    <option value="" selected disabled> Select a provider</option>
                                    @foreach($providers as $provider)
                                        <option value="{{ $provider->id }}">{{ $provider->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-2 control-label">Payment Provider</label>
                            <div class="col-sm-10">
                                <select name="branch_id" id="branch_id" class="form-control mlselect">
                                    <option value="" selected disabled> Select a provider</option>
                                    @foreach($branches as $id => $branch)

                                        <option value="{{ $id }}">{{ $branch }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-2 control-label">Use For Payments</label>
                            <div class="col-sm-10">
                                <select name="use_for_payments" id="use_for_payments" class="form-control mlselect">
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-2 control-label">Use For Receipts</label>
                            <div class="col-sm-10">
                                <select name="use_for_receipts" id="use_for_receipts" class="form-control mlselect">
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="use_in_pos" class="col-sm-2 control-label">Use For POS</label>
                            <div class="col-sm-10">
                                <select name="use_in_pos" id="use_in_pos" class="form-control mlselect">
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="is_mpesa" class="col-sm-2 control-label">Is MPESA</label>
                            <div class="col-sm-10">
                                <select name="is_mpesa" id="is_mpesa" class="form-control mlselect">
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="is_cash" class="col-sm-2 control-label">Is Cash</label>
                            <div class="col-sm-10">
                                <select name="is_cash" id="is_cash" class="form-control mlselect">
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="use_as_channel" class="col-sm-2 control-label">Use As Channel</label>
                            <div class="col-sm-10">
                                <select name="use_as_channel" id="use_as_channel" class="form-control mlselect">
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-2 control-label">GL Account</label>
                            <div class="col-sm-10">
                                {!! Form::select('gl_account_id', getChartOfAccountsDropdown(),null, ['maxlength'=>'255', 'required'=>true, 'class'=>'form-control mlselect','placeholder'=>'Please select']) !!}
                            </div>
                        </div>
                    </div>


                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection

@section('uniquepagestyle')
    <link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet"/>
@endsection

@section('uniquepagescript')
    <script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
    <script type="text/javascript">
        $(function () {

            $(".mlselect").select2();
        });
    </script>

@endsection

