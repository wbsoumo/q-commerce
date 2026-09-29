<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Amazon-Style Inventory Management | Q-Commerce Admin</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  <style>
    .inventory-card { transition: all 0.2s ease-in-out; border-radius: 8px; }
    .inventory-card:hover { transform: translateY(-3px); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    .table-amazon th { background-color: #232f3e; color: #ffffff; border: none; vertical-align: middle; }
    .table-amazon td { vertical-align: middle; }
    .product-img-thumb { width: 44px; height: 44px; object-fit: contain; border-radius: 4px; border: 1px solid #e0e0e0; background: #fff; padding: 2px; }
    .badge-amazon-active { background-color: #007600; color: #fff; }
    .badge-amazon-lowstock { background-color: #b12704; color: #fff; }
    .badge-amazon-outstock { background-color: #d9534f; color: #fff; }
    .bulk-toolbar { background: #f7f9fa; border: 1px solid #d5dbdb; border-radius: 6px; padding: 10px 16px; margin-bottom: 16px; }
  </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
      <li class="nav-item"><a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a></li>
      <li class="nav-item d-none d-sm-inline-block"><a href="/admin" class="nav-link">Dashboard</a></li>
      <li class="nav-item d-none d-sm-inline-block"><a href="/admin/products" class="nav-link active font-weight-bold"><i class="fas fa-boxes mr-1"></i> Inventory Management</a></li>
    </ul>
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <a class="btn btn-warning btn-sm font-weight-bold" style="background-color: #ff9900; border-color: #e68a00; color: #111;" href="/admin/products/create">
          <i class="fas fa-plus-circle mr-1"></i> Add New Product
        </a>
      </li>
    </ul>
  </nav>

  @include('admin.layouts.sidebar')

  <div class="content-wrapper">
    <!-- Header -->
    <div class="content-header">
      <div class="container-fluid d-flex justify-content-between align-items-center">
        <div>
          <h1 class="m-0 font-weight-bold text-dark"><i class="fas fa-boxes text-warning mr-2"></i>Amazon Inventory Management</h1>
          <p class="text-muted small mb-0">Manage multi-store catalog, bulk actions, real-time stock levels, and store assignments.</p>
        </div>
        <a href="/admin/products/create" class="btn font-weight-bold text-dark" style="background-color: #ff9900; border-color: #e68a00;">
          <i class="fas fa-plus mr-1"></i> Add Product
        </a>
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

        <!-- Stat Metric Cards (Amazon Seller Style) -->
        <div class="row">
          <div class="col-lg-3 col-6">
            <div class="small-box bg-white border shadow-sm inventory-card">
              <div class="inner pl-3 pt-3">
                <h3 class="text-primary font-weight-bold mb-0">{{ number_format($totalProductsCount ?? 0) }}</h3>
                <p class="text-muted font-weight-bold mb-2">Total Listings</p>
              </div>
              <div class="icon text-primary opacity-50"><i class="fas fa-cubes"></i></div>
              <a href="/admin/products" class="small-box-footer bg-light text-muted">View All <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="small-box bg-white border shadow-sm inventory-card">
              <div class="inner pl-3 pt-3">
                <h3 class="text-success font-weight-bold mb-0">{{ number_format($activeProductsCount ?? 0) }}</h3>
                <p class="text-muted font-weight-bold mb-2">Active Products</p>
              </div>
              <div class="icon text-success opacity-50"><i class="fas fa-check-circle"></i></div>
              <a href="/admin/products" class="small-box-footer bg-light text-muted">Active Catalog <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="small-box bg-white border shadow-sm inventory-card">
              <div class="inner pl-3 pt-3">
                <h3 class="text-warning font-weight-bold mb-0" style="color: #b12704 !important;">{{ number_format($lowStockCount ?? 0) }}</h3>
                <p class="text-muted font-weight-bold mb-2">Low Stock Alerts (≤ 5)</p>
              </div>
              <div class="icon text-warning opacity-50"><i class="fas fa-exclamation-triangle"></i></div>
              <a href="/admin/products?stock_status=low_stock" class="small-box-footer bg-light text-muted">Filter Low Stock <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="small-box bg-white border shadow-sm inventory-card">
              <div class="inner pl-3 pt-3">
                <h3 class="text-danger font-weight-bold mb-0">{{ number_format($outOfStockCount ?? 0) }}</h3>
                <p class="text-muted font-weight-bold mb-2">Out of Stock (0)</p>
              </div>
              <div class="icon text-danger opacity-50"><i class="fas fa-times-circle"></i></div>
              <a href="/admin/products?stock_status=out_of_stock" class="small-box-footer bg-light text-muted">Filter Out of Stock <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
        </div>

        <!-- Filter & Search Bar -->
        <div class="card card-outline card-warning mb-3 shadow-sm">
          <div class="card-header bg-light py-2">
            <h3 class="card-title font-weight-bold text-dark mb-0"><i class="fas fa-filter text-warning mr-1"></i> Amazon Catalog Search & Advanced Filters</h3>
          </div>
          <div class="card-body py-3">
            <form action="/admin/products" method="GET" class="form-row align-items-center">
              <div class="col-md-3 mb-2">
                <div class="input-group">
                  <div class="input-group-prepend"><span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span></div>
                  <input type="text" name="search" class="form-control" placeholder="Search Title or SKU..." value="{{ request('search') }}">
                </div>
              </div>

              <div class="col-md-2 mb-2">
                <select name="category_id" class="form-control" onchange="this.form.submit()">
                  <option value="">All Categories</option>
                  @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                  @endforeach
                </select>
              </div>

              <div class="col-md-2 mb-2">
                <select name="scope" class="form-control" onchange="this.form.submit()">
                  <option value="">All Scopes</option>
                  <option value="global" {{ request('scope') == 'global' ? 'selected' : '' }}>Global Only</option>
                  <option value="store_specific" {{ request('scope') == 'store_specific' ? 'selected' : '' }}>Store Specific Only</option>
                </select>
              </div>

              <div class="col-md-2 mb-2">
                <select name="store_id" class="form-control" onchange="this.form.submit()">
                  <option value="">All Stores</option>
                  @foreach($stores as $st)
                    <option value="{{ $st->id }}" {{ request('store_id') == $st->id ? 'selected' : '' }}>{{ $st->name }}</option>
                  @endforeach
                </select>
              </div>

              <div class="col-md-2 mb-2">
                <select name="stock_status" class="form-control" onchange="this.form.submit()">
                  <option value="">All Stock Status</option>
                  <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>In Stock (>5)</option>
                  <option value="low_stock" {{ request('stock_status') == 'low_stock' ? 'selected' : '' }}>Low Stock (1-5)</option>
                  <option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock (0)</option>
                </select>
              </div>

              <div class="col-md-1 mb-2 d-flex">
                <button type="submit" class="btn btn-warning btn-block font-weight-bold" style="background-color: #ff9900; border-color: #e68a00;" title="Search"><i class="fas fa-search"></i></button>
                @if(request('search') || request('category_id') || request('scope') || request('store_id') || request('stock_status'))
                  <a href="/admin/products" class="btn btn-secondary ml-1" title="Reset Filters"><i class="fas fa-undo"></i></a>
                @endif
              </div>
            </form>
          </div>
        </div>

        <!-- Bulk Multi-Actions Bar -->
        <form id="bulkActionForm" action="/admin/products/bulk-action" method="POST">
          @csrf
          <div class="bulk-toolbar d-flex align-items-center justify-content-between flex-wrap shadow-sm">
            <div class="d-flex align-items-center flex-wrap my-1">
              <div class="custom-control custom-checkbox mr-3">
                <input type="checkbox" class="custom-control-input" id="selectAllHeader">
                <label class="custom-control-label font-weight-bold text-dark" for="selectAllHeader">Select All</label>
              </div>
              <span class="badge badge-warning text-dark font-weight-bold px-2 py-1 mr-3" id="selectedCountBadge">0 items selected</span>

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

              <button type="submit" id="btnApplyBulk" class="btn btn-warning btn-sm font-weight-bold" style="background-color: #ff9900; border-color: #e68a00;" disabled onclick="return confirmBulkAction()">
                <i class="fas fa-play mr-1"></i> Apply Multi Action
              </button>
            </div>

            <div class="my-1 text-muted small">
              Showing <strong>{{ $products->count() }}</strong> product listing(s)
            </div>
          </div>

          <!-- Product Catalog Table -->
          <div class="card card-outline card-dark shadow-sm">
            <div class="card-header bg-dark d-flex justify-content-between align-items-center">
              <h3 class="card-title font-weight-bold text-white mb-0"><i class="fas fa-list-alt text-warning mr-2"></i> Amazon Inventory Listing Table</h3>
            </div>
            <div class="card-body p-0 table-responsive">
              <table class="table table-hover table-striped table-amazon mb-0">
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
                    <th>Price</th>
                    <th>Stock Level</th>
                    <th>Status</th>
                    <th style="width: 140px;" class="text-center">Actions</th>
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
                      <img src="{{ asset($imgSrc) }}" alt="{{ $prod->name }}" class="product-img-thumb" onerror="this.src='https://via.placeholder.com/44?text=Product';">
                    </td>
                    <td class="align-middle">
                      <a href="/admin/products/{{ $prod->id }}/edit" class="font-weight-bold text-dark" title="Click to Edit Listing">
                        {{ $prod->name }}
                      </a>
                      <div class="small text-muted">
                        SKU: <code class="text-primary font-weight-bold">{{ $prod->sku }}</code> | Unit: <span>{{ $prod->unit }}</span>
                      </div>
                    </td>
                    <td class="align-middle">
                      <span class="badge badge-light border text-dark">{{ $prod->category_name ?? 'Uncategorized' }}</span>
                    </td>
                    <td class="align-middle">
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
                    <!-- Price Column (MRP column removed per request) -->
                    <td class="align-middle text-success font-weight-bold">
                      ₹{{ number_format($prod->price, 2) }}
                    </td>
                    <!-- Stock Level with status indicator -->
                    <td class="align-middle">
                      @if($prod->stock <= 0)
                        <span class="badge badge-amazon-outstock px-2 py-1"><i class="fas fa-times-circle mr-1"></i> Out of Stock (0)</span>
                      @elseif($prod->stock <= 5)
                        <span class="badge badge-amazon-lowstock px-2 py-1"><i class="fas fa-exclamation-triangle mr-1"></i> Low Stock ({{ $prod->stock }})</span>
                      @else
                        <span class="badge badge-amazon-active px-2 py-1"><i class="fas fa-check-circle mr-1"></i> {{ $prod->stock }} pcs</span>
                      @endif

                      <button type="button" class="btn btn-link btn-xs text-primary p-0 ml-1" title="Quick Stock Update" onclick="openQuickUpdateModal({{ $prod->id }}, {{ $prod->stock }}, {{ $prod->price }})">
                        <i class="fas fa-edit"></i>
                      </button>
                    </td>
                    <td class="align-middle">
                      @if(!isset($prod->is_active) || $prod->is_active)
                        <span class="badge badge-success">Active</span>
                      @else
                        <span class="badge badge-secondary">Inactive</span>
                      @endif
                    </td>
                    <!-- Actions Column with Delete & Edit -->
                    <td class="align-middle text-center">
                      <div class="btn-group btn-group-sm">
                        <a href="/admin/products/{{ $prod->id }}/edit" class="btn btn-default text-primary" title="Edit Product">
                          <i class="fas fa-edit"></i>
                        </a>
                        <button type="button" class="btn btn-default text-danger" title="Delete Product" onclick="confirmDeleteProduct({{ $prod->id }}, '{{ addslashes($prod->name) }}')">
                          <i class="fas fa-trash-alt"></i>
                        </button>
                      </div>
                    </td>
                  </tr>
                  @empty
                  <tr>
                    <td colspan="10" class="text-center py-5 text-muted">
                      <i class="fas fa-box-open fa-3x mb-3 text-secondary"></i>
                      <h5>No product listings match your criteria.</h5>
                      <a href="/admin/products/create" class="btn btn-warning btn-sm font-weight-bold mt-2" style="background-color: #ff9900; border-color: #e68a00;">
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

  <!-- Single Product Delete Form Hidden -->
  <form id="singleDeleteForm" action="" method="POST" style="display:none;">
    @csrf
  </form>

  <!-- Quick Stock / Price Inline Modal -->
  <div class="modal fade" id="quickUpdateModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <form action="/admin/products/quick-update" method="POST">
          @csrf
          <input type="hidden" name="id" id="quickProdId">
          <div class="modal-header bg-dark text-white">
            <h5 class="modal-title font-weight-bold"><i class="fas fa-bolt text-warning mr-2"></i> Amazon Quick Inventory Update</h5>
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
            <button type="submit" class="btn btn-warning font-weight-bold" style="background-color: #ff9900; border-color: #e68a00;">Save Quick Changes</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <footer class="main-footer"><strong>Copyright &copy; 2026 Q-Commerce Admin | Amazon Inventory Engine.</strong></footer>
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

  function confirmBulkAction() {
    const action = $('#bulkActionSelect').val();
    const count = $('.product-checkbox:checked').length;
    if (!action) {
      alert('Please select a valid multi action.');
      return false;
    }
    if (count === 0) {
      alert('Please select at least one product checkbox.');
      return false;
    }
    if (action === 'delete') {
      return confirm(`⚠️ Are you sure you want to PERMANENTLY DELETE ${count} selected product(s)? This action cannot be undone!`);
    }
    return confirm(`Are you sure you want to perform bulk action '${action}' on ${count} item(s)?`);
  }

  function confirmDeleteProduct(id, name) {
    if (confirm(`⚠️ Are you sure you want to delete '${name}'?`)) {
      const form = document.getElementById('singleDeleteForm');
      form.action = `/admin/products/delete/${id}`;
      form.submit();
    }
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
