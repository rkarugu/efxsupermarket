@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title"> Fuel Purchase Orders </h3>
                    {{-- <a href="{{ route("$base_route.create") }}" class="btn btn-primary"> Create LPO </a> --}}
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <div class="table-responsive">
                    <table class="table" id="create_datatable">
                        <thead>
                        <tr>
                            <th scope="col"> #</th>
                            <th scope="col"> Shift Date</th>
                            <th scope="col"> LPO Number</th>
                            <th scope="col"> Shift Type</th>
                            <th scope="col"> Shift Description</th>
                            <th scope="col"> Document Number</th>
                            <th scope="col"> Vehicle</th>
                            <th scope="col"> Fueling Branch</th>
                            {{-- <th scope="col"> Action</th> --}}
                  
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($lpos as $index => $lpo)
                            <tr>
                                <th scope="row"> {{ $index + 1 }}</th>
                                <td> {{ \Carbon\Carbon::parse($lpo->created_at)->toDateString() }} </td>
                                <td style="text-align: center;"> {{ $lpo->lpo_number }} </td>
                                <td> {{ $lpo->shift_type }} </td>
                                @if ($lpo->shift_type == 'Miscellaneous')
                                <td> {{ $lpo->comments ?? '' }} </td>

                                @else
                                <td> {{ $lpo->getRelatedShift?->route?->route_name }} </td>
                                    
                                @endif
                                @if($lpo->getRelatedShift)
                                    <td style="text-align: center;"> <a href="{{route('salesman-shift-details', $lpo->getRelatedShift?->shift_id)}}" target="_blank"> {{ $lpo->getRelatedShift?->delivery_number }}</a></td>
                                @else
                                    <td style="text-align: center;"> - </td>
                                    @endif
                                <td> {{ $lpo->getRelatedVehicle?->license_plate_number. ' ('.$lpo->getRelatedVehicle?->driver?->name . ')' }} </td>
                                <td>{{ $lpo->getRelatedShift?->route?->branch->name }}</td>
                                {{-- <td>
                                    <div class="action-button-div">
                                        @if ($permission == 'superadmin' || isset($permission['fuel-lpos___archive']))
                                            <button  class="expire-lpo" style="background:transparent; border:none;" data-lpo-id="{{$lpo->id}}"><i class="fa-solid fa-eye-slash" style="color: red;"></i></button>
                                        @endif
                                    </div>
                                </td> --}}
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
    <div class="modal fade" id="confirmationModal2" data-backdrop="static" data-keyboard="false" tabindex="-1"
aria-labelledby="staticBackdropLabel" aria-hidden="true">
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title" id="staticBackdropLabel">Are you sure you want to Expire this Lpo?</h4>
           
        </div>
        <form method="POST" id="confirmationForm2" action="">
            @csrf
            @method("DELETE")
            
            <input name="user_requested_access2" type="hidden" id="user_requested_access2"
                    value="{{ old('user_requested_access2') }}" required />
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <a class="btn btn-success btn-submit-updated-center2"  id="delete-action" href="">Yes, Expire</a>
            </div>
        </form>
    </div>
</div>
</div>
@endsection

@section('uniquepagestyle')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet"/>
@endsection

@section('uniquepagescript')
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>

    <script>
        $(document).ready(function () {
            $(".mlselect").select2();
            $("body").addClass('sidebar-collapse');
            $('.expire-lpo').click(function() {
            var id = $(this).data('lpo-id');
            $('#delete-action').attr('href', '{{ route('fuel-lpos.expire', ['id' => ':id']) }}'.replace(':id', id));
            $('#confirmationModal2').modal('show');

        });
        });
    </script>
@endsection