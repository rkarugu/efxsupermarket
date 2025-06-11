@extends('layouts.admin.admin')

@section('content')
    <?php
    $logged_user_info = getLoggeduserProfile();
    $my_permissions = $logged_user_info->permissions;
    ?>
            <!-- Main content -->
    <section class="content">
  
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title"> Return Reasons</h3>
                    <div>

                         <a href="{{  route('return-reasons.create') }}" class="btn btn-success btn-sm"> <i class="fas fa-plus"></i> Create</a>
                    </div>
                   
                </div>
            </div>

            <div class="box-body">
                @include('message')


                <div class="col-md-12">
                    <table class="table table-bordered table-hover" id="create_datatable">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Reason</th>
                            <th>Created By</th>
                            <th>Use For Pos</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach ($reasons as $reason)
                            <tr>
                                <td>{{$loop->index + 1}}</td>
                                <td>{{$reason->reason}}</td>
                                <td>{{$reason->createdBy?->name}}</td>
                                <td>{{$reason->use_for_pos ? 'YES' : 'NO'}}</td>
                                <td>
                                    <a href="{{route('return-reasons.edit', base64_encode($reason->id)) }}" title="edit"><i class="fas fa-edit"></i></a>
                                    <form action="{{ route('return-reasons.destroy', $reason->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="background: none; border: none; cursor: pointer;">
                                            <i class="fas fa-trash-alt" style="color: red;"></i>
                                        </button>
                                    </form>

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
    <link rel="stylesheet" href="{{ asset('assets/admin/dist/datepicker.css') }}">
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet"/>
@endsection

@section('uniquepagescript')
    <script src="{{ asset('assets/admin/dist/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript">
        $(function () {

            $(".mlselect").select2();
        });
    </script>

    <script>
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd'
        });
    </script>



@endsection
