
@extends('layouts.admin.admin')

@section('content')


    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                        <div class="box-header with-border no-padding-h-b">
                        <div  style="height: 150px ! important;"> 
                            <div class="card-header">
                            <i class="fa fa-filter"></i> Filter
                            </div><br>
                            {!! Form::open(['route' => $model.'.index','method'=>'get']) !!}

                            <div>
                            <div class="col-md-12 no-padding-h">


                            <div class="col-sm-3">
                            <div class="form-group">
                                <input type="text" name="start_date" placeholder="Start Date" value="{{request()->start_date}}" class="datepicker form-control">

                            </div>
                            </div>

                            <div class="col-sm-3">
                            <div class="form-group">
                                <input type="text" name="end_date" placeholder="End Date" value="{{request()->end_date}}" class="datepicker form-control">
                           
                            </div>
                            </div>
                            
                            <div class="col-md-4">
                              <div class="form-group">
                                <select name="esd_type" class="form-control">
                                    <option value="">Show All</option>
                                    <option {{ (request()->esd_type=="pos_cash_sales")?'selected':'' }} value="pos_cash_sales">POS CASH SALES</option>
                                    <option {{ (request()->esd_type=="sales_invoice")?'selected':'' }} value="sales_invoice">SALES INVOICE</option>
                                </select>
                              </div>
                            </div>  

                            {{--

                              @if(getLoggeduserProfile()->role_id != 4)
                                <div class="col-md-4">
                                    <div class="form-group">
                                    {!! Form::select('salesman', getStoreLocationDropdown(), [], ['class'=>'form-control mlselec6t ','required'=>true]) !!}
                                    </div>
                                  </div>
                              @endif

                              --}}
                            
                            </div>

                            <div class="col-md-12 no-padding-h">
                                 <div class="col-sm-1"><button type="submit" class="btn btn-success" name="manage-request" value="filter"  >Filter</button></div>
                                 <div class="col-sm-1"><button type="submit" class="btn btn-danger" name="manage-request" value="PDF"  >PDF</button></div>
                                 
                                  {{--
                                 <div class="col-sm-1"><button type="button" onclick="print_invoice(this); return false;" class="btn btn-warning" name="manage-request" value="PRINT"  >PRINT</button></div>

                                  --}}

                                 <div class="col-sm-1">
                                <a class="btn btn-info" href="{!! route($model.'.index') !!}"  >Clear </a>
                           
                        </div>
                             <div class="col-sm-2">
                        </div>
                                
                            </div>
                            </div>

                            </form>
                        </div>
                            


                            @if(isset($permission[$pmodule.'___add']) || $permission == 'superadmin')
                          
                             @endif
                            <br>
                            
                            @include('message')
                            <div class="col-md-12 no-padding-h">
                                <table class="table table-bordered table-hover" id="create_datatable">
                                    <thead>
                                    <tr>
                                        <th>S.No.</th>
                                        <th>Invoice No</th>
                                        <th>Date</th>                             
                                        <th>Salesman</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Customer PIN</th>
                                           
                                        <!--th style = "width:10%" class = "noneedtoshort">Date</th-->
                                        
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php
                                    $total = 0;
                                    @endphp
                                    @if(isset($lists) && !empty($lists))
                                        @foreach($lists as $key=>$list)
                                            @php
                                                $total+=$list->esd_amount;
                                            @endphp
                                            <tr>
                                                <td>{!! ++$key !!}</td>
                                                <td>{!! $list->invoice_number !!}</td>
                                                <td>{!! $list->created_at !!}</td>
                                                <td>{!! @$list->esd_store_location !!}</td>
                                                <td>{!! $list->esd_amount !!}</td>
                                                <td>{!! $list->description !!}</td>
                                                <td>{!! $list->customer_pin !!}</td>
                                                          
                                            </tr>                                         
                                        @endforeach
                                    @endif
                             


                                    </tbody>
                                    <tfoot>
                                      <td colspan="4" style="text-align:right">Total: </td>
                                      <td colspan="2" style="text-align:left">{{manageAmountFormat($total)}}</td>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>


    </section>

 <script type="text/javascript">

function printMe(url,data,type){
  let isConfirm = confirm('Do you want to print this Invoice?');
        if (isConfirm) {
            jQuery.ajax({
                url: url,
                async:false,   //NOTE THIS               
                 type: type,
                data:data,
                headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                  },
              success: function (response) {               
                var divContents = response;
                var printWindow = window.open('', '', 'width=600');
                printWindow.document.write(divContents);
                printWindow.document.close();
                printWindow.print();
                printWindow.close();
              }
            });      
        }
}
       function printgrn(transfer_no)
       {      
          printMe('{{route('transfers.print')}}',{transfer_no:transfer_no},'POST');
       }
       function print_invoice(input)
       {      
          var postData = $(input).parents('form').serialize()+'&request=PRINT';
          var url = $(input).parents('form').attr('action');
          // postData.append('request','PRINT');
          printMe(url,postData,'GET');
       }
   </script>
   
@endsection
@section('uniquepagestyle')
  <link rel="stylesheet" href="{{asset('assets/admin/dist/datepicker.css')}}">

  <link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet" />
@endsection

@section('uniquepagescript')
<script src="{{asset('assets/admin/dist/bootstrap-datepicker.js')}}"></script>
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
<script>
             $(function () {
        $(".mlselec6t").select2();
    });        

                  $('.datepicker').datepicker({
        format: 'yyyy-mm-dd'
        });
            </script>

@endsection