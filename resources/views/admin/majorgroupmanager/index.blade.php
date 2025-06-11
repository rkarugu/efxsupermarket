
@extends('layouts.admin.admin')

@section('content')


    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                        <div class="box-header with-border no-padding-h-b">
                            <br>
                            @include('message')
                            <div class="col-md-12 no-padding-h">
                                <table class="table table-bordered table-hover" >
                                    <thead>
                                    <tr>
                                        <th width="10%">S.No.</th>
                                       <th width="10%">Image</th>
                                        <th width="55%"  >Name</th>
                                         <th width="15%">Order</th>
                                        
                                      
                                        
                                          <th  width="10%" class="noneedtoshort" >Action</th>
                                       
                                        
                                        
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(isset($lists) && !empty($lists))
                                        <?php $b = 1;?>
                                        @foreach($lists as $list)
                                         
                                            <tr>
                                                <td>{!! $b !!}</td>
                                               <td style="background-color: #ff0000"> <img width="100px" height="50px;"src="{{ asset('uploads/major_groups/thumb/'.$list->image) }}"></td>
                                                <td>{!! strtoupper($list->name) !!}</td>
                                                 <td>{!! strtoupper($list->display_order) !!}</td>
                                                
                                                 
                                                  
                                                
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
