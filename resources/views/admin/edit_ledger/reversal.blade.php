
@extends('layouts.admin.admin')

@section('content')

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                        <div class="box-header with-border no-padding-h-b">
                            @include('message')
                            <div class="col-md-12 no-padding-h">
                               
                                    <div class="form-group">
                                      <label for="">Transaction : {{request()->transaction}}</label>
                                    </div>
                                
                                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#modelId">Reverse</button>

                             
                               <h4>General Ledger Transactions</h4>
                               <table class="table table-bordered table-hover" id="create_datatable1">
                                <thead>
                               
                                <tr> 
                                    <td width="8%">Date</td>
                                    <td width="10%">Type</td>
                                    <td width="12%">Trans No</td>
                                    <td width="12%">GL Acc</td>
                                    <td width="12%">Supplier No</td>
                                    <td width="18%">Particulars</td>
                                    <td width="10%">Debit</td>
                                    <td width="10%">Credit</td>
                                </tr>
                                </thead>
                                <tbody>
                               
                                    <?php 
                                  
                                                                     
                                                $accountsss = $glaccounts;
                                    
                                                                            ?>
                                    @foreach($lists as $list)
                                     
                                    <tr class="details detailsParent tddHead"  > 
                                        <td>{!! date('d/M/Y',strtotime($list->trans_date)) !!}</td>
                                     
                                         <td>{!! $list->transaction_type !!}</td>
                                         <td>{!! $list->transaction_no !!}</td>
                                         @if ($list->transaction_type == 'Journal')
                                         <td>{!! @$list->getAccountDetail->account_name ?? NULL !!}</td>
                                     @else
                                         @php
                                             $acGL = $accountsss->where('account_code',$list->account)->first();
                                         @endphp
                                         <td>{!! @$acGL->account_name ?? NULL !!}</td>
                                     @endif
                                         <td>{!! @$list->supplier_account_number !!}</td>
                                         <td>{!! $list->narrative !!}</td>
                                         <td>{!! $list->amount > 0 ? @manageAmountFormat($list->amount) : '-' !!}</td>
                                         <td>{!! $list->amount < 0 ? @manageAmountFormat(abs($list->amount)) : '-' !!}</td>
                                       
                                  
                                     </tr>
                                    @endforeach
                              


                                </tbody>
                            
                            </table>
                          
                            <br>
                            <h4>Bank Transactions</h4>
                            <table class="table table-bordered table-sm table-hover">
                                <thead>
                                    <tr> 
                                        <td>Date</td>
                                        <td>Trans No</td>
                                        <td>GL Acc</td>
                                        <td>Bank GL Acc</td>
                                        <td>Reference</td>
                                        <td>Debit</td>
                                        <td>Credit</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($bank_trans as $item)
                                    <tr>
                                        <td>{!! date('d/M/Y',strtotime($item->trans_date)) !!}</td>
                                        <td>{{$item->document_no}}</td>
                                        <td>{{$item->account}}</td>
                                        <td>{{$item->bank_gl_account_code}}</td>
                                        <td>{{$item->reference}}</td>
                                        <td>{!! $item->amount > 0 ? @manageAmountFormat($item->amount) : '-' !!}</td>
                                         <td>{!! $item->amount < 0 ? @manageAmountFormat(abs($item->amount)) : '-' !!}</td>
                                    </tr>                                        
                                    @endforeach
                                </tbody>
                            </table>

                            <br>
                            <h4>Debtor Transactions</h4>
                            <table class="table table-bordered table-sm table-hover">
                                <thead>
                                    <tr> 
                                        <td>Date</td>
                                        <td>Trans No</td>
                                        <td>Customer</td>
                                        <td>Reference</td>
                                        <td>Debit</td>
                                        <td>Credit</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($debtor_trans as $item)
                                    <tr>
                                        <td>{!! date('d/M/Y',strtotime($item->trans_date)) !!}</td>
                                        <td>{{$item->document_no}}</td>
                                        <td>{{$item->customer_number}}</td>
                                        <td>{{$item->reference}}</td>
                                        <td>{!! $item->amount > 0 ? @manageAmountFormat($item->amount) : '-' !!}</td>
                                         <td>{!! $item->amount < 0 ? @manageAmountFormat(abs($item->amount)) : '-' !!}</td>
                                    </tr>                                        
                                    @endforeach
                                </tbody>
                            </table>



                            <br>
                            <h4>Supplier Transactions</h4>
                            <table class="table table-bordered table-sm table-hover">
                                <thead>
                                    <tr> 
                                        <td>Date</td>
                                        <td>Trans No</td>
                                        <td>Supplier No</td>
                                        <td>Gl Account</td>
                                        <td>Reference</td>
                                        <td>Credit</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($supp_trans as $item)
                                    <tr>
                                        <td>{!! date('d/M/Y',strtotime($item->trans_date)) !!}</td>
                                        <td>{{$item->document_no}}</td>
                                        <td>{{$item->supplier_no}}</td>
                                        <td>{{$item->account}}</td>
                                        <td>{{$item->suppreference}}</td>
                                         <td>{!! manageAmountFormat(($item->total_amount_inc_vat)) !!}</td>
                                    </tr>                                        
                                    @endforeach
                                </tbody>
                            </table>



                            </div>
                        </div>
                    </div>


    </section>    
    
    <!-- Modal -->
    <div class="modal fade" id="modelId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <form action="{{route('edit-ledger.destroy',request()->transaction)}}" method="POST"  class="submitMe">
                                {{csrf_field()}}
                                {{method_field('DELETE')}}
                                      <input type="hidden" name="transaction" value="{{request()->transaction}}" id="transaction" class="form-control" placeholder="Enter Transaction Number" aria-describedby="helpId">

        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="">Transaction : {{request()->transaction}}</label>
                    </div>
                    Are you sure you want to reverse the transaction
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                    <button type="submit" class="btn btn-primary">Yes</button>
                </div>
            </div>
        </div>
        </form>
    </div>

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