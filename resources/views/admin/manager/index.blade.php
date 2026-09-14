<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Store Manager Portal | {{ $store->name ?? 'Store Management' }}</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
      <li class="nav-item"><a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a></li>
      <li class="nav-item d-none d-sm-inline-block"><a href="/admin" class="nav-link font-weight-bold">Main Admin</a></li>
      <li class="nav-item d-none d-sm-inline-block"><a href="#" class="nav-link active font-weight-bold">Store Manager Portal</a></li>
    </ul>
  </nav>

  @include('admin.layouts.sidebar')

  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div>
            <h1 class="m-0 font-weight-bold text-primary"><i class="fas fa-store mr-2"></i>Store Manager Portal</h1>
            <p class="text-muted mb-0">Branch Control & Live Inventory Management</p>
          </div>
        </div>

        <!-- 1. STORE SELECTION BANNER -->
        <div class="card card-outline card-primary bg-light mb-4 shadow-sm">
          <div class="card-body p-3">
            <form action="/admin/store-manager" method="GET" class="row align-items-center">
              <div class="col-md-5">
                <label class="font-weight-bold text-dark mb-1"><i class="fas fa-building mr-1 text-primary"></i> Select Store Branch to Manage:</label>
                <select name="store_id" class="form-control form-control-lg font-weight-bold border-primary" onchange="this.form.submit()">
                  @foreach($stores as $st)
                    <option value="{{ $st->id }}" {{ ($store->id ?? 1) == $st->id ? 'selected' : '' }}>
                      {{ $st->name }} ({{ $st->city }} - {{ $st->code }})
                    </option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-7 d-flex justify-content-end align-items-center mt-3 mt-md-0">
                <div class="mr-4 text-right">
                  <small class="text-uppercase text-muted font-weight-bold d-block">Store Operating Status</small>
                  <span class="badge badge-{{ ($store->status ?? 'Active') === 'Active' ? 'success' : 'danger' }} px-3 py-2" style="font-size: 0.95rem;">
                    <i class="fas fa-circle mr-1" style="font-size: 0.65rem;"></i> {{ $store->status ?? 'Active' }}
                  </span>
                </div>
                <div class="text-right">
                  <small class="text-uppercase text-muted font-weight-bold d-block">Hours</small>
                  <strong class="text-dark"><i class="far fa-clock mr-1 text-primary"></i> {{ $store->opening_time ?? '06:00' }} - {{ $store->closing_time ?? '23:00' }}</strong>
                </div>
              </div>
            </form>
          </div>
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

        <!-- 2. QUICK STORE STATS & QUICK EDIT CONTROLS -->
        <div class="row mb-4">
          <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
              <div class="inner">
                <h3>{{ count($products) }}</h3>
                <p>Assigned Products</p>
              </div>
              <div class="icon"><i class="fas fa-boxes"></i></div>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
              <div class="inner">
                <h3>₹{{ $store->delivery_fee ?? 15.00 }}</h3>
                <p>Branch Delivery Fee</p>
              </div>
              <div class="icon"><i class="fas fa-motorcycle"></i></div>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
              <div class="inner">
                <h3>₹{{ $store->free_delivery_threshold ?? 299.00 }}</h3>
                <p>Free Delivery Above</p>
              </div>
              <div class="icon"><i class="fas fa-gift"></i></div>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
              <div class="inner">
                <h3>{{ $store->estimated_delivery_time_mins ?? 15 }} mins</h3>
                <p>Est. Delivery Speed</p>
              </div>
              <div class="icon"><i class="fas fa-bolt"></i></div>
            </div>
          </div>
        </div>

        <!-- 3. STORE OPERATIONAL EDIT CARD (COLLAPSED BY DEFAULT) -->
        <div class="card card-outline card-info mb-4 collapsed-card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title font-weight-bold" style="cursor: pointer;" data-card-widget="collapse">
              <i class="fas fa-cog mr-2 text-info"></i>Branch Quick Settings & Operational Controls <small class="text-muted ml-2">(Click to Expand)</small>
            </h3>
            <div class="card-tools">
              <button type="button" class="btn btn-tool text-info" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
            </div>
          </div>
          <form action="/admin/stores/{{ $store->id }}/settings" method="POST">
            @csrf
            <div class="card-body">
              <div class="row">
                <div class="col-md-3 form-group">
                  <label class="font-weight-bold">Branch Operating Status</label>
                  <select name="status" class="form-control font-weight-bold">
                    <option value="Active" {{ ($store->status ?? 'Active') === 'Active' ? 'selected' : '' }}>Active (Open)</option>
                    <option value="Inactive" {{ ($store->status ?? '') === 'Inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="Temporarily Closed" {{ ($store->status ?? '') === 'Temporarily Closed' ? 'selected' : '' }}>Temporarily Closed</option>
                    <option value="Maintenance" {{ ($store->status ?? '') === 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
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
                  <label class="font-weight-bold">Min. Order Amount (₹)</label>
                  <input type="number" step="0.01" name="min_order_amount" class="form-control" value="{{ $store->min_order_amount ?? 0 }}" required>
                </div>
              </div>
              <div class="row">
                <div class="col-md-3 form-group">
                  <label class="font-weight-bold">Delivery Fee (₹)</label>
                  <input type="number" step="0.01" name="delivery_fee" class="form-control" value="{{ $store->delivery_fee ?? 15 }}" required>
                </div>
                <div class="col-md-3 form-group">
                  <label class="font-weight-bold">Free Delivery Threshold (₹)</label>
                  <input type="number" step="0.01" name="free_delivery_threshold" class="form-control" value="{{ $store->free_delivery_threshold ?? 299 }}" required>
                </div>
                <div class="col-md-3 form-group">
                  <label class="font-weight-bold">Est. Delivery Time (Mins)</label>
                  <input type="number" name="estimated_delivery_time_mins" class="form-control" value="{{ $store->estimated_delivery_time_mins ?? 15 }}" required>
                </div>
                <div class="col-md-3 form-group d-flex align-items-end">
                  <button type="submit" class="btn btn-info btn-block font-weight-bold"><i class="fas fa-save mr-1"></i> Update Store Controls</button>
                </div>
              </div>
            </div>
          </form>
        </div>

        <!-- PROMINENT PRODUCT SEARCH BAR -->
        <div class="card card-outline card-warning mb-3 shadow-sm">
          <div class="card-body py-2">
            <form action="/admin/store-manager" method="GET" class="row align-items-center">
              <input type="hidden" name="store_id" value="{{ $store->id ?? 1 }}">
              <div class="col-md-9">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text bg-warning text-dark font-weight-bold"><i class="fas fa-search mr-1"></i> Search Products</span>
                  </div>
                  <input type="text" name="search" class="form-control form-control-lg font-weight-bold" placeholder="Type Product Name or SKU code to filter..." value="{{ request('search') }}">
                </div>
              </div>
              <div class="col-md-3 mt-2 mt-md-0 text-right">
                <button type="submit" class="btn btn-warning btn-lg font-weight-bold"><i class="fas fa-search mr-1"></i> Search Catalog</button>
                @if(request('search'))
                  <a href="/admin/store-manager?store_id={{ $store->id ?? 1 }}" class="btn btn-default btn-lg font-weight-bold ml-1"><i class="fas fa-undo mr-1"></i> Clear</a>
                @endif
              </div>
            </form>
          </div>
        </div>

        <!-- 4. PRODUCT INVENTORY OVERRIDES TABLE -->
        <div class="card card-outline card-primary shadow-sm">
          <div class="card-header bg-primary text-white py-2">
            <h3 class="card-title font-weight-bold my-1"><i class="fas fa-boxes mr-2"></i>Product Price & Stock Override Manager</h3>
          </div>
          <div class="card-body p-0">
            <table class="table table-striped table-bordered mb-0">
              <thead class="bg-light">
                <tr>
                  <th>Product Name</th>
                  <th>Scope</th>
                  <th>Global Base Price / Stock</th>
                  <th>Branch Custom Price (₹)</th>
                  <th>Branch Custom Stock</th>
                  <th>Stock Availability</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @forelse($products as $prod)
                <tr>
                  <td class="font-weight-bold align-middle">{{ $prod->name }}</td>
                  <td class="align-middle">
                    @if($prod->scope === 'global')
                      <span class="badge badge-success"><i class="fas fa-globe mr-1"></i> Global</span>
                    @else
                      <span class="badge badge-primary"><i class="fas fa-store mr-1"></i> Store Specific</span>
                    @endif
                  </td>
                  <td class="align-middle">₹{{ $prod->price }} <small class="text-muted">({{ $prod->stock }} pcs)</small></td>
                  <form action="/admin/store-manager/update-inventory" method="POST">
                    @csrf
                    <input type="hidden" name="store_id" value="{{ $store->id ?? 1 }}">
                    <input type="hidden" name="product_id" value="{{ $prod->id }}">
                    <td class="align-middle">
                      <div class="input-group input-group-sm" style="width: 120px;">
                        <div class="input-group-prepend"><span class="input-group-text">₹</span></div>
                        <input type="number" step="0.01" name="custom_price" class="form-control font-weight-bold text-success" value="{{ $prod->custom_price ?? $prod->price }}">
                      </div>
                    </td>
                    <td class="align-middle">
                      <input type="number" name="custom_stock" class="form-control form-control-sm font-weight-bold text-primary" value="{{ $prod->custom_stock ?? $prod->stock }}" style="width: 100px;">
                    </td>
                    <td class="align-middle">
                      <div class="custom-control custom-switch">
                        <input type="checkbox" name="is_available" class="custom-control-input" id="avail_{{ $prod->id }}" {{ ($prod->is_available ?? true) ? 'checked' : '' }}>
                        <label class="custom-control-label font-weight-bold" for="avail_{{ $prod->id }}">In Stock</label>
                      </div>
                    </td>
                    <td class="align-middle">
                      <button type="submit" class="btn btn-primary btn-sm font-weight-bold"><i class="fas fa-save mr-1"></i> Save Branch Item</button>
                    </td>
                  </form>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-4">No products found for this store.</td></tr>
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
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>
