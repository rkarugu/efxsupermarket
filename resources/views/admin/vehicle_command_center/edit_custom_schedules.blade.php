@extends('layouts.admin.admin')
@section('content')
<section class="content" style="padding-bottom:0px;">
    <div class="box box-primary" style="margin-bottom: 0px;">
        <div class="box-header with-border">
            <div class="box-header-flex">
                <h4 class="box-title" style="font-weight: 500;">Create Custom Vehicle Schedule</h4>
            </div>
        </div>
        <div class="session-message-container">
            @include('message')
        </div>
        <form action="{{route('custom-schedules.update', $schedule->id)}}" method="POST">
            @csrf
            <div class="row">
                <div class="col-sm-6">
                    <div class="box-body">
                        <div class="form-group" style="margin-bottom: 0px">
                            <label for="date" class="col-sm-4 text-center" style="padding-left: 0px;">DATE:</label>
                            <div class="col-sm-8">
                                <input type="datetime-local" class="form-control" name="date" id="date" value="{{$schedule->time}}" required>
                            </div>
                        </div>
                    </div>
                    <div class="box-body" style="height: auto;">
                        <div class="form-group" style="margin-bottom: 0px">
                            <label for="vehicles" class="col-sm-4 text-center" style="padding-left:0px;">VEHICLES:</label>
                            <div class="col-sm-8">
                                <select name="vehicle[]" id="vehicle" class="select2 form-control" multiple>
                                    <option value="select-all">Select All</option>
                                    @foreach ($vehicles as $vehicle)
                                    <option value="{{$vehicle->id }}"
                                        @if (in_array($vehicle->id, $selectedVehicles)) selected @endif>{{ $vehicle->license_plate_number}}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>
                    </div>
                   
                    <div class="box-body" id="action-div">
                        <div class="form-group" style="margin-bottom: 0px">
                            <label for="action" class="col-sm-4 text-center" style="padding-left: 0px;">ACTION:</label>
                            <div class="col-sm-8">
                                <select name="action" id="action" class="select2 form-control" required>
                                    <option value="" selected disabled>Choose command</option>
                                    <option value="1" @if ($schedule->action == 1) selected @endif>Switch Off</option>
                                    <option value="0" @if ($schedule->action == 0) selected @endif>Switch On</option>
                                </select>
                            </div>  
                        </div>
                    </div>
                    <div class="box-body">
                        <button class="btn btn-primary">
                            Save
                        </button>
                    </div>
                </div>
                <div class="col-sm-6">
                    
                </div>
            </div>
        </form>
    </div>
</section>
    
@endsection
@section('uniquepagestyle')
<link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet" />
<style>
  .select2-container--default .select2-selection--multiple {
        overflow-y: auto; 
    }

</style>
@endsection
@section('uniquepagescript')
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>

<script>
    $(document).ready(function() {
        var selectAllOption = "select-all";
        
        $('.select2').select2();
        
        $('#vehicle').on('select2:select', function(e) {
            var selectedValue = e.params.data.id;
            
            if (selectedValue === selectAllOption) {
                var allOptions = $('#vehicle').find('option:not([value="select-all"])');
                allOptions.each(function() {
                    $('#vehicle').find('option[value="' + $(this).val() + '"]').prop('selected', true);
                });
                $('#vehicle').trigger('change');
            }
            adjustSelectHeight();
        });

        $('#vehicle').on('select2:unselect', function(e) {
            var unselectedValue = e.params.data.id;
            
            if (unselectedValue === selectAllOption) {
                $('#vehicle').val(null).trigger('change');
            }
            adjustSelectHeight();
        });

        // $('#branch').change(function() {
        //             var branchId = $(this).val();
        //             var url = $(this).data('url');
        
        //             $.ajax({
        //                 url: url,
        //                 type: 'GET',
        //                 data: { branch_id: branchId },
        //                 success: function(data) {
        //                     $('#vehicle').empty();
        //                     $('#vehicle').append('<option value="select-all">Select All</option>');
        
        //                     $.each(data.vehicles, function(key, value) {
        //                         $('#vehicle').append('<option value="' + value.id + '">' + value.license_plate_number + '</option>');
        //                     });
        //                 },
        //                 error: function(xhr, status, error) {
        //                     console.error(error);
        //                 }
        //             });
        //         });
      
        function adjustSelectHeight() {
            var selectedOptions = $('#vehicle').find('option:selected').length;
            var optionHeight = 30;
            var maxVisibleOptions = 3; 
            var newHeight = Math.min(selectedOptions, maxVisibleOptions) * optionHeight;
            
            $('#vehicle').next('.select2-container').find('.select2-selection--multiple').css({
                'height': newHeight + 'px',
                'overflow-y': 'auto'
            });
            $('#action-div').css({
                'margin-top':(newHeight - 3) + 'px'
            });
        }

        adjustSelectHeight();
    });
</script>

@endsection