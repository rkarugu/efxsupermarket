@extends('layouts.admin.admin')
@section('content')
<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="box-title">Create Competing Brands</h3>            
                <a href="{{ route('competing-brands.index') }}" class="btn btn-success btn-sm"><i class="fas fa-arrow-left"></i> Back</a>
            </div>
        </div>
        @include('message')
        <form class="validate form-horizontal" role="form" method="POST" action="{{ route('competing-brands.store') }}" enctype="multipart/form-data">
            {{ csrf_field() }}
            <div class="box-body">
                <div class="form-group">
                    <label for="name" class="col-sm-2 control-label">Name</label>
                    <div class="col-sm-9">
                        <input type="text" name="name" id="name" style="text-transform:uppercase" class="form-control" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="items" class="col-sm-2 control-label">Items</label>
                    <div class="col-sm-9">
                        <!-- Create a table for items -->
                        <table class="table table-bordered" id="items_table">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    {{-- <th>Title</th> --}}
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-primary btn-sm" id="add_item_button"><i class="fas fa-plus"></i></button>
                    </div>
                </div> 
            </div>
            <div class="box-footer text-right">
                <button type="submit" class="btn btn-primary btn-sm"><i  class="fas fa-paper-plane"></i> Submit</button>
            </div>
        </form>
    </div>
</section>
@endsection

@section('uniquepagestyle')
    <link rel="stylesheet" href="{{ asset('assets/admin/dist/datepicker.css') }}">
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet"/>
@endsection

@section('uniquepagescript')
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript">
        $(function () {
            $(".mlselect").select2();
        });

        $(document).ready(function() {
            let items = @json($items);  
            let itemIndex = 0;

            $('#add_item_button').click(function() {
                let itemOptions =  `<option value="">Select Item</option>`;
                items.forEach(item => {
                    itemOptions += `<option value="${item.id}">${item.stock_id_code} - ${item.title}</option>`;
                });
                const newRow = `
                    <tr>
                        <td>
                            <select name="items[]" class="form-control item-select mlselect">
                                ${itemOptions}
                            </select>
                        </td>
                        <td><button type="button" class="btn btn-success btn-sm remove-item"><i class="fas fa-trash"></i></button></td>
                    </tr>
                `;
                $('#items_table tbody').append(newRow);
                $('.mlselect').select2();
                let $newSelect = $('#items_table tbody').find('.mlselect').last();
                $newSelect.select2();  
                $newSelect.select2('open');
                itemIndex++;
            });

            $('#items_table').on('click', '.remove-item', function() {
                $(this).closest('tr').remove();

            });

            $('#items_table').on('change', '.item-select', function() {
                const selectedOption = $(this).find('option:selected').text().split(' - ');
             

            });
            $('form').on('submit', function(event) {
    console.log($(this).serialize()); 
});
        });
  

    </script>
@endsection

