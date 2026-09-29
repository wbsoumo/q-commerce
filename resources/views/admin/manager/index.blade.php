<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Store Management Console | {{ $store->name ?? 'Store Portal' }}</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  <style>
    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
      background-color: #f4f6f9;
    }
    .store-hero-banner {
      background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f766e 100%);
      color: #ffffff;
      border-radius: 16px;
      box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.25);
    }
    .nav-tabs-custom .nav-link {
      font-weight: 700;
      color: #64748b;
      border: none;
      border-bottom: 3px solid transparent;
      padding: 14px 20px;
      font-size: 0.95rem;
      transition: all 0.2s ease;
    }
    .nav-tabs-custom .nav-link:hover {
      color: #0f766e;
    }
    .nav-tabs-custom .nav-link.active {
      color: #0d9488;
      background: transparent;
      border-bottom-color: #0d9488;
    }
    .stat-card-modern {
      border-radius: 14px;
      border: 1px solid #e2e8f0;
      background: #ffffff;
      padding: 20px;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .stat-card-modern:hover {
      transform: translateY(-3px);
      box-shadow: 0 12px 20px -5px rgba(0,0,0,0.08);
    }
    .badge-soft-success { background-color: #dcfce7; color: #15803d; }
    .badge-soft-warning { background-color: #fef9c3; color: #a16207; }
    .badge-soft-danger { background-color: #fee2e2; color: #b91c1c; }
    .badge-soft-info { background-color: #e0f2fe; color: #0369a1; }
    .badge-soft-purple { background-color: #f3e8ff; color: #6b21a8; }
    
    .table-modern thead th {
      background-color: #f8fafc;
      color: #475569;
      font-weight: 700;
      text-transform: uppercase;
      font-size: 0.75rem;
      letter-spacing: 0.05em;
      border-bottom: 2px solid #e2e8f0;
    }
    .table-modern td {
      vertical-align: middle !important;
    }
    .product-img-thumb {
      width: 44px;
      height: 44px;
      object-fit: contain;
      border-radius: 8px;
      background: #f8fafc;
      padding: 4px;
      border: 1px solid #e2e8f0;
    }
  </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light border-bottom-0 shadow-sm">
    <ul class="navbar-nav">
      <li class="nav-item"><a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a></li>
      <li class="nav-item d-none d-sm-inline-block"><a href="/admin" class="nav-link font-weight-bold text-dark"><i class="fas fa-home text-teal mr-1"></i> Main Admin</a></li>
      <li class="nav-item d-none d-sm-inline-block"><a href="#" class="nav-link active font-weight-bold text-teal">Store Management Console</a></li>
    </ul>
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <a class="btn btn-outline-teal btn-sm font-weight-bold" href="/admin/stores" target="_blank">
          <i class="fas fa-external-link-alt mr-1"></i> View All Stores
        </a>
      </li>
    </ul>
  </nav>

  @include('admin.layouts.sidebar')

  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        
        <!-- TOP STORE HERO BANNER -->
        <div class="store-hero-banner p-4 mb-4">
          <div class="row align-items-center">
            <div class="col-lg-7 col-md-12 mb-3 mb-lg-0">
              <div class="d-flex align-items-center">
                <div class="bg-white rounded-circle p-3 mr-3 shadow-sm" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                  <i class="fas fa-store text-teal" style="font-size: 1.8rem;"></i>
                </div>
                <div>
                  <div class="d-flex align-items-center flex-wrap">
                    <h2 class="font-weight-extrabold text-white mb-0 mr-3">{{ $store->name }}</h2>
                    <span class="badge badge-soft-{{ ($store->status ?? 'Active') === 'Active' ? 'success' : 'danger' }} px-3 py-1 font-weight-bold rounded-pill">
                      <i class="fas fa-circle mr-1" style="font-size: 0.55rem;"></i> {{ $store->status ?? 'Active' }}
                    </span>
                  </div>
                  <p class="mb-0 text-white-50 mt-1">
                    <i class="fas fa-map-marker-alt mr-1 text-teal"></i> {{ $store->address ?? 'Store Address' }}, {{ $store->city ?? '' }}
                    <span class="mx-2">•</span>
                    <i class="fas fa-barcode mr-1 text-teal"></i> Store Code: <strong>{{ $store->code ?? 'STORE' }}</strong>
                  </p>
                </div>
              </div>
            </div>

            <!-- SELECT STORE SWITCHER (FOR ADMIN) -->
            <div class="col-lg-5 col-md-12 text-lg-right">
              @if(auth()->user() && auth()->user()->role === 'store_manager')
                <div class="bg-white-10 p-3 rounded-lg border border-white-20 d-inline-block text-left">
                  <small class="text-white-50 text-uppercase font-weight-bold d-block">Assigned Branch Portal</small>
                  <span class="text-white font-weight-bold"><i class="fas fa-lock text-warning mr-1"></i> {{ $store->name }}</span>
                </div>
              @else
                <form action="/admin/store-manager" method="GET" class="d-inline-block text-left w-100" style="max-width: 320px;">
                  <label class="text-white-50 text-uppercase font-weight-bold mb-1" style="font-size: 0.75rem;"><i class="fas fa-exchange-alt mr-1 text-teal"></i> Switch Store Branch:</label>
                  <select name="store_id" class="form-control font-weight-bold border-0 shadow-sm" onchange="this.form.submit()" style="height: 44px; border-radius: 10px;">
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

        @if(session('success'))
          <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-lg" role="alert">
            <i class="fas fa-check-circle mr-2"></i> <strong>Success:</strong> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
          </div>
        @endif

        <!-- TOP METRICS GRID -->
        <div class="row mb-4">
          <div class="col-xl-3 col-md-6 col-12 mb-3 mb-xl-0">
            <div class="stat-card-modern">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <small class="text-uppercase text-muted font-weight-bold" style="font-size: 0.75rem;">Branch Inventory</small>
                  <h3 class="font-weight-bold text-dark mb-0 mt-1">{{ count($products) }}</h3>
                  <small class="text-teal font-weight-semibold"><i class="fas fa-box-open mr-1"></i> Active Store Items</small>
                </div>
                <div class="p-3 bg-soft-info text-info rounded-circle">
                  <i class="fas fa-boxes fa-2x"></i>
                </div>
              </div>
            </div>
          </div>

          <div class="col-xl-3 col-md-6 col-12 mb-3 mb-xl-0">
            <div class="stat-card-modern">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <small class="text-uppercase text-muted font-weight-bold" style="font-size: 0.75rem;">Total Store Orders</small>
                  <h3 class="font-weight-bold text-dark mb-0 mt-1">{{ $totalOrdersCount ?? 0 }}</h3>
                  <small class="text-warning font-weight-semibold"><i class="fas fa-clock mr-1"></i> {{ $pendingOrdersCount ?? 0 }} Live Active Orders</small>
                </div>
                <div class="p-3 bg-soft-warning text-warning rounded-circle">
                  <i class="fas fa-shopping-bag fa-2x"></i>
                </div>
              </div>
            </div>
          </div>

          <div class="col-xl-3 col-md-6 col-12 mb-3 mb-sm-0">
            <div class="stat-card-modern">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <small class="text-uppercase text-muted font-weight-bold" style="font-size: 0.75rem;">Delivered Revenue</small>
                  <h3 class="font-weight-bold text-dark mb-0 mt-1">₹{{ number_format($totalRevenue ?? 0, 2) }}</h3>
                  <small class="text-success font-weight-semibold"><i class="fas fa-check-circle mr-1"></i> Gross Branch Revenue</small>
                </div>
                <div class="p-3 bg-soft-success text-success rounded-circle">
                  <i class="fas fa-coins fa-2x"></i>
                </div>
              </div>
            </div>
          </div>

          <div class="col-xl-3 col-md-6 col-12">
            <div class="stat-card-modern">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <small class="text-uppercase text-muted font-weight-bold" style="font-size: 0.75rem;">Active Riders</small>
                  <h3 class="font-weight-bold text-dark mb-0 mt-1">{{ count($deliveryRiders ?? []) }}</h3>
                  <small class="text-primary font-weight-semibold"><i class="fas fa-bolt mr-1"></i> {{ $store->estimated_delivery_time_mins ?? 15 }} mins speed</small>
                </div>
                <div class="p-3 bg-soft-purple text-purple rounded-circle">
                  <i class="fas fa-motorcycle fa-2x"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- MAIN TABS CONTAINER -->
        <div class="card card-outline card-teal shadow-sm rounded-lg overflow-hidden mb-5">
          <div class="card-header p-0 bg-white border-bottom">
            <ul class="nav nav-tabs nav-tabs-custom" id="storeConsoleTabs" role="tablist">
              <li class="nav-item">
                <a class="nav-link active" id="inventory-tab" data-toggle="tab" href="#tab-inventory" role="tab">
                  <i class="fas fa-boxes mr-2"></i> Inventory & Stock Overrides
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="orders-tab" data-toggle="tab" href="#tab-orders" role="tab">
                  <i class="fas fa-shopping-cart mr-2"></i> Store Orders <span class="badge badge-soft-warning ml-1">{{ count($storeOrders ?? []) }}</span>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="riders-tab" data-toggle="tab" href="#tab-riders" role="tab">
                  <i class="fas fa-motorcycle mr-2"></i> Delivery Staff <span class="badge badge-soft-info ml-1">{{ count($deliveryRiders ?? []) }}</span>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="settings-tab" data-toggle="tab" href="#tab-settings" role="tab">
                  <i class="fas fa-sliders-h mr-2"></i> Edit Store Controls
                </a>
              </li>
            </ul>
          </div>

          <div class="card-body p-0">
            <div class="tab-content" id="storeConsoleTabsContent">

              <!-- TAB 1: INVENTORY MANAGER -->
              <div class="tab-pane fade show active p-4" id="tab-inventory" role="tabpanel">
                
                <!-- SEARCH & FILTER BAR -->
                <div class="row align-items-center mb-4">
                  <div class="col-md-7 mb-2 mb-md-0">
                    <form action="/admin/store-manager" method="GET" class="d-flex">
                      <input type="hidden" name="store_id" value="{{ $store->id }}">
                      <div class="input-group shadow-sm">
                        <div class="input-group-prepend">
                          <span class="input-group-text bg-light border-right-0"><i class="fas fa-search text-muted"></i></span>
                        </div>
                        <input type="text" name="search" class="form-control border-left-0 font-weight-medium" placeholder="Search products by name or SKU..." value="{{ request('search') }}">
                        <div class="input-group-append">
                          <button class="btn btn-teal font-weight-bold" type="submit">Filter Items</button>
                          @if(request('search'))
                            <a href="/admin/store-manager?store_id={{ $store->id }}" class="btn btn-outline-secondary font-weight-bold">Clear</a>
                          @endif
                        </div>
                      </div>
                    </form>
                  </div>
                  <div class="col-md-5 text-md-right">
                    <small class="text-muted font-weight-bold mr-2">Showing {{ count($products) }} items</small>
                    <a href="/admin/products" class="btn btn-outline-teal btn-sm font-weight-bold"><i class="fas fa-plus mr-1"></i> Add Global Product</a>
                  </div>
                </div>

                <!-- INVENTORY TABLE -->
                <div class="table-responsive">
                  <table class="table table-hover table-modern mb-0">
                    <thead>
                      <tr>
                        <th>Product Details</th>
                        <th>Scope</th>
                        <th>Global Base Price / Stock</th>
                        <th>Branch Custom Price (₹)</th>
                        <th>Branch Custom Stock</th>
                        <th>In Stock Status</th>
                        <th class="text-right">Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      @forelse($products as $prod)
                      <tr>
                        <td>
                          <div class="d-flex align-items-center">
                            @if(!empty($prod->image))
                              <img src="{{ $prod->image }}" class="product-img-thumb mr-3" alt="prod">
                            @else
                              <div class="product-img-thumb mr-3 d-flex align-items-center justify-content-center text-muted"><i class="fas fa-image"></i></div>
                            @endif
                            <div>
                              <strong class="text-dark d-block" style="font-size: 0.95rem;">{{ $prod->name }}</strong>
                              <small class="text-muted"><i class="fas fa-barcode mr-1"></i> SKU: {{ $prod->sku ?? 'N/A' }}</small>
                            </div>
                          </div>
                        </td>
                        <td>
                          @if(($prod->scope ?? 'global') === 'global')
                            <span class="badge badge-soft-success font-weight-bold"><i class="fas fa-globe mr-1"></i> Global</span>
                          @else
                            <span class="badge badge-soft-info font-weight-bold"><i class="fas fa-store mr-1"></i> Store Specific</span>
                          @endif
                        </td>
                        <td>
                          <span class="font-weight-bold text-dark">₹{{ number_format($prod->price, 2) }}</span>
                          <small class="text-muted d-block">Base Stock: {{ $prod->stock }} pcs</small>
                        </td>
                        <form action="/admin/store-manager/update-inventory" method="POST">
                          @csrf
                          <input type="hidden" name="store_id" value="{{ $store->id }}">
                          <input type="hidden" name="product_id" value="{{ $prod->id }}">
                          <td>
                            <div class="input-group input-group-sm shadow-sm" style="max-width: 130px;">
                              <div class="input-group-prepend"><span class="input-group-text bg-light font-weight-bold">₹</span></div>
                              <input type="number" step="0.01" name="custom_price" class="form-control font-weight-bold text-success" value="{{ $prod->custom_price ?? $prod->price }}">
                            </div>
                          </td>
                          <td>
                            <input type="number" name="custom_stock" class="form-control form-control-sm font-weight-bold text-primary shadow-sm" value="{{ $prod->custom_stock ?? $prod->stock }}" style="max-width: 100px;">
                          </td>
                          <td>
                            <div class="custom-control custom-switch">
                              <input type="checkbox" name="is_available" class="custom-control-input" id="avail_{{ $prod->id }}" {{ ($prod->is_available ?? true) ? 'checked' : '' }}>
                              <label class="custom-control-label font-weight-bold text-dark" for="avail_{{ $prod->id }}">
                                {{ ($prod->is_available ?? true) ? 'Available' : 'Disabled' }}
                              </label>
                            </div>
                          </td>
                          <td class="text-right">
                            <button type="submit" class="btn btn-teal btn-sm font-weight-bold shadow-sm">
                              <i class="fas fa-save mr-1"></i> Save Item
                            </button>
                          </td>
                        </form>
                      </tr>
                      @empty
                      <tr>
                        <td colspan="7" class="text-center py-5">
                          <i class="fas fa-box-open text-muted fa-3x mb-3 d-block"></i>
                          <p class="text-muted font-weight-bold mb-0">No products found for this store branch.</p>
                        </td>
                      </tr>
                      @endforelse
                    </tbody>
                  </table>
                </div>

              </div>

              <!-- TAB 2: RECENT STORE ORDERS -->
              <div class="tab-pane fade p-4" id="tab-orders" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <h5 class="font-weight-bold text-dark mb-0"><i class="fas fa-shopping-bag text-teal mr-2"></i> Recent Orders for {{ $store->name }}</h5>
                  <a href="/admin/orders" class="btn btn-outline-teal btn-sm font-weight-bold"><i class="fas fa-external-link-alt mr-1"></i> All Global Orders</a>
                </div>

                <div class="table-responsive">
                  <table class="table table-hover table-modern mb-0">
                    <thead>
                      <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Payment</th>
                        <th>Order Status</th>
                        <th>Date & Time</th>
                        <th class="text-right">Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      @forelse($storeOrders as $ord)
                      <tr>
                        <td class="font-weight-bold text-teal">#{{ $ord->order_number }}</td>
                        <td>
                          <strong class="d-block text-dark">{{ $ord->user_name ?? 'Guest User' }}</strong>
                          <small class="text-muted"><i class="fas fa-phone mr-1"></i> {{ $ord->user_phone ?? 'N/A' }}</small>
                        </td>
                        <td class="font-weight-bold text-dark">₹{{ number_format($ord->grand_total, 2) }}</td>
                        <td>
                          <span class="badge badge-soft-info font-weight-bold">{{ $ord->payment_method ?? 'COD' }}</span>
                        </td>
                        <td>
                          @php
                            $st = $ord->status ?? 'Pending';
                            $badgeClass = 'badge-soft-warning';
                            if ($st === 'Delivered') $badgeClass = 'badge-soft-success';
                            if ($st === 'Cancelled') $badgeClass = 'badge-soft-danger';
                            if ($st === 'Packing' || $st === 'Out for Delivery') $badgeClass = 'badge-soft-info';
                          @endphp
                          <span class="badge {{ $badgeClass }} px-3 py-1 font-weight-bold">{{ $st }}</span>
                        </td>
                        <td class="text-muted"><small>{{ date('d M Y, h:i A', strtotime($ord->created_at)) }}</small></td>
                        <td class="text-right">
                          <a href="/admin/orders/{{ $ord->id }}" class="btn btn-light btn-sm font-weight-bold text-teal border">
                            <i class="fas fa-eye mr-1"></i> View Order
                          </a>
                        </td>
                      </tr>
                      @empty
                      <tr>
                        <td colspan="7" class="text-center py-5">
                          <i class="fas fa-receipt text-muted fa-3x mb-3 d-block"></i>
                          <p class="text-muted font-weight-bold mb-0">No orders received for this store yet.</p>
                        </td>
                      </tr>
                      @endforelse
                    </tbody>
                  </table>
                </div>
              </div>

              <!-- TAB 3: DELIVERY STAFF -->
              <div class="tab-pane fade p-4" id="tab-riders" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <h5 class="font-weight-bold text-dark mb-0"><i class="fas fa-motorcycle text-teal mr-2"></i> Delivery Partners Assigned to {{ $store->name }}</h5>
                  <a href="/admin/deliveries" class="btn btn-outline-teal btn-sm font-weight-bold"><i class="fas fa-user-plus mr-1"></i> Manage All Delivery Partners</a>
                </div>

                <div class="row">
                  @forelse($deliveryRiders as $rider)
                  <div class="col-md-6 col-lg-4 mb-3">
                    <div class="border rounded-lg p-3 bg-white shadow-sm d-flex align-items-center">
                      <div class="bg-soft-teal rounded-circle p-3 mr-3 text-teal font-weight-bold" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                        <i class="fas fa-user-ninja"></i>
                      </div>
                      <div class="flex-grow-1">
                        <strong class="text-dark d-block" style="font-size: 1rem;">{{ $rider->name }}</strong>
                        <small class="text-muted d-block"><i class="fas fa-phone mr-1"></i> {{ $rider->phone }}</small>
                        <span class="badge badge-soft-{{ ($rider->status ?? 'Active') === 'Active' ? 'success' : 'danger' }} mt-1">
                          {{ $rider->status ?? 'Active' }}
                        </span>
                      </div>
                    </div>
                  </div>
                  @empty
                  <div class="col-12 text-center py-5">
                    <i class="fas fa-user-slash text-muted fa-3x mb-3 d-block"></i>
                    <p class="text-muted font-weight-bold mb-1">No delivery staff specifically assigned to this store branch.</p>
                    <small class="text-danger">App will show "No delivery partner in your store, contact admin" warning on order dispatch screen.</small>
                  </div>
                  @endforelse
                </div>
              </div>

              <!-- TAB 4: STORE CONTROLS & SETTINGS -->
              <div class="tab-pane fade p-4" id="tab-settings" role="tabpanel">
                <form action="/admin/stores/{{ $store->id }}/settings" method="POST">
                  @csrf
                  <h5 class="font-weight-bold text-dark mb-3"><i class="fas fa-cog text-teal mr-2"></i> Branch Operational Hours & Delivery Rules</h5>

                  <div class="row">
                    <div class="col-md-3 form-group">
                      <label class="font-weight-bold text-dark">Branch Operating Status</label>
                      <select name="status" class="form-control font-weight-bold border-teal shadow-sm">
                        <option value="Active" {{ ($store->status ?? 'Active') === 'Active' ? 'selected' : '' }}>Active (Open for Orders)</option>
                        <option value="Inactive" {{ ($store->status ?? '') === 'Inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="Temporarily Closed" {{ ($store->status ?? '') === 'Temporarily Closed' ? 'selected' : '' }}>Temporarily Closed</option>
                        <option value="Maintenance" {{ ($store->status ?? '') === 'Maintenance' ? 'selected' : '' }}>Under Maintenance</option>
                      </select>
                    </div>

                    <div class="col-md-3 form-group">
                      <label class="font-weight-bold text-dark">Opening Time</label>
                      <input type="time" name="opening_time" class="form-control font-weight-medium shadow-sm" value="{{ $store->opening_time ?? '06:00' }}" required>
                    </div>

                    <div class="col-md-3 form-group">
                      <label class="font-weight-bold text-dark">Closing Time</label>
                      <input type="time" name="closing_time" class="form-control font-weight-medium shadow-sm" value="{{ $store->closing_time ?? '23:00' }}" required>
                    </div>

                    <div class="col-md-3 form-group">
                      <label class="font-weight-bold text-dark">Min. Order Amount (₹)</label>
                      <input type="number" step="0.01" name="min_order_amount" class="form-control font-weight-medium shadow-sm" value="{{ $store->min_order_amount ?? 0 }}" required>
                    </div>
                  </div>

                  <div class="row mt-2">
                    <div class="col-md-3 form-group">
                      <label class="font-weight-bold text-dark">Delivery Fee (₹)</label>
                      <input type="number" step="0.01" name="delivery_fee" class="form-control font-weight-medium shadow-sm" value="{{ $store->delivery_fee ?? 15 }}" required>
                    </div>

                    <div class="col-md-3 form-group">
                      <label class="font-weight-bold text-dark">Free Delivery Threshold (₹)</label>
                      <input type="number" step="0.01" name="free_delivery_threshold" class="form-control font-weight-medium shadow-sm" value="{{ $store->free_delivery_threshold ?? 299 }}" required>
                    </div>

                    <div class="col-md-3 form-group">
                      <label class="font-weight-bold text-dark">Est. Delivery Speed (Mins)</label>
                      <input type="number" name="estimated_delivery_time_mins" class="form-control font-weight-medium shadow-sm" value="{{ $store->estimated_delivery_time_mins ?? 15 }}" required>
                    </div>

                    <div class="col-md-3 form-group d-flex align-items-end">
                      <button type="submit" class="btn btn-teal btn-block font-weight-bold shadow-sm py-2" style="height: 40px;">
                        <i class="fas fa-save mr-1"></i> Save Store Settings
                      </button>
                    </div>
                  </div>
                </form>
              </div>

            </div>
          </div>
        </div>

      </div>
    </div>
  </div>

  <footer class="main-footer text-sm text-muted">
    <strong>Copyright &copy; 2026 Quick-Commerce Store Management System.</strong> All rights reserved.
  </footer>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>
