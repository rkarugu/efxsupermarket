@extends('layouts.admin.admin')

@section('content')
    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title" style="font-weight: 500 !important;"> {!! $title !!} </h3>
                    <div class="d-flex">
                        <a href="{{ route('admin.show.item.log') }}" class="btn btn-primary" style="margin-top:0px;"><i class="fas fa-long-arrow-alt-left"></i> Back</a>
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="box-body">
                            <div class="form-group" style="margin-bottom: 0px">
                                <label for="inputEmail3" class="col-sm-5 text-left">Title</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control" class="form-control" value="{{ $item->inventoryItem->title }}" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="form-group" style="margin-bottom: 0px">
                                <label for="inputEmail3" class="col-sm-5 text-left">Category</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control" class="form-control" value="{{ $item->inventoryItem?->category ? $item->inventoryItem?->category->category_description : '' }}" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="form-group" style="margin-bottom: 0px">
                                <label for="inputEmail3" class="col-sm-5 text-left">Category</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control" class="form-control" value="{{ date('d M, Y H:m', strtotime($item->created_at)); }}" disabled>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="box-body">
                            <div class="form-group" style="margin-bottom: 0px">
                                <label for="inputEmail3" class="col-sm-5 text-left">Action By</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control" class="form-control" value="{{ $item->approvalBy->name }}" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="form-group" style="margin-bottom: 0px">
                                <label for="inputEmail3" class="col-sm-5 text-left">Status</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control" class="form-control" value="{{ $item->status }}" disabled>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12"><hr></div>
                    <div class="col-sm-12">
                        <table class="table">
                            <thead>
                                <th></th>
                                <th>Edited Information</th>
                                <th>Original Information</th>
                            </thead>
                            <tbody>
                                @if ($changes)
                                    @foreach ($changes as $change)
                                        @php
                                            $key = key((array)$change);
                                        @endphp
                                        <tr>
                                            <td><b>{{$key}}</b></td>
                                            @foreach ($change as $item)
                                                <td>{{$item[1]}}</td>
                                                <td> {{$item[0]}}</td>
                                            @endforeach                                            
                                        </tr>
                                    @endforeach
                                @endif                   
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('uniquepagestyle')
    
@endsection

@section('uniquepagescript')

@endsection
