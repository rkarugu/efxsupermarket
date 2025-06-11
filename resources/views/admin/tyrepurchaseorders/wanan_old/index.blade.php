
@extends('layouts.admin.admin')

@section('content')


    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                        <div class="box-header with-border no-padding-h-b">
                             @if(isset($permission[$pmodule.'___add']) || $permission == 'superadmin')
                            <div align = "right"> <a href = "{!! route($model.'.create')!!}" class = "btn btn-success">Add {!! $title !!}</a></div>
                             @endif
                            <br>
                            @include('message')
                            <div class="col-md-12 no-padding-h table-responsive">
                                <table class="table table-bordered table-hover" id="create_datatable">
                                    <thead>
                                    <tr>
                                      <th width="2%">S.No.</th>
                                      <th width="5%"  >Order No</th>
                                      <th width="10%"  >Order date</th>
                                      <th width="10%"  >Initiated By</th>
                                      <th width="10%"  >Into Store Location</th>
                                      <th width="10%"  >Supplier</th>
                                      <th width="10%"  >Branch</th>
                                      <th width="10%"  >Department</th>
                                      <th width="10%"  >Total Amount</th>
                                      <th width="10%"  >Status</th>
                                      <th  width="15%" class="noneedtoshort" >Action</th>
                                       
                                        <!--th style = "width:10%" class = "noneedtoshort">Date</th-->
                                        
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(isset($lists) && !empty($lists))
                                        <?php $b = 1;?>
                                        @foreach($lists as $list)
                                         
                                            <tr>
                                              <td>{!! $b !!}</td>
                                              <td>{!! $list->purchase_no !!}</td>
                                              <td>{!! $list->purchase_date !!}</td>
                                              <td>{!! $list->getrelatedEmployee->name !!}</td>
                                              <td>{!! $list->getStoreLocation->location_name !!}</td>
                                              <td>{!! $list->getSupplier->name !!}</td>
                                              <td >{{ isset($list->getBranch)?$list->getBranch->name:'' }}</td>
                                              <td >{{ isset($list->getDepartment)?$list->getDepartment->department_name:'' }}</td>
                                              <td>{{ manageAmountFormat($list->getRelatedItem->sum('total_cost_with_vat'))}}</td>
                                              <td>{!! $list->status !!}</td>  
                                              <td class = "action_crud">
                                                       @if(isset($permission[$pmodule.'___hide']) || $permission == 'superadmin') 
                                                       <span>
                                                    <a title="Hide" href="{{ route($model.'.hidepurchaseorder', $list->slug) }}" ><i class="fa fa-eye-slash" aria-hidden="true"></i>
                                                    </a>
                                                    </span>
                                                    @endif
                                                @if($list->status == 'UNAPPROVED')
                                                    <span>
                                                    <a title="Edit" href="{{ route($model.'.edit', $list->slug) }}" ><img src="{!! asset('assets/admin/images/edit.png') !!}" alt="">
                                                    </a>
                                                    </span>

                                                   

                                                  

                                                  

                                                    <span>
                                                    <form title="Trash" action="{{ URL::route($model.'.destroy', $list->slug) }}" method="POST">
                                                    <input type="hidden" name="_method" value="DELETE">
                                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                    <button  style="float:left"><i class="fa fa-trash" aria-hidden="true"></i>
                                                    </button>
                                                    </form>
                                                    </span>

                                                    @else
                                                       <span>
                                                    <a title="View" href="{{ route($model.'.show', $list->slug) }}" ><i class="fa fa-eye" aria-hidden="true"></i>
                                                    </a>
                                                    </span>
                                                     @endif


                                                        @if($list->status == 'APPROVED')
                                                       <span>
                                                    <a title="Print" href="javascript:void(0)" onclick="printBill('{!! $list->slug!!}')"><i aria-hidden="true" class="fa fa-print" style="font-size: 20px;"></i>
                                                    </a>
                                                  </span>
                                                   <span>
                                                    <a title="Export To Pdf" href="{{ route($model.'.exportToPdf', $list->slug)}}"><i aria-hidden="true" class="fa fa-file-pdf" style="font-size: 20px;"></i>
                                                    </a>
                                                  </span>
                                                        @endif


                                                  

                                                      





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


      <script type="text/javascript">
       function printBill(slug)
       {
          var confirm_text = 'order'; 
          var isconfirmed=confirm("Do you want to print "+confirm_text+"?");
          if (isconfirmed) 
          {
            jQuery.ajax({
                url: '{{route('purchase-orders.print')}}',
                type: 'POST',
                async:false,   //NOTE THIS
                data:{slug:slug},
                headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                  },
              success: function (response) {
                var divContents = response;
                var printWindow = window.open('', '', 'width=600');
                printWindow.document.write('<html><head><title>Receipt</title>');
                printWindow.document.write('</head><body >');
                printWindow.document.write(divContents);
                printWindow.document.write('</body></html>');
                printWindow.document.close();
                printWindow.print();
              }
            });
          }
       }
   </script>
   
@endsection
