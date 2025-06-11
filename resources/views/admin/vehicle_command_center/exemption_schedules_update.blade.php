@extends('layouts.admin.admin')
@section('content')
<section class="content" style="padding-bottom:0px;">
    <div class="box box-primary" style="margin-bottom: 0px;">
        <div class="box-header with-border">
            <div class="box-header-flex">
                <h4 class="box-title" style="font-weight: 500;">Exempt Vehicles From {{$schedule->schedule_type}}  on {{\Carbon\Carbon::parse($schedule->created_at)->toDateString()}} </h4>
                <div>
                    <a href="{{route('exemption-schedules')}}" class="btn btn-success">Back</a>
                </div>
            </div>
        </div>
        <div class="session-message-container">
            @include('message')
        </div>
        <form action="{{route('exemption-schedules-update', $schedule->id)}}" method="POST">
            @csrf
            <div class="row">
                <div class="col-sm-12">
                    <div class="box-body" style="height: auto;">
                        <div class="form-group" style="margin-bottom: 0px">
                            <label for="vehicles" class="col-sm-4 text-center" style="padding-left:0px;">VEHICLES:</label>
                            <div class="col-sm-8">
                                <select name="vehicle[]" id="vehicle" class="select2 form-control" multiple>
                                    <option value="select-all">Select All</option>
                                    @foreach ($vehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}" 
                                            @if(in_array($vehicle->id, $selectedVehicleIds)) selected @endif>
                                            {{ $vehicle->license_plate_number }}
                                        </option>   
                                    @endforeach
                                </select>
                            </div>

                        </div>
                    </div>
                   
                    <div class="col-lg-12" id="action-div">
                        <div class="box-body text-right">
                            <button class="btn btn-primary">
                                Save
                            </button>
                        </div>
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

        $('#branch').change(function() {
                    var branchId = $(this).val();
                    var url = $(this).data('url');
        
                    $.ajax({
                        url: url,
                        type: 'GET',
                        data: { branch_id: branchId },
                        success: function(data) {
                            $('#vehicle').empty();
                            $('#vehicle').append('<option value="select-all">Select All</option>');
        
                            $.each(data.vehicles, function(key, value) {
                                $('#vehicle').append('<option value="' + value.id + '">' + value.license_plate_number + '</option>');
                            });
                        },
                        error: function(xhr, status, error) {
                            console.error(error);
                        }
                    });
                });
            $('#action').change(function() {
                var selectedAction = $(this).val();
                if (selectedAction === 'custom') {
                    $('#command-box').show();
                    $('#command').prop('required', true);
                } else {
                    $('#command-box').hide();
                    $('#command').prop('required', false);
                }
                if (selectedAction === 'reset-mileage') {
                    $('#mileage-input').show();
                    $('#mileage').prop('required', true);
                } else {
                    $('#mileage-input').hide();
                    $('#mileage').prop('required', false);
                }
        });
        function adjustSelectHeight() {
            var selectedOptions = $('#vehicle').find('option:selected').length;
            var optionHeight = 30; // Approximate height of each option in pixels
            var maxVisibleOptions = 3; // Maximum number of options to show without scrolling
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