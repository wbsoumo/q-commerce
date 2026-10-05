<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
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
                <div class="col-md-4 form-group">
                  <label>Selling Price (₹)</label>
                  <input type="number" step="0.01" name="price" class="form-control" value="{{ $product->price }}" required>
                </div>
                <div class="col-md-4 form-group">
                  <label>MRP (₹)</label>
                  <input type="number" step="0.01" name="mrp" class="form-control" value="{{ $product->mrp }}" required>
                </div>
                <div class="col-md-4 form-group d-flex flex-column justify-content-center mt-2">
                  <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-warning mb-2">
                    <input type="checkbox" name="is_bestseller" value="1" class="custom-control-input" id="isBestsellerSwitch" {{ !empty($product->is_bestseller) ? 'checked' : '' }}>
                    <label class="custom-control-label font-weight-bold text-warning" for="isBestsellerSwitch"><i class="fas fa-fire text-danger mr-1"></i> 🔥 Best Seller Product</label>
                  </div>
                  <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success mb-2">
                    <input type="checkbox" name="is_featured" value="1" class="custom-control-input" id="isFeaturedSwitch" {{ !empty($product->is_featured) ? 'checked' : '' }}>
                    <label class="custom-control-label font-weight-bold text-success" for="isFeaturedSwitch"><i class="fas fa-bolt text-warning mr-1"></i> ⚡ Super Savings Product</label>
                  </div>
                  <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-primary">
                    <input type="checkbox" name="is_special_deal" value="1" class="custom-control-input" id="isSpecialDealSwitch" {{ !empty($product->is_special_deal) ? 'checked' : '' }}>
                    <label class="custom-control-label font-weight-bold text-purple" for="isSpecialDealSwitch" style="color: #6f42c1;"><i class="fas fa-tags text-purple mr-1" style="color: #6f42c1;"></i> 🎁 Special Deal Product</label>
                  </div>
                </div>
              </div>

              <!-- PRODUCT MAIN IMAGE & GALLERY (WORDPRESS-STYLE MEDIA LIBRARY) -->
              <div class="card card-outline card-info p-3 mb-3 border shadow-sm">
                <h5 class="font-weight-bold text-info mb-3"><i class="fas fa-photo-video mr-1"></i> Product Main Image & Gallery</h5>
                <div class="row">
                  
                  <!-- Main Image Picker -->
                  <div class="col-md-6 form-group border-right pr-md-4">
                    <label class="font-weight-bold text-dark mb-2">Main Product Image</label>
                    <div class="mb-2">
                      <button type="button" class="btn btn-outline-primary font-weight-bold shadow-sm" onclick="selectMainImageWithMediaModal()">
                        <i class="fas fa-images mr-1"></i> Choose from Media Library
                      </button>
                    </div>

                    @php
                      $mainImgSrc = '';
                      if (!empty($product->image)) {
                          $mainImgSrc = (str_contains($product->image, 'http') || str_contains($product->image, 'uploads')) ? asset($product->image) : 'https://raw.githubusercontent.com/wbsoumo/q-commerce/main/' . $product->image;
                      }
                    @endphp

                    <div id="mainImagePreviewBox" class="mt-2 p-2 border rounded bg-light" style="display: {{ !empty($product->image) ? 'block' : 'none' }}; max-width: 200px;">
                      <label class="small text-muted font-weight-bold mb-1 d-block">Current Image Preview:</label>
                      <div class="position-relative d-inline-block">
                        <img id="mainImagePreview" src="{{ $mainImgSrc }}" class="img-thumbnail" style="max-height: 120px; object-fit: contain;">
                        <button type="button" class="btn btn-danger btn-xs position-absolute" style="top: -6px; right: -6px; border-radius: 50%; width: 22px; height: 22px; padding: 0;" onclick="removeMainImage()" title="Remove image">&times;</button>
                      </div>
                    </div>

                    <input type="text" id="mainImageInput" name="image_url" class="form-control form-control-sm mt-2" value="{{ $product->image }}" placeholder="Image URL or Path">
                    <small class="text-muted">Or upload direct file:</small>
                    <input type="file" name="image_file" class="form-control-file border p-1 rounded w-100 mt-1">
                  </div>

                  <!-- Gallery Images Picker -->
                  <div class="col-md-6 form-group pl-md-4">
                    <label class="font-weight-bold text-dark mb-2">Gallery Images (Multiple)</label>
                    <div class="mb-2">
                      <button type="button" class="btn btn-outline-info font-weight-bold shadow-sm" onclick="selectGalleryImagesWithMediaModal()">
                        <i class="fas fa-layer-group mr-1"></i> Choose Gallery Images
                      </button>
                    </div>

                    @php
                      $galleryArr = !empty($product->gallery) ? json_decode($product->gallery, true) : [];
                      $galleryText = is_array($galleryArr) ? implode("\n", $galleryArr) : '';
                    @endphp

                    <div id="galleryPreviewContainer" class="mt-2 p-2 border rounded bg-light" style="min-height: 85px;">
                      @if(!empty($galleryArr) && is_array($galleryArr))
                        @foreach($galleryArr as $gIdx => $gUrl)
                          @php
                            $gSrc = (str_contains($gUrl, 'http') || str_contains($gUrl, 'uploads')) ? asset($gUrl) : 'https://raw.githubusercontent.com/wbsoumo/q-commerce/main/' . $gUrl;
                          @endphp
                          <div class="position-relative d-inline-block mr-2 mb-2">
                            <img src="{{ $gSrc }}" class="rounded border" style="width: 75px; height: 75px; object-fit: cover;">
                            <button type="button" class="btn btn-danger btn-xs position-absolute" style="top: -5px; right: -5px; border-radius: 50%; width: 22px; height: 22px; padding: 0;" onclick="removeGalleryImage({{ $gIdx }})" title="Remove image">&times;</button>
                          </div>
                        @endforeach
                      @else
                        <span class="text-muted small">No gallery images added yet. Click above to pick from Media Library.</span>
                      @endif
                    </div>

                    <textarea id="galleryUrlsTextarea" name="gallery_urls" class="form-control form-control-sm mt-2" rows="2" placeholder="Image URLs (One per line)" style="display: none;">{{ $galleryText }}</textarea>
                    <small class="text-muted">Or upload gallery files:</small>
                    <input type="file" name="gallery_files[]" class="form-control-file border p-1 rounded w-100 mt-1" multiple>
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

@include('admin.partials.media-modal')

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

<script>
// Initialize Gallery State from backend
let selectedGalleryUrls = @json(is_array($galleryArr) ? $galleryArr : []);

// Main Image Picker logic
function selectMainImageWithMediaModal() {
  const currentVal = document.getElementById('mainImageInput').value;
  openMediaModal({
    mode: 'single',
    preselected: currentVal ? [{ url: currentVal.startsWith('http') || currentVal.startsWith('/') ? currentVal : '/' + currentVal, relative_path: currentVal }] : [],
    onSelect: function(items) {
      if (items.length > 0) {
        const item = items[0];
        document.getElementById('mainImageInput').value = item.relative_path || item.url;
        document.getElementById('mainImagePreview').src = item.url;
        document.getElementById('mainImagePreviewBox').style.display = 'block';
      }
    }
  });
}

function removeMainImage() {
  document.getElementById('mainImageInput').value = '';
  document.getElementById('mainImagePreviewBox').style.display = 'none';
}

// Gallery Images Picker logic
function selectGalleryImagesWithMediaModal() {
  const rawUrls = document.getElementById('galleryUrlsTextarea').value;
  const existing = rawUrls.split('\n').map(u => u.trim()).filter(Boolean);
  
  openMediaModal({
    mode: 'multiple',
    preselected: existing.map(u => ({ url: u.startsWith('http') || u.startsWith('/') ? u : '/' + u, relative_path: u })),
    onSelect: function(items) {
      selectedGalleryUrls = items.map(i => i.relative_path || i.url);
      renderGalleryPreviews();
    }
  });
}

function renderGalleryPreviews() {
  const textarea = document.getElementById('galleryUrlsTextarea');
  const container = document.getElementById('galleryPreviewContainer');
  textarea.value = selectedGalleryUrls.join('\n');

  if (selectedGalleryUrls.length === 0) {
    container.innerHTML = `<span class="text-muted small">No gallery images added yet. Click above to pick from Media Library.</span>`;
    return;
  }

  let html = '';
  selectedGalleryUrls.forEach((url, index) => {
    const src = (url.startsWith('http') || url.startsWith('/')) ? url : '/' + url;
    html += `
      <div class="position-relative d-inline-block mr-2 mb-2">
        <img src="${src}" class="rounded border" style="width: 75px; height: 75px; object-fit: cover;">
        <button type="button" class="btn btn-danger btn-xs position-absolute" style="top: -5px; right: -5px; border-radius: 50%; width: 22px; height: 22px; padding: 0;" onclick="removeGalleryImage(${index})" title="Remove image">&times;</button>
      </div>
    `;
  });
  container.innerHTML = html;
}

function removeGalleryImage(index) {
  selectedGalleryUrls.splice(index, 1);
  renderGalleryPreviews();
}
</script>
</body>
</html>
