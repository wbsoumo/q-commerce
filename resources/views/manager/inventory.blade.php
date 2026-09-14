<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Live Inventory & Pricing | {{ $store->name ?? 'Branch' }}</title>
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
          <h1 class="m-0 font-weight-bold text-dark"><i class="fas fa-boxes text-info mr-2"></i>Live Inventory & Pricing</h1>
          <p class="text-muted mb-0"><i class="fas fa-store text-primary mr-1"></i> {{ $store->name }} (Code: {{ $store->code }})</p>
        </div>
        <div>
          <button type="button" class="btn btn-success font-weight-bold mr-2" data-toggle="modal" data-target="#addStoreProductModal">
            <i class="fas fa-plus-circle mr-1"></i> Add New Product to Store
          </button>
          <span class="badge badge-{{ ($store->status ?? 'Active') === 'Active' ? 'success' : 'danger' }} p-2" style="font-size: 1rem;">
            <i class="fas fa-circle mr-1" style="font-size: 0.65rem;"></i> {{ $store->status ?? 'Active' }}
          </span>
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

        <div class="card card-outline card-primary shadow-sm mb-4">
          <div class="card-header bg-primary text-white">
            <h3 class="card-title font-weight-bold my-1"><i class="fas fa-boxes mr-2"></i>Branch Product Price & Stock Overrides</h3>
          </div>
          <div class="card-body">
            <form action="/manager/inventory" method="GET" class="row mb-3">
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
                    <td class="font-weight-bold align-middle">
                      <a href="/admin/products/{{ $prod->id }}/edit" class="text-primary font-weight-bold" title="Click to Edit Product">
                        {{ $prod->name }} <i class="fas fa-external-link-alt text-muted small ml-1"></i>
                      </a>
                      <br><small class="text-muted">SKU: {{ $prod->sku }}</small>
                    </td>
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

      </div>
    </div>
  </div>

  <!-- ADD PRODUCT TO STORE MODAL FOR MANAGERS -->
  <div class="modal fade" id="addStoreProductModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title font-weight-bold"><i class="fas fa-plus-circle mr-2"></i>Add New Product to {{ $store->name }}</h5>
          <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <form action="/manager/products/store" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6 form-group">
                <label class="font-weight-bold">Product Name</label>
                <input type="text" name="name" class="form-control" placeholder="Fresh Amul Butter 500g" required>
              </div>
              <div class="col-md-6 form-group">
                <label class="font-weight-bold">Category</label>
                <select name="category_id" class="form-control" required>
                  @php
                    $cats = DB::table('categories')->where('is_active', true)->get();
                  @endphp
                  @foreach($cats as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                  @endforeach
                </select>
              </div>
            </div>

            <div class="row">
              <div class="col-md-4 form-group">
                <label class="font-weight-bold">SKU Code</label>
                <input type="text" name="sku" class="form-control" placeholder="SKU-{{ time() }}" required>
              </div>
              <div class="col-md-4 form-group">
                <label class="font-weight-bold">Unit / Quantity</label>
                <input type="text" name="unit" class="form-control" value="1 pack" required>
              </div>
              <div class="col-md-4 form-group">
                <label class="font-weight-bold">Initial Branch Stock</label>
                <input type="number" name="stock" class="form-control" value="50" required>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 form-group">
                <label class="font-weight-bold">Selling Price (₹)</label>
                <input type="number" step="0.01" name="price" class="form-control" placeholder="199.00" required>
              </div>
              <div class="col-md-6 form-group">
                <label class="font-weight-bold">MRP (₹)</label>
                <input type="number" step="0.01" name="mrp" class="form-control" placeholder="220.00" required>
              </div>
            </div>

            <!-- MAIN IMAGE & GALLERY -->
            <div class="card card-outline card-info p-3 mb-3 border">
              <h6 class="font-weight-bold text-info mb-2"><i class="fas fa-images mr-1"></i> Main Image & Gallery Upload</h6>
              <div class="row">
                <div class="col-md-6 form-group mb-0">
                  <label>Main Product Image File</label>
                  <input type="file" name="image_file" class="form-control-file border p-1 rounded w-100">
                </div>
                <div class="col-md-6 form-group mb-0">
                  <label>Gallery Files (Select Multiple)</label>
                  <input type="file" name="gallery_files[]" class="form-control-file border p-1 rounded w-100" multiple>
                </div>
              </div>
            </div>

            <div class="form-group">
              <label class="font-weight-bold">Product Description / Notes</label>
              <textarea name="description" class="form-control" rows="3" placeholder="Enter product details..."></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-success font-weight-bold"><i class="fas fa-save mr-1"></i> Add Product to Store Catalog</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          </div>
        </form>
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
