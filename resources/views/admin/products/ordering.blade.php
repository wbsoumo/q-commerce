<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Q-Commerce Admin | Product Display Ordering</title>
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
      <li class="nav-item d-none d-sm-inline-block"><a href="#" class="nav-link active font-weight-bold">Product Ordering</a></li>
    </ul>
  </nav>

  @include('admin.layouts.sidebar')

  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid d-flex justify-content-between align-items-center">
        <h1 class="m-0 font-weight-bold"><i class="fas fa-sort-amount-down text-success mr-2"></i>Product Ordering by Category</h1>
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

        <!-- Category Selector Filter Form -->
        <div class="card card-outline card-success mb-3">
          <div class="card-body">
            <form method="GET" action="/admin/product-ordering" class="form-inline">
              <label class="mr-2 font-weight-bold"><i class="fas fa-filter mr-1"></i> Select Category to Reorder:</label>
              <select name="category_id" class="form-control mr-2" onchange="this.form.submit()" style="min-width: 250px;">
                <option value="">-- Choose Category --</option>
                @foreach($categories as $cat)
                  <option value="{{ $cat->id }}" {{ $selectedCategoryId == $cat->id ? 'selected' : '' }}>
                    {{ $cat->name }} (ID: {{ $cat->id }})
                  </option>
                @endforeach
              </select>
              <button type="submit" class="btn btn-success"><i class="fas fa-search mr-1"></i> Load Products</button>
            </form>
          </div>
        </div>

        @if($selectedCategoryId)
          <div class="card card-outline card-primary">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h3 class="card-title font-weight-bold m-0">
                <i class="fas fa-list-ol mr-2"></i> Products in "{{ $selectedCategoryName }}" ({{ count($products) }} items)
              </h3>
            </div>
            <div class="card-body p-0">
              @if(count($products) == 0)
                <div class="p-4 text-center text-muted">
                  <i class="fas fa-info-circle fa-2x mb-2"></i>
                  <p class="mb-0 font-weight-bold">No active products found in this category.</p>
                </div>
              @else
                <form method="POST" action="/admin/product-ordering/update">
                  @csrf
                  <input type="hidden" name="category_id" value="{{ $selectedCategoryId }}">
                  <table class="table table-striped align-middle">
                    <thead>
                      <tr>
                        <th style="width: 100px;">Sort Rank</th>
                        <th style="width: 70px;">Image</th>
                        <th>Product Name</th>
                        <th>Price</th>
                        <th>MRP</th>
                        <th>Stock</th>
                        <th>Status</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($products as $index => $prod)
                        <tr>
                          <td>
                            <input 
                              type="number" 
                              name="sort_order[{{ $prod->id }}]" 
                              value="{{ $prod->sort_order ?? ($index + 1) }}" 
                              class="form-control form-control-sm text-center font-weight-bold border-success" 
                              min="1" 
                              style="width: 80px;"
                            >
                          </td>
                          <td>
                            @if($prod->image)
                              <img src="{{ str_starts_with($prod->image, 'http') ? $prod->image : asset($prod->image) }}" width="45" height="45" class="rounded border" style="object-fit: cover;">
                            @else
                              <div class="bg-light rounded text-center py-2" style="width: 45px; height: 45px;"><i class="fas fa-box text-muted"></i></div>
                            @endif
                          </td>
                          <td class="font-weight-bold">
                            {{ $prod->name }}
                            <div class="small text-muted">ID: {{ $prod->id }} | SKU: {{ $prod->sku ?? 'N/A' }}</div>
                          </td>
                          <td class="text-success font-weight-bold">₹{{ number_format($prod->price, 2) }}</td>
                          <td class="text-muted"><del>₹{{ number_format($prod->mrp, 2) }}</del></td>
                          <td>
                            @if($prod->stock > 0)
                              <span class="badge badge-success">{{ $prod->stock }} in stock</span>
                            @else
                              <span class="badge badge-danger">Out of Stock</span>
                            @endif
                          </td>
                          <td>
                            @if($prod->is_active)
                              <span class="badge badge-success">Active</span>
                            @else
                              <span class="badge badge-secondary">Inactive</span>
                            @endif
                          </td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                  
                  <div class="p-3 bg-light border-top text-right">
                    <button type="submit" class="btn btn-success btn-lg font-weight-bold">
                      <i class="fas fa-save mr-1"></i> Save & Apply Product Display Order
                    </button>
                  </div>
                </form>
              @endif
            </div>
          </div>
        @else
          <div class="card p-5 text-center text-muted">
            <i class="fas fa-arrow-up fa-3x mb-3 text-success"></i>
            <h4 class="font-weight-bold">Please select a category above to manage its product display order.</h4>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>
