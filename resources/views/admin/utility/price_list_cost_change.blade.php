@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> {!! $title !!}</h3>
                </div>
            </div>

            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="price_list_change_table">
                        <thead>
                            <tr>
                                <th style="width: 3%;"><input type="checkbox" id="select_all"></th>
                                <th>STOCK ID CODE</th>
                                <th>DESCRIPTION</th>
                                <th>SUPPLIER</th>
                                <th>TRADE AGREEMENT</th>
                                <th>ORIGINAL PRICE LIST COST</th>
                                <th>SUGGESTED PRICE LIST COST</th>
                                <th>STATUS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $index => $record)
                                <tr>
                                    <td><input type="checkbox" class="row-select" value="{{ $record->id }}"></td>
                                    <td>{{ $record?->item?->stock_id_code }}</td>
                                    <td>{{ $record?->item?->description }}</td>
                                    <td>{{ $record?->supplier?->name }}</td>
                                    <td>{{ $record?->trade?->reference }}</td>
                                    <td>
                                        @if ($record?->item?->price_list_cost)
                                            {{ $record?->item?->price_list_cost }}
                                        @else
                                            0.00
                                        @endif
                                    </td>
                                    <td>
                                        @if ($record?->price_list_cost)
                                            {{ $record?->price_list_cost }}
                                        @else
                                            0.00
                                        @endif
                                    </td>
                                    <td>{{ $record?->status }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <button id="submit_selected" class="btn btn-primary" style="display: none;">Confirm</button>
            </div>
        </div>
    </section>
@endsection

@section('uniquepagestyle')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <style>
        .btn-group {
            display: flex;
            gap: 10px;
        }

        .no-bg {
            background: none;
            border: none;
            padding: 0;
            box-shadow: none;
            font-size: 20px;
            color: #337ab7;
        }

        .no-bg i {
            color: inherit;
        }

        .no-bg:focus {
            outline: none;
            box-shadow: none;
        }

        .no-bg:hover {
            background-color: transparent;
        }
    </style>
@endsection

@section('uniquepagescript')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('js/sweetalert.js') }}"></script>
    <script src="{{ asset('js/form.js') }}"></script>
    <script>
        $(document).ready(function() {
            var table = $('#price_list_change_table').DataTable({
                "paging": true,
                "pageLength": 10,
                "searching": true,
                "lengthChange": true,
                "lengthMenu": [10, 20, 50, 100],
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "order": [
                    [1, "asc"]
                ]
            });

            $('#select_all').on('click', function() {
                var isChecked = $(this).is(':checked');
                $('.row-select').prop('checked', isChecked);
                toggleSubmitButton();
            });

            $('#price_list_change_table tbody').on('click', '.row-select', function() {
                if (!$(this).is(':checked')) {
                    $('#select_all').prop('checked', false);
                }
                toggleSubmitButton();
            });

            function toggleSubmitButton() {
                var selected = $('.row-select:checked').length > 0;
                if (selected) {
                    $('#submit_selected').show();
                } else {
                    $('#submit_selected').hide();
                }
            }

            $('#submit_selected').on('click', function(e) {
                e.preventDefault()

                $(this).html('<i class="fas fa-spinner fa-spin"></i> Processing...').prop('disabled', true);

                var formMessage = new Form()

                var selectedIds = $('.row-select:checked').map(function() {
                    return $(this).val();
                }).get();

                $.ajax({
                    url: '{{ route('maintain-items.approve-price-list-change-confirm') }}',
                    type: 'POST',
                    data: {
                        ids: selectedIds,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        formMessage.successMessage('Data updated')
                        setTimeout(() => {
                            location.reload()
                        }, 2000);
                    },
                    error: function(response) {
                        formMessage.successMessage('Something went wrong')
                    },
                    complete: function() {
                        $('#submit_selected').html('Submit Selected').prop('disabled', false);
                    }
                });
            });
        });
    </script>
@endsection
