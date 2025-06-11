@extends('layouts.admin.admin')

@section('content')

    <style type="text/css">
        .bg-white{
            background-color: white;
        }

        .table tbody tr td{
            padding: 12px 8px;
            border: 0px !important;
        }
        .table tbody tr{
            
            border: 0px !important;
        }

        .table td.ar, .table th.ar{
            text-align: right;
        
        }
        .link_panel:before{
            content: "\f0c9";
            position: absolute;
            color: black;
            top: 13px;
            right: 30px;
            font-family: arial;
            font: normal normal normal 14px/1 FontAwesome; 
        }
        .fix-panel{
            position: relative;
        }

        .dropdown-menu{
              position: absolute;
              top: 35px;
              right: 20px !important;
            };
        
    </style>
    <!-- Main content -->

    <section class="content">
        <!-- Small boxes (Stat box) -->
         @include('message')
                    <div class="box box-primary">
                        <div class="box-header with-border no-padding-h-b">
                             @if(isset($permission[$pmodule.'___add']) || $permission == 'superadmin')
                            <div class="col-sm-10">
                                <h3 class="box-title"> {!! $title !!} </h3>
                            </div>
                            <div class="col-sm-2">
                                 <div align = "right"> <a href = "{!! route($model.'.create')!!}" class = "btn btn-success">Add {!! $title !!}</a>
                            </div>
                            </div>
                            
                             @endif
                            <br>
                           
                        </div>
                    </div>

                        @if($data->count()>0)
                        <div class="row">
                            @foreach($data as $key => $form)
                                <div class="col-sm-3 fix-panel" id="box_{{$form->id}}">
                                    <div class="panel panel-default">
                                        <a class="dropdown-toggle" href="#" data-toggle="dropdown" rel="tooltip" title="" data-container="body" data-placement="left" data-trigger="hover" data-original-title="More Actions">
                                            <div class="link_panel"></div>
                                        </a>

                                        <ul class="dropdown-menu pull-right">
                                            {{-- <li><a data-target="#generate-share-link-modal" data-toggle="modal" href="/da6062ee61/inspection_forms/153952/shared_link"><i class="fa fa-share"></i> Share Submission Link</a></li> --}}
                                            
                                            <li><a href="{{route($model.'.edit',base64_encode($form->id))}}"><i class="fa fa-edit"></i> Edit</a></li>
                                            
                                            <li><a target="_blank" href="/da6062ee61/inspection_forms/153952.pdf"><i class="fa fa-print"> </i>Print</a></li>

                                            <li class="divider"></li>

                                            <li class="dropdown-header">Manage</li>

                                            <li><a href="{{route($model.'.edit.items',['form_id'=>base64_encode($form->id)])}}">Inspection Items</a></li>

                                            <li><a href="{{route($model.'.edit.vehicle_schedule',base64_encode($form->id))}}">Vehicles & Schedules</a></li>

                                            {{-- <li><a href="/da6062ee61/inspection_forms/153952/inspection_workflows">Workflows</a></li> 

                                            <li class="divider"></li>

                                            <li><a data-target="#copy-inspection-form-modal" data-toggle="modal" href="/da6062ee61/inspection_forms/153952/copy/new"><i class="fa fa-copy"></i> Make a copy</a></li> --}}

                                            <li class="divider"></li>

                                            

                                             <li><a data-id="{{base64_encode($form->id)}}" data-id-decode="{{$form->id}}" data-action-url="{{route('inspection_forms.archive',base64_encode($form->id))}}" class="click_archive" rel="nofollow" data-method="put" href="javascript:void(0)"><i class="fa fa-archive"></i> Archive</a></li> 
                                        </ul>
                                        <div class="panel-heading">
                                            <h1 class="panel-title">{{$form->title}}</h1>
                                        </div>
                                        <div class="panel-body">
                                            <p>{{$form->description}}</p>
                                            <br>
                                            <table class="table table-hover table-condensed">
                                                <tbody data-link="row">
                                                    <tr class="border-off">
                                                        <td>
                                                            <a href="{{route($model.'.edit.items',['form_id'=>base64_encode($form->id)])}}">Items</a>
                                                        </td>
                                                        <td class="fit ar">
                                                            <span><span class="badge">{{$form->getRelatedItems->count()}}</span></span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <a href="{{route($model.'.edit.vehicle_schedule',base64_encode($form->id))}}">Vehicles</a>
                                                        </td>
                                                        <td class="fit ar">
                                                            <span><span class="badge">0</span></span>
                                                        </td>
                                                    </tr>
                                                    {{-- <tr>
                                                        <td>
                                                            <a href="/da6062ee61/inspection_forms/153952/inspection_workflows">Workflows</a>
                                                        </td>
                                                        <td class="fit ar">
                                                            <span><span class="badge">0</span></span>
                                                        </td>
                                                    </tr> --}}
                                                    <tr>
                                                        <td>
                                                            <a href="/da6062ee61/submitted_inspection_forms?q%5Binspection_form_id_in%5D%5B%5D=153952">Submissions</a>
                                                        </td>
                                                        <td class="fit ar">
                                                            <span><span class="badge">0</span></span>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <!-- <div class="col-sm-3 ">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h1 class="panel-title">Full Vehicle Inspection</h1>
                                </div>
                                <div class="panel-body">
                                    <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium.</p>
                                    <br>
                                    <table class="table table-hover table-condensed">
                                        <tbody data-link="row">
                                            <tr class="border-off">
                                                <td>
                                                    <a href="/da6062ee61/inspection_forms/153952/inspection_items/edit">Items</a>
                                                </td>
                                                <td class="fit ar">
                                                    <span><span class="badge">23</span></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <a href="/da6062ee61/inspection_forms/153952/inspection_schedule_rules">Vehicles</a>
                                                </td>
                                                <td class="fit ar">
                                                    <span><span class="badge">8</span></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <a href="/da6062ee61/inspection_forms/153952/inspection_workflows">Workflows</a>
                                                </td>
                                                <td class="fit ar">
                                                    <span><span class="badge">1</span></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <a href="/da6062ee61/submitted_inspection_forms?q%5Binspection_form_id_in%5D%5B%5D=153952">Submissions</a>
                                                </td>
                                                <td class="fit ar">
                                                    <span><span class="badge">3</span></span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3 ">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h1 class="panel-title">Mercedes Body Inspection</h1>
                                </div>
                                <div class="panel-body">
                                    <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium.</p>
                                    <br>
                                    <table class="table table-hover table-condensed">
                                        <tbody data-link="row">
                                            <tr class="border-off">
                                                <td>
                                                    <a href="/da6062ee61/inspection_forms/153952/inspection_items/edit">Items</a>
                                                </td>
                                                <td class="fit ar">
                                                    <span><span class="badge">23</span></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <a href="/da6062ee61/inspection_forms/153952/inspection_schedule_rules">Vehicles</a>
                                                </td>
                                                <td class="fit ar">
                                                    <span><span class="badge">8</span></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <a href="/da6062ee61/inspection_forms/153952/inspection_workflows">Workflows</a>
                                                </td>
                                                <td class="fit ar">
                                                    <span><span class="badge">1</span></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <a href="/da6062ee61/submitted_inspection_forms?q%5Binspection_form_id_in%5D%5B%5D=153952">Submissions</a>
                                                </td>
                                                <td class="fit ar">
                                                    <span><span class="badge">3</span></span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3 ">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h1 class="panel-title">Primary</h1>
                                </div>
                                <div class="panel-body">
                                    <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium.</p>
                                    <br>
                                    <table class="table table-hover table-condensed">
                                        <tbody data-link="row">
                                            <tr class="border-off">
                                                <td>
                                                    <a href="/da6062ee61/inspection_forms/153952/inspection_items/edit">Items</a>
                                                </td>
                                                <td class="fit ar">
                                                    <span><span class="badge">23</span></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <a href="/da6062ee61/inspection_forms/153952/inspection_schedule_rules">Vehicles</a>
                                                </td>
                                                <td class="fit ar">
                                                    <span><span class="badge">8</span></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <a href="/da6062ee61/inspection_forms/153952/inspection_workflows">Workflows</a>
                                                </td>
                                                <td class="fit ar">
                                                    <span><span class="badge">1</span></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <a href="/da6062ee61/submitted_inspection_forms?q%5Binspection_form_id_in%5D%5B%5D=153952">Submissions</a>
                                                </td>
                                                <td class="fit ar">
                                                    <span><span class="badge">3</span></span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div> -->
                    @else
                        <div class="box box-primary">
                            <div class="box-header with-border no-padding-h-b">
                                <h3 class="box-title"> Records Not Found! </h3>
                            </div>
                        </div>
                    @endif
                        
                        
                        
                    


    </section>
   
@endsection
@section('uniquepagescript')
    <script src="{{asset('js/sweetalert.js')}}"></script>
    <script src="{{asset('js/form.js')}}"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript">
        $('.click_archive').on('click',function(){
            var inspection_form_id=$(this).data('id');
            var inspection_form_id_decode=$(this).data('id-decode');
            var action_url=$(this).data('action-url');

            Swal.fire({
              title: 'Are you sure want to archive this Item?',
              showCancelButton: true,
              confirmButtonColor: '#252525',
              cancelButtonColor: 'red',
              confirmButtonText: 'Yes, I Confirm',
              cancelButtonText: `No, Cancel It`,
            }).then((result) => {
              /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    $.ajax({
                        type:'get',
                        url:action_url,
                        data:{inspection_form_id:inspection_form_id},
                        success:function(res){
                            if(res.result==1){
                                $('#box_'+inspection_form_id_decode).fadeOut(800);
                            }
                        }
                    });
                }
            })
        });
    </script>   
   
@endsection

