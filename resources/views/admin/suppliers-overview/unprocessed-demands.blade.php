@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Pending Demands </h3>
                </div>
            </div>

            <div class="box-body">
                <table class="table table-bordered" id="create_datatable_25">
                    <thead>
                        <tr>
                            <th style="width: 3%;">#</th>
                            <th>Date</th>
                            <th>Branch</th>
                            <th>Supplier</th>
                            <th>Demand No</th>
                            <th>Demand Type</th>
                            <th>Document No</th>
                            <th>Created By</th>
                            <th>Items</th>
                            <th>Total Amount</th>
                            <th>Ageing</th>
                            <th>Approved</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($demands as $i => $demand)   
                            <tr>
                                <th>{{ ++$i }}</th>
                                <td>{{ $demand->created_at->format('Y-m-d H:i:s') }}</td>
                                <td>{{ $demand->user->userRestaurent->name }}</td>
                                <td>{{ $demand->supplier->name }}</td>
                                <td>{{ $demand->demand_no }}</td>
            
                                @php
                                    $demandType = '';
                                    
                                    if ($demand->return_document_no == null) {
                                        $demandType = 'price demand';
                                    } else {
                                        $demandType = $demand->return_type;
                                    }
                                @endphp
            
                                <td>{{ ucwords($demandType) }}</td>
                                <td>{{ $demand->return_document_no }}</td>
                                <td>{{ $demand->user->name }}</td>
                                <td>{{ $demand->demand_items_count ?? $demand->return_demand_items_count }}</td>
                                <td>KES {{ number_format($demand->demand_amount) }}</td>
                                <td>{{ $demand->ageing }} days</td>
                                @if ($demand->approved)
                                    <td>Yes</td>
                                @else
                                    <td>No</td>
                                @endif
                                <td>
                                    @php
                                        $detailsRoute = $demandType == 'price demand' ? route('demands.item-demands.details.new', $demand->id) : route('return-demands.details', $demand->id);
                                        $printRoute = $demandType == 'price demand' ? route('demands.item-demands.download', $demand->id) : route('return-demands.print', $demand->id);
                                    @endphp
                                    <div class="action-button-div">
                                        <a href="{{ $detailsRoute }}" target="_blank"><i class="fas fa-eye text-primary fa-lg" style="color: #337ab7;" title="view"></i></a>
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
