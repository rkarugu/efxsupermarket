
@extends('layouts.admin.admin')

@section('content')


    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                        <div class="box-header with-border no-padding-h-b">


                        <?php //echo  date('Y-m-d H:i:s')?>

                             <div align = "right"> 
                            @if(isset($permission[$pmodule.'___add']) || $permission == 'superadmin')
                           
                            <a href = "{!! route($model.'.create')!!}" class = "btn btn-success">Add {!! $title !!}</a>

                            @endif
                            &nbsp;
                             <a href = "{!!route('admin.getmenuitemwithoutplu')!!}" class = "btn btn-success">Get Item Without plu</a>
                            &nbsp;
                             <a href = "{!!route('admin.priceListExport')!!}" class = "btn btn-success">Get Price List</a>
                              </div>
                            <br>
                            @include('message')
                            <div class="col-md-12 no-padding-h">
                                <table class="table table-bordered table-hover" id="create_datatable">
                                    <thead>
                                    <tr>
                                        <th width="10%">S.No.</th>
                                        
                                        <th width="40%">Name</th>
                                        <th width="10%">Price</th>
                                        <th width="10%">Recipe Cost</th>
                                        <th width="10%">Print Class</th>
                                        <th width="40%">Family/Sub Family Group</th>
                                        <th width="40%">Recipe</th>
                                        
                                        
                                          <th  width="30%" class="noneedtoshort" >Action</th>
                                       
                                        <!--th style = "width:10%" class = "noneedtoshort">Date</th-->
                                        
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(isset($lists) && !empty($lists))
                                        <?php $b = 1;?>
                                        @php
                                            $classname = [];
                                           // echo "<pre>"; print_r($lists); die;
                                        @endphp
                                        @foreach($lists as $key=> $list)
                                        @foreach($list->getClassName as $key=> $ClassName)
                                                @php
                                                    $classname[$key] = $ClassName->getAssociatePrintClass->name;
                                                @endphp
                                        @endforeach
                                            <tr>
                                                <td>{!! $b !!}</td>
                                                 
                                                <td>{!! strtoupper($list->name) !!}</td>
                                                <td>{!! strtoupper($list->price) !!}</td>
                                                <td>{!! strtoupper($list->recipe_cost) !!}</td>
                                                <td>{!! implode(',',$classname) !!}</td>
                                                @if(isset($list->getItemCategoryRelation->category_id))
                                                <td>{!! strtoupper(getCategoryNameById($list->getItemCategoryRelation->category_id)) !!}</td>
                                                @else
                                                <td> - </td>
                                                @endif
                                                <td>{!! isset($list->getAssociateRecipe->title) ? $list->getAssociateRecipe->title : '-' !!}</td>
                                               
                                                
                                                <td class = "action_crud">
                                                 @if(isset($permission[$pmodule.'___edit']) || $permission == 'superadmin')
                                                    <span>
                                                    <a title="Edit" href="{{ route($model.'.edit', $list->slug) }}" ><img src="{!! asset('assets/admin/images/edit.png') !!}" alt="">
                                                    </a>
                                                    </span>

                                                     @endif

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
