
@extends('layouts.admin.admin')

@section('content')

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                        <div class="box-header with-border no-padding-h-b">
                            @include('message')
                            <div class="col-md-12 no-padding-h">
                               <form action="{{route('edit-ledger.supplier-trans.update',$lists->id)}}" method="POST" class="submitMe">
                                {{csrf_field()}}
                                {{method_field('PUT')}}
                                <input type="hidden" name="id" value="{{$lists->id}}">
                                    <div class="form-group">
                                      <label for="">Transaction : {{$lists->document_no}}</label>
                                      <input type="hidden" name="transaction" value="{{$lists->document_no}}" id="transaction" class="form-control" placeholder="Enter Transaction Number" aria-describedby="helpId">
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
                                        <label for="">Reference</label>
                                        <input type="text" name="suppreference" id="suppreference" class="form-control" value="{{$lists->suppreference}}">
                                    </div>

                                    <div class="form-group">
                                        <label for="">Balancing Account</label>
                                        <select name="balancing_gl_account" class="form-control select-dropdown">
                                            <option value="" selected disabled>Select Balancing Account</option>
                                            @foreach ($glaccounts as $item)
                                                <option value="{{$item->id}}" {{$lists->bank_gl_account_code == $item->account_code ? 'selected' : NULL}}>{{$item->account_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-danger">Update</button>
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