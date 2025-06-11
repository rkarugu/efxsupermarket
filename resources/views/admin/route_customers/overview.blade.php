@extends('layouts.admin.admin') @section('content') <section class="content">
  <div class="box box-primary">
    <div class="box-header with-border">
      <div class="d-flex justify-content-between align-items-center">
        <h3 class="box-title ">Overview Map</h3>
      
        <div class="col-md-4 no-padding-h table-responsive">
          <form action="{{route('summary_report.sales_summary')}}" method="GET">
            <div class="row">
              <div class="col-md-6 form-group">
                <label for="">Branch</label>
                <select name="branch" id="mlselec6t" class="form-control mlselec6t">
                  <option value="" selected disabled>--Select Branch--</option>
                   @foreach(getBranchesDropdown() as $key => $branch)
                  <option value="{{$key}}">{{$branch}}</option>@endforeach
                </select>
              </div>
              <div class="col-md-2 ">
                <br>
                <button type="button" class="btn btn-danger" onclick="printgrn();return false;">filter</button>
              </div>
            </div>
          </form>
        </div>
      </div>
      <div class="row"></div>
      <div>
        <div id="top-cards" class="d-flex justify-content-between">
          <div class="major-detail d-flex flex-column justify-content-between border-success">
            <div class="d-flex">
              <i class="fas fa-users major-detail-icon"></i>
              <span class="major-detail-title"> Total Customers </span>
            </div>
            <span class="major-detail-value"> {{ $all }} </span>
          </div>
          <div class="major-detail d-flex flex-column justify-content-between border-success">
            <div class="d-flex">
              <i class="fas fa-weight major-detail-icon"></i>
              <span class="major-detail-title"> Unverified Customers </span>
            </div>
            <span class="major-detail-value"> {{ $unverifiedCount }} </span>
          </div>
          <div class="major-detail d-flex flex-column justify-content-between border-info">
            <div class="d-flex">
              <i class="fas fa-box-open major-detail-icon"></i>
              <span class="major-detail-title"> Verified Customers </span>
            </div>
            <span class="major-detail-value"> {{ $verifiedCount }}</span>
          </div>
          <div class="major-detail d-flex flex-column justify-content-between border-primary">
            <div class="d-flex">
              <i class="fas fa-cubes major-detail-icon"></i>
              <span class="major-detail-title"> Approved Customers </span>
            </div>
            <span class="major-detail-value">{{ $approvedCount }} </span>
          </div>
          <div class="major-detail d-flex flex-column justify-content-between border-danger">
            <div class="d-flex">
              <i class="fas fa-bed major-detail-icon"></i>
              <span class="major-detail-title"> Dormant Customers </span>
            </div>
            <span class="major-detail-value">{{ $dormantCount}}</span>
          </div>
        </div>
      </div>
                
      <div class="row no-padding-h">
        <div class="col-md-3 form-group">
          <label for="">Route name</label>
         

          <select name="branch" id="mlselec6t" class="form-control mlselec6t">



            <option value="" selected disabled>--Select route--</option>
             @foreach(getBranchesDropdown() as $key => $branch)
            <option value="{{$key}}">{{$branch}}</option>                                              @endforeach
          </select>
        </div>
        <div class="col-md-3 form-group">
          <label for="">Customer name</label>

          <select name="shop_filter" class="form-control mlselec6t">
    <option value="" selected disabled>Select Shop</option>

    @foreach ($shopdetails as $shop)

     

<option value="{{ $shop['name'] }}" >{{ $shop['name'] }}</option>
    @endforeach
</select>
         
        </div>
        <div class="col-md-3 form-group">
          <label for="">Shop</label>
          <select name="branch" id="mlselec6t" class="form-control mlselec6t">
            <option value="" selected disabled>--Select Shop--</option>
            <!--  @foreach(getBranchesDropdown() as $key => $branch)
                                                <option value="{{$key}}">{{$branch}}</option>
                                                @endforeach -->
          </select>
        </div>
        <div class="col-md-3 ">
          <br>
          <button type="button" class="btn btn-danger" onclick="printgrn();return false;">filter</button>
        </div>
      </div>
    </div>
  </div>
  <div class="box-body">
<!--     <div id="map" style="width: 100%; height: 400px;"></div> -->
  </div>
  </div>
</section>

@endsection 

@section('uniquepagescript') 

<script type="text/javascript">
  function loadMapScript() {
    const script = document.createElement("script");
    script.src = "https://maps.googleapis.com/maps/api/js?key={{ $googleMapsApiKey }}&callback=initMap";
    script.defer = true;
    script.async = true;
    document.body.appendChild(script);
  }
  async function initMap() {
    const {
      Map,
      InfoWindow
    } = await google.maps.importLibrary("maps");
    const {
      AdvancedMarkerElement,
      PinElement
    } = await google.maps.importLibrary("marker");
    const shopLocations = {!! json_encode($shop_location_data) !!};
    const map = new google.maps.Map(document.getElementById("map"), {
      zoom: 10,
      center: {
        lat: shopLocations[0].shop_lat,
        lng: shopLocations[0].shop_lng
      },
      mapId: "8a023462a9950e01",
    });
    for (const shop of shopLocations) {
      const icon = document.createElement("div");
      icon.innerHTML = '<i class="fa fa-shopping-bag "></i>';
      const pinScaled = new PinElement({
        scale: 1.0,
        background: "#FBBC04",
        borderColor: "#137333",
        glyph: icon,
      });
      const marker = new AdvancedMarkerElement({
        map,
        position: new google.maps.LatLng(shop.shop_lat, shop.shop_lng),
        content: pinScaled.element,
        title: `${shop.shop_name} - ${shop.shop_town}`,
      });
      const infoWindow = new InfoWindow();
      marker.addListener("click", ({
        domEvent,
        latLng
      }) => {
        const {
          target
        } = domEvent;
        infoWindow.close();
        infoWindow.setContent(marker.title);
        infoWindow.open(marker.map, marker);
      });
    }
  }
  loadMapScript();
</script> @endsection 

@section('uniquepagestyle') 
<style>
  #map {
    height: 400px;
    width: 100%;
  }

  .major-detail {
    border: 2px solid;
    border-radius: 15px;
    padding: 10px 15px;
    height: 80px;
    flex-grow: 1 !important;
    margin-right: 20px;
  }

  .major-detail.border-primary {
    border-color: #0d6efd;
  }

  .major-detail.border-success {
    border-color: #198754;
  }

  .major-detail.border-danger {
    border-color: #dc3545;
  }

  .major-detail.border-info {
    border-color: #0dcaf0;
  }

  .major-detail-icon {
    font-size: 20px;
  }

  .major-detail-title {
    font-size: 18px;
    font-weight: 500;
    margin-left: 12px;
    margin-top: -5px;
  }

  .major-detail-value {
    font-size: 20px;
    font-weight: 600;
  }

  #activity {
    position: relative;
    width: 40%;
  }

  .mt-20 {
    margin-top: 30px !important;
  }
</style> @endsection