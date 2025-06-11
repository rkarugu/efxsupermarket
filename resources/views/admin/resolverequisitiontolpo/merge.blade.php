@extends('layouts.admin.admin')

@section('content')


    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border no-padding-h-b">
                <div class="row">
                    <div class="col-sm-6">
                       <h3>Merge - {{ $mergeId->purchase_no}}</h3>
                    </div>
                    
                </div>
                <br>
                @include('message')
                <div class="col-md-12 no-padding-h">
                    <form action="{{ route('merge-lpo')}}" method="post">
                        @csrf
                    <table class="table table-bordered table-hover" id="create_datatable">
                        <thead>
                            <tr>
                                
                            <th >S.No.</th>                                
                                
                                <th>Purchase No</th>
                                <th>Date</th>
                                <th>User Name</th>
                                <th>Store Location</th>
                                <th>Bin Location</th>
                                <th>Supplier</th>
                                <th >Total Lists</th>
                                <th>Status</th>
                                <th>Action</th>
                               
                               
                            </tr>
                        </thead>
                        <tbody>
                            @if (isset($lists) && !empty($lists) )
                                @foreach ($lists as $key => $list)
                                @php
                                $selctedId = $list->id
                                @endphp

                                <tr>
                                    @if(($mergeId->wa_supplier_id ==  $list->supplier->id) == ($mergeId->wa_store_location_id ==  $list->store_location->id && $list->purchase_no != $mergeId->purchase_no ))
                                <td>{!! $key+1 !!}</td>
                                

                                        <td>{!! $list->purchase_no !!}</td>
                                        <td>{!! $list->requisition_date !!}</td>
                                        <td>{!! @$list->getrelatedEmployee->name !!}</td>
                                        <td>{{ @$list->store_location->location_name }}</td>
                                        <td>{{ @$list->unit_of_measure->title }}</td>
                                        <td>{{ @$list->supplier->name }}</td>

                                        <td>{{ count($list->getRelatedItem) }}</td>
                                        <td>{!! $list->status !!}</td>
                                        <td> 
                                            <input type="checkbox" name="selectedIds[]"  value="{{$selctedId}}">
                                            <input type="text" name="mergeId"  value="{{$mergeId->id}}" hidden>
                                        </td>
                                @endif

                                        
                                </tr>

                                @endforeach
                            @endif
                        </tbody>
                    </table>
                    
                 <button type="submit" class="btn btn-primary" id="merge" style="float:right; margin-top:px;">Merge</button>
                </form>
                </div>
            </div>
        </div>


    </section>

@endsection

@section('uniquepagescript')
<link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet" />
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
<script>
    $(document).ready(function () {
        function toggleButton() {
            if ($('input[name="selectedIds[]"]:checked').length > 0) {
                $('#merge').prop('disabled', false);
            } else {
                $('#merge').prop('disabled', true);
            }
        }

        toggleButton();

        $('input[name="selectedIds[]"]').on('change', function () {
            toggleButton();
        });
    });
    $(document).ready(function(){
        $(".mlselec6t").select2();
    });
</script>
@endsection
