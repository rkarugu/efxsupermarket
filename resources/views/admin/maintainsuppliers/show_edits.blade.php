@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Comparison</h3>
                    
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <div class="row">
                    <div class="col-md-6">

                     <table class="supplier-table edited-info">
                         <h4>Edited Infomation</h4>
    <tbody>
        
        <tr>
            <td>
               <form method="get" action="{{ route('maintain-suppliers.updatesupplier', $records->supplier_code ) }}">

                    <div class="supplier-info">
                    <span class="label2">Supplier Code:</span>
                    <span class="value">{{ $records->supplier_code }}</span>
                </div>
                <div class="supplier-info">
                    <span class="label2">Supplier Name:</span>
                    <span class="value">{{ $records->name }}</span>
                </div>
       <div class="supplier-info">
                    <span class="label2">Supplier Address:</span>
                    <span class="value">{{ $records->address }}</span>
                </div>
                 <div class="supplier-info">
                    <span class="label2">Supplier Country:</span>
                    <span class="value">{{ $records->country }}</span>
                </div>
                 <div class="supplier-info">
                    <span class="label2">Supplier Telephone:</span>
                    <span class="value">{{ $records->telephone }}</span>
                </div>
                 <div class="supplier-info">
                    <span class="label2">Supplier Email:</span>
                    <span class="value">{{ $records->email }}</span>
                </div>
                 <div class="supplier-info">
                    <span class="label2">Supplier URL:</span>
                    <span class="value">{{ $records->url }}</span>
                </div>
                 <div class="supplier-info">
                    <span class="label2">Supplier Facsmile:</span>
                    <span class="value">{{ $records->facsimile }}</span>
                </div>
                 <div class="supplier-info">
                    <span class="label2">Supplier Type:</span>
                    <span class="value">{{ $records->supplier_type }}</span>
                </div>
                
                <div class="supplier-info">
                    <span class="label2">Supplier Since:</span>
                    <span class="value">{{ $records->supplier_since }}</span>
                </div>
                 <div class="supplier-info">
                    <span class="label2">Supplier Bank reference:</span>
                    <span class="value">{{ $records->bank_reference }}</span>
                </div>
                 <div class="supplier-info">
                    <span class="label2">Supplier Payment Terms :</span>
                    <span class="value">{{ $records->term_description }}</span>
                </div>
                
                  <div class="supplier-info">
                    <span class="label2">Supplier Remittance Advice :</span>
                    <span class="value">{{ $records->remittance_advice }}</span>
                </div>
                 <div class="supplier-info">
                    <span class="label2">Supplier Service Type :</span>
                    <span class="value">{{ $records->service_type }}</span>
                </div>
                <div class="supplier-info">
                    <span class="label2">Supplier Tax Withold :</span>
                    <span class="value">
                        @if($records->tax_withhold == 1 )
                        {
                            Yes

                        }@else{
                        No
                        }
                         @endif
                    </span>
                </div>

                 <div class="supplier-info">
                    <span class="label2">Supplier Payment Terms :</span>
                    <span class="value">{{ $records->term_description }}</span>
                </div>
                 <div class="supplier-info">
                    <span class="label2">Supplier Currency :</span>
                    <span class="value">{{ $records->ISO4217 }}</span>
                </div>
                  <div class="supplier-info">
                    <span class="label2">Supplier Remittance Advice :</span>
                    <span class="value">{{ $records->remittance_advice }}</span>
                </div>
                 <div class="supplier-info">
                    <span class="label2">Supplier Service Type :</span>
                    <span class="value">{{ $records->service_type }}</span>
                </div>
                <div class="supplier-info">
                    <span class="label2">Supplier Tax Withold :</span>
                    <span class="value">
                        @if($records->tax_withhold == 1 )
                        {
                            Yes

                        }@else{
                        No
                        }
                         @endif
                    </span>
                </div>

                 <div class="supplier-info">
                    <span class="label2">Supplier Tax Group:</span>
                    <span class="value">{{ $records->tax_group }}</span>
                </div>
                 <div class="supplier-info">
                    <span class="label2">Supplier KRA pin:</span>
                    <span class="value">{{ $records->kra_pin }}</span>
                </div>
               
                 <div class="supplier-info">
                    <span class="label2">Supplier Purchase Order Blocked :</span>
                    <span class="value">
                        @if($records->purchase_order_blocked == 1 )
                        {
                            Yes

                        }@else{
                        No
                        }
                         @endif
                    </span>
                </div>
                 <div class="supplier-info">
                    <span class="label2">Supplier Payements Blocked :</span>
                    <span class="value">
                        @if($records->payments_blocked == 1 )
                        {
                            Yes

                        }@else{
                        No
                        }
                         @endif
                    </span>
                </div>
                 <div class="supplier-info">
                    <span class="label2">Supplier Block note:</span>
                    <span class="value">{{ $records->blocked_note }}</span>
                </div>

                 <div class="supplier-info">
                    <span class="label2">Supplier KRA pin:</span>
                    <span class="value">{{ $records->kra_pin }}</span>
                </div>
                 <div class="supplier-info">
                    <span class="label2">Supplier Transport:</span>
                    <span class="value">{{ $records->transport }}</span>
                </div>
                 <div class="supplier-info">
                    <span class="label2">Supplier Purchase Order Blocked :</span>
                    <span class="value">
                        @if($records->purchase_order_blocked == 1 )
                        {
                            Yes

                        }@else{
                        No
                        }
                         @endif
                    </span>
                </div>
                 <div class="supplier-info">
                    <span class="label2">Supplier Payements Blocked :</span>
                    <span class="value">
                        @if($records->payments_blocked == 1 )
                        {
                            Yes

                        }@else{
                        No
                        }
                         @endif
                    </span>
                </div>
                 <div class="supplier-info">
                    <span class="label2">Supplier Block note:</span>
                    <span class="value">{{ $records->blocked_note }}</span>
                </div>

                 <div class="supplier-info">
                    <span class="label2">Supplier Bank Name:</span>
                    <span class="value">{{ $records->bank_name }}</span>
                </div>
                 <div class="supplier-info">
                    <span class="label2">Supplier Bank Branch:</span>
                    <span class="value">{{ $records->bank_branch }}</span>
                </div>
                 <div class="supplier-info">
                    <span class="label2">Supplier Bank Account Number:</span>
                    <span class="value">{{ $records->bank_account_no }}</span>
                </div>

                 <div class="supplier-info">
                    <span class="label2">Supplier Swift:</span>
                    <span class="value">{{ $records->bank_swift }}</span>
                </div>
                 <div class="supplier-info">
                    <span class="label2">Supplier Bank Cheque Payee:</span>
                    <span class="value">{{ $records->bank_cheque_payee }}</span>
                </div> <div class="supplier-info">
                    <span class="label2">Supplier Portal Status:</span>
                    <span class="value">{{ $records->portal_status }}</span>
                </div>

                 <div class="supplier-info">
                    <span class="label2">Supplier Sub Distributors:</span><br>
                    <span class="value"><br>
                    <ul   style="float: left !important ;margin-left:-4rem;  ">
                        @foreach($distributors as $distributor)
                         <li class="form-control" style="float: left;margin-top: 5px">{{ $distributor->suppliername }}</li>
                        @endforeach                       
                    </ul>
                </span>
                </div>
                <div class="supplier-info">
                    <span class="label2">Supplier Credit Limit:</span>
                    <span class="value">{{ number_format($records->credit_limit) }}</span>
                </div>
                <div class="supplier-info">
                    <span class="label2">Supplier Monthly Target:</span>
                    <span class="value">{{ number_format($records->monthly_target) }}</span>
                </div>
                <div class="supplier-info">
                    <span class="label2">Supplier Quarterly Target:</span>
                    <span class="value">{{ number_format($records->quarterly_target) }}</span>
                </div>

                

                <button type="submit" class="btn btn-primary btn-sm">Approve</button>
               </form>

               <form method="POST" action="{{ route('maintain-suppliers.rejectSupplierLog',  $records->supplier_code ) }}" style="padding-top: 10px">
 @csrf
    <button type="submit" class="btn btn-danger btn-sm">Reject</button>

            
               </form>
               
            </td>
        </tr>
     
    </tbody>
</table>
    
                </div>

                <div class="col-md-6">

                     <table class="supplier-table original-info">
                         <h4>Original  Infomation</h4>
    <tbody>
   
        <tr>
            <td>
                <div class="supplier-info">
                    <span class="label2">Supplier Code:</span>
                    <span class="value">{{ $originalrecords->supplier_code }}</span>
                </div>
                <div class="supplier-info">
                    <span class="label2">Supplier Name:</span>
                    <span class="value">{{ $originalrecords->name }}</span>
                </div>

                 <div class="supplier-info">
                    <span class="label2">Supplier Address:</span>
                    <span class="value">{{ $originalrecords->address }}</span>
                </div>
                 <div class="supplier-info">
                    <span class="label2">Supplier Country:</span>
                    <span class="value">{{ $originalrecords->country }}</span>
                </div>
                 <div class="supplier-info">
                    <span class="label2">Supplier Telephone:</span>
                    <span class="value">{{ $originalrecords->telephone }}</span>
                </div>
                 <div class="supplier-info">
                    <span class="label2">Supplier Email:</span>
                    <span class="value">{{ $originalrecords->email }}</span>
                </div>
                 <div class="supplier-info">
                    <span class="label2">Supplier URL:</span>
                    <span class="value">{{ $originalrecords->url }}</span>
                </div>
                 <div class="supplier-info">
                    <span class="label2">Supplier Facsmile:</span>
                    <span class="value">{{ $originalrecords->facsimile }}</span>
                </div>
                 <div class="supplier-info">
                    <span class="label2">Supplier Type:</span>
                    <span class="value">{{ $originalrecords->supplier_type }}</span>
                </div>
               
                <div class="supplier-info">
                    <span class="label2">Supplier Since:</span>
                    <span class="value">{{ $originalrecords->supplier_since }}</span>
                </div>
                 <div class="supplier-info">
                    <span class="label2">Supplier Bank reference:</span>
                    <span class="value">{{ $originalrecords->bank_reference }}</span>
                </div>
                 <div class="supplier-info">
                    <span class="label2">Supplier Payment Terms :</span>
                    <span class="value">{{ $originalrecords->term_description }}</span>
                </div>
                  <div class="supplier-info">
                    <span class="label2">Supplier Remittance Advice :</span>
                    <span class="value">{{ $originalrecords->remittance_advice }}</span>
                </div>
                 <div class="supplier-info">
                    <span class="label2">Supplier Service Type :</span>
                    <span class="value">{{ $originalrecords->service_type }}</span>
                </div>
                <div class="supplier-info">
                    <span class="label2">Supplier Tax Withold :</span>
                    <span class="value">
                        @if($originalrecords->tax_withhold == 1 )
                        {
                            Yes

                        }@else{
                        No
                        }
                         @endif
                    </span>
                </div>

                 <div class="supplier-info">
                    <span class="label2">Supplier Payment Terms :</span>
                    <span class="value">{{ $originalrecords->term_description }}</span>
                </div>
                 <div class="supplier-info">
                    <span class="label2">Supplier Currency :</span>
                    <span class="value">{{ $originalrecords->ISO4217 }}</span>
                </div>
                  <div class="supplier-info">
                    <span class="label2">Supplier Remittance Advice :</span>
                    <span class="value">{{ $originalrecords->remittance_advice }}</span>
                </div>
                 <div class="supplier-info">
                    <span class="label2">Supplier Service Type :</span>
                    <span class="value">{{ $originalrecords->service_type }}</span>
                </div>
                <div class="supplier-info">
                    <span class="label2">Supplier Tax Withold :</span>
                    <span class="value">
                        @if($originalrecords->tax_withhold == 1 )
                        {
                            Yes

                        }@else{
                        No
                        }
                         @endif
                    </span>
                </div>

                 <div class="supplier-info">
                    <span class="label2">Supplier Tax Group:</span>
                    <span class="value">{{ $originalrecords->tax_group }}</span>
                </div>
                 <div class="supplier-info">
                    <span class="label2">Supplier KRA pin:</span>
                    <span class="value">{{ $originalrecords->kra_pin }}</span>
                </div>
                
                 <div class="supplier-info">
                    <span class="label2">Supplier Purchase Order Blocked :</span>
                    <span class="value">
                        @if($originalrecords->purchase_order_blocked == 1 )
                        {
                            Yes

                        }@else{
                        No
                        }
                         @endif
                    </span>
                </div>
                 <div class="supplier-info">
                    <span class="label2">Supplier Payements Blocked :</span>
                    <span class="value">
                        @if($originalrecords->payments_blocked == 1 )
                        {
                            Yes

                        }@else{
                        No
                        }
                         @endif
                    </span>
                </div>
                 <div class="supplier-info">
                    <span class="label2">Supplier Block note:</span>
                    <span class="value">{{ $originalrecords->blocked_note }}</span>
                </div>

                 <div class="supplier-info">
                    <span class="label2">Supplier KRA pin:</span>
                    <span class="value">{{ $originalrecords->kra_pin }}</span>
                </div>
                 <div class="supplier-info">
                    <span class="label2">Supplier Transport:</span>
                    <span class="value">{{ $originalrecords->transport }}</span>
                </div>
                 <div class="supplier-info">
                    <span class="label2">Supplier Purchase Order Blocked :</span>
                    <span class="value">
                        @if($originalrecords->purchase_order_blocked == 1 )
                        {
                            Yes

                        }@else{
                        No
                        }
                         @endif
                    </span>
                </div>
                 <div class="supplier-info">
                    <span class="label2">Supplier Payements Blocked :</span>
                    <span class="value">
                        @if($originalrecords->payments_blocked == 1 )
                        {
                            Yes

                        }@else{
                        No
                        }
                         @endif
                    </span>
                </div>
                 <div class="supplier-info">
                    <span class="label2">Supplier Block note:</span>
                    <span class="value">{{ $originalrecords->blocked_note }}</span>
                </div>

                 <div class="supplier-info">
                    <span class="label2">Supplier Bank Name:</span>
                    <span class="value">{{ $originalrecords->bank_name }}</span>
                </div>
                 <div class="supplier-info">
                    <span class="label2">Supplier Bank Branch:</span>
                    <span class="value">{{ $originalrecords->bank_branch }}</span>
                </div>
                 <div class="supplier-info">
                    <span class="label2">Supplier Bank Account Number:</span>
                    <span class="value">{{ $originalrecords->bank_account_no }}</span>
                </div>

                 <div class="supplier-info">
                    <span class="label2">Supplier Swift:</span>
                    <span class="value">{{ $originalrecords->bank_swift }}</span>
                </div>
                 <div class="supplier-info">
                    <span class="label2">Supplier Bank Cheque Payee:</span>
                    <span class="value">{{ $originalrecords->bank_cheque_payee }}</span>
                </div> <div class="supplier-info">
                    <span class="label2">Supplier Portal Status:</span>
                    <span class="value">{{ $originalrecords->portal_status }}</span>
                </div>

                <div class="supplier-info">
                    <span class="label2">Supplier Sub Distributors:</span><br>
                    <span class="value"><br>
                    <ul   style="float: left !important ;margin-left:-4rem;  ">
                        @foreach($origindistributors as $distributor)
                         <li class="form-control" style="float: left; margin-top: 5px;">{{ $distributor->suppliername }}</li>
                        @endforeach                       
                    </ul>
                </span>
                </div>
                <div class="supplier-info">
                    <span class="label2">Supplier Credit Limit:</span>
                    <span class="value">{{ number_format($originalrecords->credit_limit) }}</span>
                </div>
                <div class="supplier-info">
                    <span class="label2">Supplier Monthly Target:</span>
                    <span class="value">{{ number_format($originalrecords->monthly_target) }}</span>
                </div>
                <div class="supplier-info">
                    <span class="label2">Supplier Quarterly Target:</span>
                    <span class="value">{{ number_format($originalrecords->quarterly_target) }}</span>
                </div>
                 

            </td>
        </tr>
       
    </tbody>
</table>
    
                </div>
            </div>
        </div>

       
      



<style type="text/css">
        .supplier-table {
    border-collapse: collapse;
    width: 100%;
    
}

.supplier-table td {
  
    padding: 10px;
}

.supplier-info {
    margin-bottom: 10px;
}

.label2 {
    font-weight: bold;
}

.value {
    margin-left: 10px;
}

</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
   

    $(document).ready(function() {
    var $editedRows = $('.edited-info .supplier-info');
    var $originalRows = $('.original-info .supplier-info');

    $editedRows.each(function(index) {
        var $editedRow = $(this);
        var $originalRow = $originalRows.eq(index);

        var rowEdited = false;

        $editedRow.find('.value li').each(function(i) {
            var $editedItem = $(this);
            var $originalItem = $originalRow.find('.value li').eq(i);

            if ($editedItem.text().trim() !== $originalItem.text().trim()) {
                $editedItem.addClass('btn-primary');
                rowEdited = true;
            }
        });

        if (rowEdited) {
            $editedRow.addClass('row-edited');
        }
    });
});

</script>
@endsection