@extends('layouts.admin.admin')

@section('content')


    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border no-padding-h-b">
                <div class="row">
                    <div class="col-sm-6">
                        <form action="" method="get">
                            <div class="row">
                                @if ($users->role_id != 152)
                                <div class="col-sm-5">
                                    
                                    <div class="form-group">
                                        
                                        <select name="store" id="inputstore" class="form-control mlselec6t">
                                            <option value="" selected disabled> Select Store Location </option>
                                            @foreach($branches as $index => $store)
                                            <option value="{{$store->id}}" {{request()->store == $store->id ? 'selected' : ''}}>{{$store->location_name.'('. $store->location_code .')'}}</option>
                                            @endforeach
                                        </select>
                                        
                                    </div>
                            </div>
                                    
                                @endif                               
                                <div class="col-sm-5">

                                        <div class="form-group">
                                        <select name="supplier" id="supplier" class="form-control wa_supplier_id mlselec6t" >
                                        <option value="" selected> Select supplier </option> 
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier['id'] }}" {{request()->supplier == $supplier['id'] ? 'selected' : ''}}> {{ $supplier['name'] }} </option>
                                        @endforeach
                                         </select>
                                     </div>                                        
                                </div>
                                <div class="col-sm-2">
                                                                            
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                    
                                </div>
                            </div>
                        </form>
                    </div>
                    @if (isset($permission[$pmodule . '___add']) || $permission == 'superadmin')
                        <div class="col-sm-6"> 
                            <div align = "right"> 
                                <a href = "{!! route($model . '.create') !!}" class = "btn btn-success">Add Branch  Requisitions</a>
                                   <!--  <a href = "{!! route($model.'.create_non_stock')!!}" class = "btn btn-success">Add Non-Stock {!! $title !!}</a> -->
                                
                            </div>
                        </div>
                    @endif
                </div>
                <br>
                @include('message')
                <div class="col-md-12 no-padding-h">
                    <table class="table table-bordered table-hover" id="create_datatable">
                        <thead>
                            <tr>
                                <th >S.No.</th>
                                <th >Purchase No</th>
                                <th >Date</th>
                                <th>User Name</th>
                                <th>Store Location</th>
                                <th>Bin Location</th>
                                <th>Supplier</th>
                                <th >Total Lists</th>
                                <th >Status</th>
                                <th class="noneedtoshort">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (isset($lists) && !empty($lists))
                                <?php $b = 1; ?>
                                @foreach ($lists as $list)
                     
                                    <tr>
                                        <td>{!! $b !!}</td>

                                        <td>{!! $list->purchase_no !!}</td>
                                        <td>{!! $list->requisition_date !!}</td>
                                        <td>{!! @$list->getrelatedEmployee->name !!}</td>
                                        <td>{{ @$list->store_location->location_name }}</td>
                                        <td>{{ @$list->unit_of_measure->title }}</td>
                                        <td>{{ @$list->supplier->name }}</td>

                                        <td>{{ count($list->getRelatedItem) }}</td>
                                        <td>{!! $list->status !!}</td>
                                        <td class = "action_crud">
                                           
                                            @if ($list->status == 'UNAPPROVED' || $list->status == 'APPROVED' || $list->status == 'PENDING')
                                                @if (isset($permission['external-requisitions___edit']) || $users->role_id == 1)
                                                    <span>
                                                        <a title="Edit" href="{{ route($model . '.edit', $list->slug) }}"><img
                                                                src="{!! asset('assets/admin/images/edit.png') !!}" alt="">
                                                        </a>
                                                    </span>
                                                @endif

                                                {{-- <span>
                                                    <form title="Trash"
                                                        action="{{ URL::route($model . '.destroy', $list->slug) }}"
                                                        method="POST">
                                                        <input type="hidden" name="_method" value="DELETE">
                                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                        <button style="float:left"><i class="fa fa-trash"
                                                                aria-hidden="true"></i>
                                                        </button>
                                                    </form>
                                                </span> --}}
                                             
                                            @endif
                                            <span>
                                                <a title="View" href="{{ route($model . '.show', $list->slug) }}"><i
                                                        class="fa fa-eye" aria-hidden="true"></i>
                                                </a>
                                            </span>
                                            <span>
                                                <a title="Hide"
                                                    href="{{ route($model . '.hideexternalquisition', $list->slug) }}"><i
                                                        class="fa fa-eye-slash" aria-hidden="true"></i>
                                                </a>
                                            </span>
                                            @if ($list->status == 'APPROVED' && 1 == 0)
                                                <span>
                                                    <a title="Print" href="javascript:void(0)"
                                                        onclick="printBill('{!! $list->slug !!}')"><i aria-hidden="true"
                                                            class="fa fa-print" style="font-size: 20px;"></i>
                                                    </a>
                                                </span>
                                                <span>
                                                    <a title="Export To Pdf"
                                                        href="{{ route($model . '.exportToPdf', $list->slug) }}"><i
                                                            aria-hidden="true" class="fa fa-file-pdf"
                                                            style="font-size: 20px;"></i>
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
        function printBill(slug) {
            var confirm_text = 'order';
            var isconfirmed = confirm("Do you want to print " + confirm_text + "?");
            if (isconfirmed) {
                jQuery.ajax({
                    url: '{{ route('external-requisitions.print') }}',
                    type: 'POST',
                    async: false, //NOTE THIS
                    data: {
                        slug: slug
                    },
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
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

@section('uniquepagescript')
<link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet" />
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
<script>
    $(document).ready(function(){
        $(".mlselec6t").select2();
    });
</script>
@endsection
