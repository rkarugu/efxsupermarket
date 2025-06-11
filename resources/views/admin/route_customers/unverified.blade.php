@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                @include('message')
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title" style="width: 30%"> Route Customer Onboarding Requests </h3>
                    <div class="d-flex flex-grow-1">
                        <form action="{{ route('route-customers.unverified') }}" method="GET"
                            class="d-flex justify-content-between flex-grow-1">
                            <select name="branch" id="branch" class="form-control select2" style="margin-left:10px">
                                <option value="">Choose Branch</option>
                                @foreach ($branch as $item)
                                    <option value="{{ $item->id }}"
                                        {{ request()->branch == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                                @endforeach
                            </select>
                            <select name="route" id="route" class="form-control select2" style="margin-left:10px">
                                <option value="">Choose Route</option>
                            </select>
                            <select name="center" id="center" class="form-control select2" style="margin-left:10px">
                                <option value="">Choose Centers</option>
                            </select>
                        </form>
                    </div>
                    <button data-toggle='modal' data-target='#confirm-approve-all-shops-modal' data-backdrop='static'
                        class="btn btn-primary">
                        <i class="fa fa-check-circle btn-icon"></i> Verify All
                    </button>
                </div>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table" id="unverifiedCustomersDataTable">
                        <thead>
                            <tr>
                                <th> Date Onboaded</th>
                                <th> Route</th>
                                <th> Center</th>
                                <th> Business Name</th>
                                <th> Customer Name</th>
                                <th> Phone Number</th>
                                <th> Status</th>
                                <th> Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </section>
    <!-- Confirm Shop Verification -->
    <div class="modal fade" id="confirm-verify-shop-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">

            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="box-title"> Confirm Route Customer Verification </h3>

                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>

                <div class="box-body">
                    <p style="font-size: 16px;"> Are you sure you want to verify route customer? </p>
                    <input type="hidden" id="subject-shop">

                </div>

                <div class="box-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>

                        <button type="button" class="btn btn-primary" onclick="verifyShop($('#subject-shop').val());">Yes,
                            Verify</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="reject-shop-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">

            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="box-title"> Reject Route Customer </h3>

                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>

                <div class="box-body">
                    <p style="font-size: 16px;"> Are you sure you want to reject route customer? </p>
                    <input type="hidden" id="subject-shop">

                </div>

                <div class="box-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>

                        <button type="button" class="btn btn-primary"
                            onclick="rejectShop($('#subject-shop').val());">Yes, Reject</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirm All Shops Approval -->
    <div class="modal fade" id="confirm-approve-all-shops-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="box-title"> Confirm Route Customers Verification </h3>

                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>

                <div class="box-body">
                    <p style="font-size: 16px;"> Are you sure you want to verify all unverified route customers? </p>
                    <form action="{{ route("route-customers.verify-all") }}" method="post"
                        id="approve-all-shops-form">
                        {{ csrf_field() }}
                    </form>
                </div>

                <div class="box-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>

                        <button type="button" class="btn btn-primary" onclick="approveAllShops();">Yes, Verify</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
@endpush

@push('scripts')
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('body').addClass('sidebar-collapse');
            $(".select2").select2();

            $("#branch").change(function() {
                let branch = $(this).val();
                $.ajax({
                    url: "{{ route('route-customers.routes') }}",
                    data: {
                        branch: branch,
                    },
                    success: function(data) {
                        $("#route").html(new Option('Please Select', '', false, false));
                        var res = data.routes.map(function(item) {
                            let option = new Option(item.route_name, item.id, false,
                                false)
                            $("#route").append(option)
                        });
                    }
                });

                refreshTable();
            })

            $("#route").change(function() {
                let route = $(this).val();
                $.ajax({
                    url: "{{ route('route-customers.centers') }}",
                    data: {
                        route: route,
                    },
                    success: function(data) {
                        $("#center").html(new Option('Please Select', '', false, false));
                        var res = data.centers.map(function(item) {
                            let option = new Option(item.name, item.id, false, false)
                            $("#center").append(option)
                        });
                    }
                });

                refreshTable();
            });

            $("#center").change(function() {
                refreshTable();
            });

            $("#unverifiedCustomersDataTable").DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [0, "desc"]
                ],
                autoWidth: false,
                pageLength: '<?= Config::get('params.list_limit_admin') ?>',
                ajax: {
                    url: '{!! route('route-customers.unverified') !!}',
                    data: function(data) {
                        data.branch = $("#branch").val();
                        data.route = $("#route").val();
                        data.center = $("#center").val();
                    }
                },
                columns: [{
                    data: 'created_at',
                    name: 'created_at'
                }, {
                    data: 'route.route_name',
                    name: 'route.route_name'
                }, {
                    data: 'center.name',
                    name: 'center.name'
                }, {
                    data: 'bussiness_name',
                    name: 'bussiness_name'
                }, {
                    data: 'name',
                    name: 'name'
                }, {
                    data: 'phone',
                    name: 'phone'
                }, {
                    data: 'status',
                    name: 'status'
                }, {
                    data: 'actions',
                    name: 'actions',
                    className: 'text-center',
                    searchable: false,
                    orderable: false
                }, ],
            });
        });

        function refreshTable() {
            $("#unverifiedCustomersDataTable").DataTable().ajax.reload();
        }

        function verifyShop(subjectShopId) {
            console.log("verifyshop called");
            console.log(subjectShopId);

            $("#source-" + subjectShopId).val('onboarding_requests');
            $("#verify-shop-form-" + subjectShopId).submit();
        }

        function rejectShop(subjectShopId) {
            console.log("rejectshop called");
            console.log(subjectShopId);

            $("#source-" + subjectShopId).val('onboarding_requests');
            $("#reject-shop-form-" + subjectShopId).submit();
        }

        function approveAllShops() {
            $("#approve-all-shops-form").submit();
        }
        $('#confirm-verify-shop-modal').on('show.bs.modal', function(event) {
            let triggeringButton = $(event.relatedTarget);
            let dataValue = triggeringButton.data('id');

            $("#subject-shop").val(dataValue);
        })
        $('#reject-shop-modal').on('show.bs.modal', function(event) {
            let triggeringButton = $(event.relatedTarget);
            let dataValue = triggeringButton.data('id');

            $("#subject-shop").val(dataValue);
        })
    </script>
@endpush
