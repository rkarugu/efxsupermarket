@extends('layouts.admin.admin')

@section('content') 
    <section class="content">
        <!-- Confirm Shop Approval -->
        <div class="modal fade" id="confirm-approve-shop-modal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="box-title"> Confirm Route Customer Approval </h3>

                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>

                    <div class="box-body">
                        <p style="font-size: 16px;"> Are you sure you want to approve route customer? </p>
                        <input type="hidden" id="subject-shop">
                    </div>

                    <div class="box-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>

                            <button type="button" class="btn btn-primary" onclick="approveShop($('#subject-shop').val());">Yes, Approve</button>
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
                            <h3 class="box-title"> Confirm Route Customers Approval </h3>

                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>

                    <div class="box-body">
                        <p style="font-size: 16px;"> Are you sure you want to approve all verified route customers? </p>
                        <form action="{{ route("$base_route_name.approve-all") }}" method="post" id="approve-all-shops-form">
                            {{ csrf_field() }}
                        </form>
                    </div>

                    <div class="box-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>

                            <button type="button" class="btn btn-primary" onclick="approveAllShops();">Yes, Approve</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title"> Route Customer Approval Requests </h3>

                    <div class="d-flex">
                        <form action="{{ route('route-customers.approval-requests')}}" method="GET" class="d-flex justify-content-between">
                            <select name="branch" id="branch" class="form-control select2" style="margin-left:10px">
                                <option value="">Choose Branch</option>
                                @foreach ($branch as $item)
                                    <option value="{{$item->id}}" {{ request()->branch == $item->id ? 'selected' : '' }}>{{$item->name}}</option>
                                @endforeach
                            </select>
                            <select name="route" id="route" class="form-control select2" style="margin-left:10px">
                                <option value="">Choose Route</option>
                                @foreach ($routes as $item)
                                    <option value="{{$item->id}}" {{ request()->route == $item->id ? 'selected' : '' }}>{{$item->route_name}}</option>
                                @endforeach
                            </select>
                            <select name="centers" id="centers" class="form-control select2" style="margin-left:10px">
                                <option value="">Choose Centers</option>
                                @foreach ($centers as $item)
                                    <option value="{{$item->id}}" {{ request()->centers == $item->id ? 'selected' : '' }}>{{$item->name}}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-primary" style="margin-left:10px">Filter</button>
                        </form>
                    </div>
                    <button data-toggle='modal' data-target='#confirm-approve-all-shops-modal' data-backdrop='static' class="btn btn-primary">
                        <i class="fa fa-check-square btn-icon"></i> Approve All
                    </button>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <div class="table-responsive">
                    <table class="table" id="create_datatable_50">
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
                        <tbody>
                            @foreach ($route_customers as $row)
                            <tr>
                            <td>{{$row->created_at->format('D, M j, Y')}}</td>
                            <td>{{$row->route?->route_name}}</td>
                            <td>{{$row->center?->name}}</td>
                            <td>{{$row->bussiness_name}}</td>
                            <td>{{$row->name}}</td>
                            <td>{{$row->phone}}</td>
                            <td>{{$row->status}}</td>
                            <td>
                                <a href='{!! route('route-customers.show-custom', [$row->id, $model] ) !!}' title='View Route Customer'>
                                <i class='fa fa-eye text-info fa-lg'></i>
                             </a>
                           
                                        <button title='Approve Route Customer' data-toggle='modal' data-target='#confirm-approve-shop-modal' data-backdrop='static' data-id='{{ $row->id }}'>
                                            <i class='fa fa-check-circle text-success fa-lg'></i>
                                            <form action=" {!! route("route-customers.approve", $row->id)  !!} " method='post' id='approve-shop-form-{{$row->id}}'>
                                                {{ csrf_field() }}
                                                
                                                <input type='hidden' id='source-{{$row->id}}' name='source' >
                                            </form>
                                        </button>
                             
                            </td>
                            </tr>


                                
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('uniquepagestyle')
<link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
@endsection

@section('uniquepagescript')
<script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>

    <script type="text/javascript">
    $(document).ready(function() {
        $('body').addClass('sidebar-collapse');
        $(".select2").select2();
    });
        // function approveShop() {
        //     let subjectShopId = $("#subject-shop").val();
        //     $(`#source-${subjectShopId}`).val('approval_requests');

        //     $(`#approve-shop-form-${subjectShopId}`).submit();
        // }

        // function approveAllShops() {
        //     $("#approve-all-shops-form").submit();
        // }
        function approveShop(subjectShopId) {
            $("#source-"+subjectShopId).val('onboarding_requests');
            $("#approve-shop-form-"+subjectShopId).submit();
        }

        function approveAllShops() {
            $("#approve-all-shops-form").submit();
        }

        $('#confirm-approve-shop-modal').on('show.bs.modal', function (event) {
            let triggeringButton = $(event.relatedTarget);
            let dataValue = triggeringButton.data('id');

            $("#subject-shop").val(dataValue);
        })
    </script>

  
@endsection