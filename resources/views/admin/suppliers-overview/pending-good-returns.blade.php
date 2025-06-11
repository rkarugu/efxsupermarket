@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Pending Goods Returns </h3>
                </div>
            </div>

            <div class="box-body">
                <table class="table table-bordered" id="create_datatable_25">
                    <thead>
                        <tr>
                            <th style="width: 3%;">#</th>
                            <th>Date Requested</th>
                            <th>Return Type</th>
                            <th>Document No</th>
                            <th>Supplier</th>
                            <th>Initiated By</th>
                            <th>Total Amount</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($returns as $i => $return)   
                            <tr>
                                <th>{{ ++$i }}</th>
                                <td>{{ $return->created_at->format('Y-m-d H:i:s') }}</td>
            
                                @php
                                    $returnType = '';
                                    
                                    if ($return->grn_id) {
                                        $returnType = 'From GRN';
                                    } else {
                                        $returnType = 'From Store';
                                    }
                                @endphp
            
                                <td>{{ $returnType }}</td>
                                <td>{{ $return->return_number ?? $return->rfs_no }}</td>
                                <td>{{ $return->supplier->name }}</td>
                                <td>{{ $return->user->name }}</td>
                                <td>KES {{ number_format($return->totalCost(), 2) }}</td>
                                <td>
                                    @php
                                        $approvePermission = $returnType == 'From Store' ? isset($user->permissions['return-to-supplier-from-store___approve']) : isset($user->permissions['return-to-supplier-from-grn___approve']);
                                        $approveRoute = $returnType == 'From Store' ? route('return-to-supplier.from-store.approve', $return->id) : route('return-to-supplier.from-grn.approve', $return->return_number); 
                                    @endphp
            
                                    <div class="action-button-div">
                                        @if ($approvePermission || $user->role_id == '1')
                                            <a title="Aprrove" href="{{ $approveRoute }}" target="_blank">
                                                <i aria-hidden="true" class="fa fa-eye" style="font-size: 20px;"></i>
                                            </a> 
                                        @endif                                           
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        $("body").addClass('sidebar-collapse');
    </script>
@endpush
