<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Q-Commerce Admin | Multi-Vendor Dashboard</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  <style>
    .brand-link { background-color: #0c831f !important; }
    .btn-success { background-color: #0c831f; border-color: #0c831f; }
  </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
      <li class="nav-item"><a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a></li>
      <li class="nav-item d-none d-sm-inline-block"><a href="/admin" class="nav-link font-weight-bold">Dashboard</a></li>
      <li class="nav-item d-none d-sm-inline-block"><a href="/admin/stores" class="nav-link">Stores</a></li>
      <li class="nav-item d-none d-sm-inline-block"><a href="/admin/products" class="nav-link">Products</a></li>
      <li class="nav-item d-none d-sm-inline-block"><a href="/admin/store-manager" class="nav-link text-primary font-weight-bold"><i class="fas fa-store mr-1"></i>Store Manager Portal</a></li>
    </ul>
    <ul class="navbar-nav ml-auto">
    </ul>
  </nav>

  <!-- Main Sidebar -->
  @include('admin.layouts.sidebar')

  <!-- Content Wrapper -->
  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 font-weight-bold text-dark">Multi-Store Control Panel</h1>
          </div>
        </div>
      </div>
    </div>

    <!-- Main content -->
    <div class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
              <div class="inner"><h3>{{ $totalStores ?? 0 }}</h3><p>Active Stores</p></div>
              <div class="icon"><i class="fas fa-store"></i></div>
              <a href="/admin/stores" class="small-box-footer">Manage Stores <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
              <div class="inner"><h3>{{ $totalProducts ?? 0 }}</h3><p>Total Products</p></div>
              <div class="icon"><i class="fas fa-box"></i></div>
              <a href="/admin/products" class="small-box-footer">Manage Products <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
              <div class="inner"><h3>{{ $totalCategories ?? 0 }}</h3><p>Categories</p></div>
              <div class="icon"><i class="fas fa-th-large"></i></div>
              <a href="/admin/products" class="small-box-footer">View Categories <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
              <div class="inner"><h3>{{ $totalOrders ?? 0 }}</h3><p>Total Orders</p></div>
              <div class="icon"><i class="fas fa-shopping-cart"></i></div>
              <a href="#" class="small-box-footer">View Orders <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
        </div>

        <!-- Quick Store Manager Switcher -->
        <div class="card card-outline card-primary">
          <div class="card-header">
            <h3 class="card-title font-weight-bold"><i class="fas fa-user-cog mr-2"></i>Switch Store Manager Portal View</h3>
          </div>
          <div class="card-body">
            <p>Select a store to view or edit inventory as a Store Manager:</p>
            <form action="/admin/store-manager" method="GET" class="form-inline">
              <select name="store_id" class="form-control mr-2 mb-2" style="min-width: 250px;">
                @foreach($stores as $st)
                  <option value="{{ $st->id }}">{{ $st->name }} ({{ $st->city }})</option>
                @endforeach
              </select>
              <button type="submit" class="btn btn-primary mb-2 font-weight-bold"><i class="fas fa-external-link-alt mr-1"></i> Open Store Manager Portal</button>
            </form>
          </div>
        </div>

        <!-- Recent Orders Table -->
        <div class="card card-outline card-success">
          <div class="card-header">
            <h3 class="card-title font-weight-bold"><i class="fas fa-list mr-2"></i>Recent Multi-Store Customer Orders</h3>
          </div>
          <div class="card-body p-0">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>Order #</th>
                  <th>Store</th>
                  <th>Customer</th>
                  <th>Phone</th>
                  <th>Total</th>
                  <th>Payment</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                @forelse($recentOrders as $ord)
                <tr>
                  <td class="font-weight-bold">{{ $ord->order_number }}</td>
                  <td><span class="badge badge-info">{{ $ord->store_name ?? 'Krishnanagar Main' }}</span></td>
                  <td>{{ $ord->user_name }}</td>
                  <td>{{ $ord->user_phone }}</td>
                  <td class="text-success font-weight-bold">₹{{ $ord->grand_total }}</td>
                  <td>{{ $ord->payment_method }}</td>
                  <td><span class="badge badge-warning">{{ $ord->status }}</span></td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-3">No orders yet.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <footer class="main-footer"><strong>Copyright &copy; 2026 Q-Commerce Admin.</strong></footer>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>
