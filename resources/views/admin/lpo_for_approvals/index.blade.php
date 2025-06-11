@extends('layouts.admin.admin')

@section('content')

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border no-padding-h-b">
                <form>
                    <div class="row">
                        <div class="form-group col-sm-3">
                            <label>From</label>
                            <input type="date" class="form-control" name="from"
                                value="{{ request()->from ?? '' }}">
                        </div>
                        <div class="form-group col-sm-3">
                            <label>To</label>
                            <input type="date" class="form-control" name="to"
                                value="{{ request()->to ?? '' }}">
                        </div>

                        <div class="form-group col-sm-3">
                            <label>Supplier </label>

                            <select name="supplier" id="inputsupplier" class="form-control mlselec6t">
                                <option value="" selected disabled> Select Supplier</option>
                                @foreach (getSupplierDropdown() as $index => $supplier)
                                    <option value="{{ $index }}"
                                        {{ request()->supplier == $index ? 'selected' : '' }}>{{ $supplier }}</option>
                                @endforeach
                            </select>

                        </div>
                        <br>
                        <div class="form-group col-sm-3" style="margin-top:5px">
                            <button type="submit" class="btn btn-danger"> <i class="fa-solid fa-filter"></i> &nbsp; Filter</button>
                        </div>
                    </div>


                </form>

                <hr>

                <ul class="nav nav-tabs">
                    <li class="active"><a href="#pending" data-toggle="tab">Pending</a></li>
                    <li><a href="#approved" data-toggle="tab">Approved</a></li>
                </ul>

                <br>

                <div class="tab-content">
                    <div class="tab-pane active" id="pending">
                        @include('admin.lpo_for_approvals.includes.pending')
                    </div>
                    <div class="tab-pane" id="approved">
                        @include('admin.lpo_for_approvals.includes.approved')
                    </div>
                </div>

                <br>

                
            </div>
        </div>

    </section>


@endsection

@section('uniquepagescript')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $(".mlselec6t").select2();

            $('#pending_table').DataTable({
                "paging": true,
                "pageLength": 100,
                "searching": true,
                "lengthChange": true,
                "lengthMenu": [10, 20, 50, 100],
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "order": [
                    [0, "asc"]
                ]
            });
            $('#approved_table').DataTable({
                "paging": true,
                "pageLength": 100,
                "searching": true,
                "lengthChange": true,
                "lengthMenu": [10, 20, 50, 100],
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "order": [
                    [0, "asc"]
                ]
            });

        });
    </script>
@endsection
