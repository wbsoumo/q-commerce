<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Q-Commerce Admin | Stores & Assigned Managers</title>
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
            <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
          </div>
        @endif

        @if(session('error'))
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle mr-1"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
          </div>
        @endif

        <div class="card card-outline card-success shadow-sm">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title font-weight-bold"><i class="fas fa-store-alt mr-2 text-success"></i>Active Store Network ({{ count($stores) }})</h3>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-striped table-hover mb-0">
                <thead>
                  <tr>
                    <th>#ID</th>
                    <th>Store Code</th>
                    <th>Store Name</th>
                    <th>City & Address</th>
                    <th>Assigned Manager</th>
                    <th>Manager Email</th>
                    <th>Status</th>
                    <th class="text-right">Action</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($stores as $st)
                  <tr>
                    <td>{{ $st->id }}</td>
                    <td><span class="badge badge-secondary font-weight-bold">{{ $st->code }}</span></td>
                    <td class="font-weight-bold text-dark">{{ $st->name }}</td>
                    <td>
                      <span class="d-block">{{ $st->city }} ({{ $st->pincode }})</span>
                      <small class="text-muted"><i class="fas fa-map-marker-alt mr-1"></i> {{ Str::limit($st->address ?? 'Location', 30) }}</small>
                    </td>
                    <td>
                      <span class="badge badge-info"><i class="fas fa-user-shield mr-1"></i>{{ $st->manager_name ?? 'Unassigned' }}</span>
                    </td>
                    <td>{{ $st->manager_email ?? 'N/A' }}</td>
                    <td>
                      <span class="badge badge-{{ ($st->status ?? 'Active') === 'Active' ? 'success' : 'danger' }}">
                        {{ $st->status ?? 'Active' }}
                      </span>
                    </td>
                    <td class="text-right">
                      <!-- ACTION DROPDOWN BUTTON -->
                      <div class="btn-group">
                        <button type="button" class="btn btn-default btn-sm dropdown-toggle font-weight-bold" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                          <i class="fas fa-cog mr-1 text-secondary"></i> Action
                        </button>
                        <div class="dropdown-menu dropdown-menu-right shadow">
                          <a class="dropdown-item text-primary" href="/admin/store-manager?store_id={{ $st->id }}">
                            <i class="fas fa-edit mr-2"></i> Open Manager Portal
                          </a>
                          <a class="dropdown-item text-warning" href="/admin/stores/{{ $st->id }}/settings">
                            <i class="fas fa-sliders-h mr-2"></i> Branch Settings & Controls
                          </a>
                          <div class="dropdown-divider"></div>
                          <button type="button" class="dropdown-item text-danger font-weight-bold" onclick="confirmDeleteStore({{ $st->id }}, '{{ addslashes($st->name) }}')">
                            <i class="fas fa-trash-alt mr-2"></i> Delete Store Branch
                          </button>
                        </div>
                      </div>
                    </td>
                  </tr>
                  @empty
                  <tr>
                    <td colspan="8" class="text-center py-4 text-muted">
                      No stores found. <a href="/admin/stores/create" class="font-weight-bold">Click here to create a store.</a>
                    </td>
                  </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <footer class="main-footer"><strong>Copyright &copy; 2026 Q-Commerce Admin.</strong></footer>
</div>

<!-- HIDDEN DELETE FORM -->
<form id="deleteStoreForm" action="" method="POST" style="display: none;">
  @csrf
</form>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<script>
  function confirmDeleteStore(storeId, storeName) {
    if (confirm("Are you sure you want to permanently delete store branch '" + storeName + "'?")) {
      var form = document.getElementById('deleteStoreForm');
      form.action = '/admin/stores/' + storeId + '/delete';
      form.submit();
    }
  }
</script>
</body>
</html>
