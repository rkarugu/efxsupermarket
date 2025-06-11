
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
                                <table class="table table-bordered table-hover" id="create_datatable">
                                    <thead>
                                    <tr>
                                        <th width="5%">S.No.</th>
                                       
                                        <th width="10%">Name</th>
                                        <th width="15%">Phone</th>
                                        <th width="15%">Nationality</th>
                                        <th width="15%">Created</th>
                                       
                                          <th  width="8%" class="noneedtoshort" >Action</th>
                                       
                                        <!--th style = "width:10%" class = "noneedtoshort">Date</th-->
                                        
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(isset($lists) && !empty($lists))
                                        <?php $b = 1;?>
                                        @foreach($lists as $list)
                                         
                                            <tr>
                                                <td>{!! $b !!}</td>
                                               
                                                <td>{!! ucfirst($list->name) !!}</td>
                                                 <td>{!! $list->phone_number !!}</td>
                                                   <td>{!! ucfirst($list->nationality) !!}</td>
                                                    <td>{!! date('Y-m-d',strtotime($list->created_at)) !!}</td>
                                                 
                                                <td class = "action_crud">
                                                    <span>
                                                    <a title="Show Detail" href="{!!route($model.'.show',$list->slug) !!}"><i class="fa fa-eye" aria-hidden="true"></i>

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


