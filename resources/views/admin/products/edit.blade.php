<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Q-Commerce Admin | Edit Product</title>
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
    window.onload = function() {
      toggleStoreSelect();
    };
  </script>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
      <li class="nav-item"><a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a></li>
      <li class="nav-item d-none d-sm-inline-block"><a href="/admin/products" class="nav-link">Products</a></li>
      <li class="nav-item d-none d-sm-inline-block"><a href="#" class="nav-link active font-weight-bold">Edit Product</a></li>
    </ul>
  </nav>

  @include('admin.layouts.sidebar')

  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <h1 class="m-0 font-weight-bold">Edit Product #{{ $product->id }} - {{ $product->name }}</h1>
      </div>
    </div>

    <div class="content">
      <div class="container-fluid">
        <div class="card card-warning">
          <div class="card-header"><h3 class="card-title font-weight-bold text-white"><i class="fas fa-edit mr-1"></i> Edit Product Details</h3></div>
          <form action="/admin/products/update" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id" value="{{ $product->id }}">
            <div class="card-body">
              <div class="row">
                <div class="col-md-6 form-group">
                  <label>Product Name</label>
                  <input type="text" name="name" class="form-control" value="{{ $product->name }}" required>
                </div>
                <div class="col-md-6 form-group">
                  <label>Category</label>
                  <select name="category_id" class="form-control" required>
                    @foreach($categories as $cat)
                      <option value="{{ $cat->id }}" {{ $product->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                  </select>
                </div>
              </div>

              <!-- SCOPE SELECTION (Global vs Store-Specific) -->
              <div class="row bg-light p-3 rounded mb-3 border">
                <div class="col-md-6 form-group mb-0">
                  <label class="text-success font-weight-bold"><i class="fas fa-globe mr-1"></i> Product Scope</label>
                  <select name="scope" id="productScope" class="form-control font-weight-bold" onchange="toggleStoreSelect()" required>
                    <option value="global" {{ $product->scope === 'global' ? 'selected' : '' }}>Global (Available in ALL Stores)</option>
                    <option value="store_specific" {{ $product->scope === 'store_specific' ? 'selected' : '' }}>Store Specific (Available ONLY in selected store)</option>
                  </select>
                  <small class="form-text text-muted">Global products appear everywhere. Store specific products only appear in assigned store.</small>
                </div>
                <div class="col-md-6 form-group mb-0" id="storeSelectGroup" style="display: none;">
                  <label class="text-primary font-weight-bold"><i class="fas fa-store mr-1"></i> Assign to Specific Store(s) <small class="text-muted">(Hold Ctrl/Cmd or select multiple)</small></label>
                  @php
                    $selectedStores = [];
                    if (!empty($product->store_ids)) {
                        $selectedStores = json_decode($product->store_ids, true) ?? [];
                    } elseif (!empty($product->store_id)) {
                        $selectedStores = [$product->store_id];
                    }
                  @endphp
                  <select name="store_ids[]" class="form-control font-weight-bold" multiple style="height: 110px;">
                    @foreach($stores as $st)
                      <option value="{{ $st->id }}" {{ in_array($st->id, $selectedStores) ? 'selected' : '' }}>
                        {{ $st->name }} ({{ $st->city }} - {{ $st->code }})
                      </option>
                    @endforeach
                  </select>
                  <small class="form-text text-muted">Select one or multiple store branches for this product.</small>
                </div>
              </div>

              <div class="row">
                <div class="col-md-4 form-group">
                  <label>SKU Code</label>
                  <input type="text" name="sku" class="form-control" value="{{ $product->sku }}" required>
                </div>
                <div class="col-md-4 form-group">
                  <label>Unit / Quantity</label>
                  <input type="text" name="unit" class="form-control" value="{{ $product->unit }}" required>
                </div>
                <div class="col-md-4 form-group">
                  <label>Stock Count</label>
                  <input type="number" name="stock" class="form-control" value="{{ $product->stock }}" required>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 form-group">
                  <label>Selling Price (₹)</label>
                  <input type="number" step="0.01" name="price" class="form-control" value="{{ $product->price }}" required>
                </div>
                <div class="col-md-6 form-group">
                  <label>MRP (₹)</label>
                  <input type="number" step="0.01" name="mrp" class="form-control" value="{{ $product->mrp }}" required>
                </div>
              </div>

              <!-- PRODUCT MAIN IMAGE & GALLERY -->
              <div class="card card-outline card-info p-3 mb-3 border">
                <h5 class="font-weight-bold text-info mb-3"><i class="fas fa-images mr-1"></i> Product Main Image & Gallery</h5>
                <div class="row">
                  <div class="col-md-6 form-group">
                    <label>Main Product Image File</label>
                    <input type="file" name="image_file" class="form-control-file border p-1 rounded w-100">
                    <label class="mt-2 text-muted small">Or Image URL / Filename:</label>
                    <input type="text" name="image_url" class="form-control form-control-sm" value="{{ $product->image }}">
                    @if($product->image)
                      <div class="mt-2">
                        <small class="d-block text-muted font-weight-bold">Current Image Preview:</label>
                        <img src="{{ str_contains($product->image, 'http') || str_contains($product->image, 'uploads') ? asset($product->image) : 'https://raw.githubusercontent.com/wbsoumo/q-commerce/main/' . $product->image }}" style="max-height: 80px;" class="rounded border">
                      </div>
                    @endif
                  </div>
                  <div class="col-md-6 form-group">
                    <label>Gallery Image Files (Multiple)</label>
                    <input type="file" name="gallery_files[]" class="form-control-file border p-1 rounded w-100" multiple>
                    <label class="mt-2 text-muted small">Or Gallery Image URLs (One per line):</label>
                    @php
                      $galleryArr = !empty($product->gallery) ? json_decode($product->gallery, true) : [];
                      $galleryText = is_array($galleryArr) ? implode("\n", $galleryArr) : '';
                    @endphp
                    <textarea name="gallery_urls" class="form-control form-control-sm" rows="3" placeholder="https://example.com/img1.png&#10;https://example.com/img2.png">{{ $galleryText }}</textarea>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <label>Product Description / Details</label>
                <textarea name="description" class="form-control" rows="4" placeholder="Enter product ingredients, usage guidelines, storage tips...">{{ $product->description }}</textarea>
              </div>

            </div>
            <div class="card-footer">
              <button type="submit" class="btn btn-warning text-white font-weight-bold"><i class="fas fa-save mr-1"></i> Update Product</button>
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
