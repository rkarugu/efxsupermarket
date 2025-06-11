
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
                                        <th width="10%">S.No.</th>
                                        <th>Route</th>
                                        <th>Toonage</th>
                                        <th>Amount Ratio</th>
                                        <th>CTNS</th>
                                        <th>LINES</th>
                                        <th>Time Posted</th>
                                        <th>UNMET</th>
                                        <th>DD Per Week</th>
                                        <th>Travel</th>
                                        <th class="noneedtoshort" >Action</th>
                                       
                                        <!--th style = "width:10%" class = "noneedtoshort">Date</th-->
                                        
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(isset($lists) && !empty($lists))
                                        <?php $b = 1;?>
                                        @foreach($lists as $list)
                                         
                                            <tr>
                                                <td>{!! $b !!}</td>
                                                
                                                <td>{!! $list->route !!}</td>
                                                <td>{!! $list->tonnage !!}</td>
                                                <td>{!! $list->amount_ratio !!}</td>
                                                <td>{!! $list->ctns !!}</td>
                                                <td>{!! $list->lines !!}</td>
                                                <td>{!! $list->time_posted !!}</td>
                                                <td>{!! $list->unmet !!}</td>
                                                <td>{!! $list->dd_per_week !!}</td>
                                                <td>{!! $list->travel !!}</td>
                                               
                                                  

                                                 
                                                
                                                <td class = "action_crud">
                                                @if($list->slug != 'mpesa')
                                                 @if(isset($permission[$pmodule.'___edit']) || $permission == 'superadmin')
                                                    <span>
                                                    <a title="Edit" href="{{ route($model.'.edit', $list->id) }}" ><img src="{!! asset('assets/admin/images/edit.png') !!}" alt="">
                                                    </a>
                                                    </span>

                                                    @endif

                                                    

                                                   @if(isset($permission[$pmodule.'___delete']) || $permission == 'superadmin')

                                                    <span>
                                                    <form title="Trash" action="{{ URL::route($model.'.destroy', $list->id) }}" method="POST">
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
