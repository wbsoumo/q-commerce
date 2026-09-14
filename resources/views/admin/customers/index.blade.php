<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Q-Commerce Admin | Customer Directory</title>
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
      <li class="nav-item d-none d-sm-inline-block"><a href="/admin/customers" class="nav-link active font-weight-bold">Customers</a></li>
    </ul>
  </nav>

  @include('admin.layouts.sidebar')

  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid d-flex justify-content-between align-items-center">
        <h1 class="m-0 font-weight-bold">Customer Management & Insights</h1>
      </div>
    </div>

    <div class="content">
      <div class="container-fluid">
        <div class="card card-outline card-info">
          <div class="card-header"><h3 class="card-title font-weight-bold"><i class="fas fa-users mr-2"></i>Registered Customer Profiles</h3></div>
          <div class="card-body p-0">
            <table class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th>#ID</th>
                  <th>Customer Name</th>
                  <th>Phone Number</th>
                  <th>Status</th>
                  <th>VIP Flag</th>
                  <th>Total Orders</th>
                  <th>Total Spent</th>
                  <th>Last Order</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @forelse($customers as $c)
                <tr>
                  <td>{{ $c->id }}</td>
                  <td class="font-weight-bold">{{ $c->name }}</td>
                  <td>{{ $c->phone }}</td>
                  <td>
                    <span class="badge badge-{{ $c->status === 'Active' ? 'success' : 'danger' }}">{{ $c->status }}</span>
                  </td>
                  <td>
                    @if($c->is_vip)
                      <span class="badge badge-warning text-dark"><i class="fas fa-crown mr-1"></i> VIP</span>
                    @else
                      <span class="badge badge-light">Regular</span>
                    @endif
                  </td>
                  <td><span class="badge badge-info">{{ $c->total_orders }} orders</span></td>
                  <td class="text-success font-weight-bold">₹{{ $c->total_spent }}</td>
                  <td>{{ $c->last_order_at ?? 'N/A' }}</td>
                  <td>
                    <a href="/admin/customers/{{ $c->id }}" class="btn btn-sm btn-info font-weight-bold"><i class="fas fa-eye mr-1"></i> Details</a>
                  </td>
                </tr>
                @empty
                <tr><td colspan="9" class="text-center py-4">No customer profiles found.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
          <div class="card-footer clearfix">
            {{ $customers->links('pagination::bootstrap-4') }}
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
