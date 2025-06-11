
@extends('layouts.admin.admin')

@section('content')


<!-- Main content -->
<section class="content">
    <!-- Small boxes (Stat box) -->
    <div class="box box-primary">
        <div class="box-header with-border no-padding-h-b">
            <div align = "right">
                <?php if (isset($permission[$pmodule . '___freeze']) || $permission == 'superadmin') { ?>
                <a href = "{!! route('admin.stock-takes.freeze-table')!!}" class = "btn btn-success">Freeze Table</a>
                <?php } ?>
                <?php if (isset($permission[$pmodule . '___add']) || $permission == 'superadmin') { ?>
                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#manage-stock-model">Add Stock check File</button>
                <?php } ?>
            </div>
            <br>
            @include('message')
            <div class="col-md-12 no-padding-h">
                <table class="table table-bordered table-hover" id="create_datatable">
                    <thead>
                        <tr>
                            <th width="5%">S.No.</th>
                            <th width="20%">Date</th>
                            <th width="20%"  >Store Location</th>
                            <th width="20%"  >Username</th>
                            <th width="15%"  >Branch</th>
                            <th width="10%"  >Total List of Items</th>
                            <th  width="10%" class="noneedtoshort" >Action</th>

                            <!--th style = "width:10%" class = "noneedtoshort">Date</th-->

                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($lists) && !empty($lists))
                        <?php $b = 1; ?>
                        @foreach($lists as $list)

                        <tr>
                            <td>{!! $b !!}</td>
                            <td>{!! date('Y-m-d', strtotime($list->created_at)) !!}</td>
                            <td>{!! $list->getAssociateLocationDetail->location_name !!}</td>
                            <td>{!! $list->getAssociateUserDetail->name !!}</td>
                            <td>{!! $list->getAssociateUserDetail->userRestaurent->name !!}</td>
                            <td>{!! $list->get_associate_items_count !!}</td>
                            

                            <td class = "action_crud">
                                <span>
                                    <a title="Print" href="{{ route('admin.stock-takes.print-to-pdf', $list->id) }}">
                                        <i aria-hidden="true" class="fa  fa-file-pdf" style="font-size: 20px;"></i>
                                    </a>
                                </span>
                                <span>
                                    <a title="Print" href="javascript:void(0)" onclick="printStockTakes('{!! $list->id!!}')"><i aria-hidden="true" class="fa fa-print" style="font-size: 20px;"></i>
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
@include('admin.stock_takes.stock_check_popup')

@endsection

@section('uniquepagestyle')
<style>
        .select2-container{width:100% !important;}
    </style>
    <link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet" />
    
@endsection

@section('uniquepagescript')
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
<script type="text/javascript">
    $(function () {
        $(".mlselec6t").select2();
    });
    
    
    function printStockTakes(id) {
       var confirm_text = 'stock take'; 
       var isconfirmed=confirm("Do you want to print "+confirm_text+"?");
       if (isconfirmed) 
       {
         jQuery.ajax({
             url: '{{route('admin.stock-takes.print')}}',
             type: 'POST',
             async:false,   //NOTE THIS
             data:{id:id},
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