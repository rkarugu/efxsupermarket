@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                @include('message')
                <div class="d-flex justify-content-between">
                    <h4 class="box-title">{{$title}}</h4>
                    
                </div>
            </div>
            <div class="box-header with-border">
                <form>
                    <div class="row">
                        <div class="col-sm-10">
                            <div class="form-group col-sm-3">
                                <label>From</label>
                                <input type="date" class="form-control" name="from"
                                    value="{{ request()->from ?? date('Y-m-d') }}">
                            </div>
                            <div class="form-group col-sm-3">
                                <label>To</label>
                                <input type="date" class="form-control" name="to"
                                    value="{{ request()->to ?? date('Y-m-d') }}">
                            </div>
                            <div class="form-group col-sm-3">
                                <label>Supplier </label>
                                <select name="supplier" id="inputsupplier" class="form-control mlselec6t">
                                    <option value="" selected disabled> Select Supplier</option>
                                    @foreach (getSupplierDropdown() as $index => $supplier)
                                        <option value="{{ $index }}"
                                            {{ request()->supplier == $index ? 'selected' : '' }}>
                                            {{ $supplier }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <label style="display:block">&nbsp;</label>
                            <button type="submit" class="btn btn-primary" value="Filter">
                                <i class="fa fa-filter"></i> Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="create_datatable">
                        <thead>
                            <tr>
                                <th width="2%">S.No.</th>
                                <th >Order No</th>
                                <th >Order date</th>
                                <th >Supplier</th>
                                <th >Total Quantity</th>
                                <th >Status</th>
                                <th class="noneedtoshort" style="text-align: center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (isset($list) && !empty($list))
                                <?php $b = 1; ?>
                                @foreach ($list as $item)
                                    <tr >
                                        <td>{!! $b !!}</td>
                                        <td>{!! $item->order_number !!}</td>
                                        <td>{!! date('d M Y',strtotime($item->order_date)) !!}</td>
                                        <td>{!! @$item->getSupplier->name !!}</td>
                                        <td>{{ manageAmountFormat($item->items->sum('quantity')) }}
                                        </td>
                                        <td
                                        @class(['bg-aqua' => $item->status == 'Pending'])
                                        @class(['bg-green' => $item->status == 'Accepted'])
                                        @class(['bg-red' => $item->status == 'Rejected'])
                                        >{!! $item->status !!}</td>
                                        <td class="actions">
                                            <a href="{{route('suggested-order.show',$item->id)}}">
                                                <i class="fa fa-eye"></i>
                                            </a>
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
    <script>
        $(document).ready(function() {
            $("select.mlselec6t").select2()
        });
    </script>
@endpush
