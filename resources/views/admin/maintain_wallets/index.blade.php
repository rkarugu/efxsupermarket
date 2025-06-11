@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title"> Wallets </h3>
                    <form action="">
                        <input type="submit" name="download" value="Download" class="btn  btn-primary">
                    </form>
                </div>
            </div>

            <div class="box-body">
                {{-- <div class="row">
                    <form method="post" action="{!! route('maintain-wallet.index')!!}">
                        {{csrf_field()}}
                        <div class="col-sm-4 ml-1">
                            {!! Form::select('role_id', getRoles(),null, ['maxlength'=>'255','placeholder' => 'Select Role', 'required'=>false, 'class'=>'form-control mlselec6t']) !!}
                        </div>
                        <div class="col-sm-4">
                            {!! Form::select('restaurant_id', getBranchListWithId(),null, ['maxlength'=>'255','placeholder' => 'Select Branch', 'required'=>false, 'class'=>'form-control mlselec6t']) !!}
                        </div>
                        
                        <div class="col-sm-2">
                            <button type="submit" class="btn btn-success">Filter by roles and branches</button>
                        </div>
                    </form>

                </div> --}}
                <div class="session-message-container">
                    @include('message')
                </div>

                <div class="col-md-12">
                    <table class="table table-bordered table-hover" id="create_datatable">
                        <thead>
                        <tr>
                            <th style="width: 3%;">#</th>
                            <th>Name</th>
                            <th>Phone Number</th>
                            <th>Role</th>
                            <th>Branch</th>
                            <th>Balance</th>
                            <th class="noneedtoshort">Action</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td style="width: 3%;">{!! $loop->index + 1 !!}</td>
                                <td>{!! ucfirst($user->name) !!}</td>
                                <td>{!! $user->phone_number !!}</td>
                                <td>{!! @$user->role !!}</td>
                                <td>{!! ucfirst($user->branch) !!}</td>
                                <td style="text-align:right;">{{ manageAmountFormat($user->balance)}}</td>
                                <td>
                                    <a href="{{route('maintain-wallet.viewWalletTransactions', $user->id)}}" style='font-size: 16px;' title="View Transactions"><i class="fa fa-list"></i></a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>

                        <tfoot>
                        <tr>
                            <th colspan="5" style="text-align: center;">TOTAL</th>
                            <th colspan="1"  style="text-align: right;">{{ manageAmountFormat($users->sum('balance')) }}</th>
                            <th></th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="assign-supplier">

        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{route('admin.users.assign_user_suppliers')}}" method="POST" class="addExpense">
                    @csrf
                    <div class="modal-header">
                        <h4 class="modal-title">Employee: <span id="user_name"></span></h4>
                    </div>
                    <div class="modal-body" style="overflow:auto; height:550px">
                        <div class="row">
                            <div class="col-sm-6">
                                <ul class="list-group" id="list-suppliers">

                                </ul>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Suppliers to be assigned</label>
                                    <select class="form-control select_supplier mlselec6t" placeholder="Input field">
                                        <option value="" selected disabled>Select Supplier</option>
                                        <option value="Select All">Select All</option>
                                        @foreach(getSuppliers() as $key => $supplier)
                                            <option value="{{$key}}">{{$supplier}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="button" class="btn btn-default" onclick="unassginSuppliers()">Remove All Assigned Suppliers</button>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Assign Suppliers</button>
                    </div>
                </form>
            </div>

        </div>

    </div>
    <div class="modal " id="assign-table" role="dialog" tabindex="-1" aria-hidden="true" role="dialog"
         aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">

            </div>
        </div>
    </div>


    <div class="modal " id="assign-authorization-level" role="dialog" tabindex="-1" aria-hidden="true"
         role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">

            </div>
        </div>
    </div>

    <div class="modal " id="assign-extarnal-authorization-level" role="dialog" tabindex="-1" aria-hidden="true"
         role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">

            </div>
        </div>
    </div>

    <div class="modal " id="assign-purchase-authorization-level" role="dialog" tabindex="-1" aria-hidden="true"
         role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">

            </div>
        </div>
    </div>
    <div class="modal " id="assign-branches" role="dialog" tabindex="-1" aria-hidden="true" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">

            </div>
        </div>
    </div>






    <script type="text/javascript">
        function unassginSuppliers() {
            $('#list-suppliers').html('');
            $('.select_supplier option').attr('disabled', false);
            return false;
        }

        function openAssignSupplier(userId, name) {
            $('.loder').show();
            $('#assign-supplier form').append("<input type='hidden' name='user_id' value='" + userId + "'>");
            $('#user_name').html(name);
            $('.select_supplier option').attr('disabled', false);
            $('#list-suppliers').html("");
            $.ajax({
                url: '{{route("admin.users.get_user_suppliers")}}',
                type: 'GET',
                dataType: 'json',
                data: {
                    id: userId
                },
                success: function (data) {
                    for (let i in data.data) {
                        $('#list-suppliers').append('<li class="list-group-item">' +
                            '<input type="checkbox" checked name="supplier[]" value="' + data.data[i].wa_supplier_id + '"> ' + data.data[i].supplier.name +
                            '</li>');
                        $('.select_supplier option[value="' + data.data[i].wa_supplier_id + '"]').attr('disabled', true);
                    }
                    $('#assign-supplier').modal('show');
                    $('.loder').hide();
                },
                error: function (xhr, status, error) {
                    // Handle error
                    alert(status + error);
                    console.error('AJAX Error:', status, error);
                }
            });
        }

        function release_all_waiter() {
            var isconfirmed = confirm("Do you want to clear all assigned table?");
            if (isconfirmed) {
                window.location.href = '{{ route('clear.all.tables.from.waiters') }}';
            }
        }

        function assignBranches(link) {
            $('#assign-branches').find(".modal-content").load(link);
        }

        function managetableassignment(link) {
            $('#assign-table').find(".modal-content").load(link);
        }

        function manageauuthorizationlevel(link) {
            $('#assign-authorization-level').find(".modal-content").load(link);
        }

        function manageexternalauuthorizationlevel(link) {
            //alert();
            $('#assign-extarnal-authorization-level').find(".modal-content").load(link);
        }

        function managepurchaseauuthorizationlevel(link) {
            //alert();
            $('#assign-purchase-authorization-level').find(".modal-content").load(link);
        }
    </script>
@endsection

@section('uniquepagescript')
    <link rel="stylesheet" href="{{asset('css/multistep-form.css')}}">
    <link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet"/>

    <style type="text/css">
        .select2 {
            width: 100% !important;
        }

        #note {
            height: 80px !important;
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
    <div id="loader-on"
         style="
position: absolute;
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

    <script src="{{asset('js/sweetalert.js')}}"></script>
    <script src="{{ asset('js/form.js') }}"></script>
    <script src="{{asset('js/multistep-form.js')}}"></script>
    <script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>

    <script type="text/javascript">
        $('.select_supplier').change(function (e) {
            $('.mlselec6t').select2("destroy");
            if ($(this).val() == "Select All") {
                $('#list-suppliers').html('');
                $('.select_supplier option').each(function (i, v) {
                    if ($(v).val() != "Select All" && $(v).val() != "") {
                        $('#list-suppliers').append('<li class="list-group-item">' +
                            '<input type="checkbox" name="supplier[]" checked value="' + $(v).val() + '"> ' + $('.select_supplier option[value="' + $(v).val() + '"]').html() +
                            '</li>');
                        $('.select_supplier option[value="' + $(v).val() + '"]').attr('disabled', true);
                    }
                });
            } else {
                $('#list-suppliers').append('<li class="list-group-item">' +
                    '<input type="checkbox" name="supplier[]" checked value="' + $(this).val() + '"> ' + $('.select_supplier option[value="' + $(this).val() + '"]').html() +
                    '</li>');
                $('.select_supplier option[value="' + $(this).val() + '"]').attr('disabled', true);

            }
            $('.select_supplier option').attr('selected', false);
            $('.select_supplier option[value=""]').attr('selected', true);
            $('.mlselec6t').select2();
        });
        var form = new Form();
        $('.invoice_r_permission').on('click', function () {

            var user_id = $(this).data('user-id');
            var is_checked = 0;
            if ($(this).prop('checked') == true) {
                is_checked = 1;
            } else {
                is_checked = 0;
            }

            var csrf_token = "{{ csrf_token() }}";

            if (confirm('Are you confirm for change permission?')) {

                $.ajax({
                    url: "{{ route('users.permissions.invoice_r') }}",
                    data: {
                        user_id: user_id,
                        is_checked: is_checked,
                        _token: csrf_token
                    },
                    method: 'POST',
                    success: function (r) {
                        if (r.result == 1) {
                            form.successMessage(r.message);
                        } else if (r.result == -1) {
                            form.errorMessage(r.message);
                        }
                    }
                });
            } else {
                return false;
            }


        });

        $(document).on('submit', '.addExpense', function (e) {
            e.preventDefault();
            $('.loder').show();
            var postData = new FormData($(this)[0]);
            var url = $(this).attr('action');
            postData.append('_token', $(document).find('input[name="_token"]').val());
            $.ajax({
                url: url,
                data: postData,
                contentType: false,
                cache: false,
                processData: false,
                method: 'POST',
                success: function (out) {
                    $(".remove_error").remove();
                    $('.loder').hide();

                    if (out.result == 0) {
                        for (let i in out.errors) {
                            var id = i.split(".");
                            if (id && id[1]) {
                                $("[name='" + id[0] + "[" + id[1] + "]']").parent().append('<label class="error d-block remove_error w-100" id="' + i + '_error">' + out.errors[i][0] + '</label>');
                            } else {
                                $("[name='" + i + "']").parent().append('<label class="error d-block remove_error w-100" id="' + i + '_error">' + out.errors[i][0] + '</label>');
                                $("." + i).parent().append('<label class="error d-block remove_error w-100" id="' + i + '_error">' + out.errors[i][0] + '</label>');
                            }
                        }
                    }
                    if (out.result === 1) {
                        form.successMessage(out.message);
                        $('#modelId-assignmenu').modal('hide');
                        $('#assign-branches').modal('hide');
                        $('#assign-supplier').modal('hide');
                    }
                    if (out.result === -1) {
                        form.errorMessage(out.message);
                    }
                },

                error: function (err) {
                    $('.loder').hide();
                    $(".remove_error").remove();
                    form.errorMessage('Something went wrong');
                }
            });
        });
    </script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('.mlselec6t').select2();
        });

    </script>
@endsection
