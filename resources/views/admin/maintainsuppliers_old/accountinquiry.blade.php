
@extends('layouts.admin.admin')

@section('content')


    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                        <div class="box-header with-border no-padding-h-b">
                            
                            <br>
                            @include('message')
                            <div class="col-md-12 no-padding-h">
                                <table class="table table-bordered table-hover" id="create_datatable">
                                    <thead>
                                    <tr>
                                        <th width="10%">S.No.</th>
                                        <th width="10%">Type</th>
                                        <th width="10%">TXN No</th>
                                        <th width="10%">Date</th>
                                        <th width="20%">Refrence</th>
                                        <th width="20%">Allocated Amount</th>
                                        <th width="20%">Settled Amount</th>                                         <th width="20%">Document No</th>
                                        <th width="20%">Total</th>
                                        <th  width="20%" class="noneedtoshort" >Action</th>
                                        
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php 

                                    $total_balance = [];
                                    ?>
                                    @if(isset($lists) && !empty($lists))
                                        <?php $b = 1;?>
                                        @foreach($lists as $list)
                                         
                                            <tr>
                                                <td>{!! $b !!}</td>
                                                <td>{!! isset($number_series_list[$list->grn_type_number])?$number_series_list[$list->grn_type_number] : '' !!}</td>
                                                <td>{!! manageOrderidWithPad($list->wa_purchase_order_id) !!}</td>
                                                <td>{!! $list->trans_date !!}</td>
                                                <td>{!! $list->suppreference !!}</td>
                                                @if ($list->total_amount_inc_vat < 0)
                                                   <td>----</td>
                                                <td>----</td>
                                                @else
                                                   <td>{!! $list->allocated_amount !!}</td>
                                                <td>{!! manageAmountFormat($list->total_amount_inc_vat - $list->allocated_amount) !!}</td>
                                                @endif

                                                <td>{!! $list->document_no !!}</td>
                                                <td>{!! manageAmountFormat($list->total_amount_inc_vat) !!}</td>
                                                <td class = "action_crud">
                                                    <span>
                                                        <a style="font-size: 16px;"  href="{{ route($model.'.supplier-movement-gl-entries', [$list->wa_purchase_order_id, $supplier_code]) }}" ><i class="fa fa-list" title= "GL Entries"></i>
                                                        </a>
                                                    </span>
                                                </td>
                                            </tr>
                                           <?php $b++; 
                                           $total_balance[] = $list->total_amount_inc_vat;

                                           ?>
                                        @endforeach
                                    @endif


                                    </tbody>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td style="font-weight: bold;">Total</td>
                                        <td style="font-weight: bold;">{{ manageAmountFormat(array_sum($total_balance)) }}</td>
                                        <td></td>

                                    <tfoot>
                                        

                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>


    </section>
   
@endsection
