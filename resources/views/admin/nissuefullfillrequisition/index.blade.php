
@extends('layouts.admin.admin')

@section('content')

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                        <div class="box-header with-border no-padding-h-b">
                            @include('message')
                            <div class="col-md-12 no-padding-h">
                                <table class="table table-bordered table-hover" id="create_datatable">
                                    <thead>
                                    <tr>
                                        <th width="5%">S.No.</th>
                                       
                                        <th width="10%"  >Requisition No</th>
                                          <th width="15%"  >Requisition Date</th>
                                          <!-- <th width="13%"  >Vehicle Reg</th>
                                           <th width="15%"  >Route</th> -->
                                           <th width="15%"  >From Store</th>
                                           <th width="15%"  >To Store</th>
                                           <th width="15%"  >Customer</th>
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
                                                
                                                <td>{!! $list->requisition_no !!}</td>
                                                  <td>{!! $list->requisition_date !!}</td>  
                                                      <!-- <td >{{ $list->vehicle_register_no }}</td>
                                                      <td>{!! $list->route !!}</td> -->
                                                      <td>{!! @getlocationRowById($list->wa_location_and_store_id)->location_name !!}</td>
                                                      <td>{!! @getlocationRowById($list->to_store_id)->location_name !!}</td>
                                                   <td>{!! $list->customer !!}</td>
                                                   <td>{!! $list->status !!}</td>
                                                 

                                                 
                                                
                                                <td class = "action_crud">

                                                
                                                    <span>
                                                    <a title="View" href="{{ route($model.'.show', $list->slug) }}" ><i class="fa fa-eye" aria-hidden="true"></i>
                                                    </a>
                                                    </span>
                                                      
                                                       <span>
                                                    <a title="Print" href="javascript:void(0)" onclick="printBill('{!! $list->slug!!}')"><i aria-hidden="true" class="fa fa-print" style="font-size: 20px;"></i>
                                                    </a>
                                                  </span>

                                                   <span>
                                                    <a title="Export To Pdf" href="{{ route($model.'.exportToPdf', $list->slug)}}"><i aria-hidden="true" class="fa fa-file-pdf" style="font-size: 20px;"></i>
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

     <script type="text/javascript">
       function printBill(slug)
       {
          var confirm_text = 'order'; 
          var isconfirmed=confirm("Do you want to print "+confirm_text+"?");
          if (isconfirmed) 
          {
            jQuery.ajax({
                url: '{{route('n-issue-fullfill-requisition.print')}}',
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
