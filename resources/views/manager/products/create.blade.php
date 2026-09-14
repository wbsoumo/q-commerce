<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Add New Product | {{ $store->name ?? 'Store Branch' }}</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  <style>
    .brand-banner { background: linear-gradient(135deg, #007bff 0%, #0056b3 100%); }
    .manager-sidebar { background-color: #1e293b !important; }
    .custom-card-header { background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%); color: white; }
    .upload-box { border: 2px dashed #007bff; background: #f8fafc; padding: 20px; border-radius: 8px; text-align: center; }
  </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  
  <nav class="main-header navbar navbar-expand navbar-white navbar-light border-bottom shadow-sm">
    <ul class="navbar-nav">
      <li class="nav-item"><a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a></li>
      <li class="nav-item d-none d-sm-inline-block"><a href="/manager/inventory" class="nav-link">Inventory Catalog</a></li>
      <li class="nav-item d-none d-sm-inline-block"><a href="#" class="nav-link active font-weight-bold text-primary">Add Store Product</a></li>
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
          <h1 class="m-0 font-weight-bold text-dark"><i class="fas fa-plus-circle text-success mr-2"></i>Add New Product to Branch Catalog</h1>
          <p class="text-muted mb-0"><i class="fas fa-store text-primary mr-1"></i> {{ $store->name }} (Code: {{ $store->code }})</p>
        </div>
        <a href="/manager/inventory" class="btn btn-outline-secondary font-weight-bold"><i class="fas fa-arrow-left mr-1"></i> Back to Inventory</a>
      </div>
    </div>

    <div class="content">
      <div class="container-fluid">
        @if(session('error'))
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle mr-1"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
          </div>
        @endif

        <form action="/manager/products/store" method="POST" enctype="multipart/form-data">
          @csrf
          
          <div class="card card-outline card-success shadow-lg mb-4">
            <div class="card-header custom-card-header py-3">
              <h3 class="card-title font-weight-bold m-0"><i class="fas fa-box-open mr-2"></i>Product General Information</h3>
            </div>
            <div class="card-body p-4">
              <div class="row">
                <div class="col-md-6 form-group">
                  <label class="font-weight-bold text-dark">Product Name <span class="text-danger">*</span></label>
                  <input type="text" name="name" class="form-control form-control-lg border-secondary" placeholder="e.g. Amul Taaza Fresh Milk 500ml" required>
                </div>
                <div class="col-md-6 form-group">
                  <label class="font-weight-bold text-dark">Category <span class="text-danger">*</span></label>
                  <select name="category_id" class="form-control form-control-lg border-secondary" required>
                    <option value="">-- Select Category --</option>
                    @foreach($categories as $cat)
                      <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                  </select>
                </div>
              </div>

              <div class="row">
                <div class="col-md-4 form-group">
                  <label class="font-weight-bold text-dark">SKU / Item Code <span class="text-danger">*</span></label>
                  <input type="text" name="sku" class="form-control font-weight-bold" value="STR{{ $store->id }}-{{ time() }}" required>
                  <small class="text-muted">Unique barcode / SKU identifier.</small>
                </div>
                <div class="col-md-4 form-group">
                  <label class="font-weight-bold text-dark">Unit / Quantity Description <span class="text-danger">*</span></label>
                  <input type="text" name="unit" class="form-control" placeholder="e.g. 500 ml, 1 kg, 1 pack" value="1 pack" required>
                </div>
                <div class="col-md-4 form-group">
                  <label class="font-weight-bold text-dark">Initial Branch Stock Count <span class="text-danger">*</span></label>
                  <input type="number" name="stock" class="form-control font-weight-bold text-primary" value="50" min="0" required>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 form-group">
                  <label class="font-weight-bold text-dark">Selling Price (₹) <span class="text-danger">*</span></label>
                  <div class="input-group input-group-lg">
                    <div class="input-group-prepend"><span class="input-group-text bg-success text-white font-weight-bold">₹</span></div>
                    <input type="number" step="0.01" name="price" class="form-control form-control-lg font-weight-bold text-success" placeholder="199.00" required>
                  </div>
                </div>
                <div class="col-md-6 form-group">
                  <label class="font-weight-bold text-dark">MRP (Maximum Retail Price) (₹) <span class="text-danger">*</span></label>
                  <div class="input-group input-group-lg">
                    <div class="input-group-prepend"><span class="input-group-text bg-secondary text-white font-weight-bold">₹</span></div>
                    <input type="number" step="0.01" name="mrp" class="form-control form-control-lg font-weight-bold" placeholder="220.00" required>
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
                  <label class="font-weight-bold text-dark">Primary Product Image File</label>
                  <div class="upload-box">
                    <i class="fas fa-cloud-upload-alt fa-3x text-primary mb-2"></i>
                    <p class="font-weight-bold text-dark mb-1">Click to select Primary Image file</p>
                    <input type="file" name="image_file" class="form-control-file border p-2 bg-white rounded">
                    <small class="text-muted d-block mt-2">Supported formats: JPG, PNG, WEBP (Max: 4MB)</small>
                  </div>
                </div>
                <div class="col-md-6 form-group">
                  <label class="font-weight-bold text-dark">Multi-Angle Gallery Images (Select Multiple Files)</label>
                  <div class="upload-box border-info">
                    <i class="fas fa-images fa-3x text-info mb-2"></i>
                    <p class="font-weight-bold text-dark mb-1">Click to select Gallery image files</p>
                    <input type="file" name="gallery_files[]" class="form-control-file border p-2 bg-white rounded" multiple>
                    <small class="text-muted d-block mt-2">Select 2 or more files to display as product gallery carousel</small>
                  </div>
                </div>
              </div>

              <div class="form-group mt-3">
                <label class="font-weight-bold text-dark">Product Description & Special Instructions</label>
                <textarea name="description" class="form-control" rows="4" placeholder="Enter product ingredients, key features, storage tips, or shelf life details..."></textarea>
              </div>
            </div>
            <div class="card-footer bg-light text-right p-3 border-top">
              <a href="/manager/inventory" class="btn btn-default btn-lg font-weight-bold mr-2">Cancel</a>
              <button type="submit" class="btn btn-success btn-lg font-weight-bold px-4 shadow"><i class="fas fa-check-circle mr-1"></i> Save & Publish Product to Store Catalog</button>
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
