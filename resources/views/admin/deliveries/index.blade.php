<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Q-Commerce Admin | Delivery Management</title>
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
      <li class="nav-item d-none d-sm-inline-block"><a href="/admin/deliveries" class="nav-link active font-weight-bold">Deliveries</a></li>
    </ul>
  </nav>

  @include('admin.layouts.sidebar')

  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid d-flex justify-content-between align-items-center">
        <h1 class="m-0 font-weight-bold">Delivery Partner & Dispatch Management</h1>
      </div>
    </div>

    <div class="content">
      <div class="container-fluid">
        @if(session('success'))
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
          </div>
        @endif

        <div class="card card-outline card-primary">
          <div class="card-header"><h3 class="card-title font-weight-bold"><i class="fas fa-truck-loading mr-2"></i>Active Deliveries & Assignments</h3></div>
          <div class="card-body p-0">
            <table class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th>#ID</th>
                  <th>Order #</th>
                  <th>Store</th>
                  <th>Delivery Status</th>
                  <th>Assigned Rider</th>
                  <th>Grand Total</th>
                  <th>Assign / Reassign Rider</th>
                </tr>
              </thead>
              <tbody>
                @forelse($deliveries as $del)
                <tr>
                  <td>{{ $del->id }}</td>
                  <td class="font-weight-bold">{{ $del->order_number }}</td>
                  <td>{{ $del->store_name }}</td>
                  <td><span class="badge badge-info">{{ $del->delivery_status }}</span></td>
                  <td><span class="badge badge-secondary">{{ $del->rider_name ?? 'Unassigned' }}</span></td>
                  <td class="text-success font-weight-bold">₹{{ $del->grand_total }}</td>
                  <td>
                    <form action="/admin/deliveries/assign" method="POST" class="form-inline">
                      @csrf
                      <input type="hidden" name="delivery_id" value="{{ $del->id }}">
                      <select name="delivery_partner_id" class="form-control form-control-sm mr-2">
                        <option value="">Select Rider</option>
                        @foreach($riders as $r)
                          <option value="{{ $r->id }}" {{ $del->delivery_partner_id == $r->id ? 'selected' : '' }}>{{ $r->name }} ({{ $r->phone }})</option>
                        @endforeach
                      </select>
                      <button type="submit" class="btn btn-sm btn-primary font-weight-bold">Assign</button>
                    </form>
                  </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-4">No deliveries created yet. Deliveries will populate automatically as orders are placed.</td></tr>
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
