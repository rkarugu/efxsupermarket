@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"> Supplier Emails </h3>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <table class="table">
                            <tr>
                                <th>Supplier</th>
                                <td>
                                    {{$trade->supplier->name}}
                                </td>
                            </tr>
                            
                            <tr>
                                <th>Trade Reference</th>
                                <td> {{$trade->reference}} </td>
                            </tr>
                        </table>                        
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <table class="table table-hover table-bordered">
                            <tr>
                                <th>
                                    Name
                                </th>
                                <th>
                                    Subscribers
                                </th>
                            </tr>
                            @foreach ($data as $item)
                                <tr>
                                    <td>{{$item['name']}}</td>
                                    <td>{{$item['subscribers']}}</td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
