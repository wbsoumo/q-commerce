<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Q-Commerce Admin | Orders & Pickup Management</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,600,700&display=fallback">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  <style>
    :root {
      --primary-green: #0c831f;
      --primary-hover: #096818;
    }
    .brand-link { background-color: var(--primary-green) !important; }
    .btn-primary-custom {
      background-color: var(--primary-green);
      border-color: var(--primary-green);
      color: #ffffff;
    }
    .btn-primary-custom:hover {
      background-color: var(--primary-hover);
      border-color: var(--primary-hover);
      color: #ffffff;
    }
    .kpi-card {
      border: none;
      border-radius: 14px;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .kpi-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 24px rgba(0, 0, 0, 0.08) !important;
    }
    .badge-pickup {
      background-color: #ede7f6 !important;
      color: #673ab7 !important;
      border: 1px solid #d1c4e9;
    }
    .badge-delivery {
      background-color: #e3f2fd !important;
      color: #1976d2 !important;
      border: 1px solid #bbdefb;
    }
    .badge-status-pending { background-color: #fff3e0; color: #e65100; border: 1px solid #ffe0b2; }
    .badge-status-packing { background-color: #e0f7fa; color: #00838f; border: 1px solid #b2ebf2; }
    .badge-status-shipping { background-color: #e8eaf6; color: #283593; border: 1px solid #c5cae9; }
    .badge-status-delivered { background-color: #e8f5e9; color: #2e7d32; border: 1px solid #c8e6c9; }
    .badge-status-cancelled { background-color: #ffebee; color: #c62828; border: 1px solid #ffcdd2; }
    .table-custom th {
      font-size: 0.78rem;
      letter-spacing: 0.05em;
      font-weight: 700;
      color: #5f6368;
      border-top: none;
    }
    .table-custom td {
      vertical-align: middle !important;
    }
    .action-btn {
      transition: all 0.2s ease;
      border-radius: 8px;
    }
    .action-btn:hover {
      background-color: var(--primary-green);
      color: #ffffff !important;
    }
  </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light shadow-sm">
    <ul class="navbar-nav">
      <li class="nav-item"><a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a></li>
      <li class="nav-item d-none d-sm-inline-block"><a href="/admin" class="nav-link">Dashboard</a></li>
      <li class="nav-item d-none d-sm-inline-block"><a href="/admin/orders" class="nav-link active font-weight-bold text-success">Orders Management</a></li>
    </ul>
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <a class="nav-link text-muted" href="/admin/deliveries" title="Delivery Logistics">
          <i class="fas fa-truck mr-1"></i> Logistics View
        </a>
      </li>
    </ul>
  </nav>

  @include('admin.layouts.sidebar')

  <!-- Content Wrapper -->
  <div class="content-wrapper bg-light">
    <div class="content-header py-3">
      <div class="container-fluid d-flex justify-content-between align-items-center flex-wrap">
        <div>
          <h1 class="m-0 font-weight-bold text-dark" style="font-size: 1.6rem;">
            <i class="fas fa-shopping-bag text-success mr-2"></i>Orders & Store Pickups
          </h1>
          <p class="text-muted mb-0 small">Monitor real-time customer orders, pickup schedules, and delivery fulfillment.</p>
        </div>
        <div class="mt-2 mt-sm-0">
          <a href="/admin/deliveries" class="btn btn-outline-success btn-sm rounded-pill font-weight-bold px-3">
            <i class="fas fa-truck-dispatch mr-1"></i> Dispatch & Logistics
          </a>
        </div>
      </div>
    </div>

    <!-- Main content -->
    <section class="content pb-4">
      <div class="container-fluid">
        
        <!-- KPI Stat Cards -->
        <div class="row mb-3">
          <div class="col-lg-3 col-6 mb-2">
            <div class="card kpi-card shadow-sm p-3 bg-white border-left border-primary" style="border-left-width: 4px !important;">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <div class="text-muted text-xs font-weight-bold text-uppercase">Total Orders</div>
                  <div class="h3 font-weight-bold mb-0 text-dark">{{ $totalCount ?? 0 }}</div>
                </div>
                <div class="bg-light p-3 rounded-circle text-primary">
                  <i class="fas fa-receipt fa-lg"></i>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-6 mb-2">
            <div class="card kpi-card shadow-sm p-3 bg-white border-left border-warning" style="border-left-width: 4px !important;">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <div class="text-muted text-xs font-weight-bold text-uppercase">Pending</div>
                  <div class="h3 font-weight-bold mb-0 text-warning">{{ $pendingCount ?? 0 }}</div>
                </div>
                <div class="bg-light p-3 rounded-circle text-warning">
                  <i class="fas fa-clock fa-lg"></i>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-6 mb-2">
            <div class="card kpi-card shadow-sm p-3 bg-white border-left border-info" style="border-left-width: 4px !important;">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <div class="text-muted text-xs font-weight-bold text-uppercase">Active / Dispatch</div>
                  <div class="h3 font-weight-bold mb-0 text-info">{{ $activeCount ?? 0 }}</div>
                </div>
                <div class="bg-light p-3 rounded-circle text-info">
                  <i class="fas fa-shipping-fast fa-lg"></i>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-6 mb-2">
            <div class="card kpi-card shadow-sm p-3 bg-white border-left border-success" style="border-left-width: 4px !important;">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <div class="text-muted text-xs font-weight-bold text-uppercase">Completed</div>
                  <div class="h3 font-weight-bold mb-0 text-success">{{ $deliveredCount ?? 0 }}</div>
                </div>
                <div class="bg-light p-3 rounded-circle text-success">
                  <i class="fas fa-check-circle fa-lg"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Filters & Search Card -->
        <div class="card border-0 shadow-sm rounded-lg mb-3">
          <div class="card-body p-3">
            <form method="GET" action="/admin/orders" class="row align-items-center">
              <div class="col-md-4 mb-2 mb-md-0">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text bg-light border-right-0"><i class="fas fa-search text-muted"></i></span>
                  </div>
                  <input type="text" name="search" class="form-control border-left-0" placeholder="Search by Order #, Name, Phone..." value="{{ request('search') }}">
                </div>
              </div>

              <div class="col-md-3 mb-2 mb-md-0">
                <select name="type" class="form-control font-weight-bold" onchange="this.form.submit()">
                  <option value="">All Order Types (Pickup & Delivery)</option>
                  <option value="pickup" {{ request('type') == 'pickup' ? 'selected' : '' }}>🏬 Store Pickup Only</option>
                  <option value="delivery" {{ request('type') == 'delivery' ? 'selected' : '' }}>🛵 Home Delivery Only</option>
                </select>
              </div>

              <div class="col-md-3 mb-2 mb-md-0">
                <select name="status" class="form-control font-weight-bold" onchange="this.form.submit()">
                  <option value="">All Order Statuses</option>
                  <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                  <option value="Processing" {{ request('status') == 'Processing' ? 'selected' : '' }}>Processing / Packing</option>
                  <option value="Out for Delivery" {{ request('status') == 'Out for Delivery' ? 'selected' : '' }}>Ready / Out for Delivery</option>
                  <option value="Delivered" {{ request('status') == 'Delivered' ? 'selected' : '' }}>Delivered / Completed</option>
                  <option value="Cancelled" {{ request('status') == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
              </div>

              <div class="col-md-2 text-md-right">
                <button type="submit" class="btn btn-primary-custom btn-sm rounded-pill font-weight-bold px-3 mr-1">
                  <i class="fas fa-filter mr-1"></i> Apply
                </button>
                @if(request('type') || request('status') || request('search'))
                  <a href="/admin/orders" class="btn btn-sm btn-light text-danger rounded-circle" title="Clear Filters">
                    <i class="fas fa-times"></i>
                  </a>
                @endif
              </div>
            </form>
          </div>
        </div>

        <!-- Orders Table Card -->
        <div class="card border-0 shadow-sm rounded-lg overflow-hidden">
          <div class="card-body p-0 table-responsive">
            <table class="table table-hover table-custom align-middle mb-0">
              <thead class="bg-white border-bottom">
                <tr class="text-uppercase text-muted">
                  <th class="pl-4">Order # & Store</th>
                  <th>Customer / Recipient</th>
                  <th>Fulfillment Type</th>
                  <th>Pickup / Delivery Window</th>
                  <th>Grand Total</th>
                  <th>Status</th>
                  <th class="pr-4 text-right">Action</th>
                </tr>
              </thead>
              <tbody>
                @forelse($orders as $order)
                <tr>
                  <td class="pl-4 py-3">
                    <div class="font-weight-bold text-dark h6 mb-1">#{{ $order->order_number }}</div>
                    <div class="text-muted small mb-1">
                      <i class="fas fa-store-alt text-success mr-1"></i>{{ $order->store_name ?? 'Krishnanagar Main Hub' }}
                    </div>
                    <div class="text-muted text-xs">
                      <i class="far fa-clock mr-1"></i>{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y, h:i A') }}
                    </div>
                  </td>
                  <td>
                    <div class="font-weight-bold text-dark">{{ $order->user_name }}</div>
                    <div class="text-muted small"><i class="fas fa-phone-alt text-muted mr-1"></i>{{ $order->user_phone }}</div>
                    @if(!empty($order->is_for_someone_else) || !empty($order->receiver_name))
                      <span class="badge badge-warning text-dark text-xs mt-1">
                        <i class="fas fa-gift mr-1"></i> For: {{ $order->receiver_name }} ({{ $order->receiver_phone }})
                      </span>
                    @endif
                  </td>
                  <td>
                    @if(isset($order->order_type) && $order->order_type === 'pickup')
                      <span class="badge badge-pickup px-3 py-2 rounded-pill font-weight-bold">
                        🏬 Store Pickup
                      </span>
                    @else
                      <span class="badge badge-delivery px-3 py-2 rounded-pill font-weight-bold">
                        🛵 Home Delivery
                      </span>
                    @endif
                  </td>
                  <td>
                    @if(isset($order->order_type) && $order->order_type === 'pickup')
                      <div class="font-weight-bold text-dark small"><i class="far fa-calendar-alt text-purple mr-1"></i>{{ $order->pickup_date ?? 'Today' }}</div>
                      <div class="badge badge-light border text-xs text-secondary mt-1">
                        <i class="far fa-clock mr-1"></i>{{ $order->pickup_time ?? '06:00 AM - 11:00 PM' }}
                      </div>
                    @else
                      <div class="text-dark small font-weight-bold"><i class="fas fa-bolt text-warning mr-1"></i>Express 10-Min Delivery</div>
                      <span class="text-muted text-xs">Standard Fulfillment</span>
                    @endif
                  </td>
                  <td>
                    <div class="font-weight-bold text-success h6 mb-0">₹{{ number_format($order->grand_total, 2) }}</div>
                    <span class="badge badge-light text-muted border text-xs mt-1">
                      {{ isset($order->payment_method) ? strtoupper($order->payment_method) : 'COD' }} • {{ isset($order->payment_status) ? $order->payment_status : 'Pending' }}
                    </span>
                  </td>
                  <td>
                    @php
                      $statusClasses = [
                        'Pending' => 'badge-status-pending',
                        'Packing' => 'badge-status-packing',
                        'Processing' => 'badge-status-packing',
                        'Out for Delivery' => 'badge-status-shipping',
                        'Ready for Pickup' => 'badge-status-shipping',
                        'Delivered' => 'badge-status-delivered',
                        'Cancelled' => 'badge-status-cancelled',
                      ];
                      $badgeClass = $statusClasses[$order->status] ?? 'badge-light border';
                    @endphp
                    <span class="badge {{ $badgeClass }} px-3 py-2 rounded-pill font-weight-bold">
                      {{ $order->status }}
                    </span>
                  </td>
                  <td class="pr-4 text-right">
                    <a href="/admin/orders/{{ $order->id }}" class="btn btn-sm btn-outline-success action-btn font-weight-bold px-3">
                      <i class="fas fa-eye mr-1"></i> View Details
                    </a>
                  </td>
                </tr>
                @empty
                <tr>
                  <td colspan="7" class="text-center py-5 text-muted">
                    <i class="fas fa-inbox fa-3x d-block mb-3 text-secondary opacity-50"></i>
                    <h5 class="font-weight-bold text-dark">No orders found</h5>
                    <p class="small text-muted mb-0">No orders match your current filter parameters.</p>
                  </td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>
          @if($orders->hasPages())
          <div class="card-footer bg-white border-top py-3 d-flex justify-content-between align-items-center">
            <div class="text-muted small">
              Showing {{ $orders->firstItem() }} to {{ $orders->lastItem() }} of {{ $orders->total() }} orders
            </div>
            <div>
              {{ $orders->withQueryString()->links() }}
            </div>
          </div>
          @endif
        </div>
      </div>
    </section>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>
