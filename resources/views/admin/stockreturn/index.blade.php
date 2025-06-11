
@extends('layouts.admin.admin')

@section('content')


    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                        <div class="box-header with-border no-padding-h-b">
                            
                            @include('message')
                            <div class="col-md-12 no-padding-h table-responsive">
                                <table class="table table-bordered table-hover" id="create_datatable">
                                    <thead>
                                    <tr>
                                      <th >S.No.</th>
                                      <th   >GRN No</th>
                                      <th   >Date Received</th>
                                      <th   >Order No</th>
                                      <th   >Received By</th>
                                      <th   >Supplier</th>
                                      <th   >Supplier Invoice No</th>
                                      <th   >Department</th>
                                      <th >Total Amount</th>
                                      <th   class="noneedtoshort" >Action</th>  
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(isset($lists) && !empty($lists))
                                        <?php $b = 1;?>
                                        @foreach($lists as $list)
                                         
                                            <tr>
                                                <td>{!! $b !!}</td>
                                                
                                              <td>{!! isset($list->grn_number) ? $list->grn_number : '' !!}</td>
                                                   <td>{!! (isset($list->delivery_date)) ? date('Y-m-d',strtotime($list->delivery_date)) : '---' !!}</td>

                                                  
                                                
                                                <td>{!! $list->purchase_no !!}</td>


                                                <td>{{ @$list->getRelatedStockMoves->first()->getRelatedUser->name}}</td>

                                                  <td>{!! @$list->getSupplier->name !!}</td>

                                                  <td>{!! @$list->getSuppTran->suppreference!!}</td>
                                                   <td >{{ @$list->getDepartment->department_name }}</td>

                                                {{--    <td>{!! manageAmountFormat(@$list->getSuppTran->total_amount_inc_vat)!!}</td>--}}
                                                @php
                                                    $gross = 0;
                                                    @endphp
                                                    @foreach($list->getRelatedGrn->where('grn_number',$list->grn_number) as $items)
                                                    <?php 
                                                        $invoice_info = json_decode($items->invoice_info);
                                                        $nett = (is_null($invoice_info->order_price) ? $invoice_info->order_price : 0)  *  $invoice_info->qty;
                                                        $net_price = $nett;
                                                        if($invoice_info->discount_percent>'0')
                                                        {
                                                                $discount_amount = ($invoice_info->discount_percent*$nett)/100;
                                                                $nett = $nett-$discount_amount;
                                                        }

                                                        $vat_amount = 0;
                                                        if($invoice_info->vat_rate > '0')
                                                        {
                                                            // $vat_amount = ($invoice_info->vat_rate*$nett)/100;
                                                            $vat_amount = round($nett - (($nett*100)/($invoice_info->vat_rate+100)),2);

                                                        }
                                                    ?>
                                                     @php
                                                      $gross += ($nett);
                                                      @endphp
                                                    @endforeach
                                                    <td>{!! manageAmountFormat(@$gross)!!}</td>


                                                
                                                <td class = "action_crud">

                                                 <span>
                                                    <a title="Export To Pdf" href="{{ route($model.'.show',['stock_return'=>$list->slug,'grn'=>$list->grn_number])}}"><i aria-hidden="true" class="fa fa-eye" style="font-size: 20px;"></i>
                                                    </a>
                                                  </span>






                                                </td>
                                                
											
                                            </tr>
                                           <?php $b++; ?>
                                        @endforeach
                                    @endif


                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>


    </section>

@endsection
