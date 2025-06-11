
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
                                      <th width="8%">S.No.</th>
                                      <th width="8%"  >Return No</th>
                                      <th width="8%"  >GRN No</th>
                                      <th width="8%"  >Date Received</th>
                                      <th width="8%"  >Order No</th>
                                      <th width="8%"  >Received By</th>
                                      <th width="8%"  > Into store location</th>
                                      <th width="8%"  >Supplier</th>
                                      <th width="8%"  >Supplier Invoice No</th>
                                      <th width="8%"  >Department</th>
                                      <th width="12%"  >Total Amount</th>
                                      <th  width="8%" class="noneedtoshort" >Action</th>  
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(isset($lists) && !empty($lists))
                                        <?php $b = 1;?>
                                        @foreach($lists as $list)
                                         
                                            <tr>
                                                <td>{!! $b !!}</td>

                                                <td>{!! isset($list->getRelatedGrnReturned->first()->grn_number) ? $list->getRelatedGrnReturned->first()->grn_number : '' !!}</td>
                                                <td>{!! isset($list->getRelatedGrn->first()->grn_number) ? $list->getRelatedGrn->first()->grn_number : '' !!}</td>
                                                   <td>{!! (isset($list->getRelatedGlTran->first()->trans_date)) ? date('Y-m-d',strtotime($list->getRelatedGlTran->first()->trans_date)) : '---' !!}</td>

                                                  
                                                
                                                <td>{!! $list->purchase_no !!}</td>


                                                <td>{{ @$list->getRelatedStockMoves->first()->getRelatedUser->name}}</td>

                                                 <td>{!! @$list->getStoreLocation->location_name !!}</td>
                                                  <td>{!! @$list->getSupplier->name !!}</td>

                                                  <td>{!! @$list->getSuppTran->suppreference!!}</td>
                                                   <td >{{ @$list->getDepartment->department_name }}</td>
                                                    <td>{!! manageAmountFormat($list->getSuppTran->total_amount_inc_vat)!!}</td>


                                                
                                                <td class = "action_crud">

                                                 <span>
                                                    <a title="Export To Pdf" href="{{ route('stock-return.show',$list->slug)}}"><i aria-hidden="true" class="fa fa-eye" style="font-size: 20px;"></i>
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
