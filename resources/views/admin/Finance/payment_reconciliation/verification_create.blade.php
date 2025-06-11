@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Payment Verification </h3>
                    <a href="{{ route('payment-reconciliation.verification') }}" class="btn btn-primary">Back</a>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>
                
                @if ($steps==1)
                    <div style="margin: 10px 0px;">
                        <b>Unused Statements: {{number_format($banks)}}</b>
                    </div>
                    <form id="fetchPaymentForm" action="{{ route('payment-reconciliation.verification.upload') }}" method="post" enctype="multipart/form-data">
                        {{ @csrf_field() }}
                        <div class="row">
                            <div class="form-group col-sm-2">
                                <label for="equity_makongeni" class="control-label"> Equity Makongeni </label>
                                <input type="file" class="form-control" name="equity_makongeni" id="equity_makongeni">
                                <small class="text-danger" id="equity_makongeni_label" ></small>
                            </div>
                            <div class="form-group col-sm-2">
                                <label for="equity_main" class="control-label"> Equity Main </label>
                                <input type="file" class="form-control" name="equity_main" id="equity_main">
                                <small class="text-danger" id="equity_main_label"></small>
                            </div>
                            <div class="form-group col-sm-2">
                                <label for="vooma" class="control-label"> Vooma </label>
                                <input type="file" class="form-control" name="vooma" id="vooma">
                                <small class="text-danger" id="vooma_label" ></small>
                            </div>
                            <div class="form-group col-sm-2">
                                <label for="kcb_main" class="control-label"> KCB Main </label>
                                <input type="file" class="form-control" name="kcb_main" id="kcb_main">
                                <small class="text-danger" id="kcb_main_label"></small>
                            </div>
                            <div class="form-group col-sm-2">
                                <label for="mpesa" class="control-label"> Mpesa </label>
                                <input type="file" class="form-control" name="mpesa" id="mpesa">
                                <small class="text-danger" id="mpesa_label" ></small>
                            </div>
                            <div class="form-group col-sm-2 d-flex justify-content-between" style="padding-top:20px;">
                                <input type="submit" name="upload" value="Upload" class="btn btn-primary">
                                <input type="submit" name="use_existing" value="Re-use" class="btn btn-primary">
                            </div>
                        </div>
                    </form>
                @endif

                @if ($steps==2)
                    <form id="fetchPaymentForm" action="{{ route('payment-reconciliation.verification.process') }}" method="post" enctype="multipart/form-data">
                        {{ @csrf_field() }}
                        <div class="row">
                            <div class="form-group col-sm-2">
                                <label for="start_date" class="control-label"> Start Date </label>
                                <input type="date" class="form-control" name="start_date" id="start_date" value="{{ old('start_date') }}" required>
                                <small class="text-danger" id="start_date_label" ></small>
                            </div>
                            <div class="form-group col-sm-2">
                                <label for="end_date" class="control-label"> End Date </label>
                                <input type="date" class="form-control" name="end_date" id="end_date" value="{{ old('end_date') }}" required>
                                <small class="text-danger" id="end_date_label"></small>
                            </div>
                            <div class="form-group col-sm-2">
                                <label for="branch" class="control-label"> Branch </label>
                                <select name="branch" id="branch" class="form-control mtselect" required>
                                    <option value="" selected disabled>--Select Branch--</option>
                                    @foreach(getBranchesDropdown() as $key => $branch)
                                        @if ($key==10)
                                            <option value="{{$key}}" @if (old('branch') == $key) selected @endif>{{$branch}}</option>    
                                        @endif
                                    @endforeach
                                </select>
                                <small class="text-danger" id="branch_label"></small>
                            </div>
                            {{-- <div class="form-group col-sm-2">
                                <label for="channel" class="control-label"> Channel </label>
                                <select name="channel" id="channel" class="form-control mtselect" required>
                                    <option value="" selected disabled>--Select Channel--</option>
                                    @foreach ($channels as $channel)
                                        <option value="{{$channel}}" {{ request()->channel == $channel ? 'selected' : '' }}>{{$channel}}</option>
                                    @endforeach
                                </select>
                                <small class="text-danger" id="channel_label"></small>
                            </div>
                            <div class="form-group col-sm-2">
                                <label for="channel_file_upload" class="control-label"> Bank File </label>
                                <div class="d_flex">
                                    <div class="">
                                        <input type="radio" name="option_file" value="use_prevoius" checked required> Use Previous Upload
                                    </div>
                                    <div class="">
                                        <input type="radio" name="option_file" value="upload_file"> Upload File
                                    </div>
                                </div>
                                <input type="file" class="form-control" name="channel_file_upload" id="channel_file_upload" required accept=".xlsx,.xls" style="display: none;">
                                <small class="text-danger" id="channel_file_upload_label"></small>
                            </div> --}}

                            <div class="form-group col-sm-2">
                                <label style="display: block;">&nbsp;</label>
                                <input type="submit" name="upload" value="Process Verification" class="btn btn-primary">
                            </div>
                        </div>
                    </form>
                @endif
               
            </div>
        </div>
    </section>
    <span class="btn-loader" style="display:none;">
        <img src="<?= asset('/assets/admin/images/loader.gif') ?>" alt="Loader" />
    </span>
@endsection

@section('uniquepagestyle')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet"/>
@endsection

@section('uniquepagescript')
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>

    <script>
        $(document).ready(function(e) {
            $('body').addClass('sidebar-collapse');
            $('.mtselect').select2();
            $('#matching_table').DataTable();
            $('#missing_bank_table').DataTable();
            $('#missing_system_table').DataTable();
            $('#checkAllRecon').on('click', function() {
                console.log('');
                var isChecked = $(this).prop('checked');
                $('#matching_table tbody .matchCheck').prop('checked', isChecked);
                $('#matching_table').DataTable().rows().nodes().to$().find('.matchCheck').prop('checked', isChecked);
            });

            $('#matching_table tbody').on('change', '.matchCheck', function() {
                $('#checkAllRecon').prop('checked', $('.matchCheck:checked').length === $('.matchCheck').length);
            });
            $('#channel_file_upload').change(function(){
                var fileName = $(this).val().split('\\').pop(); 
                $('#channel_file_upload_label').removeClass('text-danger');
                $('#channel_file_upload_label').text(fileName); 
            });
            $('#equity_makongeni').change(function(){
                var fileName = $(this).val().split('\\').pop(); 
                $('#equity_makongeni_label').removeClass('text-danger');
                $('#equity_makongeni_label').text(fileName); 
            });
            $('#equity_main').change(function(){
                var fileName = $(this).val().split('\\').pop(); 
                $('#equity_main_label').removeClass('text-danger');
                $('#equity_main_label').text(fileName); 
            });
            $('#vooma').change(function(){
                var fileName = $(this).val().split('\\').pop(); 
                $('#vooma_label').removeClass('text-danger');
                $('#vooma_label').text(fileName); 
            });
            $('#kcb_main').change(function(){
                var fileName = $(this).val().split('\\').pop(); 
                $('#kcb_main_label').removeClass('text-danger');
                $('#kcb_main_label').text(fileName); 
            });
            $('#mpesa').change(function(){
                var fileName = $(this).val().split('\\').pop(); 
                $('#mpesa_label').removeClass('text-danger');
                $('#mpesa_label').text(fileName); 
            });
            $('input[name="option_file"]').change(function(){
                if($(this).val() == 'upload_file'){
                    $('#channel_file_upload').show();
                } else{
                    $('#channel_file_upload').hide();
                }
            });

            $('input[name="upload"]').on('click', function(e) {
                e.preventDefault();
                var errors = 0;
                // $('#start_date_label').html('');
                // $('#end_date_label').html('');
                // $('#branch_label').html('');
                // $('#channel_label').html('');
                // $('#channel_file_upload_label').html('');
                // if (!$('#start_date').val()) {
                //     $('#start_date_label').html('Start Date is Required');
                //     errors++;
                // }
                // if (!$('#end_date').val()) {
                //     $('#end_date_label').html('End Date is Required');
                //     errors++;
                // }
                // if (!$('#branch').val()) {
                //     $('#branch_label').html('Branch is Required');
                //     errors++;
                // }
                // if (!$('#channel').val()) {
                //     $('#channel_label').html('Channel is Required');
                //     errors++;
                // }
                // if ($("input[name='option_file']").is(':checked')) {
                //     let optionValue = $("input[name='option_file']:checked").val();
                //     if(optionValue == 'upload_file'){
                //         if (!$('#channel_file_upload').val()) {
                //             $('#channel_file_upload_label').html('Bank File is Required');
                //             errors++;
                //         }
                //     }
                // }
                // else {
                //     $('#channel_file_upload_label').html('Choose One Option');
                //     errors++;
                // }
                
                if (errors==0) {
                    $(this).prop("disabled",true);
                    $('.btn-loader').show();
                    $('#fetchPaymentForm').get(0).submit();
                }
                
            })
        });
    </script>
@endsection