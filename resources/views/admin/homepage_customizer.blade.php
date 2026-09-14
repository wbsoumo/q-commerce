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

        <form action="/admin/homepage-customizer/save" method="POST">
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
          <!-- 3. FEATURED PRODUCTS CUSTOM SELECTION -->
          <div class="card card-warning card-outline">
            <div class="card-header"><h3 class="card-title font-weight-bold text-dark"><i class="fas fa-gem mr-2"></i>3. Select Products To Show On Main Page</h3></div>
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

  <footer class="main-footer"><strong>Copyright &copy; 2026 Q-Commerce Admin.</strong></footer>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>
