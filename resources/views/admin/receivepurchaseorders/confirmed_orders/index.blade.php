@extends('layouts.admin.admin')

@section('content')


    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border no-padding-h-b">
                <div class="row">
                    <div class="col-sm-12">
                        <form action="" method="get">
                            <div class="row">
                                <div class="col-sm-3">

                                    <div class="form-group">

                                        <select name="status" id="status" class="form-control mlselec6t">
                                            <option value="Confirm" @if (!request()->status || request()->status == 'Confirm') selected @endif>
                                                Confirmed </option>
                                            <option value="Process" @if (request()->status == 'Process') selected @endif>
                                                Processed </option>
                                            <option value="Reject" @if (request()->status == 'Reject') selected @endif>
                                                Rejected </option>
                                        </select>

                                    </div>
                                </div>
                                <div class="col-sm-3">

                                    <div class="form-group">

                                        {{-- <select name="store" id="inputstore" class="form-control mlselec6t">
                                                <option value="" selected disabled> Select Store Location </option>
                                                @foreach (getStoreLocationDropdown() as $index => $store)
                                                <option value="{{$index}}" {{request()->store == $index ? 'selected' : ''}}>{{$store}}</option>
                                                @endforeach
                                            </select> --}}

                                        <select name="store" id="inputstore" class="form-control mlselec6t"
                                            @if ($disable_select) disabled @endif>
                                            <option value="" selected disabled>Select Store Location</option>
                                            @foreach (getStoreLocationDropdown() as $index => $store)
                                                <option value="{{ $index }}"
                                                    {{ (request()->store ?? $preselect_location) == $index ? 'selected' : '' }}>
                                                    {{ $store }}
                                                </option>
                                            @endforeach
                                        </select>

                                    </div>
                                </div>

                                <div class="col-sm-3">

                                    <div class="form-group">

                                        <select name="supplier" id="inputsupplier" class="form-control mlselec6t">
                                            <option value="" selected disabled> Select Supplier </option>
                                            @foreach (getSuppliers() as $index => $supplier)
                                                <option value="{{ $index }}"
                                                    {{ request()->supplier == $index ? 'selected' : '' }}>
                                                    {{ $supplier }}</option>
                                            @endforeach
                                        </select>

                                    </div>



                                </div>
                                <div class="col-sm-2">


                                    <button type="submit" class="btn btn-primary">Filter</button>

                                </div>
                            </div>
                        </form>
                    </div>

                </div>
                <br>
                @include('message')
                <div class="col-md-12 no-padding-h table-responsive">
                    <table class="table table-bordered table-hover" id="create_datatable">
                        <thead>
                            <tr>
                                <th width="5%">Receive-No</th>

                                <th>Order No</th>
                                <th>Order date</th>
                                <th>Initiated By</th>



                                <th>Branch</th>
                                <th>Store Location</th>
                                <th>Bin Location</th>
                                <th>Supplier</th>
                                <th> Amount Delivered</th>
                                <th>Status</th>


                                <th class="noneedtoshort">Action</th>


                            </tr>
                        </thead>
                        <tbody>
                            @if (isset($lists) && !empty($lists))
                                <?php $b = 1; ?>
                                @foreach ($lists as $list)
                                    <tr>
                                        <td>{!! $list->id !!}</td>

                                        <td>{!! $list->parent->purchase_no !!}</td>
                                        <td>{!! $list->parent->purchase_date !!}</td>
                                        <td>{!! @$list->parent->getrelatedEmployee->name !!}</td>
                                        <td>{{ @$list->parent->getBranch->name }}</td>
                                        <td>{{ @$list->getStoreLocation->location_name }}</td>
                                        <td>{{ @$list->uom->title }}</td>
                                        <td>{!! @$list->parent->getSupplier->name !!}</td>
                                        <td>{{ manageAmountFormat(@$list->amount_delivered) }}</td>
                                        <td>{!! $list->status !!}</td>
                                        <td class = "action_crud">
                                            <a title="View" href="{{ route($model . '.show', $list->id) }}"><i
                                                    class="fa fa-eye" aria-hidden="true"></i>
                                            </a>
                                            @if (can('delete', $model))
                                                <x-actions.delete-record class="action_crud"
                                                    action="{{ route('confirmed-receive-purchase-order.destroy', $list->id) }}" />
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
        </div>


    </section>

@endsection
@section('uniquepagescript')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $(".mlselec6t").select2();
        });
    </script>
@endsection
