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
                           <h4>{{$title}}</h4>
                        </div>
                    </div>

                        
                        <div class="row">
                           
                            <div class="col-sm-5 fix-panel"  >
                                <div class="col-sm-12" style="border:1px solid #ccc; background: white;">
                                    <h4><b>Inspection Details</b></h4>
                                    <hr>
                                    
                                    <table class="table">
                                        <tr>
                                            <th>Vehicle</th>
                                            <td>{{@$data->vehicle->vin_sn}}</td>
                                        </tr>
                                        <tr>
                                            <th>Inspection Form</th>
                                            <td>{{@$data->form->title}}</td>
                                        </tr>
                                        <tr>
                                            <th>Started</th>
                                            <td>{{  date('j, M d, Y H:ia',strtotime(@$data->created_at))}}</td>
                                        </tr>
                                        <tr>
                                            <th>Submited</th>
                                            <td>{{  date('j, M d, Y H:ia',strtotime(@$data->created_at))}}</td>
                                        </tr>
                                        <tr>
                                            <th>Duration</th>
                                            <td>-</td>
                                        </tr>
                                        <tr>
                                            <th>Submission Source</th>
                                            <td>-</td>
                                        </tr>
                                        <tr>
                                            <th>Submitted By</th>
                                            <td>{{@$data->user->name}}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="col-sm-7 fix-panel">

                                <div class="col-sm-12" style="border:1px solid #ccc; background: white;">
                                    <h4><b>Item Checklist</b></h4>
                                    <hr>
                                    
                                    <table class="table">
                                        @if($data->items->count()>0)
                                            @foreach($data->items as $item)
                                                <tr>
                                                    <th>{{@$item->form_item->title}}</th>
                                                    <td>{{@$item->item_detail}}</td>
                                                </tr> 
                                            @endforeach
                                        @endif
                                        
                                        
                                    </table>
                                </div>
                            </div>
                           
                        </div>
                       
                        
                        
                    


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

