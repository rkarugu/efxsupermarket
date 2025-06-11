@extends('layouts.admin.admin')
@section('content')
<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="box-title">Edit Competing Brands</h3>            
                <a href="{{ route('competing-brands.index') }}" class="btn btn-success btn-sm"><i class="fas fa-arrow-left"></i> Back</a>
            </div>
        </div>
        @include('message')
        <form class="validate form-horizontal" role="form" method="POST" action="{{ route('competing-brands.update', $competingBrand->id) }}" enctype="multipart/form-data">
            {{ csrf_field() }}
            {{-- @method('PUT')  --}}

            <div class="box-body">
                <div class="form-group">
                    <label for="name" class="col-sm-2 control-label">Name</label>
                    <div class="col-sm-9">
                        <input type="text" name="name" id="name" class="form-control" style="text-transform:uppercase" value="{{ $competingBrand->name }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="items" class="col-sm-2 control-label">Items</label>
                    <div class="col-sm-9">
                        <table class="table table-bordered" id="items_table">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    {{-- <th>Title</th> --}}
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($competingBrand->getRelatedItems as $item)
                                    <tr>
                                        <td>
                                            <select name="items[]" class="form-control item-select mlselect"
                                            @if($user->id != $item->added_by && $user->id != 1) disabled @endif>
                                                <option value="{{ $item->getRelatedItem->id }}" selected>
                                                    {{ $item->getRelatedItem->stock_id_code }} - {{ $item->getRelatedItem->title }}
                                                </option>
                                                @foreach ($items as $option)
                                                    @if ($option->id !== $item->id)
                                                        <option value="{{ $option->id }}">
                                                            {{ $option->stock_id_code }} - {{ $option->title }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            @if($user->id == $item->added_by || $user->id == 1)
                                            <button type="button" class="btn btn-success btn-sm remove-item">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @else
                                            <span></span>
                                        @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-primary btn-sm" id="add_item_button"><i class="fas fa-plus"></i></button>
                        @if ($user->role_id == 1)
                            <div class="form-group ">
                                <label for="excel_file" class="col-sm-2 control-label">Upload Excel</label>
                                <div class="col-sm-9 d-flex justify-content-between align-items-center">
                                    <div>
                                        <input type="file" id="excel_file" name="excel_file" accept=".xlsx, .xls" class="form-control">
                                        <small id="file_name" class="form-text text-muted">No file chosen.</small>

                                    </div>
                                        <button type="button" class="btn btn-primary btn-sm" id="upload_button" style="margin-top: 10px;"><i class="fas fa-upload"></i> Upload</button>

                                </div>
                            </div>
                            
                        @endif
                      
                    </div>
                </div> 
            </div>
            <div class="box-footer text-right">
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-paper-plane"></i> Submit</button>
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
            let itemIndex = {{ $competingBrand->getRelatedItems->count() }};  

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
                updateItemOptions();
                
                itemIndex++;
            });

            $('#items_table').on('click', '.remove-item', function() {
                $(this).closest('tr').remove();
            });

            $('#items_table').on('change', '.item-select', function() {
                const selectedOption = $(this).find('option:selected').text().split(' - ');
                $(this).closest('tr').find('.item-title').text(selectedOption[1]);
            });

            //file upload
            $('#upload_button').click(function() {
        const fileInput = $('#excel_file')[0];
        const formData = new FormData();
        formData.append('excel_file', fileInput.files[0]);
        let csrfToken = "{{ csrf_token() }}";

        $.ajax({
            url: '{{ route('upload-excel') }}', 
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken  
            },
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                console.log(response);
                const items = response.items;
                items.forEach(item => {
                    const newRow = `
                        <tr>
                            <td>
                                <select name="items[]" class="form-control item-select mlselect">
                                    <option value="${item.id}" selected>
                                        ${item.stock_id_code} - ${item.title}
                                    </option>
                                </select>
                            </td>
                            <td><button type="button" class="btn btn-success btn-sm remove-item"><i class="fas fa-trash"></i></button></td>
                        </tr>
                    `;
                    $('#items_table tbody').append(newRow);
                });
                $('.mlselect').select2();
            },
            error: function(error) {
                console.error('Error uploading file:', error);
            }
        });
    });

        });
        $(document).ready(function() {
            $('#excel_file').change(function(e) {
                let fileName = e.target.files[0].name;  
                $('#file_name').text(fileName);  
            });
        });

    </script>
@endsection

