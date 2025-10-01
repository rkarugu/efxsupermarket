@extends('layouts.admin.admin')

@section('content')
<section class="content">
    <div class="session-message-container">
        @include('message')
    </div>

    <!-- Salesman Dashboard Header -->
    <div class="box box-primary">
        <div class="box-header with-border">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="box-title">
                    <i class="fa fa-user-tie"></i> Salesman Dashboard - {{ $user->name }}
                </h3>
                <div>
                    <span class="box-title">
                        <i class="fa fa-calendar"></i> {{ \Carbon\Carbon::now()->toFormattedDayDateString() }}
                    </span>
                </div>
            </div>
        </div>

        <div class="box-body">
            <!-- Shift Status Card -->
            <div class="row">
                <div class="col-md-12">
                    <div class="info-box {{ $activeShift ? 'bg-green' : 'bg-red' }}">
                        <span class="info-box-icon">
                            <i class="fa {{ $activeShift ? 'fa-play-circle' : 'fa-stop-circle' }}"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Shift Status</span>
                            <span class="info-box-number">
                                {{ $activeShift ? 'ACTIVE' : 'NO ACTIVE SHIFT' }}
                            </span>
                            @if($activeShift)
                                <div class="progress">
                                    <div class="progress-bar" style="width: 100%"></div>
                                </div>
                                <span class="progress-description">
                                    Started: {{ $activeShift->start_time ? \Carbon\Carbon::parse($activeShift->start_time)->format('H:i A') : 'N/A' }} | 
                                    Type: {{ ucfirst($activeShift->shift_type ?? 'N/A') }}
                                </span>
                            @endif
                        </div>
                        <div class="info-box-more">
                            @if($activeShift)
                                <button class="btn btn-danger btn-sm" onclick="closeShift()">
                                    <i class="fa fa-stop"></i> Close Shift
                                </button>
                            @else
                                <button class="btn btn-success btn-sm" onclick="openShiftModal()">
                                    <i class="fa fa-play"></i> Open Shift
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="row">
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-aqua"><i class="fa fa-shopping-cart"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Today's Orders</span>
                            <span class="info-box-number">{{ $todaysOrders->count() }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-green"><i class="fa fa-money"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Today's Sales</span>
                            <span class="info-box-number">KSh {{ number_format($todaysOrders->sum(function($order) { return $order->getOrderTotal(); }), 2) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-yellow"><i class="fa fa-users"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Route Customers</span>
                            <span class="info-box-number">{{ $routeCustomers->count() }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-red"><i class="fa fa-route"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Route</span>
                            <span class="info-box-number">{{ $routeInfo->route_name ?? 'Not Assigned' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-solid">
                        <div class="box-header">
                            <h3 class="box-title">Quick Actions</h3>
                        </div>
                        <div class="box-body">
                            @if($activeShift)
                                <a href="{{ route('salesman-orders.create') }}" class="btn btn-primary btn-lg">
                                    <i class="fa fa-plus"></i> Create New Order
                                </a>
                                <button class="btn btn-info btn-lg" onclick="showCustomers()">
                                    <i class="fa fa-users"></i> View Customers
                                </button>
                                <a href="{{ route('salesman-customers.index') }}" class="btn btn-success btn-lg">
                                    <i class="fa fa-user-plus"></i> Manage Customers
                                </a>
                            @else
                                <div class="alert alert-warning">
                                    <i class="fa fa-exclamation-triangle"></i>
                                    You need to open a shift before you can create orders.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Orders -->
    @if($todaysOrders->count() > 0)
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">Today's Orders</h3>
        </div>
        <div class="box-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Order No.</th>
                            <th>Customer</th>
                            <th>Time</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($todaysOrders as $order)
                        <tr>
                            <td>{{ $order->requisition_no }}</td>
                            <td>{{ $order->getRouteCustomer->bussiness_name ?? 'N/A' }}</td>
                            <td>{{ $order->created_at->format('H:i A') }}</td>
                            <td>{{ $order->getRelatedItem->count() }}</td>
                            <td>KSh {{ number_format($order->getOrderTotal(), 2) }}</td>
                            <td>
                                <span class="label label-{{ $order->status == 'PENDING' ? 'warning' : 'success' }}">
                                    {{ $order->status }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('salesman-orders.show', $order->id) }}" class="btn btn-sm btn-info" title="View Details">
                                    <i class="fa fa-eye"></i>
                                </a>
                                <a href="{{ route('salesman-orders.print', $order->id) }}" class="btn btn-sm btn-primary" target="_blank" title="Print Order">
                                    <i class="fa fa-print"></i>
                                </a>
                                <a href="{{ route('salesman-orders.download', $order->id) }}" class="btn btn-sm btn-success" title="Download Invoice">
                                    <i class="fa fa-download"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Route Customers Modal -->
    <div class="modal fade" id="customersModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Route Customers</h4>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Business Name</th>
                                    <th>Contact Person</th>
                                    <th>Phone</th>
                                    <th>Town</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($routeCustomers as $customer)
                                <tr>
                                    <td>{{ $customer->bussiness_name }}</td>
                                    <td>{{ $customer->name }}</td>
                                    <td>{{ $customer->phone }}</td>
                                    <td>{{ $customer->town }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Open Shift Modal -->
    <div class="modal fade" id="openShiftModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Open New Shift</h4>
                </div>
                <div class="modal-body">
                    <form id="openShiftForm">
                        <div class="form-group">
                            <label>Shift Type</label>
                            <select name="shift_type" class="form-control" required>
                                <option value="">Select Shift Type</option>
                                <option value="regular">Regular</option>
                                <option value="emergency">Emergency</option>
                                <option value="overtime">Overtime</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" onclick="submitOpenShift()">Open Shift</button>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function showCustomers() {
    $('#customersModal').modal('show');
}

function openShiftModal() {
    $('#openShiftModal').modal('show');
}

function submitOpenShift() {
    const form = document.getElementById('openShiftForm');
    const formData = new FormData(form);
    
    fetch('{{ route("salesman-orders.shift.open") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Error opening shift');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error opening shift');
    });
}

function closeShift() {
    if (confirm('Are you sure you want to close your shift?')) {
        fetch('{{ route("salesman-orders.shift.close") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Error closing shift');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error closing shift');
        });
    }
}
</script>
@endsection
