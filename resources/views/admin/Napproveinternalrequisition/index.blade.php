
@extends('layouts.admin.admin')

@section('content')


    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                        <div class="box-header with-border no-padding-h-b">
                            
                            @include('message')
                            <div class="col-md-12 no-padding-h">
                                <table class="table table-bordered table-hover" id="create_datatable">
                                    <thead>
                                    <tr>
                                        <th width="10%">S.No.</th>
                                       
                                        <th width="15%"  >Requisition No</th>
                                         <th width="15%"  >Requisition Date</th>
                                         <th width="15%"  >User Name</th>
                                         <th width="15%"  >Branch</th>
                                           <th width="15%"  >Department</th>
                                             <th width="15%"  >Total Lists</th>
                                               <th width="15%"  >Status</th>
                                         
                                        
                                          <th  width="15%" class="noneedtoshort" >Action</th>
                                       
                                        <!--th style = "width:10%" class = "noneedtoshort">Date</th-->
                                        
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(isset($lists) && !empty($lists))
                                        <?php $b = 1;?>
                                        @foreach($lists as $list)
                                         
                                            <tr>
                                                <td>{!! $b !!}</td>
                                                
                                                <td>{!! $list->getInternalPurchase->requisition_no !!}</td>
                                                 <td>{!! $list->getInternalPurchase->requisition_date !!}</td>
                                                  <td>{!! $list->getInternalPurchase->getrelatedEmployee->name !!}</td>
                                                 <td >{{ $list->getInternalPurchase->getBranch->name }}</td>
                                          <td >{{ $list->getInternalPurchase->getDepartment->department_name }}</td>

                                          <td>{{ count($list->getInternalPurchase->getRelatedItem)}}</td>
                                           <td>{!! $list->status !!}</td>
                                                 

                                                 
                                                
                                                <td class = "action_crud">

                                                    <span>
                                                    <a title="Edit" href="{{ route($model.'.edit', $list->getInternalPurchase->slug) }}" ><img src="{!! asset('assets/admin/images/edit.png') !!}" alt="">
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
