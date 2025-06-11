
@extends('layouts.admin.admin')

@section('content')


    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                      <div class="box-header with-border">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="box-title">Processed Inter-Branch  Transfers</h3>
         
                        </div>
                      
                    </div>
                        <div class="box-header with-border no-padding-h-b">
                        <div  style="height: 150px ! important;"> 
                           
                            {!! Form::open(['route' => $model.'.indexProcessed','method'=>'get']) !!}

                            <div>
                            <div class="col-md-12 no-padding-h">


                            <div class="col-sm-3">
                            <div class="form-group">
                            {!! Form::date('start-date', null, [
                            'class'=>' form-control',
                            'placeholder'=>'Start Date' ]) !!}
                            </div>
                            </div>

                            <div class="col-sm-3">
                            <div class="form-group">
                            {!! Form::date('end-date', null, [
                            'class'=>' form-control',
                            'placeholder'=>'End Date']) !!}
                            </div>
                            </div>
                            


                            </div>

                            <div class="col-md-12 no-padding-h">
                                 <div class="col-sm-1"><button type="submit" class="btn btn-success" name="manage-request" value="filter"  >Filter</button></div>

                                 <div class="col-sm-1">
                                <a class="btn btn-info" href="{!! route($model.'.index') !!}"  >Clear </a>
                           
                        </div>
                             <div class="col-sm-2">
                        </div>
                                
                            </div>
                            </div>

                            </form>
                        </div>
                         
                            <br>
                            @include('message')
                            <div class="col-md-12 no-padding-h">
                                <table class="table table-bordered table-hover" id="create_datatable">
                                    <thead>
                                    <tr>
                                        <th width="2%">S.No.</th>             
                                        <th  >Date</th>
                                        <th>Initiated By</th>              
                                         <th  >Transfer No.</th>
                                         <th>Manual Doc. No.</th>
                                          <th   >From Store</th>
                                          <th  >To Store</th>
                                           <th   >Status</th>
                                          <th   class="noneedtoshort" >Action</th>
                                       
                                        
                                    </tr>
                                    </thead>
                                    <tbody>

                                     @if(isset($lists) && !empty($lists))
                                        <?php $b = 1; //echo "<pre>"; print_r($lists);
	                                         ?>
                                        @foreach($lists as $list)
                                         <tr>
                                                <td>{!! $b !!}</td>
                                                
                                                <td>{{ \Carbon\Carbon::parse($list->created_at)->format('d-m-Y') }}</td>
                                                <td>{!!  $list->getrelatedEmployee->name !!}</td>

                                                 <td>{!! $list->transfer_no !!}</td>
                                                 <td>{!! $list->manual_doc_number ?? '-' !!}</td>
                                                 <td>{!! getlocationRowById($list->from_store_location_id)->location_name !!}</td>
                                                      <td>{!! getlocationRowById($list->to_store_location_id)->location_name !!}</td>
                                                       <td>{!! $list->status !!}</td>

                                                        <td class = "action_crud">
                                                          <span>
                                                            <a title="View" href="{{ route('n-transfers.receiveInterBranchTransferProcessed', $list->id) }}" ><i  class="fa fa-eye"></i>
                                                            </a>
                                                            </span>


                                                               @if($list->status == 'PENDING')
                                                    {{-- <span>
                                                    <a title="Edit" href="{{ route($model.'.edit', $list->slug) }}" ><img src="{!! asset('assets/admin/images/edit.png') !!}" alt="">
                                                    </a>
                                                    </span>

                                                    <span>
                                                    <form title="Trash" action="{{ URL::route($model.'.destroy', $list->slug) }}" method="POST">
                                                    <input type="hidden" name="_method" value="DELETE">
                                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                    <button  style="float:left background-color:transparent !important; border:none !important;"><i class="fas fa-trash " style="color:red !important;" aria-hidden="true"></i>
                                                    </button>
                                                    </form>
                                                    </span> --}}

                                                    @else
                                                    
                                                     <span>
                                                    <a title="Export To Pdf" href="{{ route($model.'.printToPdf', $list->slug)}}"><i aria-hidden="true" class="fa fa-file-pdf" style="font-size: 20px;"></i>
                                                    </a>
                                                  </span>
                                                      <span>
                                                        <a title="Print" href="javascript:void(0)" onClick="printgrn('{!! $list->transfer_no!!}')"><i aria-hidden="true" class="fa fa-print" style="font-size: 20px;" ></i>
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
    
       function printgrn(transfer_no)
       {

        
         var confirm_text = 'tranfer receipt'; 
          var isconfirmed=confirm("Do you want to print "+confirm_text+"?");
          if (isconfirmed) 
          {
            jQuery.ajax({
                url: '{{route('transfers.print')}}',
                async:false,   //NOTE THIS
                 type: 'POST',
                data:{transfer_no:transfer_no},
                headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                  },
              success: function (response) {
               
                var divContents = response;
                //alert(divContents);
                var printWindow = window.open('', '', 'width=600');
               // printWindow.document.write('<html><head><title>Receipt</title>');
                //printWindow.document.write('</head><body >');
                printWindow.document.write(divContents);
               // printWindow.document.write('</body></html>');
                printWindow.document.close();
                printWindow.print();
              }
            });
          }
       }
   </script>
   
@endsection
@section('uniquepagestyle')
  <link rel="stylesheet" href="{{asset('assets/admin/dist/datepicker.css')}}">
@endsection

@section('uniquepagescript')
<script src="{{asset('assets/admin/dist/bootstrap-datepicker.js')}}"></script>
<script>
                 

                  $('.datepicker').datepicker({
        format: 'yyyy-mm-dd'
        });
        function printgrn(transfer_no)
       {

        
         var confirm_text = 'tranfer receipt'; 
          var isconfirmed=confirm("Do you want to print "+confirm_text+"?");
          if (isconfirmed) 
          {
            jQuery.ajax({
                url: '{{route('transfers.print')}}',
                async:false,   //NOTE THIS
                 type: 'POST',
                data:{transfer_no:transfer_no},
                headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                  },
              success: function (response) {
               
                var divContents = response;
                //alert(divContents);
                var printWindow = window.open('', '', 'width=600');
               // printWindow.document.write('<html><head><title>Receipt</title>');
                //printWindow.document.write('</head><body >');
                printWindow.document.write(divContents);
               // printWindow.document.write('</body></html>');
                printWindow.document.close();
                printWindow.print();
              }
            });
          }
       }
   </script>
            </script>

@endsection