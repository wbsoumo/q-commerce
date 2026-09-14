<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Q-Commerce Admin | Staff Management</title>
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
      <li class="nav-item d-none d-sm-inline-block"><a href="/admin/staff" class="nav-link active font-weight-bold">Staff Members</a></li>
    </ul>
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <button type="button" class="btn btn-success btn-sm font-weight-bold" data-toggle="modal" data-target="#staffModal">
          <i class="fas fa-user-plus mr-1"></i> Add New Staff Member
        </button>
      </li>
    </ul>
  </nav>

  @include('admin.layouts.sidebar')

  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid d-flex justify-content-between align-items-center">
        <h1 class="m-0 font-weight-bold"><i class="fas fa-users-cog mr-2"></i>Staff & Multi-Role Personnel Management</h1>
        <button type="button" class="btn btn-success font-weight-bold" data-toggle="modal" data-target="#staffModal">
          <i class="fas fa-user-plus mr-1"></i> Add Staff
        </button>
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

        <div class="card card-outline card-primary shadow-sm">
          <div class="card-header bg-primary text-white"><h3 class="card-title font-weight-bold">Active Staff Personnel</h3></div>
          <div class="card-body p-0">
            <table class="table table-striped table-bordered mb-0">
              <thead>
                <tr>
                  <th>#ID</th>
                  <th>Staff Name</th>
                  <th>Contact Info</th>
                  <th>Assigned Store</th>
                  <th>Assigned Roles (Multiple)</th>
                  <th>Status</th>
                  <th>Joining Date</th>
                </tr>
              </thead>
              <tbody>
                @forelse($staff as $st)
                <tr>
                  <td>{{ $st->id }}</td>
                  <td class="font-weight-bold">{{ $st->name }}</td>
                  <td>
                    <div><i class="fas fa-phone mr-1 text-muted"></i> {{ $st->phone }}</div>
                    @if($st->email) <small class="text-muted"><i class="fas fa-envelope mr-1"></i> {{ $st->email }}</small> @endif
                  </td>
                  <td><span class="badge badge-info">{{ $st->store_name ?? 'All Stores' }}</span></td>
                  <td>
                    @php
                      $roles = is_array($st->roles) ? $st->roles : json_decode($st->roles, true);
                    @endphp
                    @if(is_array($roles))
                      @foreach($roles as $r)
                        @php
                          $badgeColor = match($r) {
                            'store_staff' => 'primary',
                            'delivery_staff' => 'success',
                            'cleaning_staff' => 'secondary',
                            'inventory_staff' => 'info',
                            'billing_staff' => 'warning',
                            'security_staff' => 'dark',
                            default => 'light'
                          };
                          $roleName = match($r) {
                            'store_staff' => 'Store Staff',
                            'delivery_staff' => 'Delivery Staff',
                            'cleaning_staff' => 'Cleaning Staff',
                            'inventory_staff' => 'Inventory Staff',
                            'billing_staff' => 'Billing Staff',
                            'security_staff' => 'Security Staff',
                            default => $r
                          };
                        @endphp
                        <span class="badge badge-{{ $badgeColor }} mr-1 p-1">{{ $roleName }}</span>
                      @endforeach
                    @endif
                  </td>
                  <td><span class="badge badge-success">{{ $st->status }}</span></td>
                  <td>{{ $st->created_at }}</td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-4">No staff members created yet. <button type="button" class="btn btn-link" data-toggle="modal" data-target="#staffModal">Click to Add Staff</button></td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- CREATE STAFF MODAL WITH MULTI-ROLE SELECTION -->
  <div class="modal fade" id="staffModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <form action="/admin/staff/store" method="POST">
          @csrf
          <div class="modal-header bg-success text-white">
            <h5 class="modal-title font-weight-bold"><i class="fas fa-user-plus mr-1"></i> Register Staff Member & Assign Roles</h5>
            <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label>Staff Member Name</label>
              <input type="text" name="name" class="form-control" placeholder="e.g. Rajesh Sharma" required>
            </div>
            <div class="row">
              <div class="col-md-6 form-group">
                <label>Phone Number</label>
                <input type="text" name="phone" class="form-control" placeholder="9876543210" required>
              </div>
              <div class="col-md-6 form-group">
                <label>Email Address</label>
                <input type="email" name="email" class="form-control" placeholder="staff@store.com">
              </div>
            </div>
            <div class="form-group">
              <label>Assigned Store Branch</label>
              <select name="store_id" class="form-control" required>
                @foreach($stores as $st)
                  <option value="{{ $st->id }}">{{ $st->name }} ({{ $st->city }})</option>
                @endforeach
              </select>
            </div>

            <!-- MULTIPLE ROLES CHECKBOX SELECTOR -->
            <div class="form-group bg-light p-3 border rounded">
              <label class="font-weight-bold text-success"><i class="fas fa-user-tag mr-1"></i> Assign Roles (Multiple Selectable):</label>
              <div class="row">
                <div class="col-6">
                  <div class="custom-control custom-checkbox mb-2">
                    <input class="custom-control-input" type="checkbox" id="role_store" name="roles[]" value="store_staff" checked>
                    <label for="role_store" class="custom-control-label">Store Staff</label>
                  </div>
                  <div class="custom-control custom-checkbox mb-2">
                    <input class="custom-control-input" type="checkbox" id="role_delivery" name="roles[]" value="delivery_staff" checked>
                    <label for="role_delivery" class="custom-control-label text-primary font-weight-bold">Delivery Staff</label>
                  </div>
                  <div class="custom-control custom-checkbox mb-2">
                    <input class="custom-control-input" type="checkbox" id="role_cleaning" name="roles[]" value="cleaning_staff">
                    <label for="role_cleaning" class="custom-control-label">Cleaning Staff</label>
                  </div>
                </div>
                <div class="col-6">
                  <div class="custom-control custom-checkbox mb-2">
                    <input class="custom-control-input" type="checkbox" id="role_inventory" name="roles[]" value="inventory_staff">
                    <label for="role_inventory" class="custom-control-label">Inventory Staff</label>
                  </div>
                  <div class="custom-control custom-checkbox mb-2">
                    <input class="custom-control-input" type="checkbox" id="role_billing" name="roles[]" value="billing_staff">
                    <label for="role_billing" class="custom-control-label">Billing Staff</label>
                  </div>
                  <div class="custom-control custom-checkbox mb-2">
                    <input class="custom-control-input" type="checkbox" id="role_security" name="roles[]" value="security_staff">
                    <label for="role_security" class="custom-control-label">Security Staff</label>
                  </div>
                </div>
              </div>
              <small class="form-text text-muted">Checking 'Delivery Staff' automatically registers the personnel for order delivery dispatch assignments.</small>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-success font-weight-bold">Save Staff Member</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <footer class="main-footer"><strong>Copyright &copy; 2026 Q-Commerce Admin.</strong></footer>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>
