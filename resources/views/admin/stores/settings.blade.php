<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Q-Commerce Admin | Store Operational Settings</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
      <li class="nav-item"><a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a></li>
      <li class="nav-item d-none d-sm-inline-block"><a href="/admin/stores" class="nav-link">Stores</a></li>
      <li class="nav-item d-none d-sm-inline-block"><a href="#" class="nav-link active font-weight-bold">Store Operational Settings</a></li>
    </ul>
  </nav>

  @include('admin.layouts.sidebar')

  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <h1 class="m-0 font-weight-bold">Manage Store Operating Hours & Delivery Thresholds</h1>
        <p class="text-muted">Editing: <strong>{{ $store->name }} ({{ $store->code }})</strong></p>
      </div>
    </div>

    <div class="content">
      <div class="container-fluid">
        <div class="card card-success card-outline">
          <div class="card-header"><h3 class="card-title font-weight-bold"><i class="fas fa-clock mr-2"></i>Store Operational Controls</h3></div>
          <form action="/admin/stores/{{ $store->id }}/settings" method="POST">
            @csrf
            <div class="card-body">
              <div class="row">
                <div class="col-md-4 form-group">
                  <label>Store Status</label>
                  <select name="status" class="form-control font-weight-bold">
                    <option value="Active" {{ ($store->status ?? 'Active') === 'Active' ? 'selected' : '' }}>Active</option>
                    <option value="Inactive" {{ ($store->status ?? '') === 'Inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="Temporarily Closed" {{ ($store->status ?? '') === 'Temporarily Closed' ? 'selected' : '' }}>Temporarily Closed</option>
                    <option value="Maintenance" {{ ($store->status ?? '') === 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
                  </select>
                </div>
                <div class="col-md-4 form-group">
                  <label>Opening Time</label>
                  <input type="time" name="opening_time" class="form-control" value="{{ $store->opening_time ?? '06:00' }}" required>
                </div>
                <div class="col-md-4 form-group">
                  <label>Closing Time</label>
                  <input type="time" name="closing_time" class="form-control" value="{{ $store->closing_time ?? '23:00' }}" required>
                </div>
              </div>

              <div class="row bg-light p-3 rounded mb-3 border">
                <div class="col-md-6 form-group">
                  <label class="text-danger font-weight-bold"><i class="fas fa-umbrella-beach mr-1"></i> Vacation Mode / Temporary Closure</label>
                  <div class="custom-control custom-switch mt-1">
                    <input type="checkbox" class="custom-control-input" id="vacationSwitch" name="vacation_mode" {{ !empty($store->vacation_mode) ? 'checked' : '' }}>
                    <label class="custom-control-label" for="vacationSwitch">Enable Vacation Mode (Close store for all orders)</label>
                  </div>
                </div>
                <div class="col-md-6 form-group">
                  <label>Temporary Closure Reason</label>
                  <input type="text" name="temporary_closure_reason" class="form-control" value="{{ $store->temporary_closure_reason ?? '' }}" placeholder="e.g. Closed for holiday renovation">
                </div>
              </div>

              <div class="row bg-light p-3 rounded mb-3 border">
                <div class="col-md-6 form-group">
                  <label class="text-success font-weight-bold"><i class="fas fa-tags mr-1"></i> Mega Sale / Front Page Banner Title</label>
                  <input type="text" name="banner_title" class="form-control font-weight-bold" value="{{ $store->banner_title ?? 'Mega Diwali Sale' }}" placeholder="e.g. Mega Diwali Sale or Festival Offer">
                  <small class="form-text text-muted">This title is dynamically displayed on top of the front page banner in the Flutter app.</small>
                </div>
                <div class="col-md-6 form-group">
                  <label class="text-primary font-weight-bold"><i class="fas fa-info-circle mr-1"></i> Banner Subtitle / Announcement</label>
                  <input type="text" name="banner_subtitle" class="form-control" value="{{ $store->banner_subtitle ?? 'Upto 50% Off' }}" placeholder="e.g. Upto 50% Off on all items">
                </div>
              </div>

              <div class="row">
                <div class="col-md-4 form-group">
                  <label class="font-weight-bold text-success"><i class="fas fa-compass mr-1"></i> Delivery Radius (km)</label>
                  <div class="input-group">
                    <input type="number" step="0.5" min="1" max="50" name="delivery_radius_km" class="form-control font-weight-bold" value="{{ $store->delivery_radius_km ?? 5.0 }}" required>
                    <div class="input-group-append">
                      <span class="input-group-text font-weight-bold">KM</span>
                    </div>
                  </div>
                  <small class="form-text text-muted">Maximum distance in KM this store can serve.</small>
                </div>
                <div class="col-md-4 form-group">
                  <label>Min. Order Amount (₹)</label>
                  <input type="number" step="0.01" name="min_order_amount" class="form-control" value="{{ $store->min_order_amount ?? 0 }}" required>
                </div>
                <div class="col-md-4 form-group">
                  <label>Standard Delivery Fee (₹)</label>
                  <input type="number" step="0.01" name="delivery_fee" class="form-control" value="{{ $store->delivery_fee ?? 15 }}" required>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 form-group">
                  <label>Free Delivery Threshold (₹)</label>
                  <input type="number" step="0.01" name="free_delivery_threshold" class="form-control" value="{{ $store->free_delivery_threshold ?? 299 }}" required>
                </div>
                <div class="col-md-6 form-group">
                  <label>Est. Delivery Time (Mins)</label>
                  <input type="number" name="estimated_delivery_time_mins" class="form-control" value="{{ $store->estimated_delivery_time_mins ?? 15 }}" required>
                </div>
              </div>
            </div>
            <div class="card-footer">
              <button type="submit" class="btn btn-success font-weight-bold"><i class="fas fa-save mr-1"></i> Save Store Settings</button>
              <a href="/admin/stores" class="btn btn-default">Cancel</a>
            </div>
          </form>
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
