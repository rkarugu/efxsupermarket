@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="modal fade" id="confirmDownloadModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="box-title"> Reject Customer</h3>

                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>
                    <form action="{{ route('route-customers.verification-reject-show', [$shopdetails->id, $model]) }}" method="POST" class="complete-schedule-form" id="complete-schedule-form">
                        @csrf

                        <div class="box-body">
                            <div class="form-group">
                                <label for="comment">Reason </label><br>
                                <textarea name="comment" id="comment" cols="90" rows="7" placeholder="Please provide a reason for rejecting  customers" required></textarea>
                            </div>
                        </div>

                        <div class="box-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <button type="submit" id="confirmDownloadBtn" class="btn btn-primary">Reject</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    
                    <h3 class="box-title text-danger">{{ $shopdetails->name }} - Location Details</h3>
                    <div>
                        @if ($model == 'route-customers-onboarding-requests')
                        
                        <a href="{{ route('route-customers.verify-show', $shopdetails->id) }}" class="btn btn-outline-primary">Verify</a> 
                        {{-- <a href="{{ route('route-customers.verification-reject-show', [$shopdetails->id, $model]) }}" class="btn btn-outline-primary reject">Reject</a>  --}}
                        <a href="{{ route('route-customers.verification-reject-show', [$shopdetails->id, $model]) }}" class="btn btn-outline-primary reject">Reject</a> 
                        <button  class="btn btn-success reject">Reject</button>

                        <a href="{{ route('route-customers.unverified') }}" class="btn btn-outline-primary">Back</a>
  
                        @elseif ($model == 'route-customers-approval-requests')
                        @if ($shopdetails->status != 'approved')
                        <a href="{{ route('route-customers.approve-show', [$shopdetails->id, 'schedule_id' => $schedule_id]) }}" class="btn btn-outline-primary">Approve</a> 
                        <button  class="btn btn-success reject">Reject</button>
                        @endif

                        <a href="{{ route('route-customers.approval-requests') }}" class="btn btn-outline-primary">Back</a>

                        @elseif ($model == 'geomapping-schedules' )
                        @if ($shopdetails->status != 'approved')
                        <a href="{{ route('route-customers.approve-show', [$shopdetails->id, 'schedule_id' => $schedule_id]) }}" class="btn btn-outline-primary">Approve</a> 
                        <button  class="btn btn-success reject">Reject</button>
                        @endif

                        <a href="{{ url()->previous() }}" class="btn btn-outline-primary">Back</a>
     
                        @else
                        <a href="{{ url()->previous() }}" class="btn btn-outline-primary">&lt;&lt; Back to Customers List</a>
                        @endif


                    </div>
                </div>
            </div>
            <div class="box-body">
                <div id="map" style="width: 100%; height: 300px;"></div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <div class="d-flex justify-content-between align-items-center">
                           Route Customer Details
                         </div>
                    </div>
                    <div class="box-body">
                        <img src="{{ asset('uploads/shops/' . $shopdetails->image_url) }}" alt="" style="height:20vh;width:12vw;">

                        <div class="table-responsive"> 
                            <table class="table table-bordered">
                                <thead>
                                    <tr> 
                                        <th>Item</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Customer Name</td>
                                        <td>{{$shopdetails->name ?? ''}}</td>
                                    </tr>
                                    <tr> 
                                    <tr>
                                        <td>Phone Number</td>
                                        <td>{{$shopdetails->phone ?? ''}}</td>
                                    </tr>
                                    <tr>
                                        <td>Business Name</td>
                                        <td>{{$shopdetails->bussiness_name ?? ''}}</td>
                                    </tr>
                                    <tr>
                                        <td>Town</td>
                                        <td>{{$shopdetails->town ?? ''}}</td>
                                    </tr>
                                    <tr>
                                        <td>Status</td>
                                        <td>{{$shopdetails->status ?? ''}}</td>
                                    </tr>
                                    <tr>
                                        <td>Gender</td>
                                        <td>{{$shopdetails->gender ?? ''}}</td>
                                    </tr>
                                    <tr>
                                        <td>KRA PIN</td>
                                        <td>{{$shopdetails->kra_pin ?? ''}}</td>
                                    </tr>
                                    <tr>
                                        <td>Date Created</td>
                                        <td>{{ $shopdetails->created_at != null ? $shopdetails->created_at->format('M, d Y, h:i A') : ''}}</td>
                                    </tr> 
                                </tbody>
                           </table>
                        </div>
                       
                    </div>
                </div>
            </div>
            <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <div class="d-flex justify-content-between align-items-center">
                           Route Information
                         </div>
                    </div>
                    <div class="box-body">
                        <div class="table-responsive"> 
                            <table class="table table-bordered">
                                <thead>
                                    <tr> 
                                        <th>Item</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Route Name</td>
                                        <td>{{$shopdetails->route->route_name ?? ''}}</td>
                                    </tr> 
                                    <tr>
                                        <td>Starts From</td>
                                        <td>{{$shopdetails->route->starting_location_name ?? ''}}</td>
                                    </tr> 
                                   
                                    <tr>
                                        <td>Tonnage Target</td>
                                        <td>{{$shopdetails->route->tonnage_target ?? ''}}</td>
                                    </tr>
                                    <tr>
                                        <td>Sales Target</td>
                                        <td>{{$shopdetails->route->sales_target ?? ''}}</td>
                                    </tr>
                                    <tr>
                                        <td>Delivery Days</td>
                                        <td>Day(s) - {{$shopdetails->route->delivery_days ?? ''}}</td>
                                    </tr>
                                    <tr>
                                        <td>Order Taking Days </td>
                                        <td>Day(s) - {{$shopdetails->route->order_taking_days ?? ''}}</td>
                                    </tr> 
                                    
                                </tbody>
                           </table>
                        </div>
                       
                    </div>
                </div>
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <div class="d-flex justify-content-between align-items-center">
                           Delivery Center Information
                         </div>
                    </div>
                    <div class="box-body">
                        <div class="table-responsive"> 
                            <table class="table table-bordered">
                                <thead>
                                    <tr> 
                                        <th>Item</th>
                                        <th>Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Center  Name</td>
                                        <td>{{$shopdetails->center->name ?? ''}}</td>
                                    </tr> 
                                    <tr>
                                        <td>Center Location Name</td>
                                        <td>{{$shopdetails->center->center_location_name ?? ''}}</td>
                                    </tr> 
                                   
                                    <tr>
                                        <td>Cordinates</td>
                                        <td>({{$shopdetails->center->lat ?? ''}} , {{$shopdetails->center->lng ?? ''}})</td>
                                    </tr>
                                     
                                    
                                </tbody>
                           </table>
                        </div>
                       
                    </div>
                </div>
            </div>
             
        </div>
        
    </section>
@endsection

@section('uniquepagescript')
<script async src="https://maps.googleapis.com/maps/api/js?key={{ $googleMapsApiKey }}&callback=initMap"></script>

<script type="text/javascript">
    $('.reject').on('click', function (event) {
            event.preventDefault();
            $('#confirmDownloadModal').modal('show');
        });
    let map;

    async function initMap() {
        const { Map, InfoWindow } = await google.maps.importLibrary("maps");
        const { AdvancedMarkerElement, PinElement } = await google.maps.importLibrary("marker");
 
        let shopLat = {{ $shopdetails->lat }};
        let shopLng = {{ $shopdetails->lng }};
        let shopName = "{{ $shopdetails->name }}";

        const map = new google.maps.Map(document.getElementById("map"), {
            zoom: 15,
            center: { lat: shopLat, lng: shopLng },
            mapId: "8a023462a9950e01",
        });
 
        const icon = document.createElement("div");
        icon.innerHTML = '<i class="fa fa-shopping-bag fa-2x"></i>';
        const pinScaled = new PinElement({
            scale: 2.0,
            background: "#FBBC04",
            borderColor: "#137333",
            glyph: icon,
        });

        const marker = new AdvancedMarkerElement({
            map,
            position: new google.maps.LatLng(shopLat, shopLng),
            content: pinScaled.element,
            title: shopName,
        });

        const infoWindow = new InfoWindow();
        marker.addListener("click", ({ domEvent, latLng }) => {
            const { target } = domEvent;
            infoWindow.close();
            infoWindow.setContent(marker.title);
            infoWindow.open(marker.map, marker);
        });
    }
</script>
@endsection

@section('uniquepagestyle')
<style>
    #map {
        height: 600px;
        width: 100%;
    }
</style>
@endsection
