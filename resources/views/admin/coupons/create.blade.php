<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Q-Commerce Admin | Create Security Coupon</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
      <li class="nav-item"><a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a></li>
      <li class="nav-item d-none d-sm-inline-block"><a href="/admin" class="nav-link">Dashboard</a></li>
      <li class="nav-item d-none d-sm-inline-block"><a href="/admin/coupons" class="nav-link">Coupons Engine</a></li>
      <li class="nav-item d-none d-sm-inline-block"><a href="#" class="nav-link active font-weight-bold">Create Coupon</a></li>
    </ul>
  </nav>

  @include('admin.layouts.sidebar')

  <!-- Content Wrapper -->
  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid d-flex justify-content-between align-items-center">
        <div>
          <h1 class="m-0 font-weight-bold"><i class="fas fa-shield-alt text-success mr-2"></i>Create Security Coupon</h1>
          <p class="text-muted mb-0 small">Define discount value & enforce enterprise security rules (1 device limit, store targeting, user whitelist).</p>
        </div>
        <div>
          <a href="/admin/coupons" class="btn btn-outline-secondary btn-sm rounded-pill font-weight-bold px-3">
            <i class="fas fa-arrow-left mr-1"></i> Back to Coupons
          </a>
        </div>
      </div>
    </div>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <form action="/admin/coupons/store" method="POST">
          @csrf
          <div class="row">
            <!-- Left Box: Core Coupon Info -->
            <div class="col-md-7">
              <div class="card card-primary card-outline shadow-sm">
                <div class="card-header">
                  <h3 class="card-title font-weight-bold"><i class="fas fa-tag mr-2"></i>Coupon Code & Discount Details</h3>
                </div>
                <div class="card-body">
                  <div class="form-group">
                    <label for="code" class="font-weight-bold">Coupon Code <span class="text-danger">*</span></label>
                    <input type="text" name="code" id="code" class="form-control text-uppercase font-weight-bold" placeholder="e.g. WELCOME100, ONECARD30, FREEDEL" required>
                    <small class="form-text text-muted">Unique promotional code entered by users at checkout.</small>
                  </div>

                  <div class="form-group">
                    <label for="visibility" class="font-weight-bold">Coupon Access & Visibility <span class="text-danger">*</span></label>
                    <select name="visibility" id="visibility" class="form-control font-weight-bold">
                      <option value="public">🌐 Public Coupon (Visible in App Coupon List)</option>
                      <option value="private">🔒 Private Coupon (Hidden from list, valid ONLY by typing code)</option>
                    </select>
                    <small class="form-text text-muted">Private coupons will not be displayed in the app list; users must type the exact code manually.</small>
                  </div>

                  <div class="form-group">
                    <label for="title" class="font-weight-bold">Coupon Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" id="title" class="form-control" placeholder="e.g. Flat ₹30 Off or Get 10% OFF upto ₹200" required>
                  </div>

                  <div class="form-group">
                    <label for="description" class="font-weight-bold">Description / Terms</label>
                    <textarea name="description" id="description" class="form-control" rows="2" placeholder="e.g. Applicable only on OneCard Credit Cards. Max discount ₹30."></textarea>
                  </div>

                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="discount_type" class="font-weight-bold">Discount Type <span class="text-danger">*</span></label>
                        <select name="discount_type" id="discount_type" class="form-control font-weight-bold">
                          <option value="flat">Flat Amount OFF (₹)</option>
                          <option value="percentage">Percentage OFF (%)</option>
                          <option value="free_delivery">🚚 FREE Delivery Waiver</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="discount_value" class="font-weight-bold">Discount Value (₹ or %) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="discount_value" id="discount_value" class="form-control font-weight-bold" value="30.00" required>
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="max_discount_amount" class="font-weight-bold">Max Discount Cap (₹)</label>
                        <input type="number" step="0.01" name="max_discount_amount" id="max_discount_amount" class="form-control" placeholder="e.g. 200 (for % discounts)">
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="min_cart_amount" class="font-weight-bold">Minimum Cart Subtotal (₹)</label>
                        <input type="number" step="0.01" name="min_cart_amount" id="min_cart_amount" class="form-control font-weight-bold" value="149.00">
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Right Box: Security Rules & Targeting -->
            <div class="col-md-5">
              <div class="card card-warning card-outline shadow-sm">
                <div class="card-header">
                  <h3 class="card-title font-weight-bold"><i class="fas fa-lock text-warning mr-2"></i>Enterprise Security Controls</h3>
                </div>
                <div class="card-body">
                  <!-- Device Lock Rule -->
                  <div class="custom-control custom-checkbox mb-3 p-2 border rounded bg-light">
                    <input type="checkbox" class="custom-control-input" id="restrict_one_device" name="restrict_one_device" value="1" checked>
                    <label class="custom-control-label font-weight-bold text-dark" for="restrict_one_device">
                      📱 Restrict to 1 Device Only (Anti-Fraud)
                    </label>
                    <div class="small text-muted mt-1">Locks coupon redemption to max 1 use per physical mobile hardware fingerprint.</div>
                  </div>

                  <!-- First Order Only -->
                  <div class="custom-control custom-checkbox mb-3 p-2 border rounded bg-light">
                    <input type="checkbox" class="custom-control-input" id="is_first_order_only" name="is_first_order_only" value="1">
                    <label class="custom-control-label font-weight-bold text-dark" for="is_first_order_only">
                      ✨ First Order Only (New Users)
                    </label>
                  </div>

                  <!-- Order Type Restriction -->
                  <div class="form-group">
                    <label for="allowed_order_type" class="font-weight-bold">Fulfillment Restriction</label>
                    <select name="allowed_order_type" id="allowed_order_type" class="form-control">
                      <option value="all">Allowed on Both (Home Delivery & Store Pickup)</option>
                      <option value="delivery">🛵 Home Delivery Only</option>
                      <option value="pickup">🏬 Store Pickup Only</option>
                    </select>
                  </div>

                  <!-- Allowed Darkstore -->
                  <div class="form-group">
                    <label for="allowed_store_id" class="font-weight-bold">Darkstore Access</label>
                    <select name="allowed_store_id" id="allowed_store_id" class="form-control">
                      <option value="">Global (Valid Across All Darkstores)</option>
                      @foreach($stores as $store)
                        <option value="{{ $store->id }}">{{ $store->name }} ({{ $store->city }})</option>
                      @endforeach
                    </select>
                  </div>

                  <!-- Whitelisted User Phones -->
                  <div class="form-group">
                    <label for="allowed_user_phones" class="font-weight-bold">User Phone Whitelist (Optional)</label>
                    <input type="text" name="allowed_user_phones" id="allowed_user_phones" class="form-control" placeholder="e.g. 8016222991, 9830098300">
                    <small class="form-text text-muted">Comma-separated phone numbers for exclusive VIP codes.</small>
                  </div>

                  <!-- Usage Limits -->
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="max_uses_per_user" class="font-weight-bold">Uses Per Customer</label>
                        <input type="number" name="max_uses_per_user" id="max_uses_per_user" class="form-control" value="1">
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="max_global_uses" class="font-weight-bold">Global Redemption Limit</label>
                        <input type="number" name="max_global_uses" id="max_global_uses" class="form-control" placeholder="e.g. 1000">
                      </div>
                    </div>
                  </div>

                  <button type="submit" class="btn btn-success btn-block font-weight-bold py-2 mt-3 shadow-sm">
                    <i class="fas fa-check-circle mr-1"></i> Save & Publish Security Coupon
                  </button>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </section>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>
