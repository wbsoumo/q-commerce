<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Branch Operations | {{ $store->name ?? 'Branch' }}</title>
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
          <h1 class="m-0 font-weight-bold text-dark"><i class="fas fa-cog text-secondary mr-2"></i>Branch Operations & Settings</h1>
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

        <div class="card card-outline card-secondary shadow-sm mb-4">
          <div class="card-header bg-dark text-white">
            <h3 class="card-title font-weight-bold my-1"><i class="fas fa-sliders-h mr-2"></i>Operational Controls</h3>
          </div>
          <form action="/manager/settings/update" method="POST">
            @csrf
            <div class="card-body">
              <div class="row">
                <div class="col-md-3 form-group">
                  <label class="font-weight-bold">Branch Operating Status</label>
                  <select name="status" class="form-control font-weight-bold">
                    <option value="Active" {{ ($store->status ?? 'Active') === 'Active' ? 'selected' : '' }}>Active (Open)</option>
                    <option value="Inactive" {{ ($store->status ?? '') === 'Inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="Temporarily Closed" {{ ($store->status ?? '') === 'Temporarily Closed' ? 'selected' : '' }}>Temporarily Closed</option>
                  </select>
                </div>
                <div class="col-md-3 form-group">
                  <label class="font-weight-bold">Opening Time</label>
                  <input type="time" name="opening_time" class="form-control" value="{{ $store->opening_time ?? '06:00' }}" required>
                </div>
                <div class="col-md-3 form-group">
                  <label class="font-weight-bold">Closing Time</label>
                  <input type="time" name="closing_time" class="form-control" value="{{ $store->closing_time ?? '23:00' }}" required>
                </div>
                <div class="col-md-3 form-group">
                  <label class="font-weight-bold">Est. Delivery Speed (Mins)</label>
                  <input type="number" name="estimated_delivery_time_mins" class="form-control" value="{{ $store->estimated_delivery_time_mins ?? 15 }}" required>
                </div>
              </div>
              <div class="row">
                <div class="col-md-4 form-group">
                  <label class="font-weight-bold">Delivery Fee (₹)</label>
                  <input type="number" step="0.01" name="delivery_fee" class="form-control" value="{{ $store->delivery_fee ?? 15 }}" required>
                </div>
                <div class="col-md-4 form-group">
                  <label class="font-weight-bold">Free Delivery Threshold (₹)</label>
                  <input type="number" step="0.01" name="free_delivery_threshold" class="form-control" value="{{ $store->free_delivery_threshold ?? 299 }}" required>
                </div>
                <div class="col-md-4 form-group d-flex align-items-end">
                  <button type="submit" class="btn btn-dark btn-block font-weight-bold"><i class="fas fa-save mr-1"></i> Save Controls</button>
                </div>
              </div>
            </div>
          </form>
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
