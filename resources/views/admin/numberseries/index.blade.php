
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
                                       
                                        <th width="10%">Code</th>
                                          <th width="15%"  >Module</th>
                                             <th width="20%"  >Description</th>
                                              <th width="15%"  >Starting No.</th>
                                               <th width="15%"  >Type No.</th>
                                                <th width="15%"  >Last Date Used</th>
                                                  <th width="15%"  >Last No Used</th>
                                        
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
                                                
                                                <td>{!! $list->code !!}</td>
                                                 <td>{!! $list->module !!}</td>
                                                  <td>{!! $list->description !!}</td>
                                                   <td>{!! $list->code.'-'.$list->starting_number !!}</td>
                                                    <td>{!! $list->type_number !!}</td>
                                                    <td>{!! $list->last_date_used?date('Y-m-d',strtotime($list->last_date_used)):'' !!}</td>
                                                      <td>{!! $list->last_number_used?$list->last_number_used:'' !!}</td>

                                                 
                                                
                                                <td class = "action_crud">
                                              
                                                 @if(isset($permission[$pmodule.'___edit']) || $permission == 'superadmin')
                                                    <span>
                                                    <a title="Edit" href="{{ route($model.'.edit', $list->slug) }}" ><img src="{!! asset('assets/admin/images/edit.png') !!}" alt="">
                                                    </a>
                                                    </span>

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
