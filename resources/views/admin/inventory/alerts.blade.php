<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Q-Commerce Admin | Inventory Threshold Alerts</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
      <li class="nav-item"><a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a></li>
      <li class="nav-item d-none d-sm-inline-block"><a href="/admin" class="nav-link">Dashboard</a></li>
      <li class="nav-item d-none d-sm-inline-block"><a href="/admin/inventory/alerts" class="nav-link active font-weight-bold">Stock Alerts</a></li>
    </ul>
  </nav>

  @include('admin.layouts.sidebar')

  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <h1 class="m-0 font-weight-bold text-danger"><i class="fas fa-exclamation-triangle mr-2"></i>Action Required: Inventory Threshold Alerts</h1>
      </div>
    </div>

    <div class="content">
      <div class="container-fluid">
        <div class="card card-outline card-danger">
          <div class="card-header"><h3 class="card-title font-weight-bold">Low, Critical & Out-of-Stock Notifications</h3></div>
          <div class="card-body p-0">
            <table class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th>#ID</th>
                  <th>Product Name</th>
                  <th>Store Branch</th>
                  <th>Alert Level</th>
                  <th>Current Stock</th>
                  <th>Threshold</th>
                  <th>Timestamp</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @forelse($alerts as $alt)
                <tr>
                  <td>{{ $alt->id }}</td>
                  <td class="font-weight-bold">{{ $alt->product_name }}</td>
                  <td>{{ $alt->store_name ?? 'Global' }}</td>
                  <td>
                    @if($alt->alert_type === 'out_of_stock')
                      <span class="badge badge-danger p-2"><i class="fas fa-ban mr-1"></i> Out of Stock</span>
                    @elseif($alt->alert_type === 'critical_stock')
                      <span class="badge badge-warning p-2"><i class="fas fa-exclamation-circle mr-1"></i> Critical Stock (<=5)</span>
                    @else
                      <span class="badge badge-info p-2"><i class="fas fa-info-circle mr-1"></i> Low Stock (<=10)</span>
                    @endif
                  </td>
                  <td class="font-weight-bold text-danger" style="font-size: 1.1rem;">{{ $alt->current_stock }} pcs</td>
                  <td>{{ $alt->threshold }} pcs</td>
                  <td>{{ $alt->created_at }}</td>
                  <td>
                    <a href="/admin/inventory/transactions" class="btn btn-sm btn-success font-weight-bold"><i class="fas fa-plus mr-1"></i> Restock Now</a>
                  </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center py-4 text-success font-weight-bold"><i class="fas fa-check-circle mr-1"></i> All stock levels are healthy! No active inventory alerts.</td></tr>
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
