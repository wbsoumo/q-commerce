<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SB Mart Admin | Promotional Home Sliders</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  <style>
    .slider-img-preview { width: 140px; height: 70px; object-fit: cover; border-radius: 8px; border: 1px solid #ddd; }
    .badge-active { background-color: #28a745; color: #fff; }
    .badge-inactive { background-color: #6c757d; color: #fff; }
  </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
      <li class="nav-item"><a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a></li>
      <li class="nav-item d-none d-sm-inline-block"><a href="/admin" class="nav-link">Dashboard</a></li>
      <li class="nav-item d-none d-sm-inline-block"><a href="/admin/sliders" class="nav-link active font-weight-bold">Promotional Sliders</a></li>
    </ul>
  </nav>

  @include('admin.layouts.sidebar')

  <!-- Content Wrapper -->
  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid d-flex justify-content-between align-items-center">
        <div>
          <h1 class="m-0 font-weight-bold"><i class="fas fa-images text-success mr-2"></i>Promotional Home Sliders</h1>
          <p class="text-muted mb-0 small">Create, order, and toggle promotional sliders displayed below the home search bar.</p>
        </div>
        <div>
          <button type="button" class="btn btn-success btn-sm rounded-pill font-weight-bold px-3 shadow-sm" data-toggle="modal" data-target="#createSliderModal">
            <i class="fas fa-plus-circle mr-1"></i> Add New Slider
          </button>
        </div>
      </div>
    </div>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        @if(session('success'))
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
        @endif

        <div class="card card-outline card-success shadow-sm">
          <div class="card-header border-0">
            <h3 class="card-title font-weight-bold">Active & Saved Promotional Sliders</h3>
          </div>
          <div class="card-body p-0 table-responsive">
            <table class="table table-striped table-hover align-middle mb-0">
              <thead class="thead-light">
                <tr>
                  <th style="width: 60px;">Order</th>
                  <th>Banner Image</th>
                  <th>Title</th>
                  <th>Target Category / Link</th>
                  <th>Status</th>
                  <th class="text-right" style="width: 180px;">Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse($sliders as $slider)
                  <tr>
                    <td class="font-weight-bold text-center align-middle">{{ $slider->display_order }}</td>
                    <td class="align-middle">
                      @php
                        $imgSrc = $slider->image ?? '';
                        if ($imgSrc && !str_contains($imgSrc, 'http')) {
                          $imgSrc = asset(ltrim($imgSrc, '/'));
                        }
                      @endphp
                      @if($imgSrc)
                        <img src="{{ $imgSrc }}" alt="Slider Banner" class="slider-img-preview" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1542838132-92c53300491e?w=500&q=80'">
                      @else
                        <span class="badge badge-secondary p-2"><i class="fas fa-image mr-1"></i> No Image</span>
                      @endif
                    </td>
                    <td class="align-middle">
                      <strong class="text-dark d-block" style="font-size: 15px;">{{ $slider->title ?? 'Promotional Banner' }}</strong>
                      @if($slider->subtitle)
                        <small class="text-muted">{{ $slider->subtitle }}</small>
                      @endif
                    </td>
                    <td class="align-middle">
                      @if($slider->category_id)
                        @php
                          $targetCat = DB::table('categories')->where('id', $slider->category_id)->first();
                        @endphp
                        <span class="badge badge-success font-weight-bold px-2 py-1"><i class="fas fa-th-large mr-1"></i>{{ $targetCat->name ?? ('Category #' . $slider->category_id) }}</span>
                      @elseif($slider->redirect_url)
                        <span class="badge badge-info font-weight-bold px-2 py-1"><i class="fas fa-link mr-1"></i>{{ $slider->redirect_url }}</span>
                      @else
                        <span class="badge badge-secondary font-weight-bold px-2 py-1"><i class="fas fa-folder mr-1"></i>Default Category</span>
                      @endif
                    </td>
                    <td class="align-middle">
                      @if($slider->is_active)
                        <span class="badge badge-active px-2 py-1"><i class="fas fa-check-circle mr-1"></i> Active</span>
                      @else
                        <span class="badge badge-inactive px-2 py-1"><i class="fas fa-ban mr-1"></i> Disabled</span>
                      @endif
                    </td>
                    <td class="text-right align-middle">
                      <form action="/admin/sliders/{{ $slider->id }}/toggle" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm {{ $slider->is_active ? 'btn-outline-warning' : 'btn-outline-success' }} px-2" title="{{ $slider->is_active ? 'Disable Slider' : 'Enable Slider' }}">
                          <i class="fas {{ $slider->is_active ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                        </button>
                      </form>
                      <form action="/admin/sliders/{{ $slider->id }}/delete" method="POST" class="d-inline" onsubmit="return confirm('Delete this promotional slider?')">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-sm px-2" title="Delete Slider">
                          <i class="fas fa-trash"></i>
                        </button>
                      </form>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                      <i class="fas fa-images fa-2x mb-2 d-block text-secondary"></i>
                      No promotional sliders created yet. Click "Add New Slider" above to create one.
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </section>
  </div>

  <!-- Create Slider Modal -->
  <div class="modal fade" id="createSliderModal" tabindex="-1" role="dialog" aria-labelledby="createSliderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <form action="/admin/sliders/store" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="modal-header bg-success text-white">
            <h5 class="modal-title font-weight-bold" id="createSliderModalLabel"><i class="fas fa-plus-circle mr-2"></i>Create New Home Slider</h5>
            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6 form-group">
                <label class="font-weight-bold">Slider Title <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control" placeholder="e.g. Big Savings Every Day" required>
              </div>
              <div class="col-md-6 form-group">
                <label class="font-weight-bold">Badge Offer Text</label>
                <input type="text" name="offer_text" class="form-control" placeholder="e.g. UP TO 50% OFF">
              </div>
            </div>
            <div class="form-group">
              <label class="font-weight-bold">Subtitle / Description</label>
              <input type="text" name="subtitle" class="form-control" placeholder="e.g. Fresh products, great quality at lowest prices.">
            </div>
            <div class="row">
              <div class="col-md-6 form-group">
                <label class="font-weight-bold">CTA Button Text</label>
                <input type="text" name="cta_text" class="form-control" value="Shop Now →" placeholder="e.g. Shop Now →">
              </div>
              <div class="col-md-6 form-group">
                <label class="font-weight-bold">Display Order</label>
                <input type="number" name="display_order" class="form-control" value="1" min="1">
              </div>
            </div>
            <!-- LINK & ACTION CONFIGURATION -->
            <div class="card card-outline card-success p-3 mb-3 bg-light border">
              <h6 class="font-weight-bold text-success mb-2"><i class="fas fa-link mr-1"></i> Banner Click Link Action</h6>
              <div class="row">
                <div class="col-md-6 form-group">
                  <label class="font-weight-bold">Link Type Target</label>
                  <select name="link_type" class="form-control font-weight-bold">
                    <option value="category">Category Page (Open selected Category)</option>
                    <option value="url">Search Query / Custom Page Link</option>
                  </select>
                </div>
                <div class="col-md-6 form-group">
                  <label class="font-weight-bold">Select Target Category</label>
                  <select name="category_id" class="form-control font-weight-bold">
                    <option value="">-- Select Target Category --</option>
                    @foreach($categories as $cat)
                      <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="form-group mb-0">
                <label class="font-weight-bold">Or Search Query / Custom Page URL</label>
                <input type="text" name="redirect_url" class="form-control" placeholder="e.g. bestseller or /category/1">
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 form-group">
                <label class="font-weight-bold">Banner Image Upload <span class="text-danger">*</span></label>
                <input type="file" name="image_file" class="form-control-file border p-1 rounded w-100 bg-white" accept="image/*">
                <small class="text-muted d-block mt-1">Or direct Image URL below:</small>
                <input type="url" name="image_url" class="form-control mt-1" placeholder="https://images.unsplash.com/photo-...">
              </div>
              <div class="col-md-6 form-group">
                <label class="font-weight-bold">Display Order</label>
                <input type="number" name="display_order" class="form-control" value="1" min="1">
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-success font-weight-bold px-4"><i class="fas fa-save mr-1"></i> Save & Publish Slider</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <footer class="main-footer text-muted text-center py-3"><strong>Copyright &copy; 2026 Q-Commerce Admin.</strong></footer>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>
