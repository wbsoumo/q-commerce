@extends('admin.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Orders Management</h3>
        <p class="text-muted small mb-0">View, filter, and manage all customer orders including Store Pickups & Home Deliveries.</p>
    </div>
    <div>
        <a href="{{ url('/admin/deliveries') }}" class="btn btn-outline-primary shadow-sm rounded-pill px-3">
            <i class="bi bi-truck me-1"></i> Logistics & Dispatch View
        </a>
    </div>
</div>

<!-- Filters & Tabs -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ url('/admin/orders') }}" class="row g-3 align-items-center">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-light border-0"><i class="bi bi-funnel text-muted"></i></span>
                    <select name="type" class="form-select border-0 bg-light fw-medium" onchange="this.form.submit()">
                        <option value="">All Fulfillment Types (Delivery & Pickup)</option>
                        <option value="pickup" {{ request('type') == 'pickup' ? 'selected' : '' }}>🏬 Store Pickup Only</option>
                        <option value="delivery" {{ request('type') == 'delivery' ? 'selected' : '' }}>🛵 Home Delivery Only</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-light border-0"><i class="bi bi-flag text-muted"></i></span>
                    <select name="status" class="form-select border-0 bg-light fw-medium" onchange="this.form.submit()">
                        <option value="">All Order Statuses</option>
                        <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Processing" {{ request('status') == 'Processing' ? 'selected' : '' }}>Processing</option>
                        <option value="Out for Delivery" {{ request('status') == 'Out for Delivery' ? 'selected' : '' }}>Out for Delivery / Ready for Pickup</option>
                        <option value="Delivered" {{ request('status') == 'Delivered' ? 'selected' : '' }}>Delivered / Completed</option>
                        <option value="Cancelled" {{ request('status') == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4 text-end">
                @if(request('type') || request('status'))
                    <a href="{{ url('/admin/orders') }}" class="btn btn-link text-decoration-none text-muted small">
                        <i class="bi bi-x-circle me-1"></i> Clear Filters
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Orders Table -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light border-bottom">
                <tr class="text-secondary small text-uppercase">
                    <th class="ps-4">Order Details</th>
                    <th>Customer / Receiver</th>
                    <th>Fulfillment Type</th>
                    <th>Pickup Date & Slot</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th class="pe-4 text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr>
                    <td class="ps-4">
                        <div class="fw-bold text-dark">#{{ $order->order_number }}</div>
                        <div class="text-muted extra-small"><i class="bi bi-shop me-1"></i>{{ $order->store_name ?? 'Darkstore' }}</div>
                        <div class="text-muted extra-small"><i class="bi bi-clock me-1"></i>{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y, h:i A') }}</div>
                    </td>
                    <td>
                        <div class="fw-semibold text-dark">{{ $order->user_name }}</div>
                        <div class="text-muted small">{{ $order->user_phone }}</div>
                        @if($order->is_for_someone_else)
                            <span class="badge bg-warning text-dark extra-small rounded-pill mt-1">
                                <i class="bi bi-gift me-1"></i> Recipient: {{ $order->receiver_name }} ({{ $order->receiver_phone }})
                            </span>
                        @endif
                    </td>
                    <td>
                        @if($order->order_type === 'pickup')
                            <span class="badge bg-purple-subtle text-purple border border-purple-subtle px-2 py-1 rounded-pill fw-bold">
                                🏬 Store Pickup
                            </span>
                        @else
                            <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1 rounded-pill fw-bold">
                                🛵 Home Delivery
                            </span>
                        @endif
                    </td>
                    <td>
                        @if($order->order_type === 'pickup')
                            <div class="fw-semibold text-dark">{{ $order->pickup_date ?? 'Today' }}</div>
                            <div class="badge bg-light text-secondary border extra-small mt-1">
                                <i class="bi bi-clock-history me-1"></i>{{ $order->pickup_time ?? 'Standard Operating Hours' }}
                            </div>
                        @else
                            <span class="text-muted small">Standard Delivery</span>
                        @endif
                    </td>
                    <td>
                        <div class="fw-bold text-dark">₹{{ number_format($order->grand_total, 2) }}</div>
                        <div class="text-muted extra-small">{{ strtoupper($order->payment_method) }} ({{ $order->payment_status }})</div>
                    </td>
                    <td>
                        @php
                            $statusColors = [
                                'Pending' => 'bg-warning text-dark',
                                'Processing' => 'bg-info text-white',
                                'Out for Delivery' => 'bg-primary text-white',
                                'Delivered' => 'bg-success text-white',
                                'Cancelled' => 'bg-danger text-white'
                            ];
                            $badgeClass = $statusColors[$order->status] ?? 'bg-secondary text-white';
                        @endphp
                        <span class="badge {{ $badgeClass }} px-2 py-1 rounded-pill">
                            {{ $order->status }}
                        </span>
                    </td>
                    <td class="pe-4 text-end">
                        <a href="{{ url('/admin/orders/' . $order->id) }}" class="btn btn-sm btn-light border shadow-sm rounded-pill px-3">
                            <i class="bi bi-eye text-primary me-1"></i> View Details
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                        No orders found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($orders->hasPages())
    <div class="card-footer bg-white border-0 py-3">
        {{ $orders->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
