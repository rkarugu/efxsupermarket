@extends('layouts.admin.admin')

@section('content')

    <section class="content">
        <div class="box box-primary">
            <div class="box-header">
                <div class="box-header-flex">
                </div>
            </div>

            <div class="box-body">
                <h4 class="text-center" style="margin-bottom:30px;margin-top:-20px;font-weight:bolder">
                    END OF DAY PROCESS
                </h4>
                <div class=" multistep">
                    <div class="container">
                        <div class="stepwizard">
                            <div class="stepwizard-row setup-panel">
                                <div class="stepwizard-step col-xs-3">
                                    <a href="#step-1" type="button" class="btn btn-success btn-circle step-buttons step-buttons1">1</a>
                                    <p><b>Pending Returns </b></p>
                                </div>
                                <div class="stepwizard-step col-xs-3">
                                    <a href="#step-2" type="button" class="btn btn-default btn-circle step-buttons step-buttons2" disabled="disabled">2</a>
                                    <p><b>Pending Splits</b></p>
                                </div>
                                <div class="stepwizard-step col-xs-3">
                                    <a href="#step-3" type="button" class="btn btn-default btn-circle step-buttons step-buttons3" disabled="disabled">3</a>
                                    <p><b>Sales Vs Stock vs Payments</b></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <form class="validate"  role="form" method="POST" action="{!! route('banking.reconcile.daily.transactions.store') !!}" enctype = "multipart/form-data">
                        @csrf
                        <section class="content setup-content" id="step-1">
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <div class="d-flex justify-content-between">
                                        <div class="justify-content-start">
                                            <p style="font-weight:bolder">
                                               Pending Returns
                                            </p>
                                        </div>
                                        <div class="justify-content-end">
                                            @if($data['returns_count'] == 0)
                                                <span style="color:green;font-weight:bolder">
                                                Passed
                                            </span>
                                            @else
                                                <span  style="color: red;font-weight:bolder">
                                                Failed
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-hover" id="returns">
                                                    <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Pending return Count </th>
                                                        <th>Pending Returns Amount</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr>
                                                        <td>{{ $date  }}</td>
                                                        <td>{{ $data['returns_count'] }}</td>
                                                        <td>{{ $data['returns_count'] }}</td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="box-footer">
                                    <button type="submit" class="btn btn-primary submitMe" name="current_step" value="1"  style="float: right;">Next</button>
                                </div>
                            </div>

                        </section>
                        <section class="content setup-content" id="step-2">
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <div class="d-flex justify-content-between">
                                        <div class="justify-content-start">
                                            <p style="font-weight:bolder">
                                                Pending Split
                                            </p>
                                        </div>
                                        <div class="justify-content-end">
                                            @if($data['splits'] == 0)
                                                <span style="color:green;font-weight:bolder">
                                                Passed
                                            </span>
                                            @else
                                                <span  style="color: red;font-weight:bolder">
                                                Failed
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-hover" id="splits">
                                                    <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Pending splits Count </th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr>
                                                        <td>{{ $date  }}</td>
                                                        <td>{{ $data['splits'] }}</td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="box-footer">
                                    <button type="button" class="btn btn-primary" style="float: left;" onclick="$('.step-buttons1').trigger('click'); return false;">Previous</button>
                                    <button type="submit" class="btn btn-primary submitMe" name="current_step" value="2"  style="float: right;">Next</button>
                                </div>
                            </div>

                        </section>
                        <section class="content setup-content" id="step-3">
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <div class="d-flex justify-content-between">
                                        <div class="justify-content-start">
                                            <p style="font-weight:bolder">
                                                Pending Split
                                            </p>
                                        </div>
                                        <div class="justify-content-end">
                                            @if($data['stockVsPay'])
                                             <span style="color:green;font-weight:bolder">
                                                Passed
                                            </span>
                                            @else
                                            <span  style="color: red;font-weight:bolder">
                                                Failed
                                            </span>
                                            @endif


                                        </div>
                                    </div>
                                </div>
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-hover" id="stockVsPay">
                                                    <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Sales </th>
                                                        <th>Stock </th>
                                                        <th>Payments </th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr>
                                                        <td>{{ $date  }}</td>
                                                        <td>{{ number_format($data['sales'], 2) }}</td>
                                                        <td>{{ number_format($data['stock'], 2)}}</td>
                                                        <td>{{ number_format($data['payments'], 2) }}</td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="box-footer">
                                    <button type="button" class="btn btn-primary" style="float: left;" onclick="$('.step-buttons2').trigger('click'); return false;">Previous</button>
                                    <button type="submit" id="close_day" class="btn btn-primary submitMe" style="float: right;">Close Day</button>
                                </div>
                            </div>

                        </section>

                    </form>
                </div>
            </div>
        </div>

    </section>
@endsection

@section('uniquepagestyle')
    <link rel="stylesheet" href="{{ asset('css/multistep-form.css') }}">
    <div id="loader-on"
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
            "
         class="loder">
        <div class="loader" id="loader-1"></div>
    </div>
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <style>
        a.btn.btn-default.btn-circle.step-button.active {
            background: #ff0000 !important;
            border: none;
            color: white;
            font-weight: bolder;
        }
    </style>
@endsection

@section('uniquepagescript')
    <script src="{{ asset('js/sweetalert.js') }}"></script>
    <script src="{{ asset('js/multistep-form.js') }}"></script>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>

    <script>
        $('body').addClass('sidebar-collapse');

        $(document).ready(function() {
            $('select_branch').select2();
        });

        $('#close_day').click(function(){
            var formData = new FormData();
            let data = {
                'branch_id': @json($branch),
                'data': @json($data),
                'date': @json($date),
            };
            jQuery.ajax({
                url: "close-branch",
                type: 'POST',
                data: data,
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if (response.status == 200) {
                        if (response.cash_receipt_url)
                        {
                            print(response.cash_receipt_url)
                        }
                        window.location.href='{{ url('admin/get-end-of-day-veiw') }}'
                    }

                },
                error: function(error) {
                    // alert('noh')
                    Swal.fire({
                        title: "Stop!",
                        text: error.responseJSON.message,
                        icon: "error"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '{{ url('admin/get-end-of-day-veiw') }}';
                        }
                    });

                }
            });
        });
    </script>


@endsection
