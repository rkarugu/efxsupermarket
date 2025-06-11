
@extends('layouts.admin.admin')

@section('content')

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                        <div class="box-header with-border no-padding-h-b">
                            @include('message')
                            <div class="col-md-12 no-padding-h">
                               <form action="" method="get">
                                    <div class="form-group">
                                      <label for="">Search Transaction</label>
                                      <input type="text" name="transaction" value="{{request()->transaction}}" id="transaction" class="form-control" placeholder="Enter Transaction Number" aria-describedby="helpId">
                                    </div>
{{--                                    <b>Do you want to--}}
{{--                                    @if (isset($permission[$pmodule . '___reverse_transaction']) || $permission == 'superadmin')--}}
{{--                                    <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#reverseTr">Reverse</button>--}}
{{--                                    @endif--}}
{{--                                    @if (isset($permission[$pmodule . '___edit_transaction']) || $permission == 'superadmin')--}}
{{--                                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#editTr">Edit</button>--}}
{{--                                    @endif--}}
                                    @if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin')
                                    <button type="button" data-toggle="modal" data-target="#viewTr" class="btn btn-primary">View Transaction</button>
                                    @endif
{{--                                    Transaction?</b>--}}
                                    
                                    
                                    <div class="modal fade" id="reverseTr" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-body">
                                                    Are you sure you want to check the details to reverse
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                                                    <button type="submit" name="manage" value="reversal" class="btn btn-primary">Yes</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal fade" id="editTr" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-body">
                                                    Are you sure you want to check the details to Edit
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                                                    <button type="submit" name="manage" value="edit" class="btn btn-primary">Yes</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                     <div class="modal fade" id="viewTr" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-body">
                                                    Are you sure you want to check the details of transaction
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                                                    <button type="submit" name="manage" value="view" class="btn btn-primary">Yes</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


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
   
    $(".mlselect").select2();
});
</script>
@endsection