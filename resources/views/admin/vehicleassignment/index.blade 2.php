@extends('layouts.admin.admin')

@section('content')
    <style>
        .buttons-excel {
            background-color: #f39c12 !important;
            border-color: #e08e0b !important;
            border-radius: 3px !important;
            -webkit-box-shadow: none !important;
            box-shadow: none !important;
            border: 1px solid transparent !important;
            color: #fff !important;
            display: inline-block !important;
            padding: 6px 12px !important;
            margin-bottom: 0 !important;
            font-size: 14px !important;
            font-weight: 400 !important;
            line-height: 1.42857143 !important;
            text-align: center !important;
            white-space: nowrap !important;
            vertical-align: middle !important;
        }
    </style>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border no-padding-h-b">

                <div align="right"> <a href="{!! route($model . '.create') !!}" class="btn btn-success">Assign Vehicle</a></div>

                @include('message')
                <div class="col-md-12 no-padding-h table-responsive">
                    <!--   <div class="form-group">
                                                    <label for="">Vehicle Listing</label>
                                                    <select name="type" id="type_supplier_amount" class="form-control">
                                                        <option value="All" selected>All</option>
                                                        <option value="zero" > Supplier with Zero Balances</option>
                                                        <option value="more" > Supplier with Greater OR Less than zero Balances</option>
                                                    </select>
                                                </div> -->
                    <!-- <a href="{{ route('supplier-listing.index', ['print' => 'pdf']) }}" onclick="type_supplier_amount(this); return false;" class="btn btn-warning"><i class="fa fa-file-excel" aria-hidden="true"></i></a> -->

                    <div class="col-md-3">
                        <div class="btn-div">
                            <div><a class="btn" href="{{ route('exportpdflisting') }}"><i class="fa fa-file-pdf"
                                        aria-hidden="true" style="font-size:24px; color:#ff0000 ;"></i></a></div>
                        </div>

                    </div>
                    <table class="table table-bordered table-hover" id="create_datatable1">
                        <thead>
                            <tr>
                                <th width="5%">S.No.</th>
                                <th width="5%">Driver</th>
                                <th width="5%">Plate</th>
                                <th width="5%">Type</th>
                                <th width="5%">Model</th>
                                <th width="10%">Actions</th>
                            </tr>
                        </thead>
                        <?php $b = 1; ?>
                        @if (isset($lists) && !empty($lists))
                            @foreach ($lists as $list)
                                <tr>

                                    <td>{!! $b !!}</td>
                                    <td>{!! $list->user->name !!}</td>

                                    <td>{!! $list->vehicle->license_plate !!}</td>
                                    <td>{!! $list->vehicle->vehicle->title !!}</td>
                                    <td>{!! $list->vehicle->models->title !!}</td>
                                    <td>

                                        <span>
                                            <a title="View" class="btn btn-warning btn-sm"
                                                href="{{ route($model . '.show', $list->id) }}"><i class="fa fa-eye"
                                                    aria-hidden="true"></i>
                                            </a>
                                        </span>

                                        <span>
                                            <a title="Edit" class="btn btn-primary btn-sm"
                                                href="{{ route($model . '.edit', $list->id) }}"><i class="fa fa-edit"
                                                    aria-hidden="true"></i>
                                            </a>
                                        </span>

                                        <span>
                                            <form title="Trash" action="{{ URL::route($model . '.destroy', $list->id) }}"
                                                method="POST">
                                                <input type="hidden" name="_method" value="DELETE">
                                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                <button style="float:left"><i class="fa fa-trash" aria-hidden="true"></i>
                                                </button>
                                            </form>
                                        </span>

                                    </td>

                                </tr>
                                <?php $b++; ?>
                            @endforeach
                        @endif
                    </table>
                </div>
            </div>
        </div>


    </section>
@endsection
@section('uniquepagescript')
    <script type="text/javascript" language="javascript"
        src="https://cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" language="javascript"
        src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.flash.min.js"></script>
    <script type="text/javascript" language="javascript"
        src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" language="javascript"
        src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.html5.min.js"></script>
    <script type="text/javascript" language="javascript"
        src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.print.min.js"></script>
    <script type="text/javascript" class="init">
        function type_supplier_amount(params) {
            var url = $(params).attr('href');
            var ty = $('#type_supplier_amount').val();
            url = url + '&type=' + ty;
            location.href = url;
        }

        $(document).ready(function() {
            $('#create_datatable1').DataTable({
                pageLength: "100",
                dom: 'frtip',
            });
            // $('#create_datatable2').DataTable( {
            //     pageLength: "100",
            // 	dom: 'B',
            // 	buttons: [
            // 		{ extend: 'excelHtml5', text: '<i class="fa fa-file-excel" aria-hidden="true">', footer: true },
            // 	]
            // } );
        });
    </script>
@endsection
