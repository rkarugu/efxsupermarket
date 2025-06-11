@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">

            <div class="box-header with-border">
                <div class="d-flex justify-content-between">
                    <h3 class="box-title">Price Timeline Report</h3>
                    {{-- <a href="{{ url()->previous() }}" class="btn btn-primary" role="button">
                        << Back to Inventory Reports </a> --}}
                </div>
            </div>

            <div class="box-body">
                <!--
                    <div class="session-message-container">
                        @include('message')
                    </div> -->
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
                            <label for="">Transcation Type</label>
                            <select name="transcation_type" class="form-control">
                                <option value="" selected>Select Transaction Type</option>
                                <option value="Price Change" @if ($type == 'Price Change') selected @endif>Price Change
                                </option>
                                <option value="GRN" @if ($type == 'GRN') selected @endif>GRN</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group col-md-3">
                        <div class="form-group">
                            <label class="control-label">Select item</label>
                            <select name="id" id="id" class="form-control select2">
                                <option value="" selected>Select item</option>
                                @foreach ($inventoryItems as $item)
                                    <option value="{{ $item->id }}" @if (request()->input('id') == $item->id) selected @endif>
                                        {{ $item->title }}
                                    </option>
                                @endforeach
                            </select>

                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group" style="margin-top: 25px; ">
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <button type="submit" class="btn btn-primary" name="manage" value="excel">Excel</button>


                        </div>
                    </div>
                </div>
                </form>


                <hr>



                <div class="table-responsive">
                    <table class="table table-bordered" id="create_datatable_10">
                        <thead>
                            <tr>
                                <th style="width: 3%">#</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Stock Id Code</th>
                                <th>Item</th>
                                <th>Transcation Type</th>
                                <th>Branch</th>
                                <th>Processed By</th>
                                <th>Before SOH</th>
                                <th>Incoming SOH</th>
                                <th>New SOH</th>
                                <th>Incoming Standard Cost</th>
                                <th>Current Standard Cost</th>
                                <th>Current Selling Price</th>
                                <th>Incoming Selling Price</th>
                                <th>Delta</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                use Carbon\Carbon;
                                use App\Model\WaStockMove;
                            @endphp

                            @foreach ($timelines as $record)
                                @php
                                    $qoh_new = ($record->qty_received ?: 0) + ($record->current_stock ?: 0);

                                @endphp
                                <tr>
                                    <th scope="row" style="width: 3%;">{{ $loop->index + 1 }}</th>
                                    <td>{{ \Carbon\Carbon::parse($record->updated_at)->format('d/m/Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($record->updated_at)->format('h:i:sa') }}</td>
                                    <td>{{ $record->stock_id_code }}</td>
                                    <td>{{ $record->title }}</td>
                                    <td>{{ $record->transcation_type }}</td>
                                    <td>THIKA STORE(Nampak)</td>
                                    <td>{{ $record->username }}</td>

                                    <td>
                                        @if ($record->transcation_type == 'Price Change')
                                            {{ number_format($record->qoh_before) }}
                                        @else
                                            {{ number_format($record->current_stock + $record->qoh_before) }}
                                        @endif
                                    </td>




                                    <td>{{ number_format($record->qty_received, 2) }}</td>
                                    <td>
                                        @if ($record->transcation_type == 'Price Change')
                                            {{ number_format($record->qoh_before) }}
                                        @else
                                            {{ number_format($qoh_new, 2) }}
                                        @endif
                                    </td>

                                    <td>{{ number_format($record->standart_cost_unit, 2) }}</td>
                                    <td>{{ number_format($record->current_standard_cos_moves, 2) }}</td>
                                    <td>
                                        @if ($record->transcation_type == 'Price Change')
                                            {{ number_format($record->current_selling_price, 2) }}
                                        @else
                                            {{ number_format($record->current_selling_moves, 2) }}
                                        @endif
                                    </td>
                                    <td>{{ number_format($record->selling_price, 2) }}</td>
                                    <td>{{ number_format($record->delta, 2) }}</td>


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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
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
    </script>
@endsection
