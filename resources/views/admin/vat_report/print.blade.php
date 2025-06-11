@extends('layouts.report')

@section('title', 'VAT REPORTS')

@section('content')
    <table class="table table-bordered table-hover" id="create_datatable2">
                                    <thead>
                                    <tr>
                                      <th>Supplier KRA Pin</th>
                                      <th>Supplier Name</th>
                                      <th>Tax Group</th>
                                    
                                      <th>Item Description</th>
                                      <th>Cu Invoice no</th>
                                      <th>Create Date</th>
                                      <th>Vat Amount</th>
                                      <th>Total Amount Inc. VAT</th>
                                     
                                    </tr>
                                    </thead>
                                    <tbody>
 
                                  @foreach($customer as $val)
                                  @php
                                  $invoiceInfo = json_decode($val['invoice_info']);
                                  $rate=($invoiceInfo->vat_rate == 0 ? 0: ($invoiceInfo->vat_rate /100));
                                 
                                  if($rate>0){
                                  $vat=($invoiceInfo->order_price)*($val['qty_received']) * $rate ;
                                }else{
                                $vat=0;
                              }


@endphp 
                                    <tr>    
                                    <!-- <td>{{$invoiceInfo->order_price}}</td>   
                                    <td>{{$invoiceInfo->vat_rate}}</td> -->    
                                      <td>{{$val['kra_pin']}}</td>                       
                                      <td>{{$val['name']}}</td>
                                      <td>{{$val['tax_manager_id']}}</td>

                                      

                                      
                                      <td>{{$val['item_description']}}</td>
                                      <td>{{$val['cu_invoice_number']}}</td>
                                      <td>{{$val['created_at']}}</td>
                                      <td> {{number_format($vat,2)}}
                                     

                                   </td>
                                      <th>{{ number_format ((floatval($invoiceInfo->order_price)) * (floatval($val['qty_received'])),2) }}
</th>
                                    </tr>
                                   @endforeach
                  
                                    </tbody>
                                </table>
@endsection
