
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
                                       
                                        <th width="40%"  >Key </th>
                                        <th width="40%"  >Value</th>
                                       
                                      
                                          <th  width="10%" class="noneedtoshort" >Action</th>
                                       
                                       
                                        
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(isset($lists) && !empty($lists))
                                        <?php $b = 1;?>
                                        @foreach($lists as $list)
                                         
                                            <tr>
                                                <td>{!! $b !!}</td>
                                                
                                                <td>{!! $list->name !!}</td>
                                                <td>
                                                <?php 
                                                if($list->parameter_type == 'boolean')
                                                {
                                                    echo $list->description=='1'?'Yes':'No';
                                                }
                                                else
                                                {
                                                    echo $list->description;
                                                }

                                                ?>
                                                </td>
                                               
                                                
                                                <td class = "action_crud">
                                                    <span>
                                                    <a title="Edit" href="{{ route($model.'.edit', $list->slug) }}" ><img src="{!! asset('assets/admin/images/edit.png') !!}" alt="">
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
