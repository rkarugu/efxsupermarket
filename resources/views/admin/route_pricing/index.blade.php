@extends('layouts.admin.admin')

@section('content')
    <style>
        .span-action {

            display: inline-block;
            margin: 0 3px;

        }
    </style>
    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border no-padding-h-b">
                <div class="row">
                    <div class="col-md-9">
                        <h3>{{ $inventoryItem->title }} | Route Pricing</h3>

                      
                       
                    </div>
                    <div class="col-sm-3">
                        @if(isset($permission[$pmodule.'___route-pricing']) || $permission == 'superadmin')
                        <div align="right">
                            <a href="{!! url()->previous()!!}" class="btn btn-success">Back</a>
                            <a href="{!! route('route.pricing.create' , $inventoryItem->id)!!}" class="btn btn-success">Add pricing</a>

                        </div>
                        @endif
                    </div>
                   
                </div>
                <br>
                @include('message')
                <div class="col-md-12 no-padding-h">
                    <table class="table table-bordered table-hover" id="create_datatable_10">
                        <thead>
                        <tr>
                            <th width="3%">#</th>
                            <th>Branch</th>
                            <th>Routes</th>
                            <th>Price</th>
                            <th>Route Price</th>
                            <th>Created By</th>
                            <th >Flash/Non Flash</th>
                            <th >Status</th>
                            <th >Action</th>
                            
                        </tr>
                        </thead>
                        <tbody>
                            @foreach ($routePricing as $pricing)
                            <tr>
                                <td>{{$loop->index + 1}}</td>
                                <td>{{$pricing->restaurant?->name}}</td>
                                <td>@foreach ($pricing->getRoutesAttribute() as $route)

                                    {{$route->route_name . ',   '}} 

                                @endforeach
                                </td>
                                <td style="text-align: right">{{ number_format($pricing->getInventoryItemDetails?->selling_price, 2)}}</td>
                                <td style="text-align: right">{{$pricing->price}}</td>
                                <td>{{$pricing->createdBy?->name}}</td>
                                <td>{{($pricing->is_flash == 1) ? 'Flash' : 'Non Flash'}}</td>
                                <td>{{($pricing->status == 0) ? 'Active' : 'Inactive'}}</td>
                                <td>
                                    <div class="action-button-div">
                                        <a href="{{route('route.pricing.edit',[$inventoryItem->id, $pricing->id])}}"><i class="fas fa-pen" title="edit"></i></a>
                                        {{-- <a href="#"><i class="fas fa-trash-alt" style="color: red;"></i></a> --}}
        
                                    </div>
                                </td>
                            </tr>
                                
                            @endforeach
                         
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
{{-- approve discounts modal --}}

{{-- <div class="modal fade" id="confirmationModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
aria-labelledby="staticBackdropLabel" aria-hidden="true">
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title" id="staticBackdropLabel">Are you sure you want to block this promotion?</h4>
           
        </div>
        <form method="post" id="confirmationForm" action="">
            @csrf
            
            <input name="user_requested_access" type="hidden" id="user_requested_access"
                    value="{{ old('user_requested_access') }}" required />
           
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary btn-submit-updated-center">Yes, Block Promotion</button>
            </div>
        </form>
    </div>
</div>
</div> --}}

{{-- Delete discounts --}}

{{-- <div class="modal fade" id="confirmationModal2" data-backdrop="static" data-keyboard="false" tabindex="-1"
aria-labelledby="staticBackdropLabel" aria-hidden="true">
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title" id="staticBackdropLabel">Are you sure you want to Delete this promotion?</h4>
           
        </div>
        <form method="POST" id="confirmationForm2" action="">
            @csrf
            @method("DELETE")
            
            <input name="user_requested_access2" type="hidden" id="user_requested_access2"
                    value="{{ old('user_requested_access2') }}" required />
           
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary btn-submit-updated-center2">Yes, Delete Promotion</button>
            </div>
        </form>
    </div>
</div>
</div> --}}


    </section>

  

@endsection
@section('uniquepagescript')
{{-- <script>
    $(document).ready(function() {
        $('.btn-decline').click(function() {
            var promotionId = $(this).data('promotion-id');
            $('#confirmationModal').find('#promotion_id').val(promotionId);
            console.log(promotionId);
            $('#confirmationForm').attr('action', '{{ route('promotions-bands.block', ['promotionId' => ':promotionId']) }}'.replace(':promotionId', promotionId));
            console.log("Form action:", $('#confirmationForm').attr('action')); 

        });
    
        $('#confirmationModal').on('show.bs.modal', function(event) {
            var modal = $(this);
            modal.find('.btn-submit-updated-center').off('click').on('click', function() {
                modal.find('form').submit();
                modal.modal('hide');
            });
        });


        $('.btn-decline2').click(function() {
            var promotionId = $(this).data('promotion-id');
            $('#confirmationModal2').find('#promotion_id').val(promotionId);
            $('#confirmationForm2').attr('action', '{{ route('promotions-bands.delete', ['promotionId' => ':promotionId']) }}'.replace(':promotionId', promotionId));
        });
    
        $('#confirmationModal2').on('show.bs.modal', function(event) {
            var modal = $(this);
            modal.find('.btn-submit-updated-center2').off('click').on('click', function() {
                modal.find('form').submit();
                modal.modal('hide');
            });
        });

      

    });
    </script> --}}
@endsection



