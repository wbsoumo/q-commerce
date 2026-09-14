<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Store Manager Control Panel | {{ $store->name ?? 'My Branch' }}</title>
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
  
  <!-- TOP NAVBAR -->
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

  <!-- STORE MANAGER SIDEBAR -->
  <aside class="main-sidebar manager-sidebar sidebar-dark-primary elevation-4 position-fixed">
    <a href="/manager/dashboard" class="brand-link text-center brand-banner border-0">
      <span class="brand-text font-weight-bold text-white"><i class="fas fa-shopping-basket mr-2"></i>Branch Manager</span>
    </a>
    <div class="sidebar">
      <div class="user-panel mt-3 pb-3 mb-3 d-flex border-bottom border-secondary">
        <div class="image text-white font-weight-bold pl-2">
          <i class="fas fa-user-circle fa-2x text-info"></i>
        </div>
        <div class="info">
          <a href="#" class="d-block font-weight-bold text-light">{{ auth()->user()->name ?? 'Store Manager' }}</a>
          <span class="badge badge-info mt-1"><i class="fas fa-map-marker-alt mr-1"></i> {{ $store->city ?? 'Branch' }}</span>
        </div>
      </div>
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
          <li class="nav-item">
            <a href="#inventory-section" class="nav-link active">
              <i class="nav-icon fas fa-boxes text-info"></i><p>Live Inventory & Pricing</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="#orders-section" class="nav-link">
              <i class="nav-icon fas fa-shopping-cart text-warning"></i><p>Store Orders</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="#dispatch-section" class="nav-link">
              <i class="nav-icon fas fa-motorcycle text-success"></i><p>Delivery Dispatch</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="#settings-section" class="nav-link">
              <i class="nav-icon fas fa-cog text-light"></i><p>Branch Operations</p>
            </a>
          </li>
          <li class="nav-header">ACCOUNT</li>
          <li class="nav-item">
            <form action="/logout" method="POST" id="mgr-logout">
              @csrf
              <a href="#" class="nav-link text-danger" onclick="event.preventDefault(); document.getElementById('mgr-logout').submit();">
                <i class="nav-icon fas fa-power-off"></i><p>Logout Session</p>
              </a>
            </form>
          </li>
        </ul>
      </nav>
    </div>
  </aside>

  <!-- CONTENT WRAPPER -->
  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row align-items-center mb-3">
          <div class="col-sm-6">
            <h1 class="m-0 font-weight-bold text-dark"><i class="fas fa-building text-primary mr-2"></i>{{ $store->name ?? 'Store Branch' }}</h1>
            <p class="text-muted mb-0"><i class="fas fa-map-pin text-danger mr-1"></i>{{ $store->address ?? 'Location' }}, {{ $store->city ?? '' }} (Code: {{ $store->code ?? 'STR' }})</p>
          </div>
          <div class="col-sm-6 text-right">
            <span class="badge badge-{{ ($store->status ?? 'Active') === 'Active' ? 'success' : 'danger' }} p-2" style="font-size: 1rem;">
              <i class="fas fa-circle mr-1" style="font-size: 0.65rem;"></i> {{ $store->status ?? 'Active' }}
            </span>
            <span class="ml-2 font-weight-bold text-dark"><i class="far fa-clock text-primary mr-1"></i> {{ $store->opening_time ?? '06:00' }} - {{ $store->closing_time ?? '23:00' }}</span>
          </div>
        </div>

        @if(session('success'))
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
          </div>
        @endif
        @if(session('error'))
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle mr-1"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
          </div>
        @endif
      </div>
    </div>

    <div class="content">
      <div class="container-fluid">

        <!-- 1. STATS BANNER -->
        <div class="row mb-4">
          <div class="col-lg-3 col-6">
            <div class="small-box bg-info shadow-sm">
              <div class="inner">
                <h3>{{ count($products) }}</h3>
                <p>Assigned Products</p>
              </div>
              <div class="icon"><i class="fas fa-boxes"></i></div>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="small-box bg-warning shadow-sm">
              <div class="inner">
                <h3>{{ $pendingOrdersCount }}</h3>
                <p>Pending Orders</p>
              </div>
              <div class="icon"><i class="fas fa-clock"></i></div>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="small-box bg-success shadow-sm">
              <div class="inner">
                <h3>{{ $outForDeliveryCount }}</h3>
                <p>Out For Delivery</p>
              </div>
              <div class="icon"><i class="fas fa-motorcycle"></i></div>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="small-box bg-secondary shadow-sm">
              <div class="inner">
                <h3>{{ $totalOrdersCount }}</h3>
                <p>Total Orders</p>
              </div>
              <div class="icon"><i class="fas fa-receipt"></i></div>
            </div>
          </div>
        </div>

        <!-- 2. BRANCH INVENTORY CONTROL -->
        <div id="inventory-section" class="card card-outline card-primary shadow-sm mb-4">
          <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h3 class="card-title font-weight-bold my-1"><i class="fas fa-boxes mr-2"></i>Branch Product Price & Stock Overrides</h3>
          </div>
          <div class="card-body">
            <!-- Search bar -->
            <form action="/manager/dashboard" method="GET" class="row mb-3">
              <div class="col-md-10">
                <input type="text" name="search" class="form-control form-control-lg" placeholder="Search product by name or SKU..." value="{{ request('search') }}">
              </div>
              <div class="col-md-2 mt-2 mt-md-0">
                <button type="submit" class="btn btn-primary btn-lg btn-block font-weight-bold"><i class="fas fa-search mr-1"></i> Search</button>
              </div>
            </form>

            <div class="table-responsive">
              <table class="table table-striped table-bordered mb-0">
                <thead class="bg-light">
                  <tr>
                    <th>Product Name</th>
                    <th>Scope</th>
                    <th>Base Price</th>
                    <th>Branch Custom Price (₹)</th>
                    <th>Branch Stock</th>
                    <th>In-Stock Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($products as $prod)
                  <tr>
                    <td class="font-weight-bold align-middle">{{ $prod->name }} <br><small class="text-muted">SKU: {{ $prod->sku }}</small></td>
                    <td class="align-middle">
                      @if($prod->scope === 'global')
                        <span class="badge badge-success">Global</span>
                      @else
                        <span class="badge badge-info">Store Specific</span>
                      @endif
                    </td>
                    <td class="align-middle">₹{{ $prod->price }}</td>
                    <form action="/manager/inventory/update" method="POST">
                      @csrf
                      <input type="hidden" name="product_id" value="{{ $prod->id }}">
                      <td class="align-middle">
                        <div class="input-group input-group-sm" style="width: 130px;">
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
                          <label class="custom-control-label font-weight-bold" for="avail_{{ $prod->id }}">Available</label>
                        </div>
                      </td>
                      <td class="align-middle">
                        <button type="submit" class="btn btn-primary btn-sm font-weight-bold"><i class="fas fa-save mr-1"></i> Save Item</button>
                      </td>
                    </form>
                  </tr>
                  @empty
                  <tr><td colspan="7" class="text-center py-4 text-muted">No products found.</td></tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- 3. STORE ORDERS & LIFECYCLE MANAGEMENT -->
        <div id="orders-section" class="card card-outline card-warning shadow-sm mb-4">
          <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
            <h3 class="card-title font-weight-bold my-1"><i class="fas fa-shopping-cart mr-2"></i>Branch Orders Management</h3>
          </div>
          <div class="card-body p-0">
            <table class="table table-striped table-bordered mb-0">
              <thead class="bg-light">
                <tr>
                  <th>Order #</th>
                  <th>Customer Phone</th>
                  <th>Total Amount</th>
                  <th>Payment</th>
                  <th>Current Status</th>
                  <th>Update Order Status</th>
                </tr>
              </thead>
              <tbody>
                @forelse($orders as $ord)
                <tr>
                  <td class="font-weight-bold align-middle">#{{ $ord->order_number }}</td>
                  <td class="align-middle">{{ $ord->user_phone ?? 'N/A' }}</td>
                  <td class="align-middle text-success font-weight-bold">₹{{ $ord->grand_total }}</td>
                  <td class="align-middle"><span class="badge badge-light border">{{ $ord->payment_method ?? 'COD' }}</span></td>
                  <td class="align-middle"><span class="badge badge-info">{{ $ord->status }}</span></td>
                  <td class="align-middle">
                    <form action="/manager/orders/{{ $ord->id }}/status" method="POST" class="form-inline">
                      @csrf
                      <select name="status" class="form-control form-control-sm font-weight-bold mr-2">
                        @foreach(['Pending', 'Confirmed', 'Preparing', 'Ready for Pickup', 'Out for Delivery', 'Delivered', 'Cancelled'] as $st)
                          <option value="{{ $st }}" {{ $ord->status === $st ? 'selected' : '' }}>{{ $st }}</option>
                        @endforeach
                      </select>
                      <button type="submit" class="btn btn-warning btn-sm font-weight-bold">Update</button>
                    </form>
                  </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-4 text-muted">No recent orders for this store branch.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>

        <!-- 4. DELIVERY PARTNER DISPATCH ASSIGNMENT -->
        <div id="dispatch-section" class="card card-outline card-success shadow-sm mb-4">
          <div class="card-header bg-success text-white">
            <h3 class="card-title font-weight-bold my-1"><i class="fas fa-motorcycle mr-2"></i>Branch Delivery Partner Dispatch</h3>
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

        <!-- 5. BRANCH OPERATIONAL CONTROLS -->
        <div id="settings-section" class="card card-outline card-secondary shadow-sm mb-4">
          <div class="card-header bg-dark text-white">
            <h3 class="card-title font-weight-bold my-1"><i class="fas fa-cog mr-2"></i>Branch Quick Operational Settings</h3>
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
                  <button type="submit" class="btn btn-dark btn-block font-weight-bold"><i class="fas fa-save mr-1"></i> Save Operating Hours & Delivery Controls</button>
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
