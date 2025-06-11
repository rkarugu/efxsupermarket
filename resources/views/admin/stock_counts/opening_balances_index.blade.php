
@extends('layouts.admin.admin')

@section('content')
<!-- Main content -->
<section class="content">
    <!-- Small boxes (Stat box) -->
    <div class="box box-primary">
        <div class="box-header with-border no-padding-h-b">
            <div align = "right">
                <?php if (isset($permission[$pmodule . '___add']) || $permission == 'superadmin') { ?>
                <a href = "{!! route('admin.opening-balances.stock-counts.enter-stock-counts')!!}" class = "btn btn-success">Enter Stock Counts</a>
                <?php } ?>
            </div>
            <br>
            @include('message')
            <div class="col-md-12 no-padding-h">
                <table class="table table-bordered table-hover" id="create_datatable">
                    <thead>
                        <tr>
                            <th width="10%">S.No.</th>
                            <th width="15%">Stock ID Code</th>
                            <th width="10%">Title</th>
                            <th width="15%">Location</th>
                            <th width="10%">UOM</th>
                            <th width="15%">Category</th>
                            <th width="15%">Quantity</th>
                            <th  width="10%" class="noneedtoshort" >Action</th>
                        </tr>
                    </thead>
                    <tbody>
                       
                        <?php $b = 1; ?>
                        @foreach($lists as $list)

                        <tr>
                            <td>{!! $b !!}</td>
                            <td>{!! $list->getAssociateItemDetail->stock_id_code !!}</td>
                            <td>{!! $list->getAssociateItemDetail->title !!}</td>
                            <td>{!! $list->getAssociateLocationDetail->location_name !!}</td>
                            <td>{!! $list->getUomDetail?->title !!}</td>
                            <td>{!! @$list->category->category_description !!}</td>
                            <td>{!! $list->quantity !!}</td>
                            

                            <td class = "action_crud">
                                 @if(isset($permission[$pmodule.'___edit']) || $permission == 'superadmin')
                                 <span>
                                     <a title="Edit" href="javascript:void(0);" onclick="openEditForm('<?= $list->id ?>','<?= $list->quantity ?>')" ><img src="{!! asset('assets/admin/images/edit.png') !!}" alt="">
                                    </a>
                                </span>
                                @endif
                                @if(isset($permission[$pmodule.'___delete']) || $permission == 'superadmin')
                                    <span>
                                        <form title="Trash" action="{{ URL::route('admin.opening-balances.stock-counts.destroy', $list->id) }}" method="POST">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <button  style="float:left"><i class="fa fa-trash-o" aria-hidden="true" style="font-size: 16px;"></i>
                                        </button>
                                        </form>
                                    </span>
                                @endif
                            </td>


                        </tr>
                        <?php $b++; ?>
                        @endforeach
                       


                    </tbody>
                </table>
            </div>
        </div>
    </div>


</section>
@if(isset($permission[$pmodule.'___edit']) || $permission == 'superadmin')
@include('admin.stock_counts.opening_balances_stock_count_edit_popup')
@endif
@endsection

@section('uniquepagescript')
<script>
function openEditForm(row_id, quantity){
    $('#hidden_row_id').val(row_id);
    $('#row-quantity').val(quantity);
    $('#edit-stock-model').modal('show');
}
</script>
@endsection