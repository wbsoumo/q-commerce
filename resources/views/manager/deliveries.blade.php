<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Delivery Dispatch | {{ $store->name ?? 'Branch' }}</title>
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
      <div class="container-fluid d-flex justify-content-between align-items-center">
        <div>
          <h1 class="m-0 font-weight-bold text-dark"><i class="fas fa-motorcycle text-success mr-2"></i>Delivery Dispatch</h1>
          <p class="text-muted mb-0"><i class="fas fa-store text-primary mr-1"></i> {{ $store->name }}</p>
        </div>
      </div>
    </div>

    <div class="content">
      <div class="container-fluid">
        @if(session('success'))
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
          </div>
        @endif

        <div class="card card-outline card-success shadow-sm mb-4">
          <div class="card-header bg-success text-white">
            <h3 class="card-title font-weight-bold my-1"><i class="fas fa-truck mr-2"></i>Delivery Rider Assignments</h3>
          </div>
          <div class="card-body p-0">
            <table class="table table-striped table-bordered mb-0">
              <thead class="bg-light">
                <tr>
                  <th>Order #</th>
                  <th>Order Status</th>
                  <th>Delivery Status</th>
                  <th>Assigned Rider</th>
                  <th>Assign / Change Rider</th>
                </tr>
              </thead>
              <tbody>
                @forelse($deliveries as $del)
                <tr>
                  <td class="font-weight-bold align-middle">#{{ $del->order_number }}</td>
                  <td class="align-middle"><span class="badge badge-secondary">{{ $del->order_status }}</span></td>
                  <td class="align-middle"><span class="badge badge-info">{{ $del->delivery_status }}</span></td>
                  <td class="align-middle"><strong class="text-primary">{{ $del->rider_name ?? 'Unassigned' }}</strong></td>
                  <td class="align-middle">
                    <form action="/manager/deliveries/assign" method="POST" class="form-inline">
                      @csrf
                      <input type="hidden" name="delivery_id" value="{{ $del->id }}">
                      <select name="delivery_partner_id" class="form-control form-control-sm mr-2" required>
                        <option value="">Select Rider</option>
                        @foreach($riders as $r)
                          <option value="{{ $r->id }}" {{ $del->delivery_partner_id == $r->id ? 'selected' : '' }}>{{ $r->name }} ({{ $r->phone }})</option>
                        @endforeach
                      </select>
                      <button type="submit" class="btn btn-success btn-sm font-weight-bold">Assign Rider</button>
                    </form>
                  </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center py-4 text-muted">No deliveries pending for dispatch.</td></tr>
                @endforelse
              </tbody>
            </table>
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
