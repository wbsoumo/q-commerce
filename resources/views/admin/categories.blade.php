<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Q-Commerce Admin | Category Management</title>
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
      <li class="nav-item d-none d-sm-inline-block"><a href="#" class="nav-link active font-weight-bold">Categories</a></li>
    </ul>
  </nav>

  @include('admin.layouts.sidebar')

  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid d-flex justify-content-between align-items-center">
        <h1 class="m-0 font-weight-bold"><i class="fas fa-folder text-warning mr-2"></i>Category Management</h1>
        <div>
          <a href="/admin/categories/sync-icons" class="btn btn-info font-weight-bold mr-2" onclick="return confirm('Assign clean grocery icons to all categories?');">
            <i class="fas fa-sync-alt mr-1"></i> Sync Category Icons
          </a>
          <button class="btn btn-success font-weight-bold" data-toggle="modal" data-target="#createCategoryModal">
            <i class="fas fa-plus mr-1"></i> Create New Category
          </button>
        </div>
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

        <div class="card card-outline card-success">
          <div class="card-header"><h3 class="card-title font-weight-bold">All Product Categories</h3></div>
          <div class="card-body p-0">
            <table class="table table-striped align-middle">
              <thead>
                <tr>
                  <th>#ID</th>
                  <th>Image</th>
                  <th>Category Name</th>
                  <th>Slug</th>
                  <th>Display Order</th>
                  <th>Front Page Status</th>
                  <th class="text-right">Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse($categories as $cat)
                <tr>
                  <td>{{ $cat->id }}</td>
                  <td>
                    @if(!empty($cat->image))
                      @php
                        $catImg = $cat->image;
                        if (\Illuminate\Support\Str::startsWith($catImg, ['http://', 'https://'])) {
                            $parsed = parse_url($catImg);
                            if (isset($parsed['path']) && \Illuminate\Support\Str::startsWith($parsed['path'], '/uploads/')) {
                                $catImg = $parsed['path'];
                            } else {
                                $catImg = preg_replace('/^http:/i', 'https:', $catImg);
                            }
                        } else {
                            $catImg = '/' . ltrim($catImg, '/');
                        }
                      @endphp
                      <img src="{{ $catImg }}" width="45" height="45" style="object-fit:cover; border-radius:8px;" class="border" onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name={{ urlencode($cat->name) }}&background=0c831f&color=fff&size=100';">
                    @else
                      <span class="badge badge-secondary">No Image</span>
                    @endif
                  </td>
                  <td class="font-weight-bold text-dark">{{ $cat->name }}</td>
                  <td><code>{{ $cat->slug }}</code></td>
                  <td><span class="badge badge-info">{{ $cat->display_order }}</span></td>
                  <td>
                    @if($cat->show_on_homepage ?? true)
                      <span class="badge badge-success"><i class="fas fa-eye mr-1"></i>Visible on Home</span>
                    @else
                      <span class="badge badge-secondary">Hidden</span>
                    @endif
                  </td>
                  <td class="text-right">
                    <button class="btn btn-warning btn-sm font-weight-bold" onclick='editCategory(@json($cat))'>
                      <i class="fas fa-edit mr-1"></i> Edit
                    </button>
                  </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-4">No categories found.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- CREATE CATEGORY MODAL -->
  <div class="modal fade" id="createCategoryModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <form action="/admin/categories/store" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="modal-header bg-success text-white">
            <h5 class="modal-header-title font-weight-bold"><i class="fas fa-folder-plus mr-2"></i>Create New Category</h5>
            <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label class="font-weight-bold">Category Name</label>
              <input type="text" name="name" class="form-control" placeholder="e.g. Snack Foods or Cold Drinks" required>
            </div>
            <div class="form-group">
              <label class="font-weight-bold"><i class="fas fa-icons text-primary mr-1"></i> Choose Header Tab Icon</label>
              <input type="hidden" name="icon" id="create_cat_icon_value" value="shopping_bag_outlined">
              <div class="d-flex align-items-center p-2 border rounded bg-light">
                <div id="create_cat_icon_preview" class="btn btn-dark btn-circle mr-3" style="width:42px; height:42px; display:flex; align-items:center; justify-content:center; border-radius:50%; font-size:18px;">
                  <i class="fas fa-shopping-bag"></i>
                </div>
                <div class="flex-grow-1">
                  <span id="create_cat_icon_label" class="font-weight-bold text-dark d-block">Shopping Bag (All)</span>
                  <small class="text-muted" id="create_cat_icon_code">shopping_bag_outlined</small>
                </div>
                <button type="button" class="btn btn-outline-primary btn-sm font-weight-bold" onclick="openIconPicker('create')">
                  <i class="fas fa-th mr-1"></i> Browse Icon Library
                </button>
              </div>
            </div>
            <div class="form-group">
              <label class="font-weight-bold">Upload Category Image File</label>
              <input type="file" name="image_file" class="form-control-file border p-2 rounded w-100" accept="image/*">
              <small class="form-text text-muted">Or paste image URL below:</small>
              <input type="text" name="image_url" class="form-control mt-1" placeholder="https://images.unsplash.com/...">
            </div>
            <div class="form-group">
              <label class="font-weight-bold">Display Order</label>
              <input type="number" name="display_order" class="form-control" value="0">
            </div>
            <div class="custom-control custom-switch">
              <input type="checkbox" class="custom-control-input" id="createShowHp" name="show_on_homepage" value="1" checked>
              <label class="custom-control-label font-weight-bold" for="createShowHp">Show on Front Page</label>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-success font-weight-bold"><i class="fas fa-save mr-1"></i> Save Category</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- EDIT CATEGORY MODAL -->
  <div class="modal fade" id="editCategoryModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <form action="/admin/categories/update" method="POST" enctype="multipart/form-data">
          @csrf
          <input type="hidden" name="id" id="edit_cat_id">
          <div class="modal-header bg-warning text-dark">
            <h5 class="modal-header-title font-weight-bold"><i class="fas fa-edit mr-2"></i>Edit Category</h5>
            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label class="font-weight-bold">Category Name</label>
              <input type="text" name="name" id="edit_cat_name" class="form-control" required>
            </div>
            <div class="form-group">
              <label class="font-weight-bold"><i class="fas fa-icons text-primary mr-1"></i> Choose Header Tab Icon</label>
              <input type="hidden" name="icon" id="edit_cat_icon_value" value="shopping_bag_outlined">
              <div class="d-flex align-items-center p-2 border rounded bg-light">
                <div id="edit_cat_icon_preview" class="btn btn-dark btn-circle mr-3" style="width:42px; height:42px; display:flex; align-items:center; justify-content:center; border-radius:50%; font-size:18px;">
                  <i class="fas fa-shopping-bag"></i>
                </div>
                <div class="flex-grow-1">
                  <span id="edit_cat_icon_label" class="font-weight-bold text-dark d-block">Shopping Bag (All)</span>
                  <small class="text-muted" id="edit_cat_icon_code">shopping_bag_outlined</small>
                </div>
                <button type="button" class="btn btn-outline-primary btn-sm font-weight-bold" onclick="openIconPicker('edit')">
                  <i class="fas fa-th mr-1"></i> Browse Icon Library
                </button>
              </div>
            </div>
            <div class="form-group">
              <label class="font-weight-bold">Upload New Image File</label>
              <input type="file" name="image_file" class="form-control-file border p-2 rounded w-100" accept="image/*">
              <small class="form-text text-muted">Or update image URL:</small>
              <input type="text" name="image_url" id="edit_cat_image_url" class="form-control mt-1">
            </div>
            <div class="form-group">
              <label class="font-weight-bold">Display Order</label>
              <input type="number" name="display_order" id="edit_cat_order" class="form-control">
            </div>
            <div class="custom-control custom-switch">
              <input type="checkbox" class="custom-control-input" id="editShowHp" name="show_on_homepage" value="1">
              <label class="custom-control-label font-weight-bold" for="editShowHp">Show on Front Page</label>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-warning font-weight-bold"><i class="fas fa-save mr-1"></i> Update Category</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- INTERACTIVE ICON PICKER MODAL -->
  <div class="modal fade" id="iconPickerModal" tabindex="-1" style="z-index: 1060;">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title font-weight-bold"><i class="fas fa-icons mr-2"></i>Select Material & Category Icon</h5>
          <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <div class="modal-body">
          <input type="text" id="iconSearchInput" class="form-control mb-3 font-weight-bold" placeholder="🔍 Search icons by name (e.g. food, bag, phone, gift, drinks)..." onkeyup="filterIconGrid()">
          <div class="row" id="iconGridContainer" style="max-height: 380px; overflow-y: auto;">
            <!-- Dynamic Icon Cards Injected via JS -->
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
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
  let activeIconTarget = 'create';

  const iconLibrary = [
    { code: 'shopping_bag_outlined', fa: 'fa-shopping-bag', label: 'Shopping Bag (All)', tags: 'bag shop store default' },
    { code: 'festival_outlined', fa: 'fa-box-open', label: 'Festival / Diya / Lights', tags: 'diwali lights fest celebration' },
    { code: 'headphones_outlined', fa: 'fa-headphones', label: 'Electronics / Headphones', tags: 'phone gadgets audio electronics' },
    { code: 'brush_outlined', fa: 'fa-paint-brush', label: 'Beauty & Cosmetics', tags: 'makeup beauty cream face wash' },
    { code: 'card_giftcard_outlined', fa: 'fa-gift', label: 'Gifting & Sweets', tags: 'gift hamper sweets diwali box' },
    { code: 'local_hospital_outlined', fa: 'fa-first-aid', label: 'Pharmacy & Health', tags: 'medical medicine doctor hospital' },
    { code: 'pets_outlined', fa: 'fa-paw', label: 'Pet Care & Dog Food', tags: 'dog cat pet animal food' },
    { code: 'toys_outlined', fa: 'fa-gamepad', label: 'Toys & Games', tags: 'toys kids puzzle game' },
    { code: 'fastfood_outlined', fa: 'fa-hamburger', label: 'Fast Food & Snacks', tags: 'chips snack burger food' },
    { code: 'local_drink_outlined', fa: 'fa-wine-bottle', label: 'Beverages & Cold Drinks', tags: 'soda drink juice water milk' },
    { code: 'local_grocery_store_outlined', fa: 'fa-shopping-cart', label: 'Grocery & Kitchen', tags: 'atta dal rice oil grocery' },
  ];

  function renderIconGrid() {
    let html = '';
    iconLibrary.forEach(item => {
      html += `
        <div class="col-md-3 col-6 mb-3 icon-card-col" data-tags="${item.code} ${item.label.toLowerCase()} ${item.tags}">
          <div class="card h-100 text-center p-3 border hover-shadow" style="cursor:pointer; transition: transform 0.2s;" onclick="selectIcon('${item.code}', '${item.fa}', '${item.label}')">
            <div class="mb-2 text-primary" style="font-size: 28px;">
              <i class="fas ${item.fa}"></i>
            </div>
            <span class="font-weight-bold text-dark small d-block">${item.label}</span>
            <code class="text-muted d-block mt-1" style="font-size: 10px;">${item.code}</code>
          </div>
        </div>
      `;
    });
    $('#iconGridContainer').html(html);
  }

  function filterIconGrid() {
    const q = $('#iconSearchInput').val().toLowerCase();
    $('.icon-card-col').each(function() {
      const tags = $(this).data('tags');
      if (tags.includes(q)) {
        $(this).show();
      } else {
        $(this).hide();
      }
    });
  }

  function openIconPicker(target) {
    activeIconTarget = target;
    renderIconGrid();
    $('#iconPickerModal').modal('show');
  }

  function selectIcon(code, fa, label) {
    $(`#${activeIconTarget}_cat_icon_value`).val(code);
    $(`#${activeIconTarget}_cat_icon_preview`).html(`<i class="fas ${fa}"></i>`);
    $(`#${activeIconTarget}_cat_icon_label`).text(label);
    $(`#${activeIconTarget}_cat_icon_code`).text(code);
    $('#iconPickerModal').modal('hide');
  }

  function editCategory(cat) {
    $('#edit_cat_id').val(cat.id);
    $('#edit_cat_name').val(cat.name);
    let imgVal = cat.image || '';
    if (imgVal.startsWith('http://') || imgVal.startsWith('https://')) {
      try {
        let u = new URL(imgVal);
        if (u.pathname.startsWith('/uploads/')) {
          imgVal = u.pathname;
        }
      } catch(e){}
    } else if (imgVal && !imgVal.startsWith('/')) {
      imgVal = '/' + imgVal;
    }
    $('#edit_cat_image_url').val(imgVal);
    $('#edit_cat_order').val(cat.display_order || 0);
    $('#editShowHp').prop('checked', !!cat.show_on_homepage);

    const found = iconLibrary.find(i => i.code === (cat.icon || 'shopping_bag_outlined')) || iconLibrary[0];
    selectIcon(found.code, found.fa, found.label);

    $('#editCategoryModal').modal('show');
  }
</script>
</body>
</html>
