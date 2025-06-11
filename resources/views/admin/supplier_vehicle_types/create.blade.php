@extends('layouts.admin.admin')
@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> {!! $title !!}</h3>
                </div>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <form action="{{ route('supplier-vehicle-type.store') }}" method="POST" class="submitMe">
                        @csrf
                        <div class="form-group">
                            <label>Name:</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}">
                        </div>
                        <div class="form-group">
                            <label>Vehicle Type:</label>
                            <input type="text" name="vehicle_type" class="form-control" value="{{ old('vehicle_type') }}">
                        </div>
                        <div class="form-group">
                            <label>Tonnage (Max Weight):</label>
                            <input type="number" name="tonnage" class="form-control" value="{{ old('tonnage') }}">
                        </div>
                        <div class="form-group">
                            <label>Offloading Time:</label>
                            <select name="offloading_time" class="form-control">
                                @php
                                    $start = strtotime('00:00');
                                    $end = strtotime('23:30');
                                    while ($start <= $end) {
                                        $time = date('H:i', $start);
                                        echo "<option value=\"$time\">$time hrs</option>";
                                        $start = strtotime('+30 minutes', $start);
                                    }
                                @endphp
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Create</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('uniquepagescript')
<div id="loader-on" style="
        position: absolute;
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

<script src="{{asset('js/sweetalert.js')}}"></script>
<script src="{{ asset('js/form.js') }}"></script>
@endsection