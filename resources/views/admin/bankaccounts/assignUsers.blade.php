
@extends('layouts.admin.admin')
@section('content')
<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border"><h3 class="box-title"> {!! $title !!} </h3></div>
         @include('message')
         <form action="{{route('bank-accounts.assignUsers',$row->id)}}" method="post" class="submitMe">
            {{csrf_field()}}
            <input type="hidden" name="id" value="{{$row->id}}">
            <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <label for="">Select Users</label>
                    <div>
                        <span class="user"></span>
                    </div>
                    <div class="form-check">
                    @foreach ($users as $user)
                        <label class="form-check-label" style="margin: 4px">
                            <input type="checkbox" class="form-check-input" name="user[{{$user->id}}]"  value="{{$user->id}}" @if(in_array($user->id,$assinedUserIds)) checked @endif>
                            {{$user->name}}
                        </label>
                    @endforeach
                    </div>
                </div>
                <div class="col-md-12">
                    <button type="submit" class="btn btn-danger">Save</button>
                </div>
            </div>
        </div>
            </form>
    </div>
</section>
@endsection

@section('uniquepagestyle')

@endsection

@section('uniquepagescript')

<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>

<script src="{{asset('js/sweetalert.js')}}"></script>
<script src="{{asset('js/form.js')}}"></script>
@endsection




