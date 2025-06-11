
@extends('layouts.admin.admin')

@section('content')


    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                        <div class="box-header with-border no-padding-h-b">
                            
                            @include('message')
                            <div class="col-md-12 no-padding-h">
                                <table class="table table-bordered table-hover" id="create_datatable">
                                    <thead>
                                    <tr>
                                        <th>S.No.</th>
                                        <th>Branch</th>

                                        <th class="noneedtoshort" >Action</th>                                        
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(isset($branchs) && !empty($branchs))
                                        <?php $b = 1;?>
                                        @foreach($branchs as $branch)                                         
                                            <tr>
                                                <td>{!! $b !!}</td>                                                
                                                <td>{!! $branch->name !!}</td>
                                                <td class = "action_crud">
                                                @if(isset($permission[$pmodule.'___edit']) || $permission == 'superadmin')
                                                    <span>
                                                        <a title="Edit" href="{{ route($model.'.index', $branch->id) }}" ><img src="{!! asset('assets/admin/images/edit.png') !!}" alt="">
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
@section('uniquepagescript')
<script src="{{asset('js/sweetalert.js')}}"></script>
<script src="{{asset('js/form.js')}}"></script>
@endsection