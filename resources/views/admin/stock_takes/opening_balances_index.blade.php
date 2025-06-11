
@extends('layouts.admin.admin')

@section('content')


<!-- Main content -->
<section class="content">
    <!-- Small boxes (Stat box) -->
    <div class="box box-primary">
        <div class="box-header with-border no-padding-h-b">
            <div align = "right">
                <?php if (isset($permission[$pmodule . '___freeze']) || $permission == 'superadmin') { ?>
                <a href = "{!! route('admin.opening-balances.stock-takes.freeze-table')!!}" class = "btn btn-success">Freeze Table</a>
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
                            <th width="15%">Date</th>
                            <th width="15%"  >Store Location</th>
                            <th width="15%"  >User</th>
                            {{-- <th width="15%"  >Bin</th> --}}
                            <th width="15%"  >Branch</th>
                            <th width="10%"  >Items</th>
                            <th  width="10%" class="noneedtoshort" >Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($lists) && !empty($lists))
                        <?php $b = 1; ?>
                        @foreach($lists as $list)
                        <tr>
                            <td>{!! $b !!}</td>
                            <td>{!! date('Y-m-d', strtotime($list->created_at)) !!}</td>
                            <td>{!! @$list->getAssociateLocationDetail->location_name !!}</td>
                            <td>{!! $list->getAssociateUserDetail->name !!}</td>
                            {{-- <td>{!! @$list->unit_of_measure->title !!}</td> --}}
                            <td>{!! isset($list->getAssociateUserDetail->userRestaurent)?$list->getAssociateUserDetail->userRestaurent->name:'' !!}</td>
                            <td>{!! $list->get_associate_items_count !!}</td>
                            <td class = "action_crud">
                                <span>
                                    <a title="Print" href="{{ route('admin.opening-balances.stock-takes.print-to-pdf', $list->id) }}">
                                        <i aria-hidden="true" class="fa  fa-file-pdf-o" style="font-size: 20px;"></i>
                                    </a>
                                </span>
                                {{-- <span>
                                    <a title="Print" href="javascript:void(0)" onclick="printStockTakes('{!! $list->id!!}')"><i aria-hidden="true" class="fa fa-print" style="font-size: 20px;"></i>
                                    </a>
                                </span> --}}
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
@include('admin.stock_takes.opening_balances_stock_check_popup')

@endsection

@section('uniquepagestyle')
<style>
    .select2-container {
        width: 100% !important;
    }

    .select2.select2-container.select2-container--default {
        height: auto !important;
    }
</style>
    <link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet" />
    
@endsection

@section('uniquepagescript')
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
<script type="text/javascript">
    $(function() {
        $(".store_location_id").change(function() {
            $.ajax({
                url: '{{ route('uom.search_by_item_location') }}',
                type: "GET",
                data: {
                    id: $('.store_location_id option:selected').val()
                },
                success: function(data) {
                    $(".wa_unit_of_measures_id").html(new Option('Select Bin', '', false,
                        false));
                    var res = data.map(function(item) {
                        let option = new Option(item.title, item.id, false, false)
                        $(".wa_unit_of_measures_id").append(option)
                    });
                }
            });
        });

        $('.wa_unit_of_measures_id').select2({
            placeholder: 'Select Bin Location'
        });

        $(".wa_inventory_category_id").select2({
            placeholder: 'Select All'
        });
        $(".authorization_level").select2({
            placeholder: 'Select All'
        });

        $(".mlselec6t").select2({
            placeholder: 'Select All'
        });
    });
</script>
@endsection