<div style="padding:10px">
    <div class="row" style="padding-bottom:10px;">        
        <div class="col-md-2">
            <label for="date">Start</label>
            <input type="datetime-local" class="form-control" name="date" id="date" value="{{\Carbon\Carbon::now()->startOfDay()}}">
        </div>
     
        <div class="col-md-2" >
            <label for="date">End</label>
            <input type="datetime-local" class="form-control" name="to_date" id="to_date" value="{{\Carbon\Carbon::now()->endOfDay()}}">
        </div>
    </div>
    <div class="row" style="margin: 0; padding: 0;">
        <div class="col-md-7" id="map-view">

        </div>
    </div>
    <div id="custom-control" class="box-primary box">
        
        <p>
            <span><i class="fas fa-tachometer-alt speed"></i></span> Speed - <span name="speed"
                id="speed"></span> Km/hr
        </p>
        <p>
            <span><i class="fas fa-road mileage"></i></span> Mileage - <span name="mileage"
                id="mileage"></span> Kms
        </p>
        <p>
            <span><i class="fas fa-gas-pump fuel"></i></span> Fuel - <span name="fuel" id="fuel"></span> lts
        </p>
    </div>
</div>

@section('uniquepagestyle')
    <style>
        #map-view,
        #details-view {
            position: relative;
            /* height: 800px; */
            height: 80vh;
            width: 100%;
        }

        #custom-control {
            position: absolute;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
            z-index: 1;
            right: 55px;
            bottom: 0;
            width: 200px;
            min-height: 150px;
            background-color: white;


        }

        .moving {
            margin-right: 2px;
            color: blue;
        }

        .stationery {
            margin-right: 2px;
            color: red;
        }

        .blue-vehicle i {
            color: blue !important;
        }

        .red-vehicle i {
            color: red !important;
        }

        .olive-vehicle i {
            color: olive !important;
        }

        .green-vehicle i {
            color: green !important;
        }

        #details-view {
            overflow-y: auto;
            padding: 15px;
        }

        /* #vehicle-name {
                    margin: 0;
                    font-weight: 700;
                    font-size: 22px;
                } */

        #vehicle-address {
            font-size: 15px;
            font-weight: 500;
        }

        .major-detail {
            border: 1px solid rgba(0, 0, 0, .125);
            border-radius: 15px;
            padding: 10px 15px;
            height: 80px;
            min-width: 200px;
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

        .price-tag {
            /* border-radius: 50%;
            position: relative;
            width: 40px;
            height: 40px;
            display: flex;
            justify-content: center;
            align-items: center; */

        }

        .price-tag i {
            color: #fff;
        }

        .siteNotice {
            font-size: 4px;
        }

        .truck-icon {
            font-size: 20px;
            color: black !important;
        }
    </style>
@endsection

