@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title">{{ $route->route_name }} Route Delivery Centers </h3>

                    <a href="{{ route("$base_route.index") }}" role="button" class="btn btn-primary">
                        Back To Routes List </a>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>
                @if (session()->has('center_update'))
                    <div class="alert alert-success">
                        {{ session('center_update') }}
                    </div>
                @endif
                <form method="POST" action="{{ route('admin-upload-delivery-center') }}">
                    @csrf
                    <input type="hidden" name="route_id" value="{{ $route->id }}">
                    <div class="row delivery_route_centers" id="delivery_centers">
                        <div class="col-12 all_centers_array">

                        </div>
                    </div>
                    <div class="col-lg-12 col-md-12 col-xs-12 col-sm-12">
                        <div class="form-group" style="margin-top:25px;float:right;">
                            <button class="btn btn-success btn_delivery_center btn-sm" type="button">+ Delivery
                                Center</button>

                            <button class="btn btn-success btn-sm" type="submit" id="saveCentersBtn"
                                style="margin-right:30px;">Save
                                Centers</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">

                        </div>
                    </div>

                </form>
                <div class="box-body">
                    <div class="row" style="padding:15px;">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-bordered table-stripped" id="route-delivery-centerss">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Center Name</th>
                                            <th>Center Location</th>
                                            <th>Preferred Center Radius (in Meters)</th>
                                            <th>Date Created</th>
                                            <th>Shops Count</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($centers as $key => $center)
                                            <tr>
                                                <td>{{ ++$key }}</td>
                                                <td>{{ $center->name }}</td>
                                                <td>{{ $center->center_location_name }}</td>
                                                <td>{{ $center->preferred_center_radius }}</td>
                                                <td>{{ $center->created_at->format('M d, Y') ?? '' }}</td>
                                                <td>{{ $center->waRouteCustomers->count() }}</td>
                                                <td>
{{--                                                    <button type="button" class="text-primary mr-2 btn-edit"--}}
{{--                                                        data-toggle="modal" data-target="#staticBackdrop"--}}
{{--                                                        data-id="{{ $center->id }}"><i--}}
{{--                                                            class='fa fa-edit text-primary fa-lg'></i></button>--}}

                                                    <a href="{{ route('edit-route-centres', $center->id) }}" class="text-primary mr-2"><i
                                                                class='fa fa-edit text-primary fa-lg'></i></a>
                                                    |
                                                    <button type="button" class="text-danger mr-2 btn-delete"
                                                        data-toggle="modal" data-target="#staticDeleteBackdrop"
                                                        data-id="{{ $center->id }}"><i
                                                            class='fa fa-trash text-danger'></i></button>

                                                </td>
                                            </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
    <div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" style="z-index: 1051 !important;"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Update Delivery Center Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="{{ route('update-get-center-update-details') }}">
                    {{-- <form id="updateCenterForm"> --}}
                    @csrf
                    <div class="modal-body">
                        <input name="selected_center_id" type="hidden" id="selected_center_id"
                            value="{{ old('selected_center_id') }}" required />
                        <div class="form-group">
                            <label for="name" class="control-label"> Center Name </label>
                            <input type="text" class="form-control" name="update_center_name" id="update_center_name"
                                placeholder="Delivery center name" required value="{{ old('update_center_name') }}" >
                        </div>
                        <div class="form-group">
                            <label for="ce
                        nter_location_name" class="control-label"> Center
                                Location
                            </label>
                            <input type="text" name="center_location_name" id="update_center_location_name"
                                class="form-control google_location" required value="{{ old('center_location_name') }}" >
                        </div>
                        <div class="form-group">
                            <label for="center-latitude" class="control-label"> Latitude </label>

                            <input type="text" class="form-control" name="update_center_latitude"
                                id="update-center-latitudes" placeholder="Latitude" required
                                value="{{ old('update_center_latitude') }}">

                        </div>
                        <div class="form-group">
                            <label for="center-longitude" class="control-label "> Longitude </label>

                            <input type="text" class="form-control" name="update_center_longitude"
                                id="update-center-longitudes" placeholder="Longitude" required
                                value="{{ old('update_center_longitude') }}">

                        </div>
                        <div class="form-group">
                            <label for="center-longitude" class="control-label "> Preferred Center Radius
                                (Meters) </label>

                            <input type="number" class="form-control" name="update_preferred_center_radius"
                                id="preferred_center_radius" placeholder="100" min="0" required step="0.01"
                                value="{{ old('preferred_center_radius') }}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-submit-updated-center">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="staticDeleteBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="staticBackdropLabel">Confirm Center Deletion</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="{{ route('delete-selected-route-center-details') }}">
                    @csrf
                    <div style="padding:10px;">
                        <p>Are you sure to delete delivery center? All linked shops will also be deleted.</p>
                    </div>
                    <div class="modal-body">
                        <input name="delete_selected_center_id" type="hidden" id="delete_selected_center_id"
                            value="{{ old('delete_selected_center_id') }}" required />
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-submit-updated-center">Yes, Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('uniquepagescript')
    <script src="https://maps.googleapis.com/maps/api/js?libraries=places&key={{ $googleMapsApiKey }}"></script>



    <script type="text/javascript">
        document.getElementById('saveCentersBtn').style.display = 'none';

        $(document).ready(function() {

            // $('.btn-edit').click(function() {
            //     var centerId = $(this).data('id');
            //     $.ajax({
            //         url: '/admin/get-center-update-details/' + centerId,
            //         method: 'GET',
            //         success: function(data) {
            //
            //             $('#update_center_name').val(data.name);
            //             $('#update_center_location_name').val(data.center_location_name);
            //             $('#update-center-latitudes').val(data.lat);
            //             $('#update-center-longitudes').val(data.lng);
            //             $('#preferred_center_radius').val(data.preferred_center_radius);
            //             $('#selected_center_id').val(data.id);
            //
            //         },
            //         error: function(xhr, status, error) {
            //             console.error(xhr.responseText);
            //         }
            //     });
            // });

            $('.btn-delete').click(function() {
                var centerId = $(this).data('id');
                $.ajax({
                    url: '/admin/get-center-update-details/' + centerId,
                    method: 'GET',
                    success: function(data) {

                        $('#delete_selected_center_id').val(data.id);
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            });



            var max_fields = 5000;
            var addButton = $('.btn_delivery_center');
            var wrapper = $('.delivery_route_centers');
            var centersForm =
                ' <div class="col-12 all_centers_array"> <div class="col-lg-2 col-md-2 col-xs-12 col-sm-6"> <div class="form-group"> <label for="name" class="control-label"> Center Name </label> <input type="text" class="form-control" name="center_name[]" id="name"  placeholder="Delivery center name"  required> </div> </div> <div class="col-lg-3 col-md-3 col-xs-12 col-sm-6"> <div class="form-group"> <label for="center_location_name" class="control-label"> Center Location </label>  <input type="text" name="center_location_name[]" id="center_location_name"  class="form-control google_location" required>  </div>  </div> <div class="col-lg-2 col-md-2 col-xs-12 col-sm-4">  <div class="form-group"> <label for="center-latitude" class="control-label"> Latitude </label>  <input type="text" class="form-control latitude" name="center_latitude[]"  id="center-latitude"   placeholder="Latitude" required> </div> </div> <div class="col-lg-2 col-md-2 col-xs-12 col-sm-4"> <div class="form-group"> <label for="center-longitude" class="control-label "> Longitude </label> <input type="text" class="form-control longitude" name="center_longitude[]"  id="center-longitude"  placeholder="Longitude" required> </div> </div> <div class="col-lg-2 col-md-2 col-xs-12 col-sm-4"> <div class="form-group"> <label for="center-longitude" class="control-label "> Preferred Center Radius </label>  <input type="number" class="form-control" name="preferred_center_radius[]"  id="preferred_center_radius" placeholder="100" min="0" step="0.01" > </div> </div>  <div class="col-lg-1 col-md-1 col-xs-12 col-sm-4"> <div class="form-group add_centers" style="margin-top:25px;"> <button class="btn btn-danger remove_btn_delivery_center" type="button">-</button> </div> </div></div>';

            var x = 1;
            $(addButton).click(function() {
                if (x < max_fields) {
                    x++;
                    let newForm = $(centersForm).clone();
                    wrapper.append(newForm);

                    let locationInput = newForm.find('.google_location');
                    let latitudeInput = newForm.find('.latitude');
                    let longitudeInput = newForm.find('.longitude');

                    initializeLocationSearch(locationInput[0], latitudeInput[0], longitudeInput[0]);

                    var saveCentersBtn = document.getElementById('saveCentersBtn');
                    if (document.querySelector('.all_centers_array').childElementCount > 0) {
                        saveCentersBtn.style.display = 'none';
                    } else {
                        saveCentersBtn.style.display = 'inline-block';
                    }
                } else {
                    alert('Maximum number of ' + max_fields + 'is reached');
                }
            });

            $(wrapper).on('click', '.remove_btn_delivery_center', function(e) {
                e.preventDefault();
                $(this).closest('.all_centers_array').remove();
                if (document.querySelector('.all_centers_array').childElementCount > 0) {
                    saveCentersBtn.style.display = 'inline-block';
                } else {
                    saveCentersBtn.style.display = 'none';
                }
            });

            $('#route-delivery-centerss').DataTable();
            $('#route-delivery-centers').DataTable({
                "processing": true,
                "serverSide": true,
                'searching': true,
                "order": [
                    [0, "desc"]
                ],
                "pageLength": '<?= Config::get('params.list_limit_admin') ?>',
                "ajax": {
                    "url": '{!! route('route-delivery-centers-list', ['routeId' => $route->id, 'all' => 1]) !!}',
                    "dataType": "json",
                    "type": "GET",
                    "data": {
                        _token: "{{ csrf_token() }}"
                    }
                },
                "columns": [{
                        data: 'name',
                        name: 'name',
                        orderable: true
                    },
                    {
                        data: 'center_location_name',
                        name: 'center_location_name',
                        orderable: true
                    },
                    {
                        data: 'preferred_center_radius',
                        name: 'preferred_center_radius',
                        orderable: false
                    },
                    {
                        data: 'points',
                        name: 'points',
                        orderable: false
                    },
                    {
                        data: 'action_links',
                        name: 'action_links',
                        orderable: false
                    }
                ],
                "columnDefs": [{
                    "searchable": false,
                    "targets": 0
                }, ]
            });
        });


        function modalInitializeLocationSearchs() {
            let centerLocationInputs = document.getElementById('update_center_location_name');
            let optionss = {};

            let autocompletes = new google.maps.places.Autocomplete(centerLocationInputs, optionss);
            google.maps.event.addListener(autocompletes, 'place_changed', function() {
                let place = autocompletes.getPlace();
                let lats = place.geometry.location.lat();
                let lngs = place.geometry.location.lng();
                $("#update-center-latitudes").val(lats);
                $("#update-center-longitudes").val(lngs);
            });

        }

        function initializeLocationSearch(locationInput, latitudeInput, longitudeInput) {
            let options = {};

            let autocomplete = new google.maps.places.Autocomplete(locationInput, options);
            google.maps.event.addListener(autocomplete, 'place_changed', function() {
                let place = autocomplete.getPlace();
                let lat = place.geometry.location.lat();
                let lng = place.geometry.location.lng();
                latitudeInput.value = lat;
                longitudeInput.value = lng;
            });
        }

        function initializeRouteLocationSearch() {
            let StartingLocationInput = document.getElementById('starting_location_name');
            let locationoptions = {};

            let autocomplete = new google.maps.places.Autocomplete(StartingLocationInput, locationoptions);

            google.maps.event.addListener(autocomplete, 'place_changed', function() {
                let location_place = autocomplete.getPlace();
                let location_lat = location_place.geometry.location.lat();
                let location_lng = location_place.geometry.location.lng();
                $("#location-latitude").val(location_lat);
                $("#location-longitude").val(location_lng);
            });
        }



        google.maps.event.addDomListener(window, 'load', initializeLocationSearch);
        // google.maps.event.addDomListener(window, 'load', initializeLocationSearchs);
        google.maps.event.addDomListener(window, 'load', initializeRouteLocationSearch);
    </script>
@endsection
