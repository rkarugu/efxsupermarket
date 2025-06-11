
@extends('layouts.admin.admin')

@section('content')
@php
$user = getLoggeduserProfile();
@endphp

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                        <div class="box-header with-border no-padding-h-b">
                            
                            @include('message')
                            <div class="col-md-12 no-padding-h table-responsive">
                                <table class="table table-bordered table-hover" id="create_datatable">
                                    <thead>
                                    <tr>
                                      



                                           <th width="5%">S.No.</th>
                                       
                                        <th width="5%"  >Order No</th>
                                         <th width="10%"  >Order date</th>
                                         <th width="10%"  >Initiated By</th>

                                          <th width="10%"  >Into Store Location</th>
                                           <th width="10%"  >Supplier</th>


                                         <th width="10%"  >Branch</th>
                                           <th width="10%"  >Department</th>
                                             <th width="10%"  >Total Amount</th>
                                               <th width="10%"  >Status</th>
                                         
                                        
                                          <th  width="10%" class="noneedtoshort" >Action</th>
                                       
                                        <!--th style = "width:10%" class = "noneedtoshort">Date</th-->
                                        
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(isset($lists) && !empty($lists))
                                        <?php $b = 1;?>
                                        @foreach($lists as $list)
                                         
                                            <tr>
                                                <td>{!! $b !!}</td>
                                                
                                                <td>{!! @$list->getPurchaseOrder->purchase_no !!}</td>
                                                 <td>{!! @$list->getPurchaseOrder->purchase_date !!}</td>
                                                  <td>{!! @$list->getPurchaseOrder->getrelatedEmployee->name !!}</td>
                                                   <td>{!! @$list->getPurchaseOrder->getStoreLocation->location_name !!}</td>

                                                     <td>{!! @$list->getPurchaseOrder->getSupplier->name !!}</td>

                                                 <td >{{ @$list->getPurchaseOrder->getBranch->name }}</td>
                                          <td >{{ @$list->getPurchaseOrder->getDepartment->department_name }}</td>

                                          <td>{{ manageAmountFormat(@$list->getPurchaseOrder?->getRelatedItem?->sum('total_cost_with_vat'))}}</td>
                                           <td>{!! $list->status !!}</td>
                                                 

                                                 
                                                                                                <td class = "action_crud">
                                                    @if($list->getPurchaseOrder->status == 'APPROVED')
                                                        <!-- Disabled button for approved LPOs -->
                                                        <button title="Approved" class="btn btn-default btn-sm" disabled>Approved</button>
                                                        <!-- PDF download button for approved LPOs -->
                                                        <a title="Download PDF" href="{{ url('admin/purchase-orders/exportToPdf/' . $list->getPurchaseOrder->slug) }}" class="btn btn-success btn-sm" target="_blank"><i class="fa fa-file-pdf-o"></i> PDF</a>
                                                    @else
                                                        <!-- Edit button for pending LPOs -->
                                                        <a title="Edit" href="{{ route($model.'.edit', $list->getPurchaseOrder->slug) }}" class="btn btn-primary btn-sm">Edit</a>
                                                    @endif
                                                    

                                                   

                                                   
                                                    
                                                    


                                                   

                                                       





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
