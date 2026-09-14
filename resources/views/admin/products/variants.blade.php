<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Product Variants | {{ $product->name }}</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
      <li class="nav-item"><a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a></li>
      <li class="nav-item d-none d-sm-inline-block"><a href="/admin/products" class="nav-link">Products</a></li>
      <li class="nav-item d-none d-sm-inline-block"><a href="#" class="nav-link active font-weight-bold">Variants for {{ $product->name }}</a></li>
    </ul>
  </nav>

  <aside class="main-sidebar sidebar-dark-primary elevation-4 position-fixed">
    <a href="/admin" class="brand-link text-center" style="background:#0c831f">
      <span class="brand-text font-weight-bold text-white"><i class="fas fa-bolt mr-2"></i>Q-Commerce</span>
    </a>
    <div class="sidebar">
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column">
          <li class="nav-item"><a href="/admin" class="nav-link"><i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p></a></li>
          <li class="nav-item"><a href="/admin/products" class="nav-link active"><i class="nav-icon fas fa-boxes"></i><p>Products</p></a></li>
        </ul>
      </nav>
    </div>
  </aside>

  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid d-flex justify-content-between align-items-center">
        <h1 class="m-0 font-weight-bold">Manage Product Variants (Weight/Pack Sizes)</h1>
        <button type="button" class="btn btn-success font-weight-bold" data-toggle="modal" data-target="#variantModal">
          <i class="fas fa-plus mr-1"></i> Add Variant
        </button>
      </div>
    </div>

    <div class="content">
      <div class="container-fluid">
        @if(session('success'))
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
          </div>
        @endif

        <div class="card card-outline card-warning">
          <div class="card-header"><h3 class="card-title font-weight-bold"><i class="fas fa-tags mr-2"></i>Variants for {{ $product->name }}</h3></div>
          <div class="card-body p-0">
            <table class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th>#ID</th>
                  <th>Variant Name</th>
                  <th>SKU</th>
                  <th>Unit</th>
                  <th>Selling Price</th>
                  <th>MRP</th>
                  <th>Stock</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                @forelse($variants as $var)
                <tr>
                  <td>{{ $var->id }}</td>
                  <td class="font-weight-bold">{{ $var->variant_name }}</td>
                  <td><code>{{ $var->sku }}</code></td>
                  <td>{{ $var->unit }}</td>
                  <td class="text-success font-weight-bold">₹{{ $var->price }}</td>
                  <td><del class="text-muted">₹{{ $var->mrp }}</del></td>
                  <td><span class="badge badge-secondary">{{ $var->stock }} pcs</span></td>
                  <td><span class="badge badge-success">Active</span></td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center py-4">No variants created for this product. Base product details are being used.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Variant Modal -->
  <div class="modal fade" id="variantModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <form action="/admin/products/{{ $product->id }}/variants/store" method="POST">
          @csrf
          <div class="modal-header bg-success text-white">
            <h5 class="modal-title font-weight-bold"><i class="fas fa-plus mr-1"></i> Create Product Variant</h5>
            <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label>Variant Name (e.g. 500ml, 1L, 5kg)</label>
              <input type="text" name="variant_name" class="form-control" placeholder="1 Liter Pack" required>
            </div>
            <div class="form-group">
              <label>SKU</label>
              <input type="text" name="sku" class="form-control" value="{{ $product->sku }}-VAR-{{ rand(10,99) }}" required>
            </div>
            <div class="row">
              <div class="col-md-6 form-group">
                <label>Selling Price (₹)</label>
                <input type="number" step="0.01" name="price" class="form-control" placeholder="65.00" required>
              </div>
              <div class="col-md-6 form-group">
                <label>MRP (₹)</label>
                <input type="number" step="0.01" name="mrp" class="form-control" placeholder="75.00" required>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6 form-group">
                <label>Stock Count</label>
                <input type="number" name="stock" class="form-control" value="50" required>
              </div>
              <div class="col-md-6 form-group">
                <label>Unit / Pack Size</label>
                <input type="text" name="unit" class="form-control" value="1 L" required>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-success font-weight-bold">Save Variant</button>
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
