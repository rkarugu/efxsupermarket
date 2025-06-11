
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
                                            
                                        <select name="supplier" id="supplier" class="form-control wa_supplier_id mlselec6t" required>
                                        <option value="" selected> Select supplier </option> 
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier['id'] }}"> {{ $supplier['name'] }} </option>
                                        @endforeach
                                         </select>
                                            
                                        </div>


                                        
                                </div>
                                <div class="col-sm-2">
                                        
                                    
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                    
                                </div>
                            </div>
                        </form>
                    </div>
                            @include('message')
                            <div class="col-md-12 no-padding-h">
                                <table class="table table-bordered table-hover" id="create_datatable">
                                    <thead>
                                    <tr>
                                        <th>S.No.</th>
                                       
                                        <th>Purchase No</th>
                                        <th>Date</th>
                                         <th>User Name</th>
                                         <th>Store Location</th>
                                         <th>Bin Location</th>
                                         <th>Supplier</th>
                                         <th>Total Lists</th>
                                        <th>Status</th>
                                         
                                        
                                          <th class="noneedtoshort" >Action</th>
                                       
                                        
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(isset($lists) && !empty($lists))
                                        <?php $b = 1;?>
                                        @foreach($lists as $list)
                                         
                                            <tr>
                                                <td>{!! $b !!}</td>
                                                
                                                <td>{!! @$list->getExternalPurchase->purchase_no !!}</td>
                                                <td>{!! @$list->getExternalPurchase->requisition_date !!}</td>
                                                  <td>{!! @$list->getExternalPurchase->getrelatedEmployee->name !!}</td>
                                                 <td>{{ @$list->getExternalPurchase->store_location->location_name }}</td>
                                        <td>{{ @$list->getExternalPurchase->unit_of_measure->title }}</td>
                                         
                                          <td>{{ @$list->getExternalPurchase->supplier->name }}</td>
                                          <td>{{ count(@$list->getExternalPurchase->getRelatedItem ?? [])}}</td>
                                           <td>{!! $list->status !!}</td>
                                                 

                                                 
                                                
                                                <td class = "action_crud">

                                                    <span>
                                                    <a title="Edit" href="{{ route($model.'.edit', @$list->getExternalPurchase->slug) }}" ><img src="{!! asset('assets/admin/images/edit.png') !!}" alt="">
                                                    </a>
                                                    </span>
                                                </td>
                                                
											
                                            </tr>
                                           <?php $b++; ?>
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
