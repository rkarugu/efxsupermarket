
@extends('layouts.admin.admin')
@section('content')
<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="box-title"> Edit Return Reasons  </h3>            
                <a href="{{  route('return-reasons.index') }}" class="btn btn-success btn-sm"><i class="fas fa-arrow-left"></i> Back</a>
            
            </div>

        </div>
    
        {{-- @include('message') --}}
        <form class="validate form-horizontal"  role="form" method="POST" action="{{ route('return-reasons.update', $returnReason->id) }}" enctype = "multipart/form-data">
            @method('PUT')
            {{ csrf_field() }}
            <div class="box-body">
                <div class="form-group">
        
              
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Reason</label>
                    <div class="col-sm-9">
                        {!! Form::text('reason', $returnReason->reason, ['maxlength'=>'255','placeholder' => 'enter reason', 'required'=>true, 'class'=>'form-control']) !!}  
                   
                    </div>
                    
                </div>

                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Use For Pos</label>
                    <input type="hidden" name="use_for_pos" value="0">
                    <input type="checkbox" name="use_for_pos" id="use_for_pos" value="1" {{ $returnReason->use_for_pos ? 'checked' : '' }}>                    
                    
                </div>
                </div>              
            </div>  
            <div class="box-footer" >
                <button type="submit" class="btn btn-success btn-sm pull-right" >Submit</button>
            </div>
        </form>
    </div>
</section>
@endsection
@section('uniquepagestyle')
    <link rel="stylesheet" href="{{ asset('assets/admin/dist/datepicker.css') }}">
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet"/>
@endsection

@section('uniquepagescript')
    <script src="{{ asset('assets/admin/dist/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript">
        $(function () {

            $(".mlselect").select2();
        });
    </script>

    <script>
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd'
        });
    </script>



    <script type="text/javascript" class="init">
        $(document).ready(function () {
            $('#create_datatable1').DataTable({
                pageLength: "100",
                "order": [
                    [0, "desc"]
                ]
            });
        });
        $(document).ready(function() {
    $('#branch').change(function() {
        var branchId = $(this).val(); 

        $.ajax({
            url: '{{ route("get.routes.by.branch") }}', 
            type: 'GET',
            data: {branch_id: branchId},
            success: function(response) {
                $('#routes').empty();
                $.each(response.routes, function(key, value) {
                    $('#routes').append('<option value="' + value.id + '">' + value.route_name + '</option>');
                });
            }
        });
    });
});
    </script>
    
@endsection



