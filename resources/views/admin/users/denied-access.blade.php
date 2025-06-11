<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <title>Access Denied - #</title>

    <link rel="icon" href="{{ asset('favicon.ico') }}">

    <link rel="stylesheet" href="{{asset('css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('css/style.css')}}">

    <style>
        .error {
            color: red;
        }
    </style>
</head>
<body>
<div class="main-doc">
    <div class="container">
        <div class="row login-page">
            <div class="col-md-6 text-center vertical-center">
                <div id="login" class="">
                    <img src="{{ asset('/assets/admin/images/loginLogo.png') }}" alt="bizwiz POS & ERP System">
                </div>
            </div>


            <div class="col-md-6" style="padding-right:0;">
                @if(session('active_message'))
                <div class="alert alert-danger" style="margin:10px;">
                    {{ session('active_message') }}
                </div>
            @endif
                <div id="login">
                    

                    
                    {!! Form::open(['route' => 'admin.make.access-request','class'=>'validate form-horizontal']) !!}

                    <div class="input-group">
                        @include('message')
                    </div>
                     
                    <div class="input-group"> 
                        <input type="text" name="request_access" id="password" class="form-control" required placeholder="Request for access on this device by writing your reason here ..."/>
                        <div class="form-group">
                        </div>
                        <input type="submit" value="Request Access" class="btn btn-primary">
                    </div>
                    <div style="height:3vh;width:100%">
                    {{ Form::close() }}
                </div>
            </div>


        </div>
    </div>
</div>

<script src="{{asset('assets/admin/bower_components/jquery/dist/jquery.min.js')}}"></script>
<!-- jQuery UI 1.11.4 -->

<script src="{{asset('assets/admin/bower_components/bootstrap/dist/js/bootstrap.min.js')}}"></script>
<script src="{{asset('assets/admin/jquery.validate.min.js')}}"></script>
<script>
    $(".validate").validate();
</script>
</body>
</html>