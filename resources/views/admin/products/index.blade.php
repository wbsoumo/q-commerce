<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Q-Commerce Admin | Product Catalog</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
      <li class="nav-item"><a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a></li>
      <li class="nav-item d-none d-sm-inline-block"><a href="/admin" class="nav-link">Dashboard</a></li>
      <li class="nav-item d-none d-sm-inline-block"><a href="/admin/products" class="nav-link active font-weight-bold">Products</a></li>
    </ul>
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <a class="btn btn-success btn-sm font-weight-bold" href="/admin/products/create"><i class="fas fa-plus mr-1"></i> Add New Product</a>
      </li>
    </ul>
  </nav>

  @include('admin.layouts.sidebar')

  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid d-flex justify-content-between align-items-center">
        <h1 class="m-0 font-weight-bold">Multi-Store Product Catalog</h1>
        <a href="/admin/products/create" class="btn btn-success font-weight-bold"><i class="fas fa-plus mr-1"></i> Add Product</a>
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

        <!-- Filter Bar & Search -->
        <div class="card card-outline card-primary mb-3">
          <div class="card-body py-2">
            <form action="/admin/products" method="GET" class="form-inline d-flex justify-content-between flex-wrap">
              <div class="d-flex align-items-center flex-wrap my-1">
                <label class="mr-2 font-weight-bold"><i class="fas fa-search mr-1 text-primary"></i> Search:</label>
                <input type="text" name="search" class="form-control mr-3" placeholder="Search product name or SKU..." value="{{ request('search') }}" style="min-width: 250px;">

                <label class="mr-2 font-weight-bold">Scope:</label>
                <select name="scope" class="form-control mr-3" onchange="this.form.submit()">
                  <option value="">All Scopes</option>
                  <option value="global" {{ request('scope') == 'global' ? 'selected' : '' }}>Global Only</option>
                  <option value="store_specific" {{ request('scope') == 'store_specific' ? 'selected' : '' }}>Store Specific Only</option>
                </select>

                <label class="mr-2 font-weight-bold">Store:</label>
                <select name="store_id" class="form-control mr-3" onchange="this.form.submit()">
                  <option value="">All Stores</option>
                  @foreach($stores as $st)
                    <option value="{{ $st->id }}" {{ request('store_id') == $st->id ? 'selected' : '' }}>{{ $st->name }}</option>
                  @endforeach
                </select>
              </div>

              <div class="my-1">
                <button type="submit" class="btn btn-primary font-weight-bold"><i class="fas fa-search mr-1"></i> Search</button>
                @if(request('search') || request('scope') || request('store_id'))
                  <a href="/admin/products" class="btn btn-default font-weight-bold ml-1"><i class="fas fa-undo mr-1"></i> Reset</a>
                @endif
              </div>
            </form>
          </div>
        </div>

        <div class="card card-outline card-success">
          <div class="card-header"><h3 class="card-title font-weight-bold">Products List</h3></div>
          <div class="card-body p-0">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>#ID</th>
                  <th>Product Name</th>
                  <th>Category</th>
                  <th>Scope</th>
                  <th>Assigned Store</th>
                  <th>Price</th>
                  <th>MRP</th>
                  <th>Stock</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse($products as $prod)
                <tr>
                  <td>{{ $prod->id }}</td>
                  <td class="font-weight-bold">{{ $prod->name }}</td>
                  <td><span class="badge badge-info">{{ $prod->category_name }}</span></td>
                  <td>
                    @if($prod->scope === 'global')
                      <span class="badge badge-success"><i class="fas fa-globe mr-1"></i> Global</span>
                    @else
                      <span class="badge badge-primary"><i class="fas fa-store mr-1"></i> Store Specific</span>
                    @endif
                  </td>
                  <td>{{ $prod->store_name ?? 'All Stores (Global)' }}</td>
                  <td class="text-success font-weight-bold">₹{{ $prod->price }}</td>
                  <td><del class="text-muted">₹{{ $prod->mrp }}</del></td>
                  <td><span class="badge badge-secondary">{{ $prod->stock }} pcs</span></td>
                  <td>
                    <a href="/admin/products/{{ $prod->id }}/edit" class="btn btn-sm btn-warning font-weight-bold"><i class="fas fa-edit mr-1"></i> Edit</a>
                  </td>
                </tr>
                @empty
                <tr><td colspan="9" class="text-center py-4">No products found. <a href="/admin/products/create">Add a new product</a></td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
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
