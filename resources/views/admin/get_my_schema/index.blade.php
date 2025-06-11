@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> My Schema </h3>
                    
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>
                <form id="fetchForm" action="">
                    @csrf
                    <div class="row">
                        <div class="form-group col-sm-3">
                            <label for="table_name" class="control-label"> Table Name. </label>
                            <input type="text" class="form-control" name="table_name" id="table_name" required>
                        </div>
    
                        <div class="col-sm-3">
                            <div class="text-left">
                                <input type="checkbox" name="date_filter" id="date_filter" value="1"> Date Filter
                            </div> 
                            <div id="date_filter_body">
                                <div class="form-group mt-2 mb-4">
                                    <label class="">Date Column. <small><i>(Default Created_at)</i></small></label>
                                    <input type="text" name="date_column" id="date_column" value="created_at" class="form-control">
                                </div>
                                <input type="hidden" id="startDate" name="from">
                                <input type="hidden" id="endDate" name="to">
                                <label class="">Date.</label>
                                <div class="reportRange">
                                    <i class="fa fa-calendar" style="padding:8px"></i>
                                    <span class="flex-grow-1" style="padding:8px">Select Dates</span>
                                    <i class="fa fa-caret-down" style="padding:8px"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="text-left">
                                <input type="checkbox" name="branch_filter" id="branch_filter" value="1"> Branch Filter
                            </div> 
                            <div id="branch_filter_body">
                                <div class="form-group mt-2 mb-4">
                                    <label class="">Branch Column. <small><i>(Default restaurant_id)</i></small></label>
                                    <input type="text" name="branch_column" id="branch_column" value="restaurant_id" class="form-control">
                                </div>
                                <label for="branch">Branch</label>
                                <select class="form-control select2" name="branch" id="branch">
                                    <option value="all">Choose Branch</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{$branch->id}}">{{$branch->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>                       
                        <div class="form-group col-sm-3">
                            <label style="display: block;">&nbsp;</label>
                            <button type="submit" name="intent" class="fetch btn btn-primary">Fetch</button>
                        </div>
                    </div>
                </form>
                <hr>
            </div>
        </div>
    </section>
    <span class="btn-loader" style="display:none;">
        <img src="<?= asset('/assets/admin/images/loader.gif') ?>" alt="Loader"/>
    </span>
@endsection

@section('uniquepagestyle')
    <link rel="stylesheet" href="{{ asset('assets/admin/dist/datepicker.css') }}">
    <link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" rel="stylesheet" />
    <style>
        .reportRange {
            display: flex;
            align-content: center;
            justify-content: stretch;
            border: 1px solid #eee;
            cursor: pointer;
            height: 35px;
        }
    </style>
@endsection

@section('uniquepagescript')
    <script src="{{ asset('assets/admin/dist/bootstrap-datepicker.js') }}"></script>
    <script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="{{asset('js/sweetalert.js')}}"></script>
    <script src="{{asset('js/form.js')}}"></script>

    <script>
        $('body').addClass('sidebar-collapse');
        $('.select2').select2();
        $('#date_filter_body').hide();
        $('#branch_filter_body').hide();
        let start = moment().startOf('month');
        let end = moment().endOf('month');

        // $("#startDate").val(start.format('YYYY-MM-DD'));
        // $("#endDate").val(end.format('YYYY-MM-DD'));

        $('.reportRange').daterangepicker({
            startDate: start,
            endDate: end,
            alwaysShowCalendars: true,
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(7, 'days'), moment()],
                'Last 30 Days': [moment().subtract(30, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                    'month').endOf('month')]
            }
        });
        
        $('.reportRange').on('apply.daterangepicker', function(ev, picker) {
            $("#startDate").val(picker.startDate.format('YYYY-MM-DD'));
            $("#endDate").val(picker.endDate.format('YYYY-MM-DD'));

            $('.reportRange span').html(picker.startDate.format('MMM D, YYYY') + ' - ' + picker.endDate
                .format('MMM D, YYYY'));
        });

        let fetchedItems = [];

        
        $('#fetchForm').on('submit', function(e) {
            e.preventDefault();
            let isValid = true;

            // Reset all error messages
            $('.error-message').hide().text('');
            
            $(this).find('input[required]').each(function() {
                if ($(this).val().trim() === '') {
                    $(this).css('border-color', 'red');
                    $(this).next('.error-message').text('This field is required.').show();
                    isValid = false;
                } else {
                    $(this).css('border-color', '#d2d6de');
                }
            });

            // Loop through each required select field (handling select2)
            $(this).find('select[required]').each(function() {console.log($(this).val());
                if ($(this).val().trim() === '') {
                    $(this).next('.select2-container').find('.select2-selection').css('border-color', 'red');
                    $(this).nextAll('.error-message').text('This field is required.').show();
                    isValid = false;
                } else {
                    $(this).next('.select2-container').find('.select2-selection').css('border-color', '#d2d6de');
                }
            });
console.log(isValid);
            if (!isValid) {
                e.preventDefault();
                return;
            }

            $('.btn-loader').show();
            var table_name = $('#table_name').val();console.log(table_name);
            var branch = $('#branch').val();
            var dateFrom = $('#startDate').val();
            var dateTo = $('#endDate').val();
            
            // var postData = new FormData($(this).parents('form')[0]);
            var postData = $('#fetchForm').serialize();
            var actionUrl = "{{ route('get-my-schema.fetch_data') }}"; 

            actionUrl += '?' + postData;
            $.ajax({
                url:actionUrl,
                data:postData,
                contentType: false,
                cache: false,
                processData: false,
                method:'GET',
                success: function(data, textStatus, xhr) {
                    actionUrl += '&excel=1';
                    window.location.href = actionUrl;
                    $('.btn-loader').hide();
                },
                
                error:function(err)
                {
                    let errorMessage = '';
                    if (err?.responseJSON?.errors) {
                        for (let key in err.responseJSON.errors) {
                            if (err.responseJSON.errors.hasOwnProperty(key)) {
                                errorMessage += err.responseJSON.errors[key].join('<br>') + '<br>';
                            }
                        }
                    } else if (err?.responseJSON?.error) {
                        errorMessage = err.responseJSON.error
                    }else{
                        errorMessage = 'Something went wrong.'
                    }
                    $('.btn-loader').hide();
                    form.errorMessage(errorMessage);
                },
            });
        });

        $('#date_filter').change(function()
        {
            if ($(this).is(':checked')) {
                $('#date_filter_body').show();
                $('#date_column').prop("required", true);
            } else {
                $('#date_filter_body').hide();
                $('#date_column').prop("required", false);
            }
        });

        $('#branch_filter').change(function()
        {
            if ($(this).is(':checked')) {
                $('#branch_filter_body').show();
                $('#branch_column').prop("required", true);
                $('#branch').prop("required", true);
            } else {
                $('#branch_filter_body').hide();
                $('#branch_column').prop("required", false);
                $('#branch').prop("required", false);
            }
        });

    </script>
@endsection