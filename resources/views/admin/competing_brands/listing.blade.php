@extends('layouts.admin.admin')

@section('content')
    <style>
        .span-action {

            display: inline-block;
            margin: 0 3px;

        }
    </style>
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between">
                    <h3 class="box-title">Competing Brands</h3>

                </div>
            </div>
            <div class="box-body">
                {!! Form::open(['route' => 'competing-brands.listing', 'method' => 'get']) !!}

                <div class="row">
                        <div class="col-md-3 form-group">
                            <select name="item" id="item" class="form-control mlselec6t" >
                                <option value="" selected disabled>Select Item</option>
                                @foreach ($items as $item)
                                    <option value="{{$item->id}}" {{ $item->id == request()->item ? 'selected' : '' }}>{{$item->stock_id_code.' - '.$item->title}}</option>

                                @endforeach
                            </select>

                        </div>                  
                    <div class="col-md-3 form-group">
                        <button type="submit" class="btn btn-success" name="manage-request" value="filter"><i class="fas fa-filter"></i> Filter</button>
                        <a class="btn btn-success ml-12" href="{!! route('competing-brands.listing') !!}"><i class="fas  fa-eraser"></i> Clear</a>
                    </div>
                </div>
                {!! Form::close(); !!}

                @include('message')
                <div class="col-md-12 no-padding-h table-responsive">
                    <table  class="table table-bordered table-hover" id="create_datatable">
                        <thead>
                            <th>#</th>
                            <th>Name</th>
                            <th>Created By</th>
                            <th>Item Count</th>
                            <th>Action</th>
                        </thead>
                        <tbody>
                            @foreach ($competingBrands as $competingBrand)
                                <tr class="return-row" data-competing-brand-id="{{ $competingBrand->id }}" >
                                    <th><i class="fa fa-plus-circle toggle-details" style="cursor: pointer; font-size: 16px;"></i></th>
                                    <td>{{$competingBrand->name}}</td>
                                    <td>{{$competingBrand->getRelatedUser?->name}}</td>
                                    <td style="text-align: center;">{{$competingBrand->get_related_items_count}}</td>
                                    <td>
                                        <a href="{{route('competing-brands.details', $competingBrand->id)}}" title="Show Details"> <i class="fas fa-eye"></i> </a>
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
@section('uniquepagescript')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <style>
        .select2 {
            width: 100% !important;
        }
    </style>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript">
       
        $(function() {
            $(".mlselec6t").select2();

            $('.toggle-details').on('click', function() {
            var $row = $(this).closest('tr');
            var competingBrandsId = $row.data('competing-brand-id');
            var $icon = $(this);
            var url = '{{ route("completedBrandsItems", [":competingBrandsId"]) }}';
            url = url.replace(':competingBrandsId', competingBrandsId);

            $('.competing-brands-items').not($row.next('.competing-brands-items')).remove();
            $('.toggle-details').not($icon).removeClass('fa-minus-circle').addClass('fa-plus-circle');

            if ($row.next('.competing-brands-items').length > 0) {
                $row.next('.competing-brands-items').toggle();
                $icon.toggleClass('fa-plus-circle fa-minus-circle');
                return;
            }

            $.ajax({
                url: url,
                method: 'GET',
                success: function(data) {
                    console.log(data);
                    var detailsRow = '<tr class="competing-brands-items"><td colspan="6"><table class="table table-bordered"><thead><tr><th>Stock Id Code</th><th>Title</th><th>QOH</th><th>Standard Cost</th><th>Selling Price</th></tr></thead><tbody>';

                    data.forEach(function(item) {
                        let qoh = item.qoh !== null ? item.qoh : 0;
                        detailsRow += '<tr><td>' + item.stock_id_code + '</td><td>' + item.title + '</td><td class="sub-table-qty">' + qoh + '</td><td class="sub-table-amounts">' + item.standard_cost + '</td><td class="sub-table-amounts">' + item.selling_price + '</td></tr>';
                    });

                    detailsRow += '</tbody></table></td></tr>';
                    $row.after(detailsRow);
                    $icon.toggleClass('fa-plus-circle fa-minus-circle');
                },
                error: function() {
                    alert('Error loading return details.');
                }
            });
        });
        var $rows = $('#create_datatable tbody tr');
        if ($rows.length === 1) {
            $rows.find('.toggle-details').trigger('click');
        }

        });
    </script>
@endsection
