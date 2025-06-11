@extends('layouts.admin.admin')

@section('content')
    <section class="content" id="order-taking-overview">
        <div class="modal fade" id="confirmDownloadModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="box-title"> Mark Schedule as Complete</h3>

                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>
                    <form method="POST" class="complete-schedule-form" id="complete-schedule-form">
                        @csrf

                        <div class="box-body">
                            <div class="form-group">
                                <label for="comment">Comment * </label><br>
                                <textarea class="form-control" name="comment" id="comment" cols="90" rows="7" placeholder="Please provide a detailed comment" required></textarea>
                            </div>
                        </div>

                        <div class="box-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <button type="submit" id="confirmDownloadBtn" class="btn btn-primary">Complete</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title"> {{ $title }}</h3>
                    <div class="d-flex">
                        @if ($logged_user_info->role_id == 1 || isset($my_permissions['route-customers___HQ-approve']))
                            @if ($schedule->status == 'completed')
                                <a href="{{route('mark-geomapping-schedule-as-Hq-approved', $schedule->id)}}" class="btn btn-success" style="margin-right:2px;">Approve</a>
                            @endif
                        @endif
                        @if ($logged_user_info->role_id == 1 || isset($my_permissions['route-customers___mark-complete']))
                            @if ($schedule->status == 'incomplete')
                                <a href="" class="btn btn-success download-link" data-schedule-id="{{ $schedule->id }}" style="margin-right:2px;">Complete</a>
                            @endif
                        @endif

                        <form action="{{route('geomapping-schedules.show', $schedule->id)}}" method="GET">
                        <input type="submit" name="download" value="Download" class="btn btn-success">  
                        <a href="{{route('geomapping-schedules.index')}}" class="btn btn-success" style="margin-right:2px;">Back</a> 
                        </form>   
                    </div>
                </div>

            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <label for="">Day</label>
                        <p>
                            @if ($schedule->date)
                                {{ \Carbon\Carbon::parse($schedule->date)->toFormattedDayDateString() }}
                            @else
                                -
                            @endif
                        </p>
                    </div>
                    <div class="col-md-3">
                        <label for="">Branch</label>
                        <p>{{ $schedule->branchDetails?->name }}</p>
                    </div>
                    <div class="col-md-3">
                        <label for="">Route</label>
                        <p>{{ $schedule->route?->route_name }}</p>
                    </div>
                    <div class="col-md-3">
                        <label for="">SalesMan</label>
                        <p>{{ $schedule->route->salesman() ? $schedule->route->salesman()->name : 'Not Assigned' }}</p>
                    </div>
                    <div class="col-md-3">
                        <label for="">Route Manager</label>
                        <p>{{ $schedule->supervisor ?? '-' }}</p>
                    </div>
                    <div class="col-md-3">
                        <label for="">Supervisor</label>
                        <p>{{ $schedule->route_manager }}</p>
                    </div>
                    <div class="col-md-3">
                        <label for="">Bizwiz Rep</label>
                        <p>{{ $schedule->bizwiz_rep }}</p>
                    </div>
                    <div class="col-md-3">
                        <label for="">Status</label>
                        <p>{{ $schedule->status }}</p>
                    </div>

                </div>

            </div>
            <hr>

            <div id="top-cards" class="d-flex justify-content-between">
                <div class="major-detail d-flex flex-column justify-content-between border-success">
                    <div class="d-flex">
                        {{-- <i class="fas fa-users major-detail-icon"></i> --}}
                        <span class="major-detail-title"> Existing Customers </span>
                    </div>

                    <span class="major-detail-value"> {{ $page_stats['total_customers'] - $page_stats['new_customers'] }}
                    </span>
                </div>
                <div class="major-detail d-flex flex-column justify-content-between border-primary">
                    <div class="d-flex">
                        <span class="major-detail-title"> New Customers </span>
                    </div>

                    <span class="major-detail-value"> {{ $page_stats['new_customers'] }} </span>
                </div>
                <div class="major-detail d-flex flex-column justify-content-between border-success">
                    <div class="d-flex">
                        <span class="major-detail-title"> Geomapped </span>
                    </div>

                    <span class="major-detail-value"> {{ $page_stats['verified_count'] }} </span>
                </div>

                <div class="major-detail d-flex flex-column justify-content-between border-info">
                    <div class="d-flex">
                        <span class="major-detail-title"> Remaining </span>
                    </div>

                    <span class="major-detail-value"> {{ $page_stats['unverified_count'] }} </span>
                </div>

                <div class="major-detail d-flex flex-column justify-content-between border-danger">
                    <div class="d-flex">
                        <span class="major-detail-title"> Centers </span>
                    </div>

                    <span class="major-detail-value"> {{ $page_stats['visited_centers'] }} /
                        {{ $page_stats['centers_in_route'] }} </span>
                </div>
                <div class="major-detail d-flex flex-column justify-content-between border-success">
                    <div class="d-flex">
                        <span class="major-detail-title"> Percentage Complete </span>
                    </div>

                    <span class="major-detail-value"> {{ $page_stats['percentage_verified'] }} %</span>
                </div>
            </div>


            <div class="mt-20 w-100">
                <div class="box">

                    <div class="box-header with-border">
                        <h3 class="box-title"> Geomapped Customers </h3>
                    </div>

                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table" id="customerTable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th> Time Attended</th>
                                        <th> Center</th>
                                        <th> Business Name</th>
                                        <th> Customer Name</th>
                                        <th> Phone Number</th>
                                        <th> Status</th>
                                        <th> Comment</th>
                                        <th> Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
            @if ($page_stats['new_customers'] > 0)
                <div class="mt-20 w-100">
                    <div class="box-header with-border">
                        <h3 class="box-title"> New Geomapped Customers </h3>
                    </div>

                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table" id="create_datatable_50">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th> Time Attended</th>
                                        <th> Center </th>
                                        <th> Business Name</th>
                                        <th> Customer Name</th>
                                        <th> Phone Number</th>
                                        <th> Status </th>
                                        <th> Comment </th>
                                        <th> Action </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($newCustomers as $customer)
                                        <tr>
                                            <td>{{ $loop->index + 1 }}</td>
                                            <td>{{ \Carbon\Carbon::parse($customer->updated_at)->toTimeString() }}</td>
                                            <td>{{ $customer->center?->name }}</td>
                                            <td>{{ $customer->bussiness_name }}</td>
                                            <td>{{ $customer->name }}</td>
                                            <td>{{ $customer->phone }}</td>
                                            <td>{{ $customer->status }}</td>
                                            <td>{{ $customer->comment }}</td>
                                            <td>
                                                <a href="{{ route('route-customers.show-custom', [$customer->id, 'geomapping-schedules', 'schedule_id'  => $schedule->id ]) }}"
                                                    title="View Route Customer">
                                                    <i class="fa fa-eye text-info fa-lg"></i></a>
                                            </td>

                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
            @if ($page_stats['new_centers'] > 0)
                <div class="mt-20 w-100">
                    <div class="box-header with-border">
                        <h3 class="box-title"> New Centres </h3>
                    </div>

                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table" id="create_datatable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th> Name</th>
                                        <th> Location </th>
                                        <th> Latitude</th>
                                        <th> Longitude</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($newCenters as $center)
                                        <tr>
                                            <td>{{ $loop->index + 1 }}</td>
                                            <td>{{ $center->name }}</td>
                                            <td>{{ $center->center_location_name ?? '-' }}</td>
                                            <td>{{ $center->lat }}</td>
                                            <td>{{ $center->lng }}</td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
            <div class="mt-20 w-100">
                <div class="box-header with-border">
                    <h3 class="box-title"> Locations </h3>
                </div>
                <div class="box-body">
                    <div id="map" style="width: 100%; height: 400px;"></div>
                </div>

            </div>


        </div>
        </div>
    </section>
@endsection


@section('uniquepagestyle')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />

    <style>
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
            text-align: center;
        }

        #activity {
            position: relative;
            width: 40%;
        }

        .mt-20 {
            margin-top: 30px !important;
        }

        .popup-bubble {
            position: absolute;
            top: 0;
            left: 0;
            transform: translate(-50%, -100%);
            background-color: white;
            padding: 13px 30px;
            /* white-space: nowrap; */
            border-radius: 5px;
            font-family: sans-serif;
            overflow-y: auto;
            max-height: 60px;
            max-width: 250px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
        }

        .popup-bubble-anchor {
            position: absolute;
            width: 500px !important;
            bottom: 8px;
            left: 0;
        }

        /* .popup-bubble-anchor::after {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            transform: translate(-50%, -50%);
            width: 0;
            height: 0;
            border-left: 6px solid transparent;
            border-right: 6px solid transparent;
            border-top: 8px solid white;
            } */

        /* JavaScript will position this div at the bottom of the popup tip. */
        .popup-container {
            cursor: pointer !important;
            height: 0;
            position: absolute;
            max-width: 350px !important;
        }

        .price-tag {
            /* background-color: darkblue; */
            border-radius: 50%;
            /*color: #FFFFFF;*/
            /*padding: 15px;*/
            position: relative;
            width: 45px;
            height: 45px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .price-tag i {
            color: #fff;
        }

        .truck-icon {
            font-size: 20px;
        }

        .device-name {
            max-height: 10px;
            padding: 1px !important;
            margin: 1px !important;
            cursor: pointer !important;
        }

        .device-name {
            font-weight: bold !important;
            color: white !important;
            text-decoration: none !important;

        }

        .device-name a {
            text-decoration: none !important;
        }
    </style>
@endsection

@section('uniquepagescript')
    <script src="https://maps.googleapis.com/maps/api/js?key={{ $googleMapsApiKey }}"></script>
    <script src="{{ asset('assets/admin/dist/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('.download-link').on('click', function (event) {
                event.preventDefault();
                var scheduleId = $(this).data('schedule-id');
                $('#complete-schedule-form').attr('action', "{{ url('admin/geomapping-summary/mark-complete/') }}/" + scheduleId);
                $('#confirmDownloadModal').modal('show');
            });
            $("#route_id").select2();
            $(".mlselec6t").select2();
            let map;
            let markers = [];
            let popups = [];
            class Popup extends google.maps.OverlayView {
                constructor(position, content, backgroundColor) {
                    super();
                    this.position = position;
                    this.containerDiv = document.createElement("div");
                    this.containerDiv.classList.add("popup-bubble");
                    this.containerDiv.style.backgroundColor = backgroundColor;

                    const bubbleAnchor = document.createElement("div");
                    bubbleAnchor.classList.add("popup-bubble-anchor");
                    bubbleAnchor.appendChild(content);

                    this.containerDiv.appendChild(bubbleAnchor);

                    Popup.preventMapHitsAndGesturesFrom(this.containerDiv);
                }

                onAdd() {
                    this.getPanes().floatPane.appendChild(this.containerDiv);
                }

                onRemove() {
                    if (this.containerDiv.parentElement) {
                        this.containerDiv.parentElement.removeChild(this.containerDiv);
                    }
                }

                draw() {
                    const divPosition = this.getProjection().fromLatLngToDivPixel(this.position);
                    const display = Math.abs(divPosition.x) < 4000 && Math.abs(divPosition.y) < 4000 ?
                        "block" : "none";

                    if (display === "block") {
                        this.containerDiv.style.left = (divPosition.x) + "px";
                        this.containerDiv.style.top = (divPosition.y - this.containerDiv.offsetHeight) +
                            "px";
                    }

                    if (this.containerDiv.style.display !== display) {
                        this.containerDiv.style.display = display;
                    }
                }

                open(map) {
                    this.setMap(map);
                }

                close() {
                    this.setMap(null);
                }
            }

            var table = $("#customerTable").DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                pageLength: 100,
                ajax: {
                    url: '{!! route('schedules-geomapping.customer-serve-time', $schedule->id) !!}',
                    data: function(data) {
                        var route_id = $('#route_id').val();
                        var from = $('#start_date').val();
                        var to = $('#end_date').val();
                        data.from = from;
                        data.to = to;
                        data.route_id = route_id;
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        searchable: false,
                        sortable: false,
                        width: "70px"
                    },
                    {
                        data: "updated_at",
                        name: "updated_at"
                    },
                    // {
                    //     data: "route.route_name",
                    //     name: "route.route_name"
                    // },
                    {
                        data: "center.name",
                        name: "center.name"
                    },
                    {
                        data: "bussiness_name",
                        name: "bussiness_name"
                    },
                    {
                        data: "name",
                        name: "name"
                    },
                    {
                        data: "phone",
                        name: "phone"
                    },
                    {
                        data: "status",
                        name: "status"
                    },
                    {
                        data: "comment",
                        name: "comment"
                    },
                    // {
                    //     data: "time_taken",
                    //     name: "time_taken",
                    //     searchable: false
                    // },
                    {
                        data: "action",
                        name: "action",
                        searchable: false
                    }
                ],

            });

            $('#filter').click(function(e) {
                e.preventDefault();
                table.draw();
            });

            async function fetchLocations() {
                results = {!! $allCustomers !!}
                newCentersLocations = {!!  $newCentersLocations !!}

                markers.forEach(marker => marker.setMap(null));
                markers = [];
                popups.forEach(popup => popup.close());
                popups = [];
                //attempt drawing delivery center
                newCentersLocations.forEach((center) => {
                    if (center.has_valid_location) {
                                // drawDeliveryCenter(center.lat, center.lng, center.preferred_center_radius)
                        const drawnCenter = new google.maps.Circle({
                        strokeColor: "#FF0000",
                        strokeOpacity: 0.8,
                        strokeWeight: 2,
                        fillColor: "#FF0000",
                        fillOpacity: 0.35,
                        map,
                        center: {lat: center.lat, lng: center.lng},
                        radius: center.preferred_center_radius,
                    });

                    // drawnCenter.setMap(map)


                                }
                });
                results.forEach((location, index) => {
                    const backgroundColor = 'green';
                    const icon = document.createElement("div");
                    icon.className = "price-tag";
                    icon.innerHTML =
                        `<i class="fas fa-map-marker truck-icon" style="color:green !important;"></i>`;
                    const content = document.createElement("div");
                    content.innerHTML = `<div class="device-name">${location.bussiness_name}</div>`;
                    const popup = new Popup(
                        new google.maps.LatLng(location.lat, location.lng),
                        content,
                        backgroundColor
                    );
                    // const infowindow = new google.maps.InfoWindow({
                    //     content: contentString,
                    //     ariaLabel: "",

                    // });

                    const marker = new google.maps.marker.AdvancedMarkerElement({
                        map: map,
                        position: {
                            lat: location.lat,
                            lng: location.lng,
                        },
                        title: location.bussiness_name,
                        content: icon,
                    });
                    if (index === results.length - 1) {
                        map.setCenter({
                            lat: location.lat,
                            lng: location.lng,
                        });
                    }

                    // infowindow.open({
                    // anchor: marker,
                    // map,
                    // });
                    marker.bussiness_name = location.bussiness_name;
                    markers.push(marker);
                    popup.open(map);
                    popups.push(popup);

                });


            }
            // function drawDeliveryCenter(lat, lng, radius) {
            //         const drawnCenter = new google.maps.Circle({
            //             strokeColor: "#FF0000",
            //             strokeOpacity: 0.8,
            //             strokeWeight: 2,
            //             fillColor: "#FF0000",
            //             fillOpacity: 0.35,
            //             center: {lat: lat, lng: lng},
            //             radius: radius
            //         });

            //         drawnCenter.setMap(map)
            //     }
            async function initMap() {
                const position = {
                    lat: -1.034444,
                    lng: 37.076805
                };
                const {
                    Map
                } = await google.maps.importLibrary("maps");
                const {
                    AdvancedMarkerElement,
                    PinElement
                } = await google.maps.importLibrary("marker");

                map = new Map(document.getElementById("map"), {
                    zoom: 18,
                    center: position,
                    mapId: "7c9bd9e078617725",
                });
                const markers = new AdvancedMarkerElement({
                    map: map,
                    position: position,
                    title: "test",
                });
                await fetchLocations();

            }
            // window.addEventListener('load', () => {
            //     initMap();
            // });
            setTimeout(function() {
                initMap();
            }, 1000);
        })
    </script>
@endsection
