@extends('layouts.admin.admin')
@section('content')
<section class="content" style="padding-bottom:0px;">
    <div class="box box-primary" style="margin-bottom: 0px;">
        <div class="box-header with-border">
            <div class="box-header-flex">
                <h4 class="box-title" style="font-weight: 500;"> Test Message </h4>
            </div>
        </div>
        <div class="session-message-container">
            @include('message')
        </div>
        <form action="{{route('bulk-sms.test-message-save')}}" method="POST">
            @csrf
            <div class="row">
                <div class="col-sm-6">
                   
                    <div class="box-body">
                        <div class="form-group" style="margin-bottom: 0px">
                            <label for="issn" class="col-sm-5 text-left" style="padding-left: 0px;">ISSN:</label>
                            <div class="col-sm-7">
                                <select name="issn" id="issn" class="select2 form-control" required>
                                    <option value="">Choose ISSN</option>
                                    <option value="{{ env("KANINI_SMS_SENDER_ID_2") }}">{{ env("KANINI_SMS_SENDER_ID_2") }}</option>
                                    <option value="{{ env("AIRTOUCH_ISSN") }}">{{ env("AIRTOUCH_ISSN") }}</option>
                                    <option value="{{ env("KANINI_SMS_SENDER_ID") }}">{{ env("KANINI_SMS_SENDER_ID") }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="form-group" style="margin-bottom: 0px">
                            <label for="msisdn" class="col-sm-5 " style="padding-left: 0px;">MSISDN:</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control" name="msisdn" value="{{old('msisdn')}}" placeholder="245700000" required>                                       
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="form-group" style="margin-bottom: 0px">
                            <label for="message" class="col-sm-5 text-left" style="padding-left: 0px;">Message:</label>
                            <div class="col-sm-7">
                                <textarea name="message" id="message" class="form-control" required></textarea>    
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <button class="btn btn-primary">
                            Send
                        </button>
                    </div>
                </div>
                <div class="col-sm-6">
                    
                </div>
            </div>
        </form>
    </div>
</section>
    
@endsection
@section('uniquepagestyle')
<link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet" />
<style>
    .select2.select2-container.select2-container--default
    {
        width: 100% !important;
    }

</style>
@endsection
@section('uniquepagescript')
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>

<script>
    $(document).ready(function() {
        $('body').addClass('sidebar-collapse');
        $('.select2').select2();
    
    });       
</script>
@endsection