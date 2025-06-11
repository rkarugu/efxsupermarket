@extends('layouts.admin.admin')

@section('content')
    <style>
        .span-action {

            display: inline-block;
            margin: 0 3px;

        }
    </style>
    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <div class="col-md-9">
                        <h3>Loaders</h3>

                      
                       
                    </div>
                    <div class="col-sm-3">
                        @if(isset($permission[$pmodule.'___manage-discount']) || $permission == 'superadmin')
                            <div align="right"><a href="{!! route('loaders.create')!!}" class="btn btn-success">Add Loader</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="box-body">  
                            @include('message')
                <div class="col-md-12 no-padding-h">
                    <table class="table table-bordered table-hover" id="create_datatable_10">
                        <thead>
                        <tr>
                            <th>#</th>

                            <th >Name</th>
                            <th >Phone</th>
                            <th >ID Number</th>
                            <th >Branch</th>
                            <th >Action</th>
                            
                        </tr>
                        </thead>
                        <tbody>
                            @foreach ($loaders as $loader)
                            <tr>
                                <td>{{$loop->index+1}}</td>
                            <td>{{$loader->name}}</td>
                            <td>{{$loader->phone_number}}</td>
                            <td>{{ $loader->id_number}}</td>
                            <td>{{ getRestaurantNameById($loader->restaurant_id) }}</td>
                            <td><a href="{{route('loaders.edit', $loader->id)}}"><i class="fa fa-pen" title="Edit Loader"></i></a></td>

                        </tr>
                                
                            @endforeach
                        </tbody>

                    </table>
                </div>
        </div>
        </div>



    </section>

  

@endsection

