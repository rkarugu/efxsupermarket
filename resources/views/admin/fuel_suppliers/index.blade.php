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
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title">Fuel Suppliers</h3>
                    <div>
                        @if(isset($permission[$pmodule.'___add']) || $permission == 'superadmin')
                            <a href="{!! route('fuel-suppliers.create')!!}" class="btn btn-success">Create Fuel Supplier</a>    
                        @endif
                    </div>
                </div>
            </div>
            <div class="box-body">
               
                @include('message')
                <div class="col-md-12 no-padding-h">
                    <table class="table table-bordered table-hover" id="create_datatable_10">
                        <thead>
                        <tr>
                            <th width="3%">#</th>
                            <th>Supplier Code</th>
                            <th>Name</th>
                            <th>Address</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Credit Limit</th>
                            <th>Balance</th>
                            <th>Action</th>                           
                            
                        </tr>
                        </thead>
                        <tbody>
                            @foreach ($fuelSuppliers as $supplier)
                            <tr>
                                <th>{{$loop->index + 1}}</th>
                                <td>{{$supplier->supplierDetails?->supplier_code}}</td>
                                <td>{{$supplier->supplierDetails?->name}}</td>
                                <td>{{$supplier->supplierDetails?->address}}</td>
                                <td>{{$supplier->supplierDetails?->email}}</td>
                                <td>{{$supplier->supplierDetails?->telephone}}</td>
                                <td>{{$supplier->supplierDetails?->credit_limit}}</td>
                                <td>{{$supplier->supplierDetails?->suppTrans?->sum('total_amount_inc_vat')}}</td>
                                <td>
                                    <div class="action-button-div">
                                        <a title="Vendor centre" href="#"><i
                                            class="fa fa-store"></i></a>
                                      
                                        @if(isset($permission[$pmodule.'___delete']) || $permission == 'superadmin')
    
                                            <button type="button"  class="text-primary mr-2 btn-decline2 transparent-btn" data-toggle="modal" title="Delete" data-target="#confirmationModal2" data-supplier-id="{{ $supplier->id }}" data-supplier-name="{{$supplier->supplierDetails->name}}" >
                                                <i class="fas fa-trash-alt" style="color: red;"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>     
                            @endforeach
                          
                        </tbody>
                     

                    </table>
                </div>

            </div>

               
        </div>

{{-- Delete --}}
<div class="modal fade" id="confirmationModal2" data-backdrop="static" data-keyboard="false" tabindex="-1"
aria-labelledby="staticBackdropLabel" aria-hidden="true">
<div class="modal-dialog" role="document">
    <div class="box box-primary">
        <div class="box-header with-border">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="modal-title" id="confirmationModalLabel">Confirm Action</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>
        <div class="box-body">
            <h5 id="staticBackdropLabel">Are you sure you want to delete <span id="supplier-name"></span> as fuel supplier?</h5>


        </div>
        
        <form method="POST" id="confirmationForm2" action="">
            @csrf
            @method("DELETE")
            
            <input name="user_requested_access2" type="hidden" id="user_requested_access2"
                    value="{{ old('user_requested_access2') }}" required />
           
            <div class="modal-footer">
                <div class="d-flex justify-content-between align-items-center">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-submit-updated-center2">Yes, Delete Supplier</button>
                </div>
            </div>
        </form>
    </div>
</div>
</div>


    </section>

  

@endsection
@section('uniquepagestyle')
    <link rel="stylesheet" href="{{ asset('assets/admin/dist/datepicker.css') }}">
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet"/>
@endsection
@section('uniquepagescript')
<script src="{{ asset('assets/admin/dist/bootstrap-datepicker.js') }}"></script>
<script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
<script type="text/javascript">
    $(function () {

        $(".mlselect").select2();
    });
</script>
<script>
    $(document).ready(function() {

        $('.btn-decline2').click(function() {
            var supplierId = $(this).data('supplier-id');
            var supplierName = $(this).data('supplier-name');
            console.log(supplierId);
            console.log(supplierName)
            $('#supplier-name').text(supplierName);

            $('#confirmationModal2').find('#supplier-name').val(supplierName);
            var actionUrl = '{{ route('fuel-suppliers.destroy', ':supplierId') }}';
            $('#confirmationForm2').attr('action', actionUrl.replace(':supplierId', supplierId));
        });
    
        $('#confirmationModal2').on('show.bs.modal', function(event) {
            var modal = $(this);
            modal.find('.btn-submit-updated-center2').off('click').on('click', function() {
                modal.find('form').submit();
                modal.modal('hide');
            });
        });

      

    });
    </script>
@endsection



