
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
                                        <th>S.No.</th>
                                        <th>Title</th>
                                        <th>Created AT</th>
                                        <th class="noneedtoshort" >Action</th>                                        
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(isset($lists) && !empty($lists))
                                        <?php $b = 1;?>
                                        @foreach($lists as $list)                                         
                                            <tr>
                                                <td>{!! $b !!}</td>                                                
                                                <td>{!! $list->title !!}</td>
                                                <td>{!! date('d m Y',strtotime($list->created_at)) !!}</td>
                                                <td class = "action_crud">
                                                @if(isset($permission[$pmodule.'___edit']) || $permission == 'superadmin')
                                                    <span>
                                                        <a title="Edit" href="{{ route($model.'.edit', $list->id) }}" ><img src="{!! asset('assets/admin/images/edit.png') !!}" alt="">
                                                        </a>
                                                    </span>
                                                @endif
                                                @if(isset($permission[$pmodule.'___delete']) || $permission == 'superadmin')
                                                    <span>
                                                        <form title="Trash" action="{{ route($model.'.destroy', $list->id) }}" method="POST" class="submitMe">
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
@section('uniquepagescript')
<script src="{{asset('js/sweetalert.js')}}"></script>
<script src="{{asset('js/form.js')}}"></script>
@endsection