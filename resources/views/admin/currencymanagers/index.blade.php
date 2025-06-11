
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
                                        <th width="10%" >S.No.</th>
                                       
                                        <th width="15%"  >ISO4217 </th>
                                         <th width="15%"  > Currency Name </th>
                                          <th width="15%"  > Country </th>
                                           <th width="15%"  >  Decimal Places </th>
                                           <th width="15%"  >  Show In Webshop </th>
                                            <th width="15%"  >  Exchange Rate </th>
                                             <th width="15%"  > 1 / Ex Rate </th>
                                              <th width="15%"  >  Ex Rate - ECB</th>

                                          

                                           

                                         

                                       
                                         
                                        
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
                                                
                                                <td><img src= "{!! asset('assets/admin/flags').'/'.$list->ISO4217.'.gif' !!}"> {!! $list->ISO4217  !!}</td>
                                                 <td>{!!  getAllCurrenciesList()[$list->ISO4217]  !!}</td>
                                                  <td>{!! $list->country  !!}</td>
                                                    <td>{!! $list->decimal_places  !!}</td>
                                                     <td>{!! $list->show_in_webshop=='1'?'Yes':'No'  !!}</td>
                                                      <td>{!! $list->exchange_rate  !!}</td>
                                                      <td>{!!
                                                      $list->exchange_rate>0?number_format($list->exchange_rate*1,2):'inf'

                                                      !!}</td>
                                                      <td>{!! number_format($list->exchange_rate,4)  !!}</td>
                                                

                                                 
                                                
                                                <td class = "action_crud">
                                               
                                                 @if(isset($permission[$pmodule.'___edit']) || $permission == 'superadmin')
                                                    <span>
                                                    <a title="Edit" href="{{ route($model.'.edit', $list->slug) }}" ><img src="{!! asset('assets/admin/images/edit.png') !!}" alt="">
                                                    </a>
                                                    </span>

                                                    @endif

                                                   @if(isset($permission[$pmodule.'___delete']) || $permission == 'superadmin')


                                                   @if($list->ISO4217 != 'KES')

                                                    <span>
                                                    <form title="Trash" action="{{ URL::route($model.'.destroy', $list->slug) }}" method="POST">
                                                    <input type="hidden" name="_method" value="DELETE">
                                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                    <button  style="float:left"><i class="fa fa-trash" aria-hidden="true"></i>
                                                    </button>
                                                    </form>
                                                    </span>
                                                       @endif
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
