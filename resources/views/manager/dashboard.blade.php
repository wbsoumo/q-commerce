<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Manager Dashboard | {{ $store->name ?? 'Branch' }}</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  <style>
    .brand-banner { background: linear-gradient(135deg, #007bff 0%, #0056b3 100%); }
    .manager-sidebar { background-color: #1e293b !important; }
  </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  <nav class="main-header navbar navbar-expand navbar-white navbar-light border-bottom shadow-sm">
    <ul class="navbar-nav">
      <li class="nav-item"><a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a></li>
      <li class="nav-item d-none d-sm-inline-block"><a href="#" class="nav-link active font-weight-bold text-primary"><i class="fas fa-store mr-1"></i> {{ $store->name ?? 'Store Branch' }} Manager Portal</a></li>
    </ul>
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <form action="/logout" method="POST" class="d-inline">
          @csrf
          <button type="submit" class="btn btn-outline-danger btn-sm font-weight-bold"><i class="fas fa-sign-out-alt mr-1"></i> Logout</button>
        </form>
      </li>
    </ul>
  </nav>

  @include('manager.layouts.sidebar')

  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row align-items-center mb-3">
          <div class="col-sm-6">
            <h1 class="m-0 font-weight-bold text-dark"><i class="fas fa-tachometer-alt text-primary mr-2"></i>Branch Dashboard</h1>
            <p class="text-muted mb-0"><i class="fas fa-building text-primary mr-1"></i> {{ $store->name ?? 'Store Branch' }} (Code: {{ $store->code ?? 'STR' }})</p>
          </div>
          <div class="col-sm-6 text-right">
            <span class="badge badge-{{ ($store->status ?? 'Active') === 'Active' ? 'success' : 'danger' }} p-2" style="font-size: 1rem;">
              <i class="fas fa-circle mr-1" style="font-size: 0.65rem;"></i> {{ $store->status ?? 'Active' }}
            </span>
            <span class="ml-2 font-weight-bold text-dark"><i class="far fa-clock text-primary mr-1"></i> {{ $store->opening_time ?? '06:00' }} - {{ $store->closing_time ?? '23:00' }}</span>
          </div>
        </div>

        @if(session('success'))
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
          </div>
        @endif
      </div>
    </div>

    <div class="content">
      <div class="container-fluid">

        <!-- METRICS TILES -->
        <div class="row mb-4">
          <div class="col-lg-3 col-6">
            <div class="small-box bg-info shadow-sm">
              <div class="inner">
                <h3>{{ $totalProductsCount }}</h3>
                <p>Assigned Products</p>
              </div>
              <div class="icon"><i class="fas fa-boxes"></i></div>
              <a href="/manager/inventory" class="small-box-footer">Manage Catalog <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="small-box bg-warning shadow-sm">
              <div class="inner">
                <h3>{{ $pendingOrdersCount }}</h3>
                <p>Pending Orders</p>
              </div>
              <div class="icon"><i class="fas fa-clock"></i></div>
              <a href="/manager/orders" class="small-box-footer">View Orders <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="small-box bg-success shadow-sm">
              <div class="inner">
                <h3>{{ $outForDeliveryCount }}</h3>
                <p>Out For Delivery</p>
              </div>
              <div class="icon"><i class="fas fa-motorcycle"></i></div>
              <a href="/manager/deliveries" class="small-box-footer">Dispatch Hub <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="small-box bg-primary shadow-sm">
              <div class="inner">
                <h3>₹{{ number_format($totalRevenue, 2) }}</h3>
                <p>Delivered Revenue</p>
              </div>
              <div class="icon"><i class="fas fa-rupee-sign"></i></div>
              <a href="/manager/orders" class="small-box-footer">Sales Details <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
        </div>

        <div class="row">
          <!-- RECENT ORDERS WIDGET -->
          <div class="col-md-7">
            <div class="card card-outline card-warning shadow-sm">
              <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h3 class="card-title font-weight-bold text-dark"><i class="fas fa-shopping-bag mr-2 text-warning"></i>Recent Branch Orders</h3>
                <a href="/manager/orders" class="btn btn-xs btn-outline-warning font-weight-bold">View All</a>
              </div>
              <div class="card-body p-0">
                <table class="table table-striped table-sm mb-0">
                  <thead>
                    <tr>
                      <th>Order #</th>
                      <th>Customer Phone</th>
                      <th>Total</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($recentOrders as $ord)
                    <tr>
                      <td class="font-weight-bold align-middle">#{{ $ord->order_number }}</td>
                      <td class="align-middle">{{ $ord->user_phone ?? 'N/A' }}</td>
                      <td class="align-middle text-success font-weight-bold">₹{{ $ord->grand_total }}</td>
                      <td class="align-middle"><span class="badge badge-info">{{ $ord->status }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center py-3 text-muted">No recent orders placed.</td></tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- ACTIVE DISPATCH WIDGET -->
          <div class="col-md-5">
            <div class="card card-outline card-success shadow-sm">
              <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h3 class="card-title font-weight-bold text-dark"><i class="fas fa-motorcycle mr-2 text-success"></i>Active Dispatch</h3>
                <a href="/manager/deliveries" class="btn btn-xs btn-outline-success font-weight-bold">Dispatch Hub</a>
              </div>
              <div class="card-body p-0">
                <table class="table table-striped table-sm mb-0">
                  <thead>
                    <tr>
                      <th>Order #</th>
                      <th>Rider</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($recentDeliveries as $del)
                    <tr>
                      <td class="font-weight-bold align-middle">#{{ $del->order_number }}</td>
                      <td class="align-middle"><strong class="text-primary">{{ $del->rider_name ?? 'Unassigned' }}</strong></td>
                      <td class="align-middle"><span class="badge badge-info">{{ $del->delivery_status }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="text-center py-3 text-muted">No active dispatches.</td></tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>

  <footer class="main-footer"><strong>Copyright &copy; 2026 Q-Commerce Store Manager.</strong></footer>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>
