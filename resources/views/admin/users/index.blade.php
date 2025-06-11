@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header">
                    <h3 class="box-title mb-3" style="margin-bottom: 5px"> Employees </h3>
                    <div class="row mt-2">
                        <form method="GET" action="{!! url('admin/employees')!!}">
                            {{csrf_field()}}
                            @if ($authuser->role_id == 1)
                                <div class="col-sm-4 ml-1">
                                    {!! Form::select('role_id', getRoles(),$roleFilter, ['maxlength'=>'255','placeholder' => 'Select Role', 'required'=>false, 'class'=>'form-control mlselec6t']) !!}
                                </div>
                            @endif

                            <div class="col-sm-4">
                                {{-- {!! Form::select('restaurant_id', getBranchListWithId(),null, ['maxlength'=>'255','placeholder' => 'Select Branch', 'required'=>false, 'class'=>'form-control mlselec6t']) !!} --}}
                                {{-- {!! Form::select('restaurant_id', getBranchListWithId(), $restaurantid, ['maxlength' => '255', 'placeholder' => 'Select Branch', 'required' => false, 'class' => 'form-control mlselec6t']) !!} --}}
                                {{-- <?php
                                $filteredRestaurants = [$restaurantid => getBranchListWithId()[$restaurantid]];
                                ?>
                                {!! Form::select('restaurant_id', $filteredRestaurants, $restaurantid, ['maxlength' => '255', 'placeholder' => 'Select Branch', 'required' => false, 'class' => 'form-control mlselec6t']) !!} --}}
                                <?php
                                if ($canviewall) {
                                    $restaurants = getBranchListWithId();
                                } else {
                                    $restaurants = [$restaurantid => getBranchListWithId()[$restaurantid]];
                                }
                                ?>
                                {!! Form::select('restaurant_id', $restaurants, $restaurantid, ['maxlength' => '255', 'placeholder' => 'Select Branch', 'required' => false, 'class' => 'form-control mlselec6t']) !!}

                            </div>

                            <div class="form-group col-md-3">
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <button type="submit" class="btn btn-warning" name="manage-request" value="excel"><i class="fa fa-file-excel"></i></button>

                            </div>
                        </form>

                        @if (isset($permission[$pmodule . '___add']) || $permission == 'superadmin')
                            <a href="{!! route($model . '.create') !!}" class="btn btn-success">Add {!! $title !!}</a>
                        @endif
                    </div>

                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <div class="col-md-12">
                    <table class="table table-bordered table-hover" id="create_datatable">
                        <thead>
                        <tr>
                            <th width="7%">S.No.</th>
                            <th width="20%">Name</th>
                            <th width="10%">Phone Number</th>
                            <th width="10%">Role</th>
                            <th width="15%">Branch</th>
                            <th width="18%">Routes</th>
                            {{--<th width="10%">Date Emp.</th>--}}
                            <th width="20%" class="noneedtoshort">Action</th>
                        </tr>
                        </thead>

                        <tbody>
                        @if (isset($users) && !empty($users))
                                <?php $b = 1; ?>
                            @foreach ($users as $user)
                                <tr>
                                    <td>{!! $b !!}</td>
                                    <td>{!! ucfirst($user->name) !!}</td>
                                    <td>{!! $user->phone_number !!}</td>
                                    <td>{!! @$user->userRole->title !!}</td>
                                    <td>{!! ucfirst(@$user->userRestaurent->name) !!}</td>
                                    <td>{!! @$user->routes !!}</td>
                                    {{--<td>{!! date('d/m/Y', strtotime($user->date_employeed)) !!}</td>--}}
                                    <td class="action_crud">
                                        @if (isset($permission[$pmodule . '___change_password']) || $permission == 'superadmin')
                                            <span>
                                                    <a title="Change Password"
                                                       href="{{ route($model . '.change_password', $user?->slug) }}">
                                                        <i class="fa fa-key"></i>
                                                    </a>
                                                </span>
                                        @endif

                                        @if (isset($permission[$pmodule . '___edit']) || $permission == 'superadmin')
                                            <span>
                                                    <a title="Edit" href="{{ route($model . '.edit', $user->slug) }}">
                                                        <img src="{!! asset('assets/admin/images/edit.png') !!}" alt="">
                                                    </a>
                                                </span>
                                        @endif

                                        @if (
                                            (isset($permission[$pmodule . '___delete']) || $permission == 'superadmin') &&
                                                $user->stock_count == 0 &&
                                                $user->debtor_count == 0 && 1==2)
                                            <span>
                                                    <form title="Trash"
                                                          action="{{ URL::route($model . '.destroy', $user->slug) }}"
                                                          method="POST">
                                                        <input type="hidden" name="_method" value="DELETE">
                                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                        <button style="float:left"><i class="fa fa-trash"
                                                                                      aria-hidden="true"></i></button>
                                                    </form>
                                                </span>
                                        @endif

                                        @if (isset($permission[$pmodule . '___edit']) || $permission == 'superadmin')
                                            <span class="">
                                                    <a title="Change Status"
                                                       href="{{ route($model . '.status', [$user->slug, $user->status]) }}">
                                                        @if ($user->status == '1')
                                                            <img src="{!! asset('assets/admin/images/icon-active.png') !!}" alt="">
                                                        @else
                                                            <img src="{!! asset('assets/admin/images/deactivate.png') !!}" alt="">
                                                        @endif


                                                    </a>
                                                </span>
                                        @endif


                                        @if (isset($permission[$pmodule . '___edit']) || $permission == 'superadmin')
                                            @if ($user->userRole?->slug == 'waiter')
                                                <span>

                                                        <a class="left-padding-small" data-href="{!! route('admin.table.assignment', $user->slug) !!}"
                                                           onclick="managetableassignment('{!! route('admin.table.assignment', $user->slug) !!}')"
                                                           data-toggle="modal" data-target="#assign-table"
                                                           data-dismiss="modal"><i class="fa fa-table "
                                                                                   style="color: #444; cursor: pointer;"
                                                                                   title="Assign Table"></i></a>
                                                    </span>
                                            @endif
                                            <span>
                                                    <a class="left-padding-small" data-href="{!! route('admin.table.assignment', $user->slug) !!}"
                                                       onclick="manageauuthorizationlevel('{!! route('admin.authorization.assignment', $user->slug) !!}')"
                                                       data-toggle="modal" data-target="#assign-authorization-level"
                                                       data-dismiss="modal"><i class="fa fa-shield"
                                                                               style="color: #444; cursor: pointer;"
                                                                               title="Internal Requisitions Authorization Level"></i></a></span>


                                            <span>
                                                    <a class="left-padding-small" data-href="{!! route('admin.table.assignment', $user->slug) !!}"
                                                       onclick="manageexternalauuthorizationlevel('{!! route('admin.external.authorization.assignment', $user->slug) !!}')"
                                                       data-toggle="modal"
                                                       data-target="#assign-extarnal-authorization-level"
                                                       data-dismiss="modal"><i class="fa fa-fw fa-anchor"
                                                                               style="color: #444; cursor: pointer;"
                                                                               title="External Requisitions Authorization Level"></i></a></span>


                                            <span>
                                                    <a class="left-padding-small" data-href="{!! route('admin.table.assignment', $user->slug) !!}"
                                                       onclick="managepurchaseauuthorizationlevel('{!! route('admin.purchase.order.authorization.assignment', $user->slug) !!}')"
                                                       data-toggle="modal"
                                                       data-target="#assign-purchase-authorization-level"
                                                       data-dismiss="modal"><i class="fa fa-fw fa-archive"
                                                                               style="color: #444; cursor: pointer;"
                                                                               title="Purchase Order Requisitions Authorization Level"></i></a></span>
                                        @endif


                                        <span>








                                            </span>

                                        @if ($permission == 'superadmin')
                                            &nbsp;
                                            <span>
                                                    <input {{ $user->invoice_r_permission_count > 0 ? 'checked' : '' }}
                                                           data-user-id="{{ base64_encode($user->id) }}" type="checkbox"
                                                           title="Invoice R Permission" class="invoice_r_permission"
                                                           value="1">
                                                </span>
                                            &nbsp;
                                        @endif

                                        @if (isset($permission[$pmodule . '___edit']) || isset($permission[$pmodule . '___suppliers']) || $permission == 'superadmin')
                                            &nbsp;
                                            <span>
                                                    
                                                    <a onclick="openAssignSupplier({{$user->id}},'{!! ucfirst($user->name) !!}')">
                                                        <i class="fa fa-users"></i>
                                                    </a>
                                                    
                                                    
                                                </span>
                                            &nbsp;
                                        @endif

                                        @if ((isset($permission[$pmodule . '___edit']) || $permission == 'superadmin') && 1==2)
                                            <!-- Button trigger modal -->
                                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
                                                    data-target="#modelId{{ $user->id }}">
                                                <i class="fa fa-lock"></i>
                                            </button>

                                            <!-- Modal -->
                                            <div class="modal fade" id="modelId{{ $user->id }}" tabindex="-1"
                                                 role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
                                                <form action="{{ route('users.appAssignUserPermission') }}"
                                                      method="post" class="submitMe">
                                                    {{ csrf_field() }}
                                                    <input type="hidden" name="user_id"
                                                           value="{{ $user->id }}">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Assign App Menu Permissions to
                                                                    {{ $user->name }}</h5>
                                                                <button type="button" class="close"
                                                                        data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                @php
                                                                    $getAssignedPermisisons = $user->app_permissions->pluck('module')->toArray();
                                                                @endphp
                                                                <div class="row">
                                                                    @foreach ($app_permissions as $perm)
                                                                        <div class="col-sm-6">
                                                                            <div class="form-check">
                                                                                <label class="form-check-label">
                                                                                    <input type="checkbox"
                                                                                           class="form-check-input"
                                                                                           name="permission[]"
                                                                                           @if (in_array($perm, $getAssignedPermisisons)) checked @endif
                                                                                           value="{{ $perm }}">
                                                                                    {{ $perm }}
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                        data-dismiss="modal">Close
                                                                </button>
                                                                <button type="submit"
                                                                        class="btn btn-primary">Save
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        @endif
                                        <span>
                                                        <a class="left-padding-small" onclick="assignBranches('{!! route('admin.users.assign_branches', ['id'=>$user->id]) !!}')" data-toggle="modal"
                                                           data-target="#assign-branches" data-dismiss="modal"><i class="fa fa-fw fa-list" style="color: #444; cursor: pointer;"
                                                                                                                  title="Assign Branches"></i></a></span>
                                        </span>

                                    </td>


                                </tr>
                                    <?php $b++; ?>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    @include('admin.users.supplier_modal')

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
        $(document).ready(function() {
            console.log('Document ready');
            
            // Initialize select2
            try {
                $('.mlselec6t').select2();
                console.log('Select2 initialized');
            } catch (e) {
                console.error('Error initializing select2:', e);
            }

            // Debug modal element
            console.log('Modal element exists:', $('#assign-supplier').length > 0);
            console.log('Modal HTML:', $('#assign-supplier').html());

            // Initialize modal
            try {
                $('#assign-supplier').modal({
                    show: false,
                    backdrop: 'static',
                    keyboard: false
                });
                console.log('Modal initialized');

                // Add modal event listeners
                $('#assign-supplier').on('show.bs.modal', function () {
                    console.log('Modal show event triggered');
                });

                $('#assign-supplier').on('shown.bs.modal', function () {
                    console.log('Modal shown event triggered');
                });

                $('#assign-supplier').on('hide.bs.modal', function () {
                    console.log('Modal hide event triggered');
                });

                $('#assign-supplier').on('hidden.bs.modal', function () {
                    console.log('Modal hidden event triggered');
                });
            } catch (e) {
                console.error('Error initializing modal:', e);
            }
        });

        function unassginSuppliers() {
            $('.assignBtn').text("Update Supplier");
            $('#list-suppliers').html('');
            $('.select_supplier option').attr('disabled', false);
            $('.select_supplier').val('').trigger('change.select2');
            return false;
        }

        function openAssignSupplier(userId, name) {
            console.log('Opening supplier modal for user:', userId, name);
            
            // Show loader
            $('.loder').show();
            
            // Clear any existing hidden inputs
            $('#assign-supplier form input[name="user_id"]').remove();
            
            // Add the user ID
            $('#assign-supplier form').append("<input type='hidden' name='user_id' value='" + userId + "'>");
            $('#user_name').html(name);
            $('.select_supplier option').attr('disabled', false);
            $('#list-suppliers').html("");
            $('.select_supplier').val('').trigger('change.select2');
            
            // Remove existing change event handler
            $('.select_supplier').off('change');
            
            // Add new change event handler
            $('.select_supplier').on('change', function() {
                var selectedValue = $(this).val();
                console.log('Supplier selected:', selectedValue);
                
                if (!selectedValue) return;
                
                $('.assignBtn').text("Assign Suppliers");
                
                if (selectedValue === "Select All") {
                    $('#list-suppliers').html('');
                    $('.select_supplier option').each(function(i, v) {
                        if ($(v).val() !== "Select All" && $(v).val() !== "") {
                            $('#list-suppliers').append('<li class="list-group-item">' +
                                '<input type="checkbox" name="supplier[]" checked value="' + $(v).val() + '"> ' + $('.select_supplier option[value="' + $(v).val() + '"]').html() +
                                '</li>');
                            $('.select_supplier option[value="' + $(v).val() + '"]').attr('disabled', true);
                        }
                    });
                } else {
                    $('#list-suppliers').append('<li class="list-group-item">' +
                        '<input type="checkbox" name="supplier[]" checked value="' + selectedValue + '"> ' + $('.select_supplier option[value="' + selectedValue + '"]').html() +
                        '</li>');
                    $('.select_supplier option[value="' + selectedValue + '"]').attr('disabled', true);
                }
                
                // Reset select2 to default option
                $(this).val('').trigger('change.select2');
            });
            
            $.ajax({
                url: '{{route("admin.users.get_user_suppliers")}}',
                type: 'GET',
                dataType: 'json',
                data: {
                    id: userId
                },
                success: function (data) {
                    console.log('Received supplier data:', data);
                    
                    // Populate suppliers list
                    for (let i in data.data) {
                        $('#list-suppliers').append('<li class="list-group-item">' +
                            '<input type="checkbox" checked name="supplier[]" value="' + data.data[i].wa_supplier_id + '"> ' + data.data[i].supplier.name +
                            '</li>');
                        $('.select_supplier option[value="' + data.data[i].wa_supplier_id + '"]').attr('disabled', true);
                    }
                    
                    // Hide loader
                    $('.loder').hide();
                    
                    // Debug modal state before showing
                    console.log('Modal element exists before show:', $('#assign-supplier').length > 0);
                    console.log('Modal is initialized before show:', $('#assign-supplier').data('bs.modal') !== undefined);
                    
                    // Show modal
                    try {
                        $('#assign-supplier').modal('show');
                        console.log('Modal show called');
                        
                        // Force modal to be visible
                        $('#assign-supplier').css('display', 'block');
                        $('#assign-supplier').addClass('in');
                        $('body').addClass('modal-open');
                        $('.modal-backdrop').addClass('in');
                    } catch (e) {
                        console.error('Error showing modal:', e);
                    }
                },
                error: function (xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                    $('.loder').hide();
                    alert('Error loading suppliers. Please try again.');
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
    <script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>

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

        /* Modal styles */
        .modal-backdrop {
            z-index: 1040;
        }
        .modal {
            z-index: 1050;
        }
    </style>

    <script type="text/javascript">
        $(document).ready(function() {
            console.log('Document ready');
            
            // Initialize select2
            try {
                $('.mlselec6t').select2();
                console.log('Select2 initialized');
            } catch (e) {
                console.error('Error initializing select2:', e);
            }

            // Debug modal element
            console.log('Modal element exists:', $('#assign-supplier').length > 0);
            console.log('Modal HTML:', $('#assign-supplier').html());

            // Initialize modal
            try {
                $('#assign-supplier').modal({
                    show: false,
                    backdrop: 'static',
                    keyboard: false
                });
                console.log('Modal initialized');

                // Add modal event listeners
                $('#assign-supplier').on('show.bs.modal', function () {
                    console.log('Modal show event triggered');
                });

                $('#assign-supplier').on('shown.bs.modal', function () {
                    console.log('Modal shown event triggered');
                });

                $('#assign-supplier').on('hide.bs.modal', function () {
                    console.log('Modal hide event triggered');
                });

                $('#assign-supplier').on('hidden.bs.modal', function () {
                    console.log('Modal hidden event triggered');
                });
            } catch (e) {
                console.error('Error initializing modal:', e);
            }
        });

        function unassginSuppliers() {
            $('.assignBtn').text("Update Supplier");
            $('#list-suppliers').html('');
            $('.select_supplier option').attr('disabled', false);
            $('.select_supplier').val('').trigger('change.select2');
            return false;
        }

        function openAssignSupplier(userId, name) {
            console.log('Opening supplier modal for user:', userId, name);
            
            // Show loader
            $('.loder').show();
            
            // Clear any existing hidden inputs
            $('#assign-supplier form input[name="user_id"]').remove();
            
            // Add the user ID
            $('#assign-supplier form').append("<input type='hidden' name='user_id' value='" + userId + "'>");
            $('#user_name').html(name);
            $('.select_supplier option').attr('disabled', false);
            $('#list-suppliers').html("");
            $('.select_supplier').val('').trigger('change.select2');
            
            // Remove existing change event handler
            $('.select_supplier').off('change');
            
            // Add new change event handler
            $('.select_supplier').on('change', function() {
                var selectedValue = $(this).val();
                console.log('Supplier selected:', selectedValue);
                
                if (!selectedValue) return;
                
                $('.assignBtn').text("Assign Suppliers");
                
                if (selectedValue === "Select All") {
                    $('#list-suppliers').html('');
                    $('.select_supplier option').each(function(i, v) {
                        if ($(v).val() !== "Select All" && $(v).val() !== "") {
                            $('#list-suppliers').append('<li class="list-group-item">' +
                                '<input type="checkbox" name="supplier[]" checked value="' + $(v).val() + '"> ' + $('.select_supplier option[value="' + $(v).val() + '"]').html() +
                                '</li>');
                            $('.select_supplier option[value="' + $(v).val() + '"]').attr('disabled', true);
                        }
                    });
                } else {
                    $('#list-suppliers').append('<li class="list-group-item">' +
                        '<input type="checkbox" name="supplier[]" checked value="' + selectedValue + '"> ' + $('.select_supplier option[value="' + selectedValue + '"]').html() +
                        '</li>');
                    $('.select_supplier option[value="' + selectedValue + '"]').attr('disabled', true);
                }
                
                // Reset select2 to default option
                $(this).val('').trigger('change.select2');
            });
            
            $.ajax({
                url: '{{route("admin.users.get_user_suppliers")}}',
                type: 'GET',
                dataType: 'json',
                data: {
                    id: userId
                },
                success: function (data) {
                    console.log('Received supplier data:', data);
                    
                    // Populate suppliers list
                    for (let i in data.data) {
                        $('#list-suppliers').append('<li class="list-group-item">' +
                            '<input type="checkbox" checked name="supplier[]" value="' + data.data[i].wa_supplier_id + '"> ' + data.data[i].supplier.name +
                            '</li>');
                        $('.select_supplier option[value="' + data.data[i].wa_supplier_id + '"]').attr('disabled', true);
                    }
                    
                    // Hide loader
                    $('.loder').hide();
                    
                    // Debug modal state before showing
                    console.log('Modal element exists before show:', $('#assign-supplier').length > 0);
                    console.log('Modal is initialized before show:', $('#assign-supplier').data('bs.modal') !== undefined);
                    
                    // Show modal
                    try {
                        $('#assign-supplier').modal('show');
                        console.log('Modal show called');
                        
                        // Force modal to be visible
                        $('#assign-supplier').css('display', 'block');
                        $('#assign-supplier').addClass('in');
                        $('body').addClass('modal-open');
                        $('.modal-backdrop').addClass('in');
                    } catch (e) {
                        console.error('Error showing modal:', e);
                    }
                },
                error: function (xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                    $('.loder').hide();
                    alert('Error loading suppliers. Please try again.');
                }
            });
        }
    </script>
@endsection
