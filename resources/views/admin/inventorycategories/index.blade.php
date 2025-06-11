
@extends('layouts.admin.admin')

@section('content')


    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                        <div class="box-header with-border no-padding-h-b">
                             @if(isset($permission[$pmodule.'___add']) || $permission == 'superadmin')
                            <div align = "right"> <a href = "{!! route($model.'.create')!!}" class = "btn btn-success">Add {!! $title !!}</a></div>
                             @endif
                            <br>
                            @include('message')
                            <div class="col-md-12 no-padding-h">
                               <table class="table table-bordered table-hover" id="create_datatable">
                                    <thead>
                                    <tr>
                                        <th>S.No.</th>
                                       
                                        <th class="noneedtoshort" >Code</th>
                                          <th   >Description</th>
                                          <th   >Family Group</th>
                                          <th   >Stock Type</th>
                                           <th   >Inventory Account</th>
                                            <!-- <th  >Adjst GL</th> -->
                                               <th   >COS GL</th>
                                                <th   >Sales GL</th>
                                                   <th   >Stock Excess</th>
                                                    <th   >Shortage</th><!--  -->
                                          
                                        
                                          <th  class="noneedtoshort" >Action</th>
                                       
                                        <!--th style = "width:10%" class = "noneedtoshort">Date</th-->
                                        
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(isset($lists) && !empty($lists))
                                        <?php $b = 1;?>
                                        @foreach($lists as $list)
                                         
                                            <tr>
                                                <td>{!! $b !!}</td>
                                                
                                                <td>{!! $list->category_code !!}</td>
                                                 <td>{!! $list->category_description!!}</td>
                                                <td>{!! $list->getStockFamilyGroup?->title!!}</td>
                                                <td>{!! $list->getStockTypecategory?->title!!}</td>
                                                <td>{!! $list->getStockGlDetail?->account_code!!}</td>
                                                 <td>{!! $list->getIssueGlDetail?->account_code!!}</td>
                                                       <td>{!! $list->getWIPGlDetail?->account_code!!}</td>
                                                  <td>{!! isset($list->getPricevarianceGlDetail?->account_code) ? $list->getPricevarianceGlDetail->account_code : '-' !!}</td>
                                                    <td>{!! isset($list->getusageGlDetail?->account_code) ? $list->getusageGlDetail->account_code : '-' !!}</td>

                                                 
                                                
                                                <td class = "action_crud">
                                                @if($list->slug != 'mpesa')
                                                 @if(isset($permission[$pmodule.'___edit']) || $permission == 'superadmin')
                                                    <span>
                                                    <a title="Edit" href="{{ route($model.'.edit', $list->slug) }}" ><img src="{!! asset('assets/admin/images/edit.png') !!}" alt="">
                                                    </a>
                                                    </span>

                                                    @endif

                                                  {{-- 

                                                   @if(isset($permission[$pmodule.'___delete']) || $permission == 'superadmin')

                                                    <span>
                                                    <form title="Trash" action="{{ URL::route($model.'.destroy', $list->slug) }}" method="POST">
                                                    <input type="hidden" name="_method" value="DELETE">
                                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                    <button  style="float:left"><i class="fa fa-trash" aria-hidden="true"></i>
                                                    </button>
                                                    </form>
                                                    </span>
                                                     @endif

--}}
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
