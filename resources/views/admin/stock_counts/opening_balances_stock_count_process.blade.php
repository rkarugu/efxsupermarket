
@extends('layouts.admin.admin')

@section('content')

<section class="content">
    <div class="box box-primary">
        <div class="box-header with-border no-padding-h-b">
            <div align = "right">
            </div>
            <br>
            @include('message')
            <div class="col-md-12 no-padding-h">
                <table class="table table-bordered table-hover" id="create_datatable">
                    <thead>
                        <tr>
                            <th width="10%">S.No.</th>
                            <th width="20%">Store Location Name</th>
                            <th  width="10%" class="noneedtoshort" >Action</th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php $b = 1; ?>
                        @foreach($data as $list)

                        <tr>
                            <td>{!! $b !!}</td>
                            <td>{!! $list->getStoreLocationName->location_name !!}</td>

                            <td class = "action_crud">
                                 <span>
                                     <a title="Process" href="{{route('admin.opening-balances.stock-counts.compare-counts-vs-stock-process',$list->getStoreLocationName->id)}}" class="btn btn-danger">
	                                     Process
                                     </a>
                                </span>
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
@include('admin.stock_counts.opening_balances_stock_count_edit_popup')
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