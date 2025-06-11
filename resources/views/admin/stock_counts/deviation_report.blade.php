
@extends('layouts.admin.admin')

@section('content')


<!-- Main content -->
<section class="content">
    <!-- Small boxes (Stat box) -->
    <div class="box box-primary">
        <div class="box-header with-border no-padding-h-b">
            <br>
            @include('message')
            <div class="col-md-12 no-padding-h">
                <table class="table table-bordered table-hover" id="create_datatable">
                    <thead>
                        <tr>
                            <th>S.No.</th>
                            <th>Date</th>
                            <th>Record Count</th>
                            <th class="noneedtoshort" >Action</th>

                            <!--th style = "width:10%" class = "noneedtoshort">Date</th-->

                        </tr>
                    </thead>
                    <tbody>
                       
                        <?php $b = 1; ?>
                        @foreach($data as $list)

                        <tr>
                            <td>{!! $b !!}</td>
                            <td>{!! $list->batch_date !!}</td>
                            <td>{!! $list->batch_date_count !!}</td>
                            

                            <td class = "action_crud">
                                <span>
                                    <a title="Print" onclick="print_this('{{ route('admin.stock-counts.deviation-report-pdf', $list->batch_date) }}'); return false;" href="#">
                                        <i aria-hidden="true" class="fa  fa-print" style="font-size: 20px;"></i>
                                    </a>
                                </span>
                                <span>
                                    <a title="Print" href="{{ route('admin.stock-counts.deviation-report-excel', $list->batch_date) }}">
                                        <i aria-hidden="true" class="fa fa-file-excel" style="font-size: 20px;"></i>
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
@include('admin.stock_counts.stock_count_edit_popup')
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