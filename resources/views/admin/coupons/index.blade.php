<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Q-Commerce Admin | Coupons & Offers Engine</title>
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
      <li class="nav-item d-none d-sm-inline-block"><a href="/admin/coupons" class="nav-link active font-weight-bold">Coupons Engine</a></li>
    </ul>
  </nav>

  @include('admin.layouts.sidebar')

  <!-- Content Wrapper -->
  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid d-flex justify-content-between align-items-center">
        <div>
          <h1 class="m-0 font-weight-bold"><i class="fas fa-ticket-alt text-warning mr-2"></i>Coupons & Promo Offers</h1>
          <p class="text-muted mb-0 small">Configure device security limits, user targeting, store rules, & instant discounts.</p>
        </div>
        <div>
          <a href="/admin/coupons/create" class="btn btn-success btn-sm rounded-pill font-weight-bold px-3 shadow-sm">
            <i class="fas fa-plus-circle mr-1"></i> Create Security Coupon
          </a>
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

        <div class="card shadow-sm border-0 rounded-4">
          <div class="card-body p-0 table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="bg-light">
                <tr class="text-uppercase text-muted text-xs">
                  <th class="pl-4">Coupon Code & Offer</th>
                  <th>Discount Type</th>
                  <th>Min Subtotal</th>
                  <th>Security Rules & Restrictions</th>
                  <th>Redemptions</th>
                  <th>Status</th>
                  <th class="pr-4 text-right">Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse($coupons as $coupon)
                <tr>
                  <td class="pl-4">
                    <div class="font-weight-bold text-dark text-md">
                      <span class="badge badge-warning text-dark border px-2 py-1 font-monospace mr-1">
                        {{ $coupon->code }}
                      </span>
                    </div>
                    <div class="font-weight-bold text-sm text-dark mt-1">{{ $coupon->title }}</div>
                    <div class="text-muted text-xs">{{ Str::limit($coupon->description, 60) }}</div>
                  </td>
                  <td>
                    @if($coupon->discount_type === 'flat')
                      <span class="badge badge-success px-2 py-1">Flat ₹{{ number_format($coupon->discount_value, 0) }} OFF</span>
                    @elseif($coupon->discount_type === 'percentage')
                      <span class="badge badge-info px-2 py-1">{{ number_format($coupon->discount_value, 0) }}% OFF (Cap ₹{{ number_format($coupon->max_discount_amount ?? 0, 0) }})</span>
                    @else
                      <span class="badge badge-purple px-2 py-1" style="background-color:#6f42c1; color:white;">🚚 FREE Delivery</span>
                    @endif
                  </td>
                  <td>
                    <div class="font-weight-bold text-dark">₹{{ number_format($coupon->min_cart_amount, 0) }}</div>
                    <span class="text-muted text-xs">Minimum Cart</span>
                  </td>
                  <td>
                    <div class="d-flex flex-wrap gap-1">
                      @if($coupon->restrict_one_device)
                        <span class="badge badge-danger text-xs mr-1" title="1 Device 1 Time Apply">
                          <i class="fas fa-mobile-alt mr-1"></i>1 Device Limit
                        </span>
                      @endif
                      @if($coupon->allowed_order_type === 'pickup')
                        <span class="badge badge-secondary text-xs mr-1">🏬 Pickup Only</span>
                      @elseif($coupon->allowed_order_type === 'delivery')
                        <span class="badge badge-primary text-xs mr-1">🛵 Delivery Only</span>
                      @endif
                      @if($coupon->allowed_store_id)
                        <span class="badge badge-info text-xs mr-1"><i class="fas fa-store mr-1"></i>{{ $coupon->store_name }}</span>
                      @endif
                      @if($coupon->is_first_order_only)
                        <span class="badge badge-warning text-dark text-xs mr-1">✨ New Users Only</span>
                      @endif
                      @if($coupon->allowed_user_phones)
                        <span class="badge badge-dark text-xs mr-1"><i class="fas fa-user-lock mr-1"></i>Targeted Users</span>
                      @endif
                    </div>
                  </td>
                  <td>
                    <div class="font-weight-bold text-dark">{{ $coupon->total_uses_count }} Uses</div>
                    <div class="text-muted text-xs">Max {{ $coupon->max_uses_per_user }} per user</div>
                  </td>
                  <td>
                    @if($coupon->is_active)
                      <span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i>Active</span>
                    @else
                      <span class="badge badge-secondary px-2 py-1">Inactive</span>
                    @endif
                  </td>
                  <td class="pr-4 text-right">
                    <form action="/admin/coupons/{{ $coupon->id }}/toggle" method="POST" class="d-inline">
                      @csrf
                      <button type="submit" class="btn btn-sm btn-outline-{{ $coupon->is_active ? 'warning' : 'success' }} rounded-circle" title="Toggle Status">
                        <i class="fas fa-power-off"></i>
                      </button>
                    </form>
                    <form action="/admin/coupons/{{ $coupon->id }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete coupon {{ $coupon->code }}?');">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle ml-1" title="Delete">
                        <i class="fas fa-trash"></i>
                      </button>
                    </form>
                  </td>
                </tr>
                @empty
                <tr>
                  <td colspan="7" class="text-center py-5 text-muted">
                    <i class="fas fa-ticket-alt fa-2x d-block mb-2 text-secondary"></i>
                    No promotional coupons configured yet. Click "Create Security Coupon" to add one!
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
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>
