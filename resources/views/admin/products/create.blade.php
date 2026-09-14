<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Q-Commerce Admin | Create Product</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  <script>
    function toggleStoreSelect() {
      var scope = document.getElementById('productScope').value;
      var storeGroup = document.getElementById('storeSelectGroup');
      if (scope === 'store_specific') {
        storeGroup.style.display = 'block';
      } else {
        storeGroup.style.display = 'none';
      }
    }
  </script>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
      <li class="nav-item"><a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a></li>
      <li class="nav-item d-none d-sm-inline-block"><a href="/admin/products" class="nav-link">Products</a></li>
      <li class="nav-item d-none d-sm-inline-block"><a href="#" class="nav-link active font-weight-bold">Add Product</a></li>
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
          <li class="nav-item"><a href="/admin/products" class="nav-link active"><i class="nav-icon fas fa-boxes"></i><p>Product Catalog</p></a></li>
        </ul>
      </nav>
    </div>
  </aside>

  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <h1 class="m-0 font-weight-bold">Add Product & Set Scope (Global vs Store Specific)</h1>
      </div>
    </div>

    <div class="content">
      <div class="container-fluid">
        <div class="card card-success">
          <div class="card-header"><h3 class="card-title font-weight-bold">Product Form</h3></div>
          <form action="/admin/products/store" method="POST">
            @csrf
            <div class="card-body">
              <div class="row">
                <div class="col-md-6 form-group">
                  <label>Product Name</label>
                  <input type="text" name="name" class="form-control" placeholder="Product Name" required>
                </div>
                <div class="col-md-6 form-group">
                  <label>Category</label>
                  <select name="category_id" class="form-control" required>
                    @foreach($categories as $cat)
                      <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                  </select>
                </div>
              </div>

              <!-- SCOPE SELECTION (Global vs Store-Specific) -->
              <div class="row bg-light p-3 rounded mb-3 border">
                <div class="col-md-6 form-group mb-0">
                  <label class="text-success font-weight-bold"><i class="fas fa-globe mr-1"></i> Product Scope</label>
                  <select name="scope" id="productScope" class="form-control font-weight-bold" onchange="toggleStoreSelect()" required>
                    <option value="global">Global (Available in ALL Stores)</option>
                    <option value="store_specific">Store Specific (Available ONLY in selected store)</option>
                  </select>
                  <small class="form-text text-muted">Global products appear everywhere. Store specific products only appear in assigned store.</small>
                </div>
                <div class="col-md-6 form-group mb-0" id="storeSelectGroup" style="display: none;">
                  <label class="text-primary font-weight-bold"><i class="fas fa-store mr-1"></i> Assign to Specific Store</label>
                  <select name="store_id" class="form-control font-weight-bold">
                    @foreach($stores as $st)
                      <option value="{{ $st->id }}">{{ $st->name }} ({{ $st->city }})</option>
                    @endforeach
                  </select>
                </div>
              </div>

              <div class="row">
                <div class="col-md-4 form-group">
                  <label>SKU Code</label>
                  <input type="text" name="sku" class="form-control" placeholder="SKU-1001" required>
                </div>
                <div class="col-md-4 form-group">
                  <label>Unit / Quantity</label>
                  <input type="text" name="unit" class="form-control" value="1 pack" required>
                </div>
                <div class="col-md-4 form-group">
                  <label>Stock Count</label>
                  <input type="number" name="stock" class="form-control" value="100" required>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 form-group">
                  <label>Selling Price (₹)</label>
                  <input type="number" step="0.01" name="price" class="form-control" placeholder="99.00" required>
                </div>
                <div class="col-md-6 form-group">
                  <label>MRP (₹)</label>
                  <input type="number" step="0.01" name="mrp" class="form-control" placeholder="120.00" required>
                </div>
              </div>
            </div>
            <div class="card-footer">
              <button type="submit" class="btn btn-success font-weight-bold"><i class="fas fa-save mr-1"></i> Save Product</button>
              <a href="/admin/products" class="btn btn-default">Cancel</a>
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
