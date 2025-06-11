@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border no-padding-h-b">
                <div class="row">
                    <div class="col-sm-12">
                        <form action="" method="get">
                            <div class="row">
                                <div class="col-sm-5">
                                    <div class="form-group">
                                        {{-- <select name="store" id="inputstore" class="form-control mlselec6t">
                                            <option value="" selected disabled> Select Store Location </option>
                                            @foreach (getStoreLocationDropdown() as $index => $store)
                                                <option value="{{ $index }}"
                                                    {{ request()->store == $index ? 'selected' : '' }}>{{ $store }}
                                                </option>
                                            @endforeach
                                        </select> --}}
                                        <select name="store" id="inputstore" class="form-control mlselec6t" @if($disable_select) disabled @endif>
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
                                <div class="col-sm-5">
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
                                <th width="5%">S.No.</th>
                                <th>Order No</th>
                                <th>Order date</th>
                                <th>Initiated By</th>
                                <th>Branch</th>
                                <th>Store Location</th>
                                {{-- <th>Bin Location</th> --}}
                                <th>Supplier</th>
                                <th>Total Amount</th>
                                <th> Amount Delivered</th>
                                <th>Status</th>
                                <th class="noneedtoshort">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (isset($lists) && !empty($lists))
                                <?php $b = 1; ?>
                                @foreach ($lists as $list)
                                    <tr class="{{ $list->amount_delivered > 0 ? 'partial' : '' }}"
                                        @if ($list->amount_delivered > 0) style="background-color: aqua !important;" @endif>
                                        <td>{!! $b !!}</td>
                                        <td>{!! $list->purchase_no !!}</td>
                                        <td>{!! $list->purchase_date !!}</td>
                                        <td>{!! @$list->getrelatedEmployee->name !!}</td>
                                        <td>{{ @$list->getBranch->name }}</td>
                                        <td>{{ @$list->getStoreLocation->location_name }}</td>
                                        {{-- <td>{{ @$list->uom->title }}</td> --}}
                                        <td>{!! @$list->getSupplier->name !!}</td>
                                        <td>{{ manageAmountFormat(@$list->getRelatedItem->sum('total_cost_with_vat') - @$list->getRelatedItem->sum('other_discounts_total')) }}
                                        </td>
                                        <td>{{ manageAmountFormat(@$list->amount_delivered) }}</td>
                                        <td>{!! $list->status !!}</td>
                                        <td style="width: 80px; text-align:center">
                                            <span style="display: inline-block">
                                                <a title="View" data-toggle="tooltip"
                                                    href="{{ route($model . '.show', $list->slug) }}"><i class="fa fa-eye"
                                                        aria-hidden="true"></i>
                                                </a>
                                            </span>
                                            <span style="display: inline-block; margin-left:10px">
                                                <a title="Mark as Complete" href="#" data-toggle="complete"
                                                    data-target="#order{{ $list->id }}">
                                                    <i class="fa fa-check" aria-hidden="true"></i>
                                                </a>
                                                <form style="display: none"
                                                    action="{{ route('receive-purchase-orders.complete', $list) }}"
                                                    method="post" id="order{{ $list->id }}">
                                                    @csrf()
                                                </form>
                                            </span>
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
@push('styles')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
@endpush
@push('scripts')
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $('body').addClass('sidebar-collapse');

        $(document).ready(function() {
            $(".mlselec6t").select2();

            $("#create_datatable tbody").on('click', '[data-toggle="complete"]', function(e) {
                e.preventDefault();
                let target = $(this).data('target');

                Swal.fire({
                    title: 'Confirm',
                    text: 'Are you sure you want to mark order as complete?',
                    showCancelButton: true,
                    confirmButtonColor: '#252525',
                    cancelButtonColor: 'red',
                    confirmButtonText: 'Yes, I Confirm',
                    cancelButtonText: `No, Cancel It`,
                }).then((result) => {
                    if (result.isConfirmed) {
                        $(target).submit();
                    }
                })
            })
        });
    </script>
@endpush
