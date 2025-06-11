@extends('layouts.admin.admin')

@section('content')
    <section class="content">
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

                            <button type="button" class="btn btn-primary" onclick="verifyShop($('#subject-shop').val());">Yes, Verify</button>
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

                            <button type="button" class="btn btn-primary" onclick="rejectShop($('#subject-shop').val());">Yes, Reject</button>
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
                        <form action="{{ route("$base_route_name.verify-all") }}" method="post" id="approve-all-shops-form">
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

        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title"> Duplicate Route Customer Onboarding Requests </h3>

                    <div class="d-flex">
                        <form action="{{ route('duplicate-route-customers')}}" method="GET" class="d-flex justify-content-between">
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
                            <button type="submit" class="btn btn-primary" style="margin-left:10px">Filter</button>
                        </form>
                    </div>
{{-- 
                    <button data-toggle='modal' data-target='#confirm-approve-all-shops-modal' data-backdrop='static' class="btn btn-primary">
                        <i class="fa fa-check-circle btn-icon"></i> Verify All
                    </button> --}}
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
                                <a href='{!! route('duplicate-route-customers.show', $row->id )  !!}' title='View Route Customer'>
                                <i class='fa fa-eye text-info fa-lg'></i>
                             </a>
                             {{-- <button title='Verify Route Customer' data-toggle='modal' data-target='#confirm-verify-shop-modal' data-backdrop='static' data-id='{{ $row->id }}'  style="border: none; background:transparent;" >
                                <i class='fa fa-check-circle text-success fa-lg'></i>
                                <form action=" {!! route("route-customers.verify", $row->id)  !!} " method='post' id='verify-shop-form-{{$row->id}}'>
                                    {{ csrf_field() }}
                                    
                                    <input type='hidden' id='source-{{$row->id}}' name='source'>
                                </form>
                            </button> --}}
                            {{-- <button title='Reject Route Customer' data-toggle='modal' data-target='#reject-shop-modal' data-backdrop='static' data-id='{{ $row->id }}' style="border: none; background:transparent;"  >
                                <i class='fa fa-trash text-danger fa-lg'></i>
                                <form action=" {!! route("route-customers.verification-reject", $row->id)  !!} " method='post' id='reject-shop-form-{{$row->id}}'>
                                    {{ csrf_field() }}
                                    
                                    <input type='hidden' id='source-{{$row->id}}' name='source'>
                                </form>
                            </button> --}}
                             
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
        function verifyShop(subjectShopId) {
            console.log("verifyshop called");
            console.log(subjectShopId);

            $("#source-"+subjectShopId).val('onboarding_requests');
            $("#verify-shop-form-"+subjectShopId).submit();
        }
        function rejectShop(subjectShopId) {
            console.log("rejectshop called");
            console.log(subjectShopId);

            $("#source-"+subjectShopId).val('onboarding_requests');
            $("#reject-shop-form-"+subjectShopId).submit();
        }

        function approveAllShops() {
            $("#approve-all-shops-form").submit();
        }
        $('#confirm-verify-shop-modal').on('show.bs.modal', function (event) {
            let triggeringButton = $(event.relatedTarget);
            let dataValue = triggeringButton.data('id');

            $("#subject-shop").val(dataValue);
        })
        $('#reject-shop-modal').on('show.bs.modal', function (event) {
            let triggeringButton = $(event.relatedTarget);
            let dataValue = triggeringButton.data('id');

            $("#subject-shop").val(dataValue);
        })


    </script>

   
@endsection