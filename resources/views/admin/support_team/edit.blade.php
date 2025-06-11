@extends('layouts.admin.admin')
@section('content')
    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title" style="font-weight: 500 !important;"> Edit {{$title}} </h3>
                    <div class="d-flex">
                        
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <div class="col-md-12 no-padding-h" id="getintervalview">
                    <form class="validate form-horizontal"  role="form" method="POST" action="{{ route($model.'.update',$support->id) }}">
                        @csrf
                        @method('PATCH')
                        <div class="">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-2 text-left">Name</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" value="{{ $support->user->name }}" disabled>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-2 text-left">Get Notification</label>
                                <div class="col-sm-10">
                                    <div class="d-flex">
                                        <div class="form-check form-check-inline" style="margin-right:10px;">
                                            <input class="form-check-input" type="radio" name="notification"
                                                id="marginPercentage" value="1" @if ($support->get_notifications) checked @endif required>
                                            <label class="form-check-label" for="marginPercentage">Yes</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="notification" id="no"
                                                value="0" @if (!$support->get_notifications) checked @endif>
                                            <label class="form-check-label" for="no">No</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>            
                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('uniquepagestyle')

</style>    
@endsection
@section('uniquepagescript')

<script>
    
    $(document).ready(function() {
        
    });

        
    </script>
@endsection