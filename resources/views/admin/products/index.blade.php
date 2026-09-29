<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Inventory Management | Q-Commerce Admin</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  <style>
    :root {
      --brand-green: #28a745;
      --brand-green-dark: #1e7e34;
      --brand-green-light: #e6f4ea;
    }
    .btn-brand-green { background-color: var(--brand-green); color: #fff; border-color: var(--brand-green); }
    .btn-brand-green:hover { background-color: var(--brand-green-dark); color: #fff; border-color: var(--brand-green-dark); }
    .inventory-card { transition: all 0.2s ease-in-out; border-radius: 8px; border-left: 4px solid var(--brand-green); }
    .inventory-card:hover { transform: translateY(-3px); box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
    .table-modern th { background-color: #1e293b; color: #ffffff; border: none; vertical-align: middle; }
    .table-modern td { vertical-align: middle; }
    .product-img-thumb { width: 44px; height: 44px; object-fit: contain; border-radius: 6px; border: 1px solid #e2e8f0; background: #fff; padding: 2px; }
    .badge-stock-active { background-color: var(--brand-green-light); color: var(--brand-green-dark); border: 1px solid #a7f3d0; }
    .badge-stock-low { background-color: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
    .badge-stock-out { background-color: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; }
    .bulk-toolbar { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 18px; }
  </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
      <li class="nav-item"><a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a></li>
      <li class="nav-item d-none d-sm-inline-block"><a href="/admin" class="nav-link">Dashboard</a></li>
      <li class="nav-item d-none d-sm-inline-block"><a href="/admin/products" class="nav-link active font-weight-bold text-success"><i class="fas fa-boxes mr-1"></i> Inventory Management</a></li>
    </ul>
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <a class="btn btn-brand-green btn-sm font-weight-bold" href="/admin/products/create">
          <i class="fas fa-plus-circle mr-1"></i> Add New Product
        </a>
      </li>
    </ul>
  </nav>

  @include('admin.layouts.sidebar')

  <div class="content-wrapper">
    <!-- Header -->
    <div class="content-header">
      <div class="container-fluid d-flex justify-content-between align-items-center flex-wrap">
        <div class="mb-2">
          <h1 class="m-0 font-weight-bold text-dark"><i class="fas fa-boxes text-success mr-2"></i>Inventory Management System</h1>
          <p class="text-muted small mb-0">Manage products, multi-select bulk operations, stock levels, and store assignments.</p>
        </div>
        <div class="mb-2">
          <a href="/admin/products/create" class="btn btn-brand-green font-weight-bold">
            <i class="fas fa-plus mr-1"></i> Add Product
          </a>
        </div>
      </div>
    </div>

    <!-- Content -->
    <div class="content">
      <div class="container-fluid">

        @if(session('success'))
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
          </div>
        @endif
        @if(session('error'))
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle mr-2"></i>{{ session('error') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
          </div>
        @endif

        <!-- Stat Summary Cards -->
        <div class="row">
          <div class="col-12 col-sm-6 col-lg-3 mb-3">
            <div class="small-box bg-white border shadow-sm inventory-card h-100">
              <div class="inner pl-3 pt-3">
                <h3 class="text-success font-weight-bold mb-0">{{ number_format($totalProductsCount ?? 0) }}</h3>
                <p class="text-muted font-weight-bold mb-2">Total Listings</p>
              </div>
              <div class="icon text-success opacity-50"><i class="fas fa-cubes"></i></div>
              <a href="/admin/products" class="small-box-footer bg-light text-success font-weight-bold">View All <i class="fas fa-arrow-circle-right ml-1"></i></a>
            </div>
          </div>

          <div class="col-12 col-sm-6 col-lg-3 mb-3">
            <div class="small-box bg-white border shadow-sm inventory-card h-100" style="border-left-color: #10b981;">
              <div class="inner pl-3 pt-3">
                <h3 class="text-success font-weight-bold mb-0">{{ number_format($activeProductsCount ?? 0) }}</h3>
                <p class="text-muted font-weight-bold mb-2">Active Products</p>
              </div>
              <div class="icon text-success opacity-50"><i class="fas fa-check-circle"></i></div>
              <a href="/admin/products" class="small-box-footer bg-light text-success font-weight-bold">Active Catalog <i class="fas fa-arrow-circle-right ml-1"></i></a>
            </div>
          </div>

          <div class="col-12 col-sm-6 col-lg-3 mb-3">
            <div class="small-box bg-white border shadow-sm inventory-card h-100" style="border-left-color: #f59e0b;">
              <div class="inner pl-3 pt-3">
                <h3 class="text-warning font-weight-bold mb-0" style="color: #d97706 !important;">{{ number_format($lowStockCount ?? 0) }}</h3>
                <p class="text-muted font-weight-bold mb-2">Low Stock (≤ 5)</p>
              </div>
              <div class="icon text-warning opacity-50"><i class="fas fa-exclamation-triangle"></i></div>
              <a href="/admin/products?stock_status=low_stock" class="small-box-footer bg-light text-warning font-weight-bold">Filter Low Stock <i class="fas fa-arrow-circle-right ml-1"></i></a>
            </div>
          </div>

          <div class="col-12 col-sm-6 col-lg-3 mb-3">
            <div class="small-box bg-white border shadow-sm inventory-card h-100" style="border-left-color: #ef4444;">
              <div class="inner pl-3 pt-3">
                <h3 class="text-danger font-weight-bold mb-0">{{ number_format($outOfStockCount ?? 0) }}</h3>
                <p class="text-muted font-weight-bold mb-2">Out of Stock (0)</p>
              </div>
              <div class="icon text-danger opacity-50"><i class="fas fa-times-circle"></i></div>
              <a href="/admin/products?stock_status=out_of_stock" class="small-box-footer bg-light text-danger font-weight-bold">Filter Out of Stock <i class="fas fa-arrow-circle-right ml-1"></i></a>
            </div>
          </div>
        </div>

        <!-- Filter & Search Bar -->
        <div class="card card-outline card-success mb-3 shadow-sm">
          <div class="card-header bg-light py-2">
            <h3 class="card-title font-weight-bold text-dark mb-0"><i class="fas fa-filter text-success mr-1"></i> Catalog Search & Filters</h3>
          </div>
          <div class="card-body py-3">
            <form action="/admin/products" method="GET" class="form-row align-items-center">
              <div class="col-12 col-md-3 mb-2">
                <div class="input-group">
                  <div class="input-group-prepend"><span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span></div>
                  <input type="text" name="search" class="form-control" placeholder="Search Title or SKU..." value="{{ request('search') }}">
                </div>
              </div>

              <div class="col-6 col-md-2 mb-2">
                <select name="category_id" class="form-control" onchange="this.form.submit()">
                  <option value="">All Categories</option>
                  @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                  @endforeach
                </select>
              </div>

              <div class="col-6 col-md-2 mb-2">
                <select name="scope" class="form-control" onchange="this.form.submit()">
                  <option value="">All Scopes</option>
                  <option value="global" {{ request('scope') == 'global' ? 'selected' : '' }}>Global Only</option>
                  <option value="store_specific" {{ request('scope') == 'store_specific' ? 'selected' : '' }}>Store Specific</option>
                </select>
              </div>

              <div class="col-6 col-md-2 mb-2">
                <select name="store_id" class="form-control" onchange="this.form.submit()">
                  <option value="">All Stores</option>
                  @foreach($stores as $st)
                    <option value="{{ $st->id }}" {{ request('store_id') == $st->id ? 'selected' : '' }}>{{ $st->name }}</option>
                  @endforeach
                </select>
              </div>

              <div class="col-6 col-md-2 mb-2">
                <select name="stock_status" class="form-control" onchange="this.form.submit()">
                  <option value="">All Stock Status</option>
                  <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>In Stock (>5)</option>
                  <option value="low_stock" {{ request('stock_status') == 'low_stock' ? 'selected' : '' }}>Low Stock (1-5)</option>
                  <option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock (0)</option>
                </select>
              </div>

              <!-- Products Count Per Page Dropdown -->
              <div class="col-6 col-md-1 mb-2">
                <select name="per_page" class="form-control font-weight-bold" onchange="this.form.submit()" title="Items per page">
                  <option value="10" {{ ($perPage ?? '50') == '10' ? 'selected' : '' }}>10 / page</option>
                  <option value="20" {{ ($perPage ?? '50') == '20' ? 'selected' : '' }}>20 / page</option>
                  <option value="50" {{ ($perPage ?? '50') == '50' ? 'selected' : '' }}>50 / page</option>
                  <option value="100" {{ ($perPage ?? '50') == '100' ? 'selected' : '' }}>100 / page</option>
                  <option value="500" {{ ($perPage ?? '50') == '50' ? 'selected' : '' }}>500 / page</option>
                  <option value="all" {{ ($perPage ?? '50') == 'all' ? 'selected' : '' }}>All</option>
                </select>
              </div>

              <div class="col-6 col-md-1 mb-2 d-flex">
                <button type="submit" class="btn btn-brand-green btn-block font-weight-bold" title="Search"><i class="fas fa-search"></i></button>
                @if(request('search') || request('category_id') || request('scope') || request('store_id') || request('stock_status') || (request('per_page') && request('per_page') !== 'all'))
                  <a href="/admin/products" class="btn btn-secondary ml-1" title="Reset Filters"><i class="fas fa-undo"></i></a>
                @endif
              </div>
            </form>
          </div>
        </div>

        <!-- Bulk Multi-Actions Bar -->
        <form id="bulkActionForm" action="/admin/products/bulk-action" method="POST">
          @csrf
          <div class="bulk-toolbar d-flex align-items-center justify-content-between flex-wrap shadow-sm mb-3">
            <div class="d-flex align-items-center flex-wrap my-1">
              <div class="custom-control custom-checkbox mr-3">
                <input type="checkbox" class="custom-control-input" id="selectAllHeader">
                <label class="custom-control-label font-weight-bold text-dark" for="selectAllHeader">Select All</label>
              </div>
              <span class="badge badge-success font-weight-bold px-2 py-1 mr-3" id="selectedCountBadge">0 items selected</span>

              <!-- Bulk Action Options -->
              <div class="input-group input-group-sm mr-2" style="width: 220px;">
                <select name="action" id="bulkActionSelect" class="form-control form-control-sm font-weight-bold" required onchange="handleBulkActionChange(this.value)">
                  <option value="">-- Choose Multi Action --</option>
                  <option value="delete" class="text-danger font-weight-bold">🗑️ Bulk Delete Selected</option>
                  <option value="activate">✔️ Set Active Status</option>
                  <option value="deactivate">🚫 Set Inactive Status</option>
                  <option value="make_global">🌐 Set Scope: Global</option>
                  <option value="update_category">📁 Move to Category...</option>
                  <option value="update_stock">📦 Update Stock Inventory...</option>
                </select>
              </div>

              <!-- Secondary Input for Category Update -->
              <div id="bulkCategoryWrapper" class="mr-2" style="display: none;">
                <select name="target_category_id" class="form-control form-control-sm">
                  <option value="">Select Target Category</option>
                  @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                  @endforeach
                </select>
              </div>

              <!-- Secondary Input for Stock Inventory Update -->
              <div id="bulkStockWrapper" class="mr-2" style="display: none;">
                <input type="number" name="target_stock" class="form-control form-control-sm" placeholder="Set Stock (e.g. 50)" min="0">
              </div>

              <button type="button" id="btnApplyBulk" class="btn btn-brand-green btn-sm font-weight-bold" disabled onclick="triggerBulkSubmit()">
                <i class="fas fa-play mr-1"></i> Apply Multi Action
              </button>
            </div>

            <div class="my-1 text-muted small font-weight-bold">
              Showing <strong>{{ $products->count() }}</strong> product listing(s)
            </div>
          </div>

          <!-- Product Catalog Table -->
          <div class="card card-outline card-success shadow-sm">
            <div class="card-header bg-dark d-flex justify-content-between align-items-center">
              <h3 class="card-title font-weight-bold text-white mb-0"><i class="fas fa-list-alt text-success mr-2"></i> Inventory Listings</h3>
            </div>
            <div class="card-body p-0 table-responsive">
              <table class="table table-hover table-striped table-modern mb-0">
                <thead>
                  <tr>
                    <th style="width: 40px;" class="text-center">
                      <input type="checkbox" id="selectAllTable">
                    </th>
                    <th style="width: 60px;">Image</th>
                    <th>Product Title & SKU</th>
                    <th>Category</th>
                    <th>Scope</th>
                    <th>Assigned Store</th>
                    <th class="text-nowrap">Price</th>
                    <th class="text-nowrap">Stock Level</th>
                    <th class="text-nowrap">Status</th>
                    <th style="width: 120px;" class="text-center text-nowrap">Action</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($products as $prod)
                  <tr>
                    <td class="text-center align-middle">
                      <input type="checkbox" name="ids[]" value="{{ $prod->id }}" class="product-checkbox" onchange="updateSelectedCount()">
                    </td>
                    <td class="align-middle">
                      @php
                        $imgSrc = $prod->image ?? '';
                        if(!str_starts_with($imgSrc, 'http') && !str_starts_with($imgSrc, 'uploads/')) {
                            $imgSrc = 'assets/images/' . $imgSrc;
                        }
                      @endphp
                      <img src="{{ asset($imgSrc) }}" alt="{{ $prod->name }}" class="product-img-thumb" onerror="this.onerror=null;this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'44\' height=\'44\' viewBox=\'0 0 44 44\'><rect width=\'44\' height=\'44\' fill=\'%23e2e8f0\'/><text x=\'50%\' y=\'55%\' dominant-baseline=\'middle\' text-anchor=\'middle\' fill=\'%2364748b\' font-size=\'9\'>No Image</text></svg>';">
                    </td>
                    <td class="align-middle">
                      <a href="/admin/products/{{ $prod->id }}/edit" class="font-weight-bold text-dark" title="Click to Edit Listing">
                        {{ $prod->name }}
                      </a>
                      <div class="small text-muted">
                        SKU: <code class="text-success font-weight-bold">{{ $prod->sku }}</code> | Unit: <span>{{ $prod->unit }}</span>
                      </div>
                    </td>
                    <td class="align-middle">
                      <span class="badge badge-light border text-dark">{{ $prod->category_name ?? 'Uncategorized' }}</span>
                    </td>
                    <td class="align-middle text-nowrap">
                      @if($prod->scope === 'global')
                        <span class="badge badge-success"><i class="fas fa-globe mr-1"></i> Global</span>
                      @else
                        <span class="badge badge-primary"><i class="fas fa-store mr-1"></i> Store Specific</span>
                      @endif
                      @if(!empty($prod->is_featured))
                        <span class="badge badge-warning text-dark ml-1"><i class="fas fa-star mr-1"></i> Featured</span>
                      @endif
                    </td>
                    <td class="align-middle small text-muted font-weight-bold">
                      {{ $prod->store_name ?? 'All Stores (Global)' }}
                    </td>
                    <!-- Price Column (No MRP column) -->
                    <td class="align-middle text-success font-weight-bold text-nowrap">
                      ₹{{ number_format($prod->price, 2) }}
                    </td>
                    <!-- Stock Level -->
                    <td class="align-middle text-nowrap">
                      @if($prod->stock <= 0)
                        <span class="badge badge-stock-out px-2 py-1"><i class="fas fa-times-circle mr-1"></i> Out of Stock (0)</span>
                      @elseif($prod->stock <= 5)
                        <span class="badge badge-stock-low px-2 py-1"><i class="fas fa-exclamation-triangle mr-1"></i> Low Stock ({{ $prod->stock }})</span>
                      @else
                        <span class="badge badge-stock-active px-2 py-1"><i class="fas fa-check-circle mr-1"></i> {{ $prod->stock }} pcs</span>
                      @endif

                      <button type="button" class="btn btn-link btn-xs text-success p-0 ml-1" title="Quick Stock Update" onclick="openQuickUpdateModal({{ $prod->id }}, {{ $prod->stock }}, {{ $prod->price }})">
                        <i class="fas fa-edit"></i>
                      </button>
                    </td>
                    <td class="align-middle text-nowrap">
                      @if(!isset($prod->is_active) || $prod->is_active)
                        <span class="badge badge-success">Active</span>
                      @else
                        <span class="badge badge-secondary">Inactive</span>
                      @endif
                    </td>
                    <!-- Action Dropdown Button -->
                    <td class="align-middle text-center text-nowrap">
                      <div class="dropdown">
                        <button class="btn btn-sm btn-outline-success dropdown-toggle font-weight-bold px-2 py-1" type="button" data-toggle="dropdown" aria-expanded="false">
                          Actions
                        </button>
                        <div class="dropdown-menu dropdown-menu-right shadow-sm">
                          <a class="dropdown-item font-weight-bold text-dark" href="/admin/products/{{ $prod->id }}/edit">
                            <i class="fas fa-edit text-primary mr-2"></i> Edit Details
                          </a>
                          <a class="dropdown-item font-weight-bold text-dark" href="javascript:void(0);" onclick="openQuickUpdateModal({{ $prod->id }}, {{ $prod->stock }}, {{ $prod->price }})">
                            <i class="fas fa-bolt text-warning mr-2"></i> Quick Update
                          </a>
                          <div class="dropdown-divider"></div>
                          <a class="dropdown-item font-weight-bold text-danger" href="javascript:void(0);" onclick="openDeleteModal({{ $prod->id }}, '{{ addslashes($prod->name) }}', '{{ $prod->sku }}')">
                            <i class="fas fa-trash-alt mr-2"></i> Delete Product
                          </a>
                        </div>
                      </div>
                    </td>
                  </tr>
                  @empty
                  <tr>
                    <td colspan="10" class="text-center py-5 text-muted">
                      <i class="fas fa-box-open fa-3x mb-3 text-secondary"></i>
                      <h5>No product listings match your criteria.</h5>
                      <a href="/admin/products/create" class="btn btn-brand-green btn-sm font-weight-bold mt-2">
                        <i class="fas fa-plus mr-1"></i> Create First Listing
                      </a>
                    </td>
                  </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </form>

      </div>
    </div>
  </div>

  <!-- Single Product Delete Confirmation Modal -->
  <div class="modal fade" id="deleteConfirmModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title font-weight-bold"><i class="fas fa-exclamation-triangle mr-2"></i> Confirm Product Deletion</h5>
          <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <div class="modal-body">
          <p class="mb-2 font-weight-bold text-dark">Are you sure you want to delete this product?</p>
          <div class="alert alert-light border">
            <div class="font-weight-bold text-danger" id="deleteProductName">Product Name</div>
            <div class="small text-muted">SKU: <span id="deleteProductSku">SKU-000</span> | ID: #<span id="deleteProductId">0</span></div>
          </div>
          <small class="text-muted"><i class="fas fa-info-circle mr-1"></i> This action cannot be undone and will remove the item from all stores.</small>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <form id="singleDeleteForm" action="" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-danger font-weight-bold"><i class="fas fa-trash-alt mr-1"></i> Confirm Delete</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Bulk Action Confirmation Modal -->
  <div class="modal fade" id="bulkConfirmModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header bg-warning text-dark" id="bulkModalHeader">
          <h5 class="modal-title font-weight-bold"><i class="fas fa-layer-group mr-2"></i> Confirm Bulk Action</h5>
          <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <div class="modal-body">
          <p class="mb-2 font-weight-bold text-dark" id="bulkConfirmText">Are you sure you want to execute this bulk action?</p>
          <div class="alert alert-light border">
            Selected Items Count: <strong id="bulkConfirmCount" class="text-success">0</strong>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-success font-weight-bold" id="btnSubmitBulkModal" onclick="submitBulkForm()">Proceed</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Quick Stock / Price Inline Modal -->
  <div class="modal fade" id="quickUpdateModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <form action="/admin/products/quick-update" method="POST">
          @csrf
          <input type="hidden" name="id" id="quickProdId">
          <div class="modal-header bg-dark text-white">
            <h5 class="modal-title font-weight-bold"><i class="fas fa-bolt text-success mr-2"></i> Quick Inventory Update</h5>
            <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label class="font-weight-bold">Stock Inventory Quantity (pcs):</label>
              <input type="number" name="stock" id="quickStockInput" class="form-control" min="0" required>
            </div>
            <div class="form-group">
              <label class="font-weight-bold">Listing Price (₹):</label>
              <input type="number" step="0.01" name="price" id="quickPriceInput" class="form-control" min="0" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-brand-green font-weight-bold">Save Changes</button>
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

<script>
  // Sync header & table check all boxes
  $('#selectAllHeader, #selectAllTable').on('change', function() {
    const isChecked = $(this).is(':checked');
    $('#selectAllHeader, #selectAllTable').prop('checked', isChecked);
    $('.product-checkbox').prop('checked', isChecked);
    updateSelectedCount();
  });

  function updateSelectedCount() {
    const selectedCount = $('.product-checkbox:checked').length;
    $('#selectedCountBadge').text(selectedCount + ' items selected');
    if (selectedCount > 0) {
      $('#btnApplyBulk').prop('disabled', false);
    } else {
      $('#btnApplyBulk').prop('disabled', true);
    }
  }

  function handleBulkActionChange(val) {
    if (val === 'update_category') {
      $('#bulkCategoryWrapper').show();
      $('#bulkStockWrapper').hide();
    } else if (val === 'update_stock') {
      $('#bulkStockWrapper').show();
      $('#bulkCategoryWrapper').hide();
    } else {
      $('#bulkCategoryWrapper').hide();
      $('#bulkStockWrapper').hide();
    }
  }

  function triggerBulkSubmit() {
    const action = $('#bulkActionSelect').val();
    const count = $('.product-checkbox:checked').length;
    if (!action) {
      alert('Please select a valid multi action.');
      return;
    }
    if (count === 0) {
      alert('Please select at least one product checkbox.');
      return;
    }

    $('#bulkConfirmCount').text(count);
    if (action === 'delete') {
      $('#bulkModalHeader').removeClass('bg-warning bg-success').addClass('bg-danger text-white');
      $('#bulkConfirmText').html(`⚠️ Are you sure you want to <strong class="text-danger">PERMANENTLY DELETE</strong> ${count} selected product(s)?`);
      $('#btnSubmitBulkModal').removeClass('btn-success').addClass('btn-danger').text('Confirm Delete All');
    } else {
      $('#bulkModalHeader').removeClass('bg-danger text-white').addClass('bg-success text-white');
      $('#bulkConfirmText').html(`Are you sure you want to perform action <strong>'${action}'</strong> on ${count} item(s)?`);
      $('#btnSubmitBulkModal').removeClass('btn-danger').addClass('btn-success').text('Confirm Action');
    }
    $('#bulkConfirmModal').modal('show');
  }

  function submitBulkForm() {
    $('#bulkConfirmModal').modal('hide');
    document.getElementById('bulkActionForm').submit();
  }

  function openDeleteModal(id, name, sku) {
    $('#deleteProductId').text(id);
    $('#deleteProductName').text(name);
    $('#deleteProductSku').text(sku);
    document.getElementById('singleDeleteForm').action = `/admin/products/delete/${id}`;
    $('#deleteConfirmModal').modal('show');
  }

  function openQuickUpdateModal(id, stock, price) {
    $('#quickProdId').val(id);
    $('#quickStockInput').val(stock);
    $('#quickPriceInput').val(price);
    $('#quickUpdateModal').modal('show');
  }
</script>
</body>
</html>
