
@extends('layouts.admin.admin')

@section('content')

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                        <div class="box-header with-border no-padding-h-b">
                            @include('message')
                            <div class="col-md-12 no-padding-h">
                               <form action="{{route('edit-ledger.update',$lists->id)}}" method="POST" class="submitMe">
                                {{csrf_field()}}
                                {{method_field('PUT')}}
                                <input type="hidden" name="id" value="{{$lists->id}}">
                                    <div class="form-group">
                                      <label for="">Transaction : {{$lists->transaction_no}}</label>
                                      <input type="hidden" name="transaction" value="{{$lists->transaction_no}}" id="transaction" class="form-control" placeholder="Enter Transaction Number" aria-describedby="helpId">
                                    </div>

                                    <div class="form-group">
                                        <label for="">Branch</label>
                                        <select name="restaurant_id" class="form-control select-dropdown">
                                            <option value="" selected disabled>Select Branch</option>
                                            @foreach ($resturants as $item)
                                                <option value="{{$item->id}}" {{$lists->restaurant_id == $item->id ? 'selected' : NULL}}>{{$item->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="">Department</label>
                                        <select name="department_id" class="form-control select-dropdown">
                                            <option value="" selected disabled>Select Department</option>
                                            @foreach ($departments as $item)
                                                <option value="{{$item->id}}" {{$lists->department_id == $item->id ? 'selected' : NULL}}>{{$item->department_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="">Projects</label>
                                        <select name="project_id" class="form-control select-dropdown">
                                            <option value="" selected disabled>Select Projects</option>
                                            @foreach ($Projects as $item)
                                                <option value="{{$item->id}}" {{$lists->project_id == $item->id ? 'selected' : NULL}}>{{$item->title}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="">Transaction Date</label>
                                                <input type="date" name="trans_date" id="trans_date" class="form-control" value="{{date('Y-m-d',strtotime($lists->trans_date))}}">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="">Transaction Time</label>
                                                <input type="time" name="trans_time" id="trans_time" class="form-control" value="{{date('H:i:s',strtotime($lists->trans_date))}}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="">Account</label>
                                        <select name="account" class="form-control select-dropdown">
                                            <option value="" selected disabled>Select Account</option>
                                            @foreach ($glaccounts as $item)
                                                <option value="{{$item->id}}" {{$lists->account == $item->account_code ? 'selected' : NULL}}>{{$item->account_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                        
                                        <div class="form-group">
                                            <label for="">Narrative</label>
                                            <input type="text" name="narrative" id="narrative" class="form-control" value="{{$lists->narrative}}">
                                        </div>
                                        <div class="form-group">
                                            <label for="">Reference</label>
                                            <input type="text" name="reference" id="reference" class="form-control" value="{{$lists->reference}}">
                                        </div>
                                   
                                    <div class="form-group">
                                        <label for="">Account</label>
                                        <select name="gl_tag" class="form-control select-dropdown">
                                            <option value="" selected disabled>Select Account</option>
                                            @foreach ($gl_tags as $item)
                                                <option value="{{$item->id}}" {{$lists->gl_tag == $item->id ? 'selected' : NULL}}>{{$item->title}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="">Balancing Account</label>
                                        <select name="balancing_gl_account" class="form-control select-dropdown">
                                            <option value="" selected disabled>Select Balancing Account</option>
                                            @foreach ($glaccounts as $item)
                                                <option value="{{$item->id}}" {{$lists->balancing_gl_account == $item->account_code ? 'selected' : NULL}}>{{$item->account_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                                                     
                                    <!-- Modal -->
                                    <div class="modal fade" id="modelId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Modal title</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                </div>
                                                <div class="modal-body">
                                                    Are you sure you want to edit the transaction
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary">Update</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#modelId">Update</button>
                               </form>

                             
                            </div>
                        </div>
                    </div>


    </section>

@endsection
@section('uniquepagestyle')
 <link rel="stylesheet" href="{{asset('assets/admin/dist/datepicker.css')}}">
 <link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet" />
 <style>
     .select2-container {
        width: 100% !important;
        padding: 0;
    }
 </style>
@endsection
@section('uniquepagescript')
<script src="{{asset('public/js/sweetalert.js')}}"></script>
<script src="{{asset('public/js/form.js')}}"></script>
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
<script type="text/javascript">
    $(function () {
    $(".select-dropdown").select2();
});
</script>
@endsection