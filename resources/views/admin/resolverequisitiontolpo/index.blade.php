@extends('layouts.admin.admin')

@section('content')


    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border no-padding-h-b">
                <div class="row">
                    <div class="col-sm-6">
                        <form action="" method="get">
                            <div class="row">
                                <div class="col-sm-5">
                                    
                                        <div class="form-group">
                                            
                                            <select name="store" id="inputstore" class="form-control mlselec6t">
                                                <option value="" selected disabled> Select Store Location </option>
                                                @foreach(getStoreLocationDropdown() as $index => $store)
                                                <option value="{{$index}}" {{request()->store == $index ? 'selected' : ''}}>{{$store}}</option>
                                                @endforeach
                                            </select>
                                            
                                        </div>
                                </div>

                                <div class="col-sm-5">

                                        <div class="form-group">

                                            <select name="supplier" id="inputsupplier" class="form-control wa_supplier_id mlselec6t" required>
                                        <option value="" selected> Select supplier </option> 
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier['id'] }}"> {{ $supplier['name'] }} </option>
                                        @endforeach
                                         </select>
                                            
                                            <!-- <select name="supplier" id="inputsupplier" class="form-control mlselec6t">
                                                <option value="" selected disabled> Select Supplier </option>
                                                @foreach(getSuppliers() as $index => $supplier)
                                                <option value="{{$index}}" {{request()->supplier == $index ? 'selected' : ''}}>{{$supplier}}</option>
                                                @endforeach
                                            </select> -->
                                            
                                        </div>


                                        
                                </div>
                                <div class="col-sm-2">
                                        
                                    
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                    
                                </div>
                            </div>
                        </form>
                    </div>
                    
                </div>
                <br>
              
                @include('message')
                <div class="col-md-12 no-padding-h">
                    <table class="table table-bordered table-hover" id="create_datatable">
                        <thead>
                            <tr>
                                
                            <th >S.No.</th>

                                <th >Purchase No</th>
                                <th >Merge LPO</th>
                                <th >Date</th>
                                <th>User Name</th>
                                <th>Store Location</th>
                                <th>Bin Location</th>
                                <th>Supplier</th>
                                <th >Total Lists</th>
                                <th >Status</th>
                                <th >Action</th>
                            </tr>
                        </thead>
                        <tbody>
    @if (!empty($lists))
        @foreach ($lists as $key => $list)
            @if($suppliers->contains('name', $list->supplier->name))
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $list->purchase_no }}</td>
                    <td>{{ $list->merge_no }}</td>
                    <td>{{ $list->requisition_date }}</td>
                    <td>{{ optional($list->getrelatedEmployee)->name }}</td>
                    <td>{{ optional($list->store_location)->location_name }}</td>
                    <td>{{ optional($list->unit_of_measure)->title }}</td>
                    <td>{{ $list->supplier->name }}</td>
                    <td>{{ count($list->getRelatedItem) }}</td>
                    <td>{{ $list->status }}</td>                    
                    <td>
                        @php
                        $slug = $list->purchase_no; 
                        @endphp
                        <a href="{{ route('resolve-requisition-to-lpo.edit', $list->id) }}"><i class="fa fa-eye"></i></a>
                        <a href="{{ route('resolve-requisition-to-lpo.merge', $list->id) }}" title="Merge Requisition"><i class="fa fa-code-fork"></i></a>
                        <a href="{{ route('resolve-requisition-to-lpo.edititem', $slug) }}" title="Edit Requisition"><i class="fa fa-edit"></i></a>
                    </td>
                </tr>
            @endif
        @endforeach  
    @endif
</tbody>
                    </table>
                </div>
            </div>
        </div>


    </section>

@endsection

@section('uniquepagescript')
<link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet" />
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
<script>
    $(document).ready(function(){
        $(".mlselec6t").select2();
    });
</script>
@endsection
