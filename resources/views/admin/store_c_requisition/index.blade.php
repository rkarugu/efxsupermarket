
@extends('layouts.admin.admin')

@section('content')


    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                        <div class="box-header with-border no-padding-h-b">
                            <div align = "right"> 
                              @if(isset($permission[$pmodule.'___add-2']) || $permission == 'superadmin')
                                <a href = "{{route('store-c-requisitions.create2')}}" class = "btn btn-success">Add {!! $title !!}2</a>    
                              @endif

                              
                              @if(isset($permission[$pmodule.'___add']) || $permission == 'superadmin')
                                <a href = "{!! route($model.'.create')!!}" class = "btn btn-success">Add {!! $title !!}</a>
                              @endif
                            </div>
                             
                            <br>
                            @include('message')
                            <div class="col-md-12 no-padding-h">
                                <table class="table table-bordered table-hover" id="create_datatable">
                                    <thead>
                                    <tr>
                                        <th width="5%">S.No.</th>
                                       
                                        <th width="10%"  >Requisition No</th>
                                          <th width="10%"  >Requisition Date</th>
                                         <th width="15%"  >User Name</th>
                                         <th width="10%"  >Store</th>
                                           <th width="15%"  >Department</th>
                                             <th width="10%"  >Total Lists</th>
                                               <th width="10%"  >Status</th>
                                         
                                        
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
                                                
                                                <td>{!! $list->requisition_no !!}</td>
                                                  <td>{!! $list->requisition_date !!}</td>
                                                  <td>{!! @$list->getrelatedEmployee->name !!}</td>
                                                 <td >{{ @$list->getRelatedToLocationAndStore->location_name }}</td>
                                          <td >{{ @$list->getDepartment->department_name }}</td>

                                          <td>{{ count($list->getRelatedItem)}}</td>
                                           <td>{!! $list->status !!}</td>
                                                 

                                                 
                                                
                                                <td class = "action_crud">

                                                @if($list->status == 'UNAPPROVED')
                                                    <span>
                                                    <a title="Edit" href="{{ route($model.'.edit', $list->slug) }}" ><img src="{!! asset('assets/admin/images/edit.png') !!}" alt="">
                                                    </a>
                                                    </span>

                                                   

                                                  

                                                  

                                                    <span>
                                                    <form title="Trash" action="{{ URL::route($model.'.destroy', $list->slug) }}" method="POST">
                                                    <input type="hidden" name="_method" value="DELETE">
                                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                    <button  style="float:left"><i class="fa fa-trash" aria-hidden="true"></i>
                                                    </button>
                                                    </form>
                                                    </span>

                                                    @else
                                                       <span>
                                                    <a  class="btn btn-sm btn-biz-pinkish"  title="View" href="{{ route($model.'.show', $list->slug) }}" ><i class="fa fa-eye" aria-hidden="true"></i>
                                                    </a>
                                                    </span>
                                                     @endif

                                                      @if($list->status == 'APPROVED')
                                                       <span>
                                                    <a class="btn btn-sm btn-biz-greenish" title="Print"  href="#" onclick="print_this('{{route('store-c-requisitions.print',['slug'=>$list->slug])}}'); return false;"><i aria-hidden="true" class="fa fa-print"></i>
                                                    </a>
                                                  </span>

                                                   {{-- <span>
                                                    <a title="Export To Pdf" href="{{ route($model.'.exportToPdf', $list->slug)}}"><i aria-hidden="true" class="fa fa-file-pdf" ></i>
                                                    </a>
                                                  </span> --}}
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
