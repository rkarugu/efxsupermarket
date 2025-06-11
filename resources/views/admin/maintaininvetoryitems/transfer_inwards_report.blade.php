@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">

            <div class="box-header with-border">
                <div class="d-flex justify-content-between">
                    <h3 class="box-title">{!! $title !!}</h3>
                    <a href="{{ url()->previous() }}" class="btn btn-primary" role="button">
                        << Back to Inventory Reports </a>
                </div>
            </div>

            <div class="box-body">
                {!! Form::open(['method' => 'get']) !!}
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">From</label>-
                            <input type="date" name="start_date" id="start_date" class="form-control"
                                value="{{ $start_date }}">
                        </div>
                    </div>
                    <div class="col-md-3">

                        <div class="form-group">
                            <label for="">To</label>
                            <input type="date" name="end_date" id="end_date" class="form-control"
                                value="{{ $end_date }}">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label">Store</label>
                            <select name="store" id="store" class="form-control select2">
                                <option value="" selected>Select Store</option>
                                @foreach ($stores as $store)
                                    <option value="{{ $store->id }}" @selected(request()->store == $store->id) }}>
                                        {{ $store->location_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label">Inwards / Outwards</label>
                            <select name="type" id="type" class="form-control select2">
                                <option value="" selected>Select Type</option>
                                <option value="Outwards" {{ request()->type == 'Outwards' ? 'selected' : '' }}>Outwards
                                </option>
                                <option value="Inwards" {{ request()->type == 'Inwards' ? 'selected' : '' }}>Inwards
                                </option>
                            </select>
                        </div>
                    </div>



                    <div class="col-md-12">
                        <div class="form-group" style="margin-top: 25px; ">
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <button type="submit" class="btn btn-primary" name="manage" value="excel">Excel</button>
                            <button type="submit" class="btn btn-primary" name="manage" value="pdf">PDF Detail</button>
                            <button type="submit" class="btn btn-primary" name="manage" value="summary">PDF
                                Summary</button>



                        </div>
                    </div>
                </div>
                </form>
                <hr>
                <div class="table-responsive">
                    <table class="table table-bordered" id="create_datatable_10">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Transfer No</th>
                                <th>Manual Doc No</th>
                                <th>Processed By</th>
                                <th>From Store</th>
                                <th>To Store</th>
                                <th>Total Cost</th>
                                <th>Items</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transfers as $transfer)
                            @php
                                $grandTotal = 0;
                            @endphp
                            @foreach ($transfer->getRelatedItem as $item)
                                @php
                                    $grandTotal += $item->total_cost;
                                @endphp
                            @endforeach
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $transfer->updated_at }}</td>
                                    <td>{{ $transfer->transfer_no }}</td>
                                    <td>{{ $transfer->manual_doc_number }}</td>
                                    <td>{{ $transfer->name }}</td>
                                    <td>{{ $transfer->location_name }}</td>
                                    <td>{{ $transfer->too }}</td>
                                    <td>{{ number_format($grandTotal, 2) }}</td>
                                    <td>
                                        <button class="toggle-details btn btn-primary btn-sm"><i class="fa fa-eye"></i></button>

                                         <a href="{{ route ('transfer_inward_download', $transfer->transfer_no )}}"  class="btn btn-primary btn-sm"><i class="fa fa-file-pdf"></i></a>
                                    </td>
                                </tr>
                                <tr class="details-row" style="display: none;">
                                    <td colspan="9">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Item No</th>
                                                    <th>Description</th>
                                                    <th>Quantity</th>
                                                    <th>Cost</th>
                                                    <th>Total Cost</th>
                                                </tr>
                                            </thead>
                                            <tbody> 
                                                @foreach ($transfer->getRelatedItem as $item)
                                                    <tr>
                                                        <td>{{ $item->getInventoryItemDetail->stock_id_code }}</td>
                                                        <td>{{ $item->getInventoryItemDetail->title }}</td>
                                                        <td>{{ $item->quantity }}</td>
                                                        <td>{{ number_format($item->standard_cost, 2) }}</td>
                                                        <td>{{ number_format($item->total_cost, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                                <tr>
                                                    <td colspan="4"><strong>Grand Total</strong></td>
                                                    <td><strong>{{ number_format($grandTotal, 2) }}</strong></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
        </div>

    </section>
@endsection

@section('uniquepagestyle')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />

    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet">
@endsection

@section('uniquepagescript')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js">
        < script src = "https://code.jquery.com/jquery-3.6.0.min.js" >
    </script>
    <script></script>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript">
        $('body').addClass('sidebar-collapse');
        $(document).ready(function() {
            $('.select2').select2();
        });

        function approveShop() {
            let subjectShopId = $("#subject-shop").val();
            $(`#source-${subjectShopId}`).val('approval_requests');

            $(`#approve-shop-form-${subjectShopId}`).submit();
        }

        function approveAllShops() {
            $("#approve-all-shops-form").submit();
        }

        $('#view-issue-modal').on('show.bs.modal', function(event) {
            let triggeringButton = $(event.relatedTarget);
            let dataValue = triggeringButton.data('issue');

            let date = new Date();
            date.setTime(date.getTime() + (2 * 60 * 1000));
            let expires = "; expires=" + date.toGMTString();

            document.cookie = 'issue' + "=" + dataValue + expires + "; path=/";
        })

        $(document).ready(function() {
            $(".toggle-details").on("click", function() {
                var $detailsRow = $(this).closest("tr").next(".details-row");
                $detailsRow.toggle();
                if ($detailsRow.is(":visible")) {
                    $(this).html('<i class="fas fa-eye-slash"></i>');
                } else {
                    $(this).html('<i class="fas fa-eye"></i>');
                }
            });
        });
    </script>
@endsection
