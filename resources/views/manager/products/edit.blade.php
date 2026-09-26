<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Edit Product #{{ $product->id }} | {{ $store->name ?? 'Store Branch' }}</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  <style>
    .brand-banner { background: linear-gradient(135deg, #007bff 0%, #0056b3 100%); }
    .manager-sidebar { background-color: #1e293b !important; }
    .custom-card-header { background: linear-gradient(135deg, #ffc107 0%, #d39e00 100%); color: #212529; }
    .upload-box { border: 2px dashed #007bff; background: #f8fafc; padding: 20px; border-radius: 8px; text-align: center; }
  </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  
  <nav class="main-header navbar navbar-expand navbar-white navbar-light border-bottom shadow-sm">
    <ul class="navbar-nav">
      <li class="nav-item"><a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a></li>
      <li class="nav-item d-none d-sm-inline-block"><a href="/manager/inventory" class="nav-link">Inventory Catalog</a></li>
      <li class="nav-item d-none d-sm-inline-block"><a href="#" class="nav-link active font-weight-bold text-warning">Edit Product</a></li>
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
          <h1 class="m-0 font-weight-bold text-dark"><i class="fas fa-edit text-warning mr-2"></i>Edit Product: {{ $product->name }}</h1>
          <p class="text-muted mb-0"><i class="fas fa-store text-primary mr-1"></i> {{ $store->name }} (Code: {{ $store->code }})</p>
        </div>
        <a href="/manager/inventory" class="btn btn-outline-secondary font-weight-bold"><i class="fas fa-arrow-left mr-1"></i> Back to Inventory</a>
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
        @if(session('error'))
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle mr-1"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
          </div>
        @endif

        <form action="/manager/products/update" method="POST" enctype="multipart/form-data">
          @csrf
          <input type="hidden" name="id" value="{{ $product->id }}">
          
          <div class="card card-outline card-warning shadow-lg mb-4">
            <div class="card-header custom-card-header py-3">
              <h3 class="card-title font-weight-bold m-0 text-dark"><i class="fas fa-edit mr-2"></i>Product General Information</h3>
            </div>
            <div class="card-body p-4">
              <div class="row">
                <div class="col-md-6 form-group">
                  <label class="font-weight-bold text-dark">Product Name <span class="text-danger">*</span></label>
                  <input type="text" name="name" class="form-control form-control-lg border-secondary font-weight-bold" value="{{ $product->name }}" required>
                </div>
                <div class="col-md-6 form-group">
                  <label class="font-weight-bold text-dark">Category <span class="text-danger">*</span></label>
                  <select name="category_id" class="form-control form-control-lg border-secondary" required>
                    @foreach($categories as $cat)
                      <option value="{{ $cat->id }}" {{ $product->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                  </select>
                </div>
              </div>

              <div class="row">
                <div class="col-md-4 form-group">
                  <label class="font-weight-bold text-dark">SKU / Item Code <span class="text-danger">*</span></label>
                  <input type="text" name="sku" class="form-control font-weight-bold" value="{{ $product->sku }}" required>
                </div>
                <div class="col-md-4 form-group">
                  <label class="font-weight-bold text-dark">Unit / Quantity Description <span class="text-danger">*</span></label>
                  <input type="text" name="unit" class="form-control" value="{{ $product->unit }}" required>
                </div>
                <div class="col-md-4 form-group">
                  <label class="font-weight-bold text-dark">Branch Custom Stock Count <span class="text-danger">*</span></label>
                  <input type="number" name="custom_stock" class="form-control font-weight-bold text-primary" value="{{ $inventory->custom_stock ?? $product->stock }}" min="0" required>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 form-group">
                  <label class="font-weight-bold text-dark">Selling Price (₹) <span class="text-danger">*</span></label>
                  <div class="input-group input-group-lg">
                    <div class="input-group-prepend"><span class="input-group-text bg-success text-white font-weight-bold">₹</span></div>
                    <input type="number" step="0.01" name="price" class="form-control form-control-lg font-weight-bold text-success" value="{{ $inventory->custom_price ?? $product->price }}" required>
                  </div>
                </div>
                <div class="col-md-6 form-group">
                  <label class="font-weight-bold text-dark">MRP (Maximum Retail Price) (₹) <span class="text-danger">*</span></label>
                  <div class="input-group input-group-lg">
                    <div class="input-group-prepend"><span class="input-group-text bg-secondary text-white font-weight-bold">₹</span></div>
                    <input type="number" step="0.01" name="mrp" class="form-control form-control-lg font-weight-bold" value="{{ $inventory->custom_mrp ?? $product->mrp }}" required>
                  </div>
                </div>
              </div>

              <!-- FEATURE & BESTSELLER FLAGS -->
              <div class="row bg-light p-3 rounded mb-3 border">
                <div class="col-md-6 form-group mb-0">
                  <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-warning">
                    <input type="checkbox" name="is_bestseller" value="1" class="custom-control-input" id="mgrIsBestsellerEdit" {{ !empty($product->is_bestseller) ? 'checked' : '' }}>
                    <label class="custom-control-label font-weight-bold text-warning" for="mgrIsBestsellerEdit"><i class="fas fa-fire text-danger mr-1"></i> 🔥 Best Seller Product (Show in Bestsellers)</label>
                  </div>
                </div>
                <div class="col-md-6 form-group mb-0">
                  <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                    <input type="checkbox" name="is_featured" value="1" class="custom-control-input" id="mgrIsFeaturedEdit" {{ !empty($product->is_featured) ? 'checked' : '' }}>
                    <label class="custom-control-label font-weight-bold text-success" for="mgrIsFeaturedEdit"><i class="fas fa-bolt text-warning mr-1"></i> ⚡ Super Savings Product (Show in Super Savings)</label>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- MEDIA & GALLERY UPLOAD CARD -->
          <div class="card card-outline card-info shadow-lg mb-4">
            <div class="card-header bg-info py-3">
              <h3 class="card-title font-weight-bold m-0 text-white"><i class="fas fa-photo-video mr-2"></i>Product Media & Multi-Image Gallery</h3>
            </div>
            <div class="card-body p-4">
              <div class="row">
                <div class="col-md-6 form-group">
                  <label class="font-weight-bold text-dark">Main Product Image File</label>
                  <div class="upload-box">
                    <i class="fas fa-cloud-upload-alt fa-2x text-primary mb-2"></i>
                    <p class="font-weight-bold text-dark mb-1">Upload New Primary Image</p>
                    <input type="file" name="image_file" class="form-control-file border p-2 bg-white rounded">
                    @if($product->image)
                      <div class="mt-3">
                        <small class="d-block text-muted font-weight-bold mb-1">Current Image Preview:</small>
                        <img src="{{ str_contains($product->image, 'http') || str_contains($product->image, 'uploads') ? asset($product->image) : 'https://raw.githubusercontent.com/wbsoumo/q-commerce/main/' . $product->image }}" style="max-height: 90px;" class="rounded border shadow-sm">
                      </div>
                    @endif
                  </div>
                </div>
                <div class="col-md-6 form-group">
                  <label class="font-weight-bold text-dark">Multi-Angle Gallery Images (Select Multiple Files)</label>
                  <div class="upload-box border-info">
                    <i class="fas fa-images fa-2x text-info mb-2"></i>
                    <p class="font-weight-bold text-dark mb-1">Upload Gallery Images</p>
                    <input type="file" name="gallery_files[]" class="form-control-file border p-2 bg-white rounded" multiple>
                    @php
                      $galleryArr = !empty($product->gallery) ? json_decode($product->gallery, true) : [];
                    @endphp
                    @if(!empty($galleryArr) && is_array($galleryArr))
                      <div class="mt-3 d-flex flex-wrap gap-2">
                        @foreach($galleryArr as $gImg)
                          <img src="{{ str_contains($gImg, 'http') || str_contains($gImg, 'uploads') ? asset($gImg) : 'https://raw.githubusercontent.com/wbsoumo/q-commerce/main/' . $gImg }}" style="max-height: 50px;" class="rounded border mr-1 mb-1">
                        @endforeach
                      </div>
                    @endif
                  </div>
                </div>
              </div>

              <div class="form-group mt-3">
                <label class="font-weight-bold text-dark">Product Description & Special Instructions</label>
                <textarea name="description" class="form-control" rows="4" placeholder="Enter product ingredients, key features, storage tips, or shelf life details...">{{ $product->description }}</textarea>
              </div>
            </div>
            <div class="card-footer bg-light text-right p-3 border-top">
              <a href="/manager/inventory" class="btn btn-default btn-lg font-weight-bold mr-2">Cancel</a>
              <button type="submit" class="btn btn-warning btn-lg font-weight-bold text-dark px-4 shadow"><i class="fas fa-save mr-1"></i> Update Product & Inventory</button>
            </div>
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
