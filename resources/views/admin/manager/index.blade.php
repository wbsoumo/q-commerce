<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Store Manager Portal | {{ $store->name ?? 'Store Portal' }}</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
      <li class="nav-item"><a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a></li>
      <li class="nav-item d-none d-sm-inline-block"><a href="/admin" class="nav-link">Main Admin</a></li>
      <li class="nav-item d-none d-sm-inline-block"><a href="#" class="nav-link active font-weight-bold">Store Manager Portal</a></li>
    </ul>
  </nav>

  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="/admin/store-manager" class="brand-link text-center" style="background:#007bff">
      <span class="brand-text font-weight-bold text-white"><i class="fas fa-user-cog mr-2"></i>Store Manager</span>
    </a>
    <div class="sidebar">
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column">
          <li class="nav-item"><a href="/admin" class="nav-link"><i class="nav-icon fas fa-arrow-left"></i><p>Back to Main Admin</p></a></li>
          <li class="nav-item"><a href="/admin/store-manager" class="nav-link active"><i class="nav-icon fas fa-boxes"></i><p>Store Inventory Manager</p></a></li>
        </ul>
      </nav>
    </div>
  </aside>

  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid d-flex justify-content-between align-items-center">
        <div>
          <h1 class="m-0 font-weight-bold text-primary">Store Manager Portal</h1>
          <p class="text-muted mb-0">Currently Managing: <strong class="text-dark">{{ $store->name ?? 'Store' }} ({{ $store->city ?? '' }})</strong></p>
        </div>

        <!-- Store Switcher Dropdown for Testing -->
        <form action="/admin/store-manager" method="GET" class="form-inline">
          <label class="mr-2 font-weight-bold">Switch Store:</label>
          <select name="store_id" class="form-control" onchange="this.form.submit()">
            @foreach($stores as $st)
              <option value="{{ $st->id }}" {{ ($store->id ?? 1) == $st->id ? 'selected' : '' }}>{{ $st->name }}</option>
            @endforeach
          </select>
        </form>
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

        <div class="card card-outline card-primary">
          <div class="card-header"><h3 class="card-title font-weight-bold">Store Specific Inventory & Custom Pricing Controls</h3></div>
          <div class="card-body p-0">
            <table class="table table-striped table-bordered">
              <thead class="bg-light">
                <tr>
                  <th>Product</th>
                  <th>Scope</th>
                  <th>Global Price / Stock</th>
                  <th>Store Custom Price (₹)</th>
                  <th>Store Custom Stock</th>
                  <th>Availability</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @forelse($products as $prod)
                <tr>
                  <td class="font-weight-bold">{{ $prod->name }}</td>
                  <td>
                    @if($prod->scope === 'global')
                      <span class="badge badge-success">Global</span>
                    @else
                      <span class="badge badge-primary">Store Specific</span>
                    @endif
                  </td>
                  <td>₹{{ $prod->price }} ({{ $prod->stock }} pcs)</td>
                  <form action="/admin/store-manager/update-inventory" method="POST">
                    @csrf
                    <input type="hidden" name="store_id" value="{{ $store->id ?? 1 }}">
                    <input type="hidden" name="product_id" value="{{ $prod->id }}">
                    <td>
                      <input type="number" step="0.01" name="custom_price" class="form-control form-control-sm" value="{{ $prod->custom_price ?? $prod->price }}" style="width: 100px;">
                    </td>
                    <td>
                      <input type="number" name="custom_stock" class="form-control form-control-sm" value="{{ $prod->custom_stock ?? $prod->stock }}" style="width: 90px;">
                    </td>
                    <td>
                      <div class="custom-control custom-switch">
                        <input type="checkbox" name="is_available" class="custom-control-input" id="avail_{{ $prod->id }}" {{ ($prod->is_available ?? true) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="avail_{{ $prod->id }}">In Stock</label>
                      </div>
                    </td>
                    <td>
                      <button type="submit" class="btn btn-primary btn-sm font-weight-bold"><i class="fas fa-save mr-1"></i> Update Store Inventory</button>
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
