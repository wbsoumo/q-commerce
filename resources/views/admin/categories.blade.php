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
        <button class="btn btn-success font-weight-bold" data-toggle="modal" data-target="#createCategoryModal">
          <i class="fas fa-plus mr-1"></i> Create New Category
        </button>
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
                      <img src="{{ $cat->image }}" width="45" height="45" style="object-fit:cover; border-radius:8px;" class="border">
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
              <select name="icon" class="form-control font-weight-bold">
                <option value="shopping_bag_outlined">🛍️ Shopping Bag (All)</option>
                <option value="festival_outlined">🪔 Festival / Lights</option>
                <option value="headphones_outlined">🎧 Electronics / Headphones</option>
                <option value="brush_outlined">💄 Beauty & Cosmetics</option>
                <option value="card_giftcard_outlined">🎁 Gifting & Sweets</option>
                <option value="local_hospital_outlined">🏥 Pharmacy & Health</option>
                <option value="pets_outlined">🐾 Pet Care</option>
                <option value="toys_outlined">🧸 Toys & Games</option>
                <option value="fastfood_outlined">🍔 Fast Food & Snacks</option>
                <option value="local_drink_outlined">🥤 Beverages & Cold Drinks</option>
                <option value="local_grocery_store_outlined">🛒 Grocery & Kitchen</option>
              </select>
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
              <select name="icon" id="edit_cat_icon" class="form-control font-weight-bold">
                <option value="shopping_bag_outlined">🛍️ Shopping Bag (All)</option>
                <option value="festival_outlined">🪔 Festival / Lights</option>
                <option value="headphones_outlined">🎧 Electronics / Headphones</option>
                <option value="brush_outlined">💄 Beauty & Cosmetics</option>
                <option value="card_giftcard_outlined">🎁 Gifting & Sweets</option>
                <option value="local_hospital_outlined">🏥 Pharmacy & Health</option>
                <option value="pets_outlined">🐾 Pet Care</option>
                <option value="toys_outlined">🧸 Toys & Games</option>
                <option value="fastfood_outlined">🍔 Fast Food & Snacks</option>
                <option value="local_drink_outlined">🥤 Beverages & Cold Drinks</option>
                <option value="local_grocery_store_outlined">🛒 Grocery & Kitchen</option>
              </select>
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

  <footer class="main-footer"><strong>Copyright &copy; 2026 Q-Commerce Admin.</strong></footer>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<script>
  function editCategory(cat) {
    $('#edit_cat_id').val(cat.id);
    $('#edit_cat_name').val(cat.name);
    $('#edit_cat_icon').val(cat.icon || 'shopping_bag_outlined');
    $('#edit_cat_image_url').val(cat.image || '');
    $('#edit_cat_order').val(cat.display_order || 0);
    $('#editShowHp').prop('checked', !!cat.show_on_homepage);
    $('#editCategoryModal').modal('show');
  }
</script>
</body>
</html>
