@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header">
                <div class="box-header-flex">
                    <div class="d-flex flex-column">
                        <h3 class="box-title"> Portal Suppliers </h3>
                    </div>

                </div>
            </div>
            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <div class="table-responsive">
                    <table class="table" id="suppliers_from_portal_table">
                        <thead>
                            <tr>
                                <th style="width: 3%;">#</th>
                                <th>Supplier</th>
                                {{--                                <th>Code</th> --}}
                                <th>Agreement No</th>
                                <th>Date Joined</th>
                                <th>Telephone</th>
                                <th>Email Address</th>
                                {{--                                <th>Physical Address</th> --}}
                                <th>Admin User</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($trades as $index => $trade)
                                <tr>
                                    <th style="width: 3%;" scope="row">{{ $index + 1 }}</th>
                                    <td>{{ $trade->supplier->name }}</td>
                                    {{--                                    <td>{{ $trade->supplier->supplier_code }}</td> --}}
                                    <td>{{ @$trade->reference }}</td>
                                    <td>{{ \Carbon\Carbon::parse($trade->linked_at)->format('d-m-Y H:i:s') }}</td>
                                    @php
                                        $portal_supplier = $portal_suppliers
                                            ->where('supplier_code', $trade->supplier->supplier_code)
                                            ->first();
                                    @endphp
                                    <td>{{ $portal_supplier ? $portal_supplier['contact_person_phone_number'] : '' }}</td>
                                    <td>{{ $portal_supplier ? $portal_supplier['contact_person_email'] : '' }}</td>
                                    {{--                                    <td>{{ $trade->supplier->address }}</td> --}}
                                    <td>
                                        {{ $portal_supplier ? $portal_supplier['contact_person_first_name'] . ' ' . $portal_supplier['contact_person_last_name'] : '' }}
                                    </td>
                                    <td class="text-center">
                                        @if (can('view', 'supplier-maintain-suppliers'))
                                            <a href="{{ route('supplier-portal.supplier-details', $trade->supplier->id) }}"
                                                title="Show Supplier Details" class="span-action">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                        @endcan

                                        @if (can('staff', 'supplier-maintain-suppliers'))
                                            <a href="{{ route('supplier-portal.get_supplier_staff', $trade->supplier->id) }}"
                                                title="Show Staff" class="span-action">
                                                <i class="fa fa-users"></i>
                                            </a>
                                        @endif

                                        @if (!$trade->supplier->is_suspended && can('suspend', 'supplier-maintain-suppliers'))
                                            <a href="#" title="Suspend Supplier" data-toggle="modal"
                                                data-target="#modelId{{ $index + 1 }}" class="span-action">
                                                <i class="fa fa-ban"></i>
                                            </a>
                                        @else
                                            <a href="#" title="Undo Suspend Supplier" data-toggle="modal"
                                                data-target="#modelId{{ $index + 1 }}" class="span-action">
                                                <i class="fa fa-circle"></i>
                                            </a>
                                        @endif

                                        @if (can('impersonate', 'supplier-maintain-suppliers') && isset($portal_supplier['contact_person_email']))
                                            <a href="{{ route('supplier-portal.impersonate', $trade->supplier->id) }}"
                                                title="Impersonate" class="span-action" target="_blank">
                                                <i class="fa fa-sign-in-alt"></i>
                                            </a>
                                        @endif

                                        <!-- Modal -->
                                        <div class="modal fade" id="modelId{{ $index + 1 }}" tabindex="-1"
                                            role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <form action="{{ route('supplier-portal.suspend-supplier') }}"
                                                    method="post">
                                                    @csrf
                                                    <input type="hidden" name="id"
                                                        value="{{ $trade->supplier->id }}">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">
                                                                @if (!$trade->supplier->is_suspended)
                                                                    Suspend Supplier
                                                                @else
                                                                    Undo Suspension
                                                                @endif
                                                            </h5>

                                                        </div>
                                                        <div class="modal-body">
                                                            Confirm and proceed with @if (!$trade->supplier->is_suspended)
                                                                Suspension
                                                            @else
                                                                Undo Suspension
                                                            @endif of Supplier Account :
                                                            {{ $trade->supplier->supplier_code }}
                                                            {{ $trade->supplier->name }}
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-dismiss="modal">Close</button>
                                                            <button type="submit"
                                                                class="btn btn-primary">Proceed</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
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

@push('styles')
<style>
    .span-action {
        margin-right: 5px;
        display: inline-block;
    }
</style>
@endpush

@section('uniquepagescript')
<script>
    $('body').addClass('sidebar-collapse');

    $('#suppliers_from_portal_table').DataTable({
        "paging": true,
        "pageLength": 100,
        "searching": true,
        "lengthChange": true,
        "lengthMenu": [10, 20, 50, 100],
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "order": [
            [0, "asc"]
        ]
    });
</script>
@endsection
