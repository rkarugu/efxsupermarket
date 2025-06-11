<div style="padding:10px">
    <div class="row">
        <div class="col-md-9">
        </div>
        <div class="col-sm-3">
            @if (can('route-pricing', 'maintain-items'))
                    <div align="right" class="form-group">
                        <a href="{!! route('route.pricing.create' , $inventoryItem->id)!!}" class="btn btn-success btn-sm">Add pricing</a>
                    </div>
            @endif
        </div>
    </div>

   

        <table class="table table-bordered table-hover" id="create_datatable">
            <thead>
            <tr>
                <th width="3%">#</th>
                <th>Created At</th>
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
                    <td>{{ $pricing->created_at }}</td>
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