<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Q-Commerce Admin | Stores & Managers</title>
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
      <li class="nav-item d-none d-sm-inline-block"><a href="/admin/stores" class="nav-link active font-weight-bold">Stores</a></li>
    </ul>
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <a class="btn btn-success btn-sm font-weight-bold" href="/admin/stores/create"><i class="fas fa-plus mr-1"></i> Add New Store & Manager</a>
      </li>
    </ul>
  </nav>

  @include('admin.layouts.sidebar')

  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid d-flex justify-content-between align-items-center">
        <h1 class="m-0 font-weight-bold">Stores & Assigned Managers</h1>
        <a href="/admin/stores/create" class="btn btn-success font-weight-bold"><i class="fas fa-plus mr-1"></i> Create Store & Manager</a>
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

        <div class="card card-outline card-success">
          <div class="card-header"><h3 class="card-title font-weight-bold">Active Store Network</h3></div>
          <div class="card-body p-0">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>#ID</th>
                  <th>Store Code</th>
                  <th>Store Name</th>
                  <th>City</th>
                  <th>Assigned Manager</th>
                  <th>Manager Email</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @forelse($stores as $st)
                <tr>
                  <td>{{ $st->id }}</td>
                  <td><span class="badge badge-secondary">{{ $st->code }}</span></td>
                  <td class="font-weight-bold">{{ $st->name }}</td>
                  <td>{{ $st->city }} ({{ $st->pincode }})</td>
                  <td><span class="badge badge-info"><i class="fas fa-user-shield mr-1"></i>{{ $st->manager_name ?? 'Unassigned' }}</span></td>
                  <td>{{ $st->manager_email ?? 'N/A' }}</td>
                  <td><span class="badge badge-success">Active</span></td>
                  <td>
                    <a href="/admin/stores/{{ $st->id }}/settings" class="btn btn-warning btn-xs font-weight-bold mr-1">
                      <i class="fas fa-cog mr-1"></i> Settings
                    </a>
                    <a href="/admin/store-manager?store_id={{ $st->id }}" class="btn btn-primary btn-xs font-weight-bold">
                      <i class="fas fa-edit mr-1"></i> Manager Portal
                    </a>
                  </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center py-4">No stores found. <a href="/admin/stores/create">Click here to create a store.</a></td></tr>
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
