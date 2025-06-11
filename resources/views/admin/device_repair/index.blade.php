@extends('layouts.admin.admin')

@section('content')
<section class="content">
    <div class="box box-primary">
        <div class="box-header with-border">
            <div class="box-header-flex">
                <h3 class="box-title"> {{ $title }} </h3>
                <div>
                    @if (can('add', $model))
                        <a href="{{ route($model.'.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> {{ $title }}</a>
                    @endif
                </div>
            </div>
        </div>
        <div class="box-body">
            <div class="session-message-container">
                @include('message')
            </div>
            <div class="table-responsive">
                <table class="table table-bordered" id="deviceRepairDataTable">
                    <thead>
                    <tr>
                        <th style="width: 3%;">#</th>
                        <th>Device</th>
                        <th>Repair Cost</th>
                        <th>Charged To</th>
                        <th>Status</th>
                        <th class="text-right">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach($repairs as $repair)
                            <tr>
                                <th style="width: 3%;" scope="row"> {{ $loop->index + 1 }} </th>
                                <td> {{ $repair->device?->device_no }} </td>
                                <td> {{ $repair->repair_cost }} </td>
                                <td> {{ $repair->charge_to=='Staff' ? $repair->chargeTo?->name :$repair->charge_to }} </td>
                                <td> {{ $repair->status }} </td>
                                <td class="text-right">
                                    <a href="{{ route($model .'.show',$repair->id) }}" class="" style="margin-left: 10px;"><i class='fa fa-eye'></i></a>
                                    @if (can('edit', $model) && $repair->status=='Repair')
                                        <a href="{{ route($model .'.edit',$repair->id) }}" class="" style="margin-left: 10px;"><i class="fas fa-pen"></i></a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

@endsection

@section('uniquepagestyle')
    <style type="text/css">
            
     /* ALL LOADERS */
     
     .loader{
       width: 100px;
       height: 100px;
       border-radius: 100%;
       position: relative;
       margin: 0 auto;
       top: 35%;
     }
     
     /* LOADER 1 */
     
     #loader-1:before, #loader-1:after{
       content: "";
       position: absolute;
       top: 0;
       left: 0;
       width: 100%;
       height: 100%;
       border-radius: 100%;
       border: 10px solid transparent;
       border-top-color: #3498db;
     }
     
     #loader-1:before{
       z-index: 100;
       animation: spin 1s infinite;
     }
     
     #loader-1:after{
       border: 10px solid #ccc;
     }
     
     @keyframes spin{
       0%{
         -webkit-transform: rotate(0deg);
         -ms-transform: rotate(0deg);
         -o-transform: rotate(0deg);
         transform: rotate(0deg);
       }
     
       100%{
         -webkit-transform: rotate(360deg);
         -ms-transform: rotate(360deg);
         -o-transform: rotate(360deg);
         transform: rotate(360deg);
       }
     }
     
         </style>
@endsection

@push('scripts')
<div id="loader-on" style="
position: fixed;
top: 0;
text-align: center;
display: block;
z-index: 999999;
width: 100%;
height: 100%;
background: #000000b8;
display:none;
">
  <div class="loader" id="loader-1"></div>
</div>
    <script src="{{asset('js/sweetalert.js')}}"></script>
    <script src="{{asset('js/form.js')}}"></script>
    <script>
        $(document).ready(function () {
            $('#deviceRepairDataTable').DataTable({
                'paging': true,
                'lengthChange': true,
                'searching': true,
                'ordering': true,
                'info': true,
                'autoWidth': false,
                'pageLength': 100,
                'aoColumnDefs': [{
                    'bSortable': false,
                    'aTargets': 'noneedtoshort'
                }],
            });
        });

    </script>
@endpush