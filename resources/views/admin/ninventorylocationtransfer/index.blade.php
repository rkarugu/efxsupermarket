@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                @include('message')
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title"> Inter-Branch Transfers</h3>
                    @if (can('add', $pmodule))
                        <a href = "{!! route($model . '.create') !!}" class="btn btn-success">
                            Add Transfer</a>
                    @endif
                </div>
            </div>
            <div class="box-body">
                <form action="{{ route($model . '.index') }}">
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                {!! Form::date('start-date', null, [
                                    'class' => ' form-control',
                                    'placeholder' => 'Start Date',
                                ]) !!}
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                {!! Form::date('end-date', null, [
                                    'class' => 'form-control',
                                    'placeholder' => 'End Date',
                                ]) !!}
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <button type="submit" class="btn btn-success" name="manage-request"
                                value="filter">Filter</button>
                            <a class="btn btn-info" href="{!! route($model . '.index') !!}">Clear </a>
                        </div>
                    </div>
                </form>
                <table class="table table-bordered table-hover" id="create_datatable">
                    <thead>
                        <tr>
                            <th width="5%">S.No.</th>
                            <th>Date</th>
                            <th>Initiated By</th> 
                            <th>Transfer No.</th>
                            <th>Manual Doc No.</th>
                            <th>From Store</th>
                            <th>To Store</th>
                            <th>Status</th>
                            <th class="noneedtoshort">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (isset($lists) && !empty($lists))
                            @php
                                $b = 1;
                            @endphp
                            @foreach ($lists as $list)
                                <tr>
                                    <td>{!! $b !!}</td>
                                    <td>{{ \Carbon\Carbon::parse($list->created_at)->format('d-m-Y') }}</td>
                                    <td>{!!  $list->getrelatedEmployee?->name !!}</td>
                                    <td>{!! $list->transfer_no !!}</td>
                                    <td>{!! $list->manual_doc_number ?? '-' !!}</td>
                                    <td>{!! getlocationRowById($list->from_store_location_id)->location_name !!}</td>
                                    <td>{!! getlocationRowById($list->to_store_location_id)->location_name !!}</td>
                                    <td>{!! $list->status !!}</td>
                                    <td class = "action_crud">
                                        <span>
                                            <a title="View"
                                                href="{{ route('n-transfers.receiveInterBranchTransfer', $list->id) }}"><i
                                                    class="fa fa-eye"></i>
                                            </a>
                                        </span>
                                        @if ($list->status == 'DRAFT')
                                            <span>
                                                <a title="Edit" href="{{ route($model . '.edit', $list->slug) }}"><img
                                                        src="{!! asset('assets/admin/images/edit.png') !!}" alt="">
                                                </a>
                                            </span>
                                            <span>
                                                <form title="Trash"
                                                    action="{{ URL::route($model . '.destroy', $list->slug) }}"
                                                    method="POST">
                                                    <input type="hidden" name="_method" value="DELETE">
                                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                    <button
                                                        style="float:left background-color:transparent !important; border:none !important;"><i
                                                            class="fas fa-trash " style="color:red !important;"
                                                            aria-hidden="true"></i>
                                                    </button>
                                                </form>
                                            </span>
                                        @elseif ($list->status == 'COMPLETED')
                                            <span>
                                                <a title="Export To Pdf"
                                                    href="{{ route($model . '.printToPdf', $list->slug) }}"><i
                                                        aria-hidden="true" class="fa fa-file-pdf"
                                                        style="font-size: 20px;"></i>
                                                </a>
                                            </span>
                                            <span>
                                                <a title="Print" href="javascript:void(0)"
                                                    onClick="printgrn('{!! $list->transfer_no !!}')"><i aria-hidden="true"
                                                        class="fa fa-print" style="font-size: 20px;"></i>
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
    </section>
@endsection
@section('uniquepagestyle')
    <link rel="stylesheet" href="{{ asset('assets/admin/dist/datepicker.css') }}">
@endsection

@section('uniquepagescript')
    <script src="{{ asset('assets/admin/dist/bootstrap-datepicker.js') }}"></script>
  
    <script type="text/javascript">
        function printgrn(transfer_no) {
            var confirm_text = 'tranfer receipt';
            var isconfirmed = confirm("Do you want to print " + confirm_text + "?");
            if (isconfirmed) {
                jQuery.ajax({
                    url: '{{ route('transfers.print') }}',
                    // async: true, //NOTE THIS
                    type: 'POST',
                    data: {
                        transfer_no: transfer_no
                    },
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        var divContents = response;
                        var printWindow = window.open('', '', 'width=600');
                        printWindow.document.write(divContents);
                        printWindow.document.close();
                        printWindow.print();
                    }
                });
            }
        }
    </script>
      <script>
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd'
        });
    </script>
@endsection
