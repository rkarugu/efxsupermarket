@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">

            <div class="box-header with-border">
                <div class="d-flex justify-content-between">
                    <h3 class="box-title">{{ $title }}</h3>
                    {{-- <a href="{{ url()->previous() }}" class="btn btn-primary" role="button">
                        << Back to Inventory Reports </a> --}}
                </div>
            </div>
            

            <div class="box-body">
                {!! Form::open(['method' => 'get']) !!}
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">From</label>
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
                            <label for="">Max Qty Sold</label>
                            <input type="number" name="sold" id="sold" class="form-control"
                                value="{{ $sold }}" placeholder="Enter max qty sold">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label">Procument Users</label>
                            <select name="user" id="user" class="form-control select2">
                                <option value="" selected>Select users</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}" @selected(request()->user == $user->id) }}>{{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>


                    <div class="col-md-3">
                        <div class="form-group" style="margin-top: 25px; ">
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <button type="submit" class="btn btn-primary" name="manage" value="excel">Excel</button>
                            <button type="submit" class="btn btn-primary" name="manage" value="pdf">PDF</button>


                        </div>
                    </div>
                </div>
                </form>
                <hr>
                <div class="table-responsive">
                    
                    <table class="table table-bordered" id="create_datatable_25">
                        <thead>
                            <tr>
                                <th style="width: 3%">#</th>
                                <th>Item Code</th>
                                <th>Item</th>
                                <th>Category</th>
                                <th>SOH</th>
                                <th>Qty Sold </th>
                                <th>Last Sold</th>
                                <th>Last GRN</th>
                                <th>Procument User</th>
                                <th>Supplier</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($movings as $record)                                
                                <tr>
                                    <th scope="row" style="width: 3%;">{{ $loop->index + 1 }}</th>
                                    <td>{{ $record->stock_id_code }}</td>
                                    <td>{{ $record->title }}</td>
                                    <td>{{ $record->category_description }}</td>
                                    <td>{{ $record->qoh }}</td>
                                    <td>{{ $record->total_sales }}</td>
                                    <td>{{ $record->last_sold }}</td>
                                    <td>{{ $record->last_purchase }}</td>
                                    <td>{{ $record->supplierUsers ?? 'No Procurement User'}}</td>
                                    <td>
                                        @foreach ($record->suppliers as $supplier)
                                            {{ $supplier->name }}
                                        @endforeach
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
