<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Q-Commerce Admin | Global Home Page Customizer</title>
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
      <li class="nav-item d-none d-sm-inline-block"><a href="#" class="nav-link active font-weight-bold">Global App Customizer</a></li>
    </ul>
  </nav>

  @include('admin.layouts.sidebar')

  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <h1 class="m-0 font-weight-bold text-dark"><i class="fas fa-desktop text-success mr-2"></i>Global App Home Page Customizer</h1>
        <p class="text-muted">Customize everything displayed on the Flutter App front home page globally across all users.</p>
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

        <form action="/admin/homepage-customizer/save" method="POST" enctype="multipart/form-data">
          @csrf

          <!-- 1. MAIN BANNER & PROMOTION HEADER -->
          <div class="card card-success card-outline">
            <div class="card-header"><h3 class="card-title font-weight-bold"><i class="fas fa-star mr-2"></i>1. Top Hero Banner Customization</h3></div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-6 form-group">
                  <label class="font-weight-bold">Global Banner Title</label>
                  <input type="text" name="banner_title" class="form-control font-weight-bold" value="{{ $config->banner_title ?? 'Mega Diwali Sale' }}" placeholder="e.g. Mega Diwali Sale, Big Festive Off">
                </div>
                <div class="col-md-6 form-group">
                  <label class="font-weight-bold">Global Banner Subtitle / Discount Tag</label>
                  <input type="text" name="banner_subtitle" class="form-control" value="{{ $config->banner_subtitle ?? 'Upto 50% Off' }}" placeholder="e.g. Upto 50% Off on all items">
                </div>
              </div>
              <div class="row">
                <div class="col-md-4 form-group">
                  <label class="font-weight-bold">Banner Background Color</label>
                  <input type="color" name="banner_color" class="form-control" value="{{ $config->banner_color ?? '#0c831f' }}">
                </div>
                <div class="col-md-4 form-group">
                  <label class="font-weight-bold">Delivery Time Guarantee Text</label>
                  <input type="text" name="delivery_text" class="form-control" value="{{ $config->delivery_text ?? 'Delivery in 15 mins' }}" placeholder="e.g. Delivery in 10-15 mins">
                </div>
                <div class="col-md-4 form-group">
                  <label class="font-weight-bold">Search Bar Hint Text</label>
                  <input type="text" name="search_hint" class="form-control" value="{{ $config->search_hint ?? 'Search milk, atta, chips, diwali lights' }}" placeholder="Comma separated search hints">
                </div>
              </div>
            </div>
          </div>

          <!-- 2. HOME PAGE SECTIONS VISIBILITY -->
          <div class="card card-primary card-outline">
            <div class="card-header"><h3 class="card-title font-weight-bold"><i class="fas fa-layer-group mr-2"></i>2. Homepage Layout & Section Controls</h3></div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-4">
                  <div class="custom-control custom-switch border p-3 rounded mb-2">
                    <input type="checkbox" class="custom-control-input" id="showHeroBanner" name="show_hero_banner" {{ ($config->show_hero_banner ?? true) ? 'checked' : '' }}>
                    <label class="custom-control-label font-weight-bold" for="showHeroBanner">Show Top Green Hero Banner</label>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="custom-control custom-switch border p-3 rounded mb-2">
                    <input type="checkbox" class="custom-control-input" id="showFeaturedSec" name="show_featured_section" {{ ($config->show_featured_section ?? true) ? 'checked' : '' }}>
                    <label class="custom-control-label font-weight-bold" for="showFeaturedSec">Show Featured Products Section</label>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="custom-control custom-switch border p-3 rounded mb-2">
                    <input type="checkbox" class="custom-control-input" id="showGrocerySec" name="show_grocery_section" {{ ($config->show_grocery_section ?? true) ? 'checked' : '' }}>
                    <label class="custom-control-label font-weight-bold" for="showGrocerySec">Show Grocery & Kitchen Section</label>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- 3. FRONT PAGE CATEGORIES SELECTION -->
          <div class="card card-info card-outline">
            <div class="card-header"><h3 class="card-title font-weight-bold"><i class="fas fa-th-large mr-2"></i>3. Select Categories To Display On Front Page</h3></div>
            <div class="card-body">
              <p class="text-muted">Choose which categories appear on the Flutter app front screen. When a user clicks a category tab in the app, all products in that category will automatically display.</p>
              <div class="row">
                @foreach($categories as $cat)
                  <div class="col-md-4 col-sm-6 mb-3">
                    <div class="border rounded p-3 bg-light d-flex align-items-center justify-content-between">
                      <div class="d-flex align-items-center">
                        <img src="{{ $cat->image ?? 'https://via.placeholder.com/40' }}" width="40" height="40" style="object-fit:cover; border-radius:8px;" class="mr-3 border">
                        <div>
                          <strong class="d-block text-dark">{{ $cat->name }}</strong>
                          <span class="small text-muted">Display order: {{ $cat->display_order }}</span>
                        </div>
                      </div>
                      <div class="custom-control custom-switch ml-2">
                        <input type="checkbox" class="custom-control-input" id="cat_hp_{{ $cat->id }}" name="show_on_homepage[{{ $cat->id }}]" {{ ($cat->show_on_homepage ?? true) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="cat_hp_{{ $cat->id }}"></label>
                      </div>
                    </div>
                  </div>
                @endforeach
              </div>
            </div>
          </div>

          <!-- 4. PROMO GRID BANNERS (4 CARDS SECTION) CUSTOMIZATION -->
          <div class="card card-danger card-outline">
            <div class="card-header"><h3 class="card-title font-weight-bold text-dark"><i class="fas fa-images mr-2"></i>4. Promo Grid Banners (4 Featured Cards)</h3></div>
            <div class="card-body">
              <p class="text-muted">Upload custom card images, preview uploaded media, and link each promo card to a Category or Specific Product easily.</p>
              
              @php
                $promoCards = json_decode($config->promo_grid_json ?? '[]', true);
                if (empty($promoCards)) {
                    $promoCards = [
                        ["title" => "Lights, Diyas & Candles", "img" => "image 50.png", "target_type" => "category", "target_id" => "1"],
                        ["title" => "Diwali Gifts", "img" => "image 51.png", "target_type" => "category", "target_id" => "2"],
                        ["title" => "Appliances & Gadgets", "img" => "image 52.png", "target_type" => "category", "target_id" => "3"],
                        ["title" => "Home & Living", "img" => "image 53.png", "target_type" => "category", "target_id" => "4"],
                    ];
                }
              @endphp

              <div class="row">
                @foreach([0, 1, 2, 3] as $idx)
                  @php $card = $promoCards[$idx] ?? []; @endphp
                  <div class="col-md-6 mb-4">
                    <div class="border rounded p-3 bg-light shadow-sm">
                      <h5 class="font-weight-bold text-danger border-bottom pb-2 d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-image mr-1"></i> Promo Card #{{ $idx + 1 }}</span>
                        <span class="badge badge-primary">Slot {{ $idx + 1 }}</span>
                      </h5>
                      
                      <div class="form-group mb-2">
                        <label class="small font-weight-bold">Card Title</label>
                        <input type="text" name="promo_cards[{{ $idx }}][title]" class="form-control form-control-sm" value="{{ $card['title'] ?? '' }}" placeholder="Card Title">
                      </div>

                      <!-- IMAGE UPLOAD & PREVIEW SECTION -->
                      <div class="form-group mb-2">
                        <label class="small font-weight-bold"><i class="fas fa-upload mr-1"></i> Upload Card Image</label>
                        <div class="input-group input-group-sm mb-2">
                          <div class="custom-file">
                            <input type="file" name="promo_card_files[{{ $idx }}]" class="custom-file-input" id="promo_file_{{ $idx }}" accept="image/*" onchange="previewCardImage(this, {{ $idx }})">
                            <label class="custom-file-label" for="promo_file_{{ $idx }}">Choose new image file...</label>
                          </div>
                        </div>
                        <input type="hidden" name="promo_cards[{{ $idx }}][img]" id="promo_img_val_{{ $idx }}" value="{{ $card['img'] ?? '' }}">
                        
                        <!-- PREVIEW CONTAINER -->
                        <div class="mt-2 p-2 border rounded text-center bg-white" style="min-height: 100px;">
                          <p class="small text-muted mb-1 font-weight-bold">Image Preview:</p>
                          @php
                            $imgSrc = $card['img'] ?? '';
                            if ($imgSrc && !str_starts_with($imgSrc, 'http') && !str_starts_with($imgSrc, '/')) {
                              $imgSrc = '/assets/' . $imgSrc;
                            }
                          @endphp
                          <img id="promo_preview_{{ $idx }}" src="{{ !empty($imgSrc) ? $imgSrc : 'https://via.placeholder.com/150x100?text=No+Image' }}" style="max-height: 110px; max-width: 100%; border-radius: 8px; object-fit: contain;" class="border shadow-sm" alt="Card Preview">
                        </div>
                      </div>

                      <!-- LINK TYPE SELECTION -->
                      <div class="row mt-3">
                        <div class="col-md-6 form-group mb-2">
                          <label class="small font-weight-bold">Link Type</label>
                          <select name="promo_cards[{{ $idx }}][target_type]" id="target_type_{{ $idx }}" class="form-control form-control-sm" onchange="onLinkTypeChange({{ $idx }})">
                            <option value="category" {{ ($card['target_type'] ?? '') == 'category' ? 'selected' : '' }}>Category</option>
                            <option value="product" {{ ($card['target_type'] ?? '') == 'product' ? 'selected' : '' }}>Specific Product</option>
                          </select>
                        </div>

                        <div class="col-md-6 form-group mb-2">
                          <label class="small font-weight-bold">Linked Target ID / Name</label>
                          <input type="text" name="promo_cards[{{ $idx }}][target_id]" id="target_id_{{ $idx }}" class="form-control form-control-sm bg-white" value="{{ $card['target_id'] ?? '' }}" placeholder="Target ID">
                        </div>
                      </div>

                      <!-- ACTION BUTTONS FOR CATEGORY & PRODUCT PICKER -->
                      <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                        <!-- CATEGORY PRODUCTS ACTION BUTTON -->
                        <div id="cat_action_sec_{{ $idx }}" class="{{ ($card['target_type'] ?? 'category') == 'category' ? 'd-block' : 'd-none' }}">
                          <label class="small font-weight-bold text-secondary d-block mb-1">Select Category:</label>
                          <select class="form-control form-control-sm" style="max-width: 180px; display:inline-block;" onchange="onCategorySelect({{ $idx }}, this)">
                            <option value="">-- Choose Category --</option>
                            @foreach($categories as $cat)
                              <option value="cat_{{ $cat->id }}" data-name="{{ $cat->name }}" data-id="{{ $cat->id }}" {{ ($card['target_id'] ?? '') == 'cat_'.$cat->id || ($card['target_id'] ?? '') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                          </select>
                          <button type="button" class="btn btn-xs btn-info ml-1" onclick="showCategoryProductsModal({{ $idx }})">
                            <i class="fas fa-eye mr-1"></i> View Products
                          </button>
                        </div>

                        <!-- PRODUCT POPUP PICKER ACTION BUTTON -->
                        <div id="prod_action_sec_{{ $idx }}" class="{{ ($card['target_type'] ?? '') == 'product' ? 'd-block' : 'd-none' }}">
                          <label class="small font-weight-bold text-secondary d-block mb-1">Select Specific Product:</label>
                          <button type="button" class="btn btn-sm btn-primary font-weight-bold" onclick="openProductPickerModal({{ $idx }})">
                            <i class="fas fa-search-plus mr-1"></i> Choose Product Popup
                          </button>
                        </div>
                      </div>

                    </div>
                  </div>
                @endforeach
              </div>
            </div>
          </div>

          <!-- 5. FEATURED PRODUCTS CUSTOM SELECTION -->
          <div class="card card-warning card-outline">
            <div class="card-header"><h3 class="card-title font-weight-bold text-dark"><i class="fas fa-gem mr-2"></i>5. Pin Featured Products To Main Page</h3></div>
            <div class="card-body">
              <p class="text-muted">Select specific products from your catalog that will be pinned as <strong>Featured</strong> on the home page front screen.</p>
              <div class="row">
                @foreach($products as $p)
                  <div class="col-md-4 col-sm-6 mb-3">
                    <div class="border rounded p-2 d-flex align-items-center bg-light">
                      <div class="custom-control custom-checkbox mr-3">
                        <input type="checkbox" class="custom-control-input" id="prod_feat_{{ $p->id }}" name="featured_product_ids[]" value="{{ $p->id }}" {{ $p->is_featured ? 'checked' : '' }}>
                        <label class="custom-control-label" for="prod_feat_{{ $p->id }}"></label>
                      </div>
                      <img src="{{ $p->image ?? 'https://via.placeholder.com/50' }}" width="45" height="45" style="object-fit:cover; border-radius:6px;" class="mr-2 border">
                      <div style="line-height:1.2;">
                        <strong class="d-block text-dark small">{{ $p->name }}</strong>
                        <span class="text-success font-weight-bold small">₹{{ $p->price }}</span>
                      </div>
                    </div>
                  </div>
                @endforeach
              </div>
            </div>
            <div class="card-footer bg-white">
              <button type="submit" class="btn btn-success btn-lg font-weight-bold px-4"><i class="fas fa-save mr-2"></i> Save Global Homepage Layout</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- PRODUCT PICKER POPUP DIALOG MODAL -->
  <div class="modal fade" id="productPickerModal" tabindex="-1" role="dialog" aria-labelledby="productPickerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-header-title font-weight-bold" id="productPickerModalLabel"><i class="fas fa-boxes mr-2"></i> Select Product for Promo Card</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <!-- SEARCH OPTION -->
          <div class="form-group mb-3">
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
              </div>
              <input type="text" id="modalProductSearch" class="form-control" placeholder="Search product by name, SKU or category..." onkeyup="filterModalProducts()">
            </div>
          </div>
          
          <p class="small text-muted font-weight-bold">Select one or check multiple products to pick:</p>
          
          <!-- LIST OF PRODUCTS WITH CHECKBOXES -->
          <div class="table-responsive border rounded" style="max-height: 380px;">
            <table class="table table-hover table-striped mb-0 align-middle">
              <thead class="thead-light">
                <tr>
                  <th width="40" class="text-center">#</th>
                  <th width="60">Image</th>
                  <th>Product Name</th>
                  <th>Category</th>
                  <th>Price</th>
                </tr>
              </thead>
              <tbody id="modalProductList">
                @foreach($products as $p)
                  @php
                    $catObj = $categories->firstWhere('id', $p->category_id);
                    $catName = $catObj ? $catObj->name : 'General';
                  @endphp
                  <tr class="modal-prod-row" data-name="{{ strtolower($p->name) }}" data-cat="{{ strtolower($catName) }}" data-sku="{{ strtolower($p->sku ?? '') }}">
                    <td class="text-center align-middle">
                      <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input modal-prod-checkbox" id="modal_chk_{{ $p->id }}" value="prod_{{ $p->id }}" data-name="{{ $p->name }}">
                        <label class="custom-control-label" for="modal_chk_{{ $p->id }}"></label>
                      </div>
                    </td>
                    <td class="align-middle">
                      <img src="{{ $p->image ?? 'https://via.placeholder.com/40' }}" width="40" height="40" style="object-fit:cover; border-radius:4px;" class="border">
                    </td>
                    <td class="align-middle font-weight-bold text-dark">{{ $p->name }}</td>
                    <td class="align-middle"><span class="badge badge-light border">{{ $catName }}</span></td>
                    <td class="align-middle text-success font-weight-bold">₹{{ $p->price }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times mr-1"></i> Cancel</button>
          <button type="button" class="btn btn-success font-weight-bold px-4" onclick="insertSelectedProducts()"><i class="fas fa-check-circle mr-1"></i> Insert Selected Product</button>
        </div>
      </div>
    </div>
  </div>

  <!-- CATEGORY PRODUCTS PREVIEW MODAL -->
  <div class="modal fade" id="categoryProductsModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-scrollable" role="document">
      <div class="modal-content">
        <div class="modal-header bg-info text-white">
          <h5 class="modal-title font-weight-bold" id="catModalTitle"><i class="fas fa-list mr-2"></i> Category Products</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p id="catModalSub" class="small text-muted font-weight-bold">Products under this category:</p>
          <ul class="list-group" id="catModalProdList">
            <!-- Populated via JS -->
          </ul>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <footer class="main-footer"><strong>Copyright &copy; 2026 Q-Commerce Admin.</strong></footer>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

<script>
  let activeCardIndex = null;

  // 1. PREVIEW UPLOADED IMAGE INSTANTLY
  function previewCardImage(input, idx) {
    if (input.files && input.files[0]) {
      const reader = new FileReader();
      reader.onload = function(e) {
        document.getElementById('promo_preview_' + idx).src = e.target.result;
        // Update label text
        const fileName = input.files[0].name;
        $(input).next('.custom-file-label').addClass("selected").html(fileName);
      };
      reader.readAsDataURL(input.files[0]);
    }
  }

  // 2. LINK TYPE TOGGLE LOGIC
  function onLinkTypeChange(idx) {
    const type = document.getElementById('target_type_' + idx).value;
    const catSec = document.getElementById('cat_action_sec_' + idx);
    const prodSec = document.getElementById('prod_action_sec_' + idx);
    
    if (type === 'category') {
      catSec.classList.remove('d-none');
      catSec.classList.add('d-block');
      prodSec.classList.remove('d-block');
      prodSec.classList.add('d-none');
    } else {
      catSec.classList.remove('d-block');
      catSec.classList.add('d-none');
      prodSec.classList.remove('d-none');
      prodSec.classList.add('d-block');
    }
  }

  // CATEGORY DROPDOWN SELECTION HANDLER
  function onCategorySelect(idx, selectElem) {
    const val = selectElem.value;
    if (val) {
      document.getElementById('target_id_' + idx).value = val;
    }
  }

  // OPEN PRODUCT PICKER POPUP DIALOG
  function openProductPickerModal(idx) {
    activeCardIndex = idx;
    // Uncheck all checkboxes in modal first
    $('.modal-prod-checkbox').prop('checked', false);
    
    // Pre-check existing selection if matches
    const currentVal = document.getElementById('target_id_' + idx).value;
    if (currentVal) {
      $(`.modal-prod-checkbox[value="${currentVal}"]`).prop('checked', true);
    }
    
    $('#modalProductSearch').val('');
    filterModalProducts();
    $('#productPickerModal').modal('show');
  }

  // SEARCH FILTER FOR PRODUCTS IN MODAL
  function filterModalProducts() {
    const query = $('#modalProductSearch').val().toLowerCase().trim();
    $('.modal-prod-row').each(function() {
      const name = $(this).data('name') || '';
      const cat = $(this).data('cat') || '';
      const sku = $(this).data('sku') || '';
      if (name.includes(query) || cat.includes(query) || sku.includes(query)) {
        $(this).show();
      } else {
        $(this).hide();
      }
    });
  }

  // INSERT BUTTON ACTION ON POPUP MODAL
  function insertSelectedProducts() {
    if (activeCardIndex === null) return;
    
    const selectedVals = [];
    $('.modal-prod-checkbox:checked').each(function() {
      selectedVals.push($(this).val());
    });

    if (selectedVals.length === 0) {
      alert('Please select at least one product with checkbox.');
      return;
    }

    // Set value into target_id input box
    document.getElementById('target_id_' + activeCardIndex).value = selectedVals.join(',');
    $('#productPickerModal').modal('hide');
  }

  // VIEW PRODUCTS UNDER CATEGORY MODAL
  const allProductsList = @json($products);
  const allCategoriesList = @json($categories);

  function showCategoryProductsModal(idx) {
    const selectElem = document.querySelector(`#cat_action_sec_${idx} select`);
    const selectedOpt = selectElem.options[selectElem.selectedIndex];
    
    if (!selectedOpt || !selectedOpt.value) {
      alert('Please select a Category from the dropdown first.');
      return;
    }

    const catId = selectedOpt.getAttribute('data-id');
    const catName = selectedOpt.getAttribute('data-name');

    $('#catModalTitle').html(`<i class="fas fa-list mr-2"></i> Category: ${catName}`);
    $('#catModalSub').text(`Products belonging to ${catName}:`);

    const filtered = allProductsList.filter(p => String(p.category_id) === String(catId));
    const container = $('#catModalProdList');
    container.empty();

    if (filtered.length === 0) {
      container.append('<li class="list-group-item text-muted text-center py-3">No products found under this category.</li>');
    } else {
      filtered.forEach(p => {
        container.append(`
          <li class="list-group-item d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
              <img src="${p.image || 'https://via.placeholder.com/35'}" width="35" height="35" style="object-fit:cover; border-radius:4px;" class="mr-2 border">
              <span class="font-weight-bold text-dark">${p.name}</span>
            </div>
            <span class="badge badge-success px-2 py-1">₹${p.price}</span>
          </li>
        `);
      });
    }

    $('#categoryProductsModal').modal('show');
  }
</script>
</body>
</html>

