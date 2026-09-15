<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Q-Commerce Admin | Orders & Pickup Management</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
      <li class="nav-item"><a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a></li>
      <li class="nav-item d-none d-sm-inline-block"><a href="/admin" class="nav-link">Dashboard</a></li>
      <li class="nav-item d-none d-sm-inline-block"><a href="/admin/orders" class="nav-link active font-weight-bold">Orders Management</a></li>
    </ul>
  </nav>

  @include('admin.layouts.sidebar')

  <!-- Content Wrapper -->
  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid d-flex justify-content-between align-items-center">
        <div>
          <h1 class="m-0 font-weight-bold">Orders & Store Pickups</h1>
          <p class="text-muted mb-0 small">View, filter, and track customer pickup dates, slots, and delivery details.</p>
        </div>
        <div>
          <a href="/admin/deliveries" class="btn btn-outline-primary btn-sm rounded-pill font-weight-bold">
            <i class="fas fa-truck mr-1"></i> Logistics & Dispatch View
          </a>
        </div>
      </div>
    </div>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- Filters Card -->
        <div class="card card-outline card-primary shadow-sm mb-3">
          <div class="card-body p-3">
            <form method="GET" action="/admin/orders" class="row align-items-center">
              <div class="col-md-5 mb-2 mb-md-0">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text bg-light border-right-0"><i class="fas fa-filter text-muted"></i></span>
                  </div>
                  <select name="type" class="form-control font-weight-bold" onchange="this.form.submit()">
                    <option value="">All Order Types (Store Pickup & Delivery)</option>
                    <option value="pickup" {{ request('type') == 'pickup' ? 'selected' : '' }}>🏬 Store Pickup Only</option>
                    <option value="delivery" {{ request('type') == 'delivery' ? 'selected' : '' }}>🛵 Home Delivery Only</option>
                  </select>
                </div>
              </div>

              <div class="col-md-5 mb-2 mb-md-0">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text bg-light border-right-0"><i class="fas fa-info-circle text-muted"></i></span>
                  </div>
                  <select name="status" class="form-control font-weight-bold" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Processing" {{ request('status') == 'Processing' ? 'selected' : '' }}>Processing</option>
                    <option value="Out for Delivery" {{ request('status') == 'Out for Delivery' ? 'selected' : '' }}>Ready for Pickup / Out for Delivery</option>
                    <option value="Delivered" {{ request('status') == 'Delivered' ? 'selected' : '' }}>Delivered / Pickup Completed</option>
                    <option value="Cancelled" {{ request('status') == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                  </select>
                </div>
              </div>

              <div class="col-md-2 text-md-right">
                @if(request('type') || request('status'))
                  <a href="/admin/orders" class="btn btn-sm btn-link text-danger">
                    <i class="fas fa-times-circle mr-1"></i> Clear Filters
                  </a>
                @endif
              </div>
            </form>
          </div>
        </div>

        <!-- Orders Table Card -->
        <div class="card shadow-sm">
          <div class="card-body p-0 table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="bg-light">
                <tr class="text-uppercase text-muted text-xs">
                  <th class="pl-4">Order Details</th>
                  <th>Customer / Recipient</th>
                  <th>Order Type</th>
                  <th>Pickup Schedule</th>
                  <th>Total Amount</th>
                  <th>Status</th>
                  <th class="pr-4 text-right">Action</th>
                </tr>
              </thead>
              <tbody>
                @forelse($orders as $order)
                <tr>
                  <td class="pl-4">
                    <div class="font-weight-bold text-dark">#{{ $order->order_number }}</div>
                    <div class="text-muted text-xs"><i class="fas fa-store mr-1"></i>{{ $order->store_name ?? 'Darkstore' }}</div>
                    <div class="text-muted text-xs"><i class="far fa-clock mr-1"></i>{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y, h:i A') }}</div>
                  </td>
                  <td>
                    <div class="font-weight-bold text-dark">{{ $order->user_name }}</div>
                    <div class="text-muted small">{{ $order->user_phone }}</div>
                    @if(!empty($order->is_for_someone_else))
                      <span class="badge badge-warning text-dark text-xs mt-1">
                        <i class="fas fa-gift mr-1"></i> For: {{ $order->receiver_name }} ({{ $order->receiver_phone }})
                      </span>
                    @endif
                  </td>
                  <td>
                    @if(isset($order->order_type) && $order->order_type === 'pickup')
                      <span class="badge badge-purple px-2 py-1" style="background-color: #6f42c1; color: white;">
                        🏬 Store Pickup
                      </span>
                    @else
                      <span class="badge badge-info px-2 py-1">
                        🛵 Home Delivery
                      </span>
                    @endif
                  </td>
                  <td>
                    @if(isset($order->order_type) && $order->order_type === 'pickup')
                      <div class="font-weight-bold text-dark">{{ $order->pickup_date ?? 'Today' }}</div>
                      <div class="badge badge-light border text-xs text-secondary mt-1">
                        <i class="far fa-clock mr-1"></i>{{ $order->pickup_time ?? '06:00 AM - 11:00 PM' }}
                      </div>
                    @else
                      <span class="text-muted small">Standard Delivery</span>
                    @endif
                  </td>
                  <td>
                    <div class="font-weight-bold text-dark">₹{{ number_format($order->grand_total, 2) }}</div>
                    <div class="text-muted text-xs">{{ strtoupper($order->payment_method ?? 'COD') }} ({{ $order->payment_status ?? 'Pending' }})</div>
                  </td>
                  <td>
                    @php
                      $statusBadges = [
                        'Pending' => 'badge-warning',
                        'Processing' => 'badge-info',
                        'Out for Delivery' => 'badge-primary',
                        'Delivered' => 'badge-success',
                        'Cancelled' => 'badge-danger'
                      ];
                      $badgeClass = $statusBadges[$order->status] ?? 'badge-secondary';
                    @endphp
                    <span class="badge {{ $badgeClass }} px-2 py-1">
                      {{ $order->status }}
                    </span>
                  </td>
                  <td class="pr-4 text-right">
                    <a href="/admin/orders/{{ $order->id }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                      <i class="fas fa-eye mr-1"></i> View Details
                    </a>
                  </td>
                </tr>
                @empty
                <tr>
                  <td colspan="7" class="text-center py-5 text-muted">
                    <i class="fas fa-inbox fa-2x d-block mb-2 text-secondary"></i>
                    No orders found matching criteria.
                  </td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>
          @if($orders->hasPages())
          <div class="card-footer bg-white py-3">
            {{ $orders->withQueryString()->links() }}
          </div>
          @endif
        </div>
      </div>
    </section>
  </div>
</div>

<script href="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>
