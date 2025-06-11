
@extends('layouts.admin.admin')

@section('content')


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
                                                
                                                <td>{!! $list->getTyrePurchaseOrder->purchase_no !!}</td>
                                                 <td>{!! $list->getTyrePurchaseOrder->purchase_date !!}</td>
                                                  <td>{!! $list->getTyrePurchaseOrder->getrelatedEmployee->name !!}</td>
                                                   <td>{!! @$list->getTyrePurchaseOrder->getStoreLocation->location_name !!}</td>

                                                     <td>{!! @$list->getTyrePurchaseOrder->getSupplier->name !!}</td>

                                                 <td >{{ $list->getTyrePurchaseOrder->getBranch->name }}</td>
                                          <td >{{ $list->getTyrePurchaseOrder->getDepartment->department_name }}</td>

                                          <td>{{ manageAmountFormat($list->getTyrePurchaseOrder->getRelatedItem->sum('total_cost_with_vat'))}}</td>
                                           <td>{!! $list->status !!}</td>
                                                 

                                                 
                                                
                                                <td class = "action_crud">

                                                    <span>
                                                    @if(getLoggeduserProfile()->id==$list->user_id)
                                                    <a title="Edit" href="{{ route($model.'.edit', $list->getTyrePurchaseOrder->slug) }}" ><img src="{!! asset('assets/admin/images/edit.png') !!}" alt="">
                                                    </a>
                                                    @else
                                                    --
                                                    @endif
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
