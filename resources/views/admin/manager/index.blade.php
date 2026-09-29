<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Store Management Portal | {{ $store->name ?? 'Store Management' }}</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  <style>
    .product-img-thumb {
      width: 40px;
      height: 40px;
      object-fit: contain;
      border-radius: 6px;
      background: #f8f9fa;
      padding: 2px;
      border: 1px solid #dee2e6;
    }
  </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
      <li class="nav-item"><a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a></li>
      <li class="nav-item d-none d-sm-inline-block"><a href="/admin" class="nav-link font-weight-bold">Main Admin</a></li>
      <li class="nav-item d-none d-sm-inline-block"><a href="#" class="nav-link active font-weight-bold">Store Management Portal</a></li>
    </ul>
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <a class="btn btn-outline-secondary btn-sm" href="/admin/stores" target="_blank">
          <i class="fas fa-external-link-alt mr-1"></i> All Stores Network
        </a>
      </li>
    </ul>
  </nav>

  @include('admin.layouts.sidebar')

  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        
        <!-- STORE SELECTION & HEADER BANNER -->
        <div class="card card-outline card-success mb-3 shadow-sm">
          <div class="card-body p-3">
            <div class="row align-items-center">
              <div class="col-lg-7 col-md-12 mb-2 mb-lg-0">
                <div class="d-flex align-items-center">
                  <div class="bg-success rounded p-3 mr-3 text-white">
                    <i class="fas fa-store fa-2x"></i>
                  </div>
                  <div>
                    <h3 class="m-0 font-weight-bold text-dark">{{ $store->name }}</h3>
                    <p class="text-muted mb-0">
                      <i class="fas fa-map-marker-alt mr-1 text-success"></i> {{ $store->address ?? 'Store Location' }}, {{ $store->city ?? '' }}
                      <span class="mx-2">•</span>
                      Code: <strong>{{ $store->code ?? 'STORE' }}</strong>
                    </p>
                  </div>
                </div>
              </div>

              <!-- SELECT STORE SWITCHER -->
              <div class="col-lg-5 col-md-12 text-lg-right">
                @if(auth()->user() && auth()->user()->role === 'store_manager')
                  <div class="d-inline-block text-left p-2 border rounded bg-light">
                    <small class="text-uppercase text-muted font-weight-bold d-block">Assigned Store Branch</small>
                    <span class="text-dark font-weight-bold"><i class="fas fa-lock text-warning mr-1"></i> {{ $store->name }}</span>
                  </div>
                @else
                  <form action="/admin/store-manager" method="GET" class="d-inline-block text-left w-100" style="max-width: 320px;">
                    <label class="text-muted text-uppercase small font-weight-bold mb-1"><i class="fas fa-exchange-alt mr-1 text-success"></i> Switch Store Branch:</label>
                    <select name="store_id" class="form-control font-weight-bold" onchange="this.form.submit()">
                      @foreach($stores as $st)
                        <option value="{{ $st->id }}" {{ ($store->id ?? 1) == $st->id ? 'selected' : '' }}>
                          {{ $st->name }} ({{ $st->city }} - {{ $st->code }})
                        </option>
                      @endforeach
                    </select>
                  </form>
                @endif
              </div>
            </div>
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
            <i class="fas fa-exclamation-triangle mr-1"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
          </div>
        @endif

        <!-- STATS GRID -->
        <div class="row mb-3">
          <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
              <div class="inner">
                <h3>{{ count($products) }}</h3>
                <p>Assigned Items</p>
              </div>
              <div class="icon"><i class="fas fa-boxes"></i></div>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
              <div class="inner">
                <h3>{{ $totalOrdersCount ?? 0 }}</h3>
                <p>Total Store Orders</p>
              </div>
              <div class="icon"><i class="fas fa-shopping-bag"></i></div>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
              <div class="inner">
                <h3>₹{{ number_format($totalRevenue ?? 0, 2) }}</h3>
                <p>Delivered Revenue</p>
              </div>
              <div class="icon"><i class="fas fa-coins"></i></div>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="small-box bg-secondary">
              <div class="inner">
                <h3>{{ count($deliveryRiders ?? []) }}</h3>
                <p>Active Staff / Riders</p>
              </div>
              <div class="icon"><i class="fas fa-motorcycle"></i></div>
            </div>
          </div>
        </div>

        <!-- MAIN NAVIGATION TABS -->
        <div class="card card-success card-outline card-tabs shadow-sm">
          <div class="card-header p-0 pt-1 border-bottom-0">
            <ul class="nav nav-tabs" id="storeConsoleTabs" role="tablist">
              <li class="nav-item">
                <a class="nav-link active" id="inventory-tab" data-toggle="pill" href="#tab-inventory" role="tab">
                  <i class="fas fa-boxes mr-1"></i> Inventory & Stock Overrides
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="orders-tab" data-toggle="pill" href="#tab-orders" role="tab">
                  <i class="fas fa-shopping-cart mr-1"></i> Store Orders <span class="badge badge-warning ml-1">{{ count($storeOrders ?? []) }}</span>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="riders-tab" data-toggle="pill" href="#tab-riders" role="tab">
                  <i class="fas fa-motorcycle mr-1"></i> Delivery Staff <span class="badge badge-info ml-1">{{ count($deliveryRiders ?? []) }}</span>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="settings-tab" data-toggle="pill" href="#tab-settings" role="tab">
                  <i class="fas fa-cog mr-1"></i> Store Controls & Settings
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link text-danger" id="security-tab" data-toggle="pill" href="#tab-security" role="tab">
                  <i class="fas fa-shield-alt mr-1"></i> Security & Danger Zone
                </a>
              </li>
            </ul>
          </div>

          <div class="card-body p-0">
            <div class="tab-content" id="storeConsoleTabsContent">

              <!-- TAB 1: INVENTORY OVERRIDES -->
              <div class="tab-pane fade show active p-3" id="tab-inventory" role="tabpanel">
                
                <!-- SEARCH FORM -->
                <div class="row align-items-center mb-3">
                  <div class="col-md-8 mb-2 mb-md-0">
                    <form action="/admin/store-manager" method="GET" class="d-flex">
                      <input type="hidden" name="store_id" value="{{ $store->id }}">
                      <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Search product by name or SKU..." value="{{ request('search') }}">
                        <div class="input-group-append">
                          <button class="btn btn-success font-weight-bold" type="submit"><i class="fas fa-search mr-1"></i> Search</button>
                          @if(request('search'))
                            <a href="/admin/store-manager?store_id={{ $store->id }}" class="btn btn-default">Clear</a>
                          @endif
                        </div>
                      </div>
                    </form>
                  </div>
                  <div class="col-md-4 text-md-right">
                    <span class="text-muted mr-2">Showing {{ count($products) }} items</span>
                    <a href="/admin/products" class="btn btn-outline-success btn-sm font-weight-bold"><i class="fas fa-plus mr-1"></i> Master Catalog</a>
                  </div>
                </div>

                <!-- INVENTORY TABLE -->
                <div class="table-responsive">
                  <table class="table table-striped table-bordered mb-0">
                    <thead class="bg-light">
                      <tr>
                        <th>Product Details</th>
                        <th>Scope</th>
                        <th>Global Price / Stock</th>
                        <th>Branch Price (₹)</th>
                        <th>Branch Stock</th>
                        <th>Availability</th>
                        <th class="text-right">Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      @forelse($products as $prod)
                      <tr>
                        <td>
                          <div class="d-flex align-items-center">
                            @if(!empty($prod->image))
                              <img src="{{ $prod->image }}" class="product-img-thumb mr-2" alt="prod">
                            @else
                              <div class="product-img-thumb mr-2 d-flex align-items-center justify-content-center text-muted"><i class="fas fa-image"></i></div>
                            @endif
                            <div>
                              <span class="font-weight-bold text-dark d-block">{{ $prod->name }}</span>
                              <small class="text-muted">SKU: {{ $prod->sku ?? 'N/A' }}</small>
                            </div>
                          </div>
                        </td>
                        <td>
                          @if(($prod->scope ?? 'global') === 'global')
                            <span class="badge badge-success"><i class="fas fa-globe mr-1"></i> Global</span>
                          @else
                            <span class="badge badge-primary"><i class="fas fa-store mr-1"></i> Store Specific</span>
                          @endif
                        </td>
                        <td>
                          <span class="font-weight-bold">₹{{ number_format($prod->price, 2) }}</span>
                          <small class="text-muted d-block">Base: {{ $prod->stock }} pcs</small>
                        </td>
                        <form action="/admin/store-manager/update-inventory" method="POST">
                          @csrf
                          <input type="hidden" name="store_id" value="{{ $store->id }}">
                          <input type="hidden" name="product_id" value="{{ $prod->id }}">
                          <td>
                            <div class="input-group input-group-sm" style="max-width: 120px;">
                              <div class="input-group-prepend"><span class="input-group-text">₹</span></div>
                              <input type="number" step="0.01" name="custom_price" class="form-control font-weight-bold text-success" value="{{ $prod->custom_price ?? $prod->price }}">
                            </div>
                          </td>
                          <td>
                            <input type="number" name="custom_stock" class="form-control form-control-sm font-weight-bold text-primary" value="{{ $prod->custom_stock ?? $prod->stock }}" style="max-width: 90px;">
                          </td>
                          <td>
                            <div class="custom-control custom-switch">
                              <input type="checkbox" name="is_available" class="custom-control-input" id="avail_{{ $prod->id }}" {{ ($prod->is_available ?? true) ? 'checked' : '' }}>
                              <label class="custom-control-label" for="avail_{{ $prod->id }}">
                                {{ ($prod->is_available ?? true) ? 'In Stock' : 'Disabled' }}
                              </label>
                            </div>
                          </td>
                          <td class="text-right">
                            <button type="submit" class="btn btn-primary btn-sm font-weight-bold">
                              <i class="fas fa-save mr-1"></i> Save Item
                            </button>
                          </td>
                        </form>
                      </tr>
                      @empty
                      <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                          No products found for this store branch.
                        </td>
                      </tr>
                      @endforelse
                    </tbody>
                  </table>
                </div>

              </div>

              <!-- TAB 2: STORE ORDERS -->
              <div class="tab-pane fade p-3" id="tab-orders" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <h5 class="font-weight-bold text-dark mb-0">Recent Orders for {{ $store->name }}</h5>
                  <a href="/admin/orders" class="btn btn-outline-primary btn-sm font-weight-bold">View Master Order List</a>
                </div>

                <div class="table-responsive">
                  <table class="table table-striped table-bordered mb-0">
                    <thead class="bg-light">
                      <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th>Date & Time</th>
                        <th class="text-right">Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      @forelse($storeOrders as $ord)
                      <tr>
                        <td class="font-weight-bold">#{{ $ord->order_number }}</td>
                        <td>
                          <span class="d-block font-weight-bold">{{ $ord->user_name ?? 'Guest Customer' }}</span>
                          <small class="text-muted"><i class="fas fa-phone mr-1"></i> {{ $ord->user_phone ?? 'N/A' }}</small>
                        </td>
                        <td class="font-weight-bold">₹{{ number_format($ord->grand_total, 2) }}</td>
                        <td><span class="badge badge-info">{{ $ord->payment_method ?? 'COD' }}</span></td>
                        <td>
                          @php
                            $st = $ord->status ?? 'Pending';
                            $badgeClass = 'badge-warning';
                            if ($st === 'Delivered') $badgeClass = 'badge-success';
                            if ($st === 'Cancelled') $badgeClass = 'badge-danger';
                            if ($st === 'Packing' || $st === 'Out for Delivery') $badgeClass = 'badge-info';
                          @endphp
                          <span class="badge {{ $badgeClass }}">{{ $st }}</span>
                        </td>
                        <td class="text-muted"><small>{{ date('d M Y, h:i A', strtotime($ord->created_at)) }}</small></td>
                        <td class="text-right">
                          <a href="/admin/orders/{{ $ord->id }}" class="btn btn-default btn-xs font-weight-bold">
                            <i class="fas fa-eye mr-1"></i> View Order
                          </a>
                        </td>
                      </tr>
                      @empty
                      <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                          No orders received for this store yet.
                        </td>
                      </tr>
                      @endforelse
                    </tbody>
                  </table>
                </div>
              </div>

              <!-- TAB 3: DELIVERY STAFF -->
              <div class="tab-pane fade p-3" id="tab-riders" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <h5 class="font-weight-bold text-dark mb-0">Delivery Partners Assigned to Store</h5>
                  <a href="/admin/deliveries" class="btn btn-outline-success btn-sm font-weight-bold">Manage Riders</a>
                </div>

                <div class="row">
                  @forelse($deliveryRiders as $rider)
                  <div class="col-md-6 col-lg-4 mb-3">
                    <div class="border rounded p-3 bg-light d-flex align-items-center">
                      <div class="bg-success rounded-circle p-2 mr-3 text-white" style="width: 44px; height: 44px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-motorcycle"></i>
                      </div>
                      <div>
                        <strong class="d-block text-dark">{{ $rider->name }}</strong>
                        <small class="text-muted"><i class="fas fa-phone mr-1"></i> {{ $rider->phone }}</small>
                        <div class="mt-1">
                          <span class="badge badge-{{ ($rider->status ?? 'Active') === 'Active' ? 'success' : 'danger' }}">
                            {{ $rider->status ?? 'Active' }}
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>
                  @empty
                  <div class="col-12 text-center py-4 text-muted">
                    <i class="fas fa-user-slash fa-2x mb-2 d-block"></i>
                    <p class="mb-1 font-weight-bold">No delivery staff assigned to this store branch.</p>
                    <small class="text-danger">Manager app will prompt: "No delivery partner in your store, contact admin".</small>
                  </div>
                  @endforelse
                </div>
              </div>

              <!-- TAB 4: STORE CONTROLS -->
              <div class="tab-pane fade p-3" id="tab-settings" role="tabpanel">
                <form action="/admin/stores/{{ $store->id }}/settings" method="POST">
                  @csrf
                  <h5 class="font-weight-bold text-dark mb-3"><i class="fas fa-sliders-h mr-1 text-success"></i> Branch Operating Controls & Rules</h5>

                  <div class="row">
                    <div class="col-md-3 form-group">
                      <label class="font-weight-bold">Operating Status</label>
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
                      <label class="font-weight-bold">Est. Delivery Speed (Mins)</label>
                      <input type="number" name="estimated_delivery_time_mins" class="form-control" value="{{ $store->estimated_delivery_time_mins ?? 15 }}" required>
                    </div>

                    <div class="col-md-3 form-group d-flex align-items-end">
                      <button type="submit" class="btn btn-success btn-block font-weight-bold">
                        <i class="fas fa-save mr-1"></i> Save Store Controls
                      </button>
                    </div>
                  </div>
                </form>
              </div>

              <!-- TAB 5: SECURITY & DANGER ZONE -->
              <div class="tab-pane fade p-3" id="tab-security" role="tabpanel">
                <div class="card card-outline card-danger">
                  <div class="card-header bg-danger text-white">
                    <h5 class="card-title font-weight-bold mb-0"><i class="fas fa-shield-alt mr-2"></i> Security & Store Deletion Control</h5>
                  </div>
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col-md-8">
                        <h5 class="font-weight-bold text-dark">Delete Store Branch</h5>
                        <p class="text-muted mb-0">
                          Permanently delete <strong>{{ $store->name }}</strong> branch and all custom store inventory records. This action cannot be undone.
                        </p>
                      </div>
                      <div class="col-md-4 text-md-right mt-3 mt-md-0">
                        @if(auth()->user() && auth()->user()->role === 'store_manager')
                          <button class="btn btn-secondary font-weight-bold" disabled>
                            <i class="fas fa-lock mr-1"></i> Admin Authorization Required
                          </button>
                        @else
                          <button type="button" class="btn btn-danger font-weight-bold" data-toggle="modal" data-target="#deleteStoreModal">
                            <i class="fas fa-trash-alt mr-1"></i> Permanently Delete Store
                          </button>
                        @endif
                      </div>
                    </div>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>

      </div>
    </div>
  </div>

  <footer class="main-footer"><strong>Copyright &copy; 2026 Q-Commerce Admin.</strong></footer>
</div>

<!-- DELETE STORE MODAL CONFIRMATION -->
<div class="modal fade" id="deleteStoreModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title font-weight-bold"><i class="fas fa-exclamation-triangle mr-2"></i> Confirm Permanent Store Deletion</h5>
        <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <form action="/admin/stores/{{ $store->id }}/delete" method="POST">
        @csrf
        <div class="card-body">
          <p class="text-dark font-weight-bold mb-2">Are you sure you want to delete this store branch?</p>
          <div class="alert alert-warning text-dark font-weight-bold p-2 small">
            <i class="fas fa-info-circle mr-1"></i> Store: {{ $store->name }} (Code: {{ $store->code }})
          </div>
          <p class="text-muted small mb-0">This will remove custom price/stock overrides for this store branch from the database.</p>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-default font-weight-bold" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger font-weight-bold"><i class="fas fa-trash-alt mr-1"></i> Yes, Delete Store Branch</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>
