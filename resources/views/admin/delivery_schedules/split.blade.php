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
                    <table class="table table-bordered" >
    <thead>
        <tr>
           
            <th>Delivery Date</th>
            <th>Shift Date</th>
            <th>Delivery No</th>
            <th>Route</th>
            <th>Tonnage </th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
            <tr>
                <td>{{ $splits->expected_delivery_date }}</td>
                <td>{{ $splits->start_time }}</td>
                <td>{{ $splits->delivery_number }}</td>
                <td>{{ $splits->route_name }}</td>
                <td>{{ number_format($splits->shift_tonnage, 2) }}</td>

                <td colspan="5"> <button data-toggle="modal" data-target="#confirm-create-dispatch-modal" data-backdrop="static" class="btn btn-primary btn-sm"><i class="fa fa-columns"></i></button>
                <!--  <button data-toggle="modal" data-target="#confirm-create-combined-modal" data-backdrop="static" class="btn btn-primary">Merge</button> --></td>
            </tr>
      
    </tbody>
  
</table>

  <table class="table table-bordered" >
    <h3>Splits</h3>
    <thead>
        <tr>
           
            <th>Split Date</th>
            <th>Vehicle</th>
            <th>Driver</th>
            <th>Tonnage Initial</th>
        </tr>
    </thead>
    <tbody>
  @if($tonnagesplits->isNotEmpty())
   
    @foreach($tonnagesplits as $split)
        <tr>
            <td>{{ $split->splitdate }}</td>
            <td>{{ $split->numberplate }}</td>
            <td>{{ $split->drivername }}</td>
            <td>{{ number_format($split->tonnange_before, 2) }}</td>
        </tr>
    @endforeach
@else
    <tr>
        <td colspan="4">No records found</td>
    </tr>
@endif
      
    </tbody>
  
</table>
</div>
</div>

 <div class="modal fade" id="confirm-create-dispatch-modal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="box-title"> Split Delivery</h3>


                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>

                    <div class="box-footer">
                        <div class="">
                            <form action="{{ route('route.split-schedules-insert')}}" method="post">
                                @csrf
                               <div class="row"> 
                                <div class="col-md-12">
                                <div class="form-group">
                                  <label>Tonnage </label>
                                  <input type="number" id="tonnage_now" name="tonnage_now" value="{{number_format( $splits->shift_tonnage , 2)}}" class="form-control" readonly>
                             <!--      <label>Tonnage Split</label>
                                  <div id="tonnage_error" style="color: red;"></div>
                                 <input type="number" id="tonnage_split" name="tonnage_split" value="tonnage_split" class="form-control" >
                                 <label>Tonnage Remaining</label>
                                 <input type="number" id="tonnage_remaining" name="tonnage_remaining" value="" class="form-control" readonly>

 -->
                                  <label>Assign Vehicle</label>
                                   <select name="selected_vehicle" id="selected_vehicle"
                               class="form-control mlselect">
                           <option value="" selected disabled> Select vehicle</option>
                           @foreach ($vehicles as $vehicle)
                           @if ($vehicle->isAvailable  == 1)
                           <option value="{{ $vehicle->id }}">
                           {{$vehicle->name }} {{ $vehicle->license_plate_number }} ( {{ $vehicle->driver->name }} )
                        </option>
                               
                           @endif  
                           @endforeach
                           
                       </select>

                       <input  id="schedule_id" name="schedule_id" value="{{ $splits->schedule_id }}"  hidden="">
                                </div>
                              </div>
                            </div>
                                <button type="submit" class="btn btn-primary">Save</button>
                            </form>


                        </div>
                    </div>
                </div>
            </div>
        </div>

</section>
@endsection

@section('uniquepagestyle')
    <link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet"/>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet">

@endsection

@section('uniquepagescript')
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"/>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
</script>
  <script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
    <script type="text/javascript">
          $('body').addClass('sidebar-collapse');
 $(document).ready(function() {
        $('.select2').select2();
    });

   
   
    var tonnageNowInput = document.getElementById('tonnage_now');
    var tonnageSplitInput = document.getElementById('tonnage_split');
    var tonnageRemainingInput = document.getElementById('tonnage_remaining');
    var tonnageErrorDiv = document.getElementById('tonnage_error');
  
    tonnageSplitInput.addEventListener('input', function() {
        var tonnageNow = parseFloat(tonnageNowInput.value);
        var tonnageSplit = parseFloat(tonnageSplitInput.value);

        
        if (!isNaN(tonnageNow) && !isNaN(tonnageSplit)) {
            
            if (tonnageSplit > tonnageNow) {
                tonnageErrorDiv.textContent = "Tonnage Split cannot exceed Tonnage Now";
                tonnageRemainingInput.value = "";
                tonnageRemainingInput.setAttribute("disabled", "disabled");
            } else {
                var tonnageRemaining = (tonnageNow - tonnageSplit).toFixed(2); 
                tonnageRemainingInput.value = tonnageRemaining;
                tonnageErrorDiv.textContent = "";
                tonnageRemainingInput.removeAttribute("disabled");
            }
        } else {
           
            tonnageErrorDiv.textContent = "";
            tonnageRemainingInput.value = "";
            tonnageRemainingInput.setAttribute("disabled", "disabled");
        }
    });



       

      
    </script>
@endsection