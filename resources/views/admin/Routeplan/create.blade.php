@extends('layouts.admin.admin')

@section('content')
    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border no-padding-h-b">
                <h4>Add Route Plan</h4>
                <form action="{{ route('create-route-plan.store') }}" method="post" class="submitMe">
                    {{ csrf_field() }}

                    {{-- <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Route</label>
                        <div>
                            <select name="route_id" id="route_id" class="form-control">
                                <option value="">Select Route</option>
                                @foreach ($routes as $route)
                                    <option value="{{ $route->id }}">{{ $route->route_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div> --}}

                    <input type="hidden" value="{{ $route_id }}" name="route_id" id="route_id" class="form-control"
                        placeholder="Distance" aria-describedby="helpId">

                    <div class="form-group">
                        <label for="distance">Total Distance</label>
                        <input type="number" name="total_distance" id="total_distance" class="form-control"
                            placeholder="Distance" aria-describedby="helpId">
                    </div>

                    <div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="time">Total Time</label>
                                <input type="number" name="total_time" id="total_time" class="form-control"
                                    placeholder="Time" aria-describedby="helpId">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="route_name">Total Fuel</label>
                                <input type="number" name="total_fuel" id="total_fuel" class="form-control"
                                    placeholder="Fuel" aria-describedby="helpId">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="route_name">Start Location Name</label>
                            <input type="text" name="start_search_location" id="start_search_location"
                                class="form-control" placeholder="Center Name" aria-describedby="helpId">
                        </div>

                        <div class="form-group">
                            <label for="route_name">End Location Name</label>
                            <input type="text" name="end_search_location" id="end_search_location" class="form-control"
                                placeholder="Center Name" aria-describedby="helpId">
                        </div>

                        <div class="form-group">
                            <label for="route_name">Start Time</label>
                            <input type="time" name="start_time" id="start_time"
                                class="form-control" aria-describedby="helpId">
                        </div>

                        <div class="form-group">
                            <label for="route_name">End Time</label>
                            <input type="time" name="end_time" id="end_time"
                                class="form-control" aria-describedby="helpId">
                        </div>
                    </div>


                    <div class="col-md-12 no-padding-h">
                        <table class="table table-bordered table-hover" id="">
                            <thead>
                                <tr>
                                    <th>#</th>

                                    <th>Center Name</th>
                                    <th>Duration</th>


                                    <!--th style = "width:10%" class = "noneedtoshort">Date</th-->

                                </tr>
                            </thead>
                            <tbody id="centers_table">
                                @if (isset($centers) && !empty($centers))
                                    <?php $b = 1; ?>
                                    @foreach ($centers as $center)
                                        <tr>
                                            <td>
                                                <div style="color:red; padding-left: 10px; float: left; font-size: 20px; cursor: pointer;"
                                                    title="change display order">
                                                    <i class="fa fa-ellipsis-v"></i>
                                                    <i class="fa fa-ellipsis-v"></i>
                                                </div>
                                            </td>

                                            <td>

                                                <label for="delivery_center">{{ $center->name }}</label>

                                                <input type="hidden" name="delivery_center_id[]" id="delivery_center_id[]"
                                                    class="form-control" placeholder="{{ $center->name }}"
                                                    value="{{ $center->id }}" aria-describedby="helpId">




                                            </td>


                                            <td>
                                                <input type="number" name="duration[]" id="duration[]" class="form-control"
                                                    value="0" aria-describedby="helpId">




                                            </td>



                                        </tr>
                                        <?php $b++; ?>
                                    @endforeach
                                @endif


                            </tbody>
                        </table>
                    </div>

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
    <div id="loader-on"
        style="
position: fixed;
top: 0;
text-align: center;
display: block;
z-index: 999999;
width: 100%;
height: 100%;
background: #000000b8;
display:none;
"
        class="loder">
        <div class="loader" id="loader-1"></div>
    </div>
    <script src="{{ asset('js/sweetalert.js') }}"></script>
    <script src="{{ asset('js/form.js') }}"></script>
    <script type="text/javascript" src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    <script src="http://maps.googleapis.com/maps/api/js?libraries=places&key=AIzaSyAcu43Sc8RemuQGR4BUh9ZJiYTWF2EPlVk">
    </script>


    <script type="text/javascript">
        $(function() {

            $("#centers_table").sortable({
                items: "tr",
                cursor: 'move',
                opacity: 0.6,
                update: function() {

                }
            });


        });
    </script>

    <script type="text/javascript">
        function initialize() {
            var start_input = document.getElementById('start_search_location');
            var end_input = document.getElementById('end_search_location');

            var options = {};

            var autocomplete = new google.maps.places.Autocomplete(start_input, options);
            var end_autocomplete = new google.maps.places.Autocomplete(end_input, options);

            google.maps.event.addListener(autocomplete, 'place_changed', function() {
                var place = autocomplete.getPlace();
                var lat = place.geometry.location.lat();
                var lng = place.geometry.location.lng();
                $("#start_lat").val(lat);
                $("#start_lng").val(lng);
            });


            google.maps.event.addListener(end_autocomplete, 'place_changed', function() {
                var place = autocomplete.getPlace();
                var lat = place.geometry.location.lat();
                var lng = place.geometry.location.lng();
                $("#end_lat").val(lat);
                $("#end_lng").val(lng);
            });
        }

        google.maps.event.addDomListener(window, 'load', initialize);
    </script>
@endsection
