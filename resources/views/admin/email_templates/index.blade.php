
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
                        <div class="box-header with-border no-padding-h-b">
                           
                            @include('message')
                            <div class="col-md-12 no-padding-h table-responsive">
                                <h3>{{$title}}</h3>
                            </div>
                            <div class="col-md-12 no-padding-h table-responsive">
                                <table class="table table-bordered table-hover">
                                    <tr>
                                        <th>Name</th>
                                        <th>Subject</th>
                                        <th>Action</th>
                                    </tr>
                                    @foreach($data as $key => $template)
                                        <tr>
                                            <td>{{ $template->name }}</td>
                                            <td>{{ $template->subject }}</td>
                                            <td><a href="{{ route('admin.email_templates.edit', $key) }}">Edit</a></td>
                                        </tr>
                                    @endforeach
                                    
                                </table>
                            </div>
                        </div>
                    </div>


    </section>
  
    @endsection
   