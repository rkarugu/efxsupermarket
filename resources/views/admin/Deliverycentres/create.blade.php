@extends('layouts.admin.admin')

@section('content')
<!-- Main content -->
<section class="content">
    <!-- Small boxes (Stat box) -->
    <div class="box box-primary">
        <div class="box-header with-border no-padding-h-b">
            <h4>Add Center</h4>

            <form action="{{ route('delivery-center.store') }}" method="post" class="submitMe">
                {{ csrf_field() }}

                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Route</label>
                    <div>
                        <select name="route_id" id="route_id" class="form-control">
                            <option value="">Select Route</option>
                            @foreach ($routes as $route)
                            <option value="{{ $route->id }}">{{ $route->route_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="route_name">Center Name</label>
                    <input type="text" name="name" id="name" class="form-control" placeholder="Center Name" aria-describedby="helpId">
                </div>

                <div class="form-group">
                    <label for="center_location_name" class="control-label"> Center Location </label>
                    <input type="text" name="center_location_name" id="center_location_name" class="form-control google_location">
                    <input type="hidden" name="center_latitude" id="center-latitude">
                    <input type="hidden" name="center_longitude" id="center-longitude">
                </div>

                <!-- <div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="route_name">Latitude</label>
                                <input type="text" name="lat" id="lat" class="form-control"
                                    placeholder="Center Latitude" aria-describedby="helpId">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="route_name">Longitude</label>
                                <input type="text" name="lng" id="lng" class="form-control"
                                    placeholder="Center Longitude" aria-describedby="helpId">
                            </div>
                        </div>
                    </div> -->
                <button type="submit" class="btn btn-primary btn-sm">Save</button>
            </form>
        </div>
    </div>
</section>
@endsection
@section('uniquepagestyle')
<style>
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
@endsection

@section('uniquepagescript')
<div id="loader-on" style="
position: fixed;
top: 0;
text-align: center;
display: block;
z-index: 999999;
width: 100%;
height: 100%;
background: #000000b8;
display:none;
" class="loder">
    <div class="loader" id="loader-1"></div>
</div>
<script src="{{ asset('js/sweetalert.js') }}"></script>
<script src="{{ asset('js/form.js') }}"></script>

<!-- Center Location Picker Script -->
<script src="http://maps.googleapis.com/maps/api/js?libraries=places&key=AIzaSyCiKA38J54kh5ZrI9J7kTOox8_4sI21TAc"></script>
<script type="text/javascript">
    function initializeLocationSearch() {
        let centerLocationInput = document.getElementById('center_location_name');
        let options = {};

        let autocomplete = new google.maps.places.Autocomplete(centerLocationInput, options);
        google.maps.event.addListener(autocomplete, 'place_changed', function() {
            let place = autocomplete.getPlace();
            let lat = place.geometry.location.lat();
            let lng = place.geometry.location.lng();
            $("#center-latitude").val(lat);
            $("#center-longitude").val(lng);
        });
    }


    google.maps.event.addDomListener(window, 'load', initializeLocationSearch);
</script>
@endsection