<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Customer 360° Profile | {{ $customer->name }}</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  <style>
    :root {
      --brand-green: #28a745;
      --brand-green-dark: #1e7e34;
      --brand-green-light: #e6f4ea;
    }
    .nav-tabs .nav-link.active {
      border-top: 3px solid var(--brand-green);
      font-weight: 700;
      color: var(--brand-green-dark);
    }
    .profile-avatar-circle {
      width: 72px; height: 72px; font-size: 28px;
      background: var(--brand-green); color: #fff;
      display: inline-flex; align-items: center; justify-content: center;
      border-radius: 50%; box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .stat-card-360 {
      border-radius: 8px; border: 1px solid #e2e8f0; background: #fff;
      transition: transform 0.2s ease;
    }
    .stat-card-360:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.06); }
  </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
      <li class="nav-item"><a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a></li>
      <li class="nav-item d-none d-sm-inline-block"><a href="/admin/customers" class="nav-link">Customers</a></li>
      <li class="nav-item d-none d-sm-inline-block"><a href="#" class="nav-link active font-weight-bold text-success">{{ $customer->name }} 360° View</a></li>
    </ul>
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <a href="/admin/customers" class="btn btn-outline-secondary btn-sm font-weight-bold"><i class="fas fa-arrow-left mr-1"></i> Back to Customers</a>
      </li>
    </ul>
  </nav>

  @include('admin.layouts.sidebar')

  <div class="content-wrapper">
    <!-- Header -->
    <div class="content-header">
      <div class="container-fluid d-flex justify-content-between align-items-center flex-wrap">
        <div>
          <h1 class="m-0 font-weight-bold text-dark"><i class="fas fa-user-shield text-success mr-2"></i>Customer 360° Profile View</h1>
          <p class="text-muted small mb-0">Real-time customer analytics, complete order history, saved addresses, and active session details for <strong>{{ $customer->name }}</strong>.</p>
        </div>
        <div class="mt-2 mt-sm-0">
          @if(!empty($customer->is_vip))
            <span class="badge badge-warning text-dark px-3 py-2 font-weight-bold mr-2"><i class="fas fa-crown mr-1"></i> VIP Customer</span>
          @endif
          <span class="badge badge-{{ $customer->status === 'Active' ? 'success' : 'danger' }} px-3 py-2 font-weight-bold">{{ $customer->status ?? 'Active' }}</span>
        </div>
      </div>
    </div>

    <!-- Content -->
    <div class="content">
      <div class="container-fluid">

        @if(session('success'))
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
          </div>
        @endif

        <!-- Key Metrics Banner -->
        <div class="row">
          <div class="col-12 col-sm-6 col-lg-3 mb-3">
            <div class="stat-card-360 p-3">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <div class="text-muted small font-weight-bold text-uppercase">Wallet Balance</div>
                  <div class="h4 font-weight-bold text-success mb-0">₹{{ number_format($customer->wallet_balance ?? 0.00, 2) }}</div>
                </div>
                <div class="bg-light p-3 rounded-circle text-success"><i class="fas fa-wallet fa-2x"></i></div>
              </div>
            </div>
          </div>

          <div class="col-12 col-sm-6 col-lg-3 mb-3">
            <div class="stat-card-360 p-3">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <div class="text-muted small font-weight-bold text-uppercase">Total Lifetime Spend</div>
                  <div class="h4 font-weight-bold text-dark mb-0">₹{{ number_format($totalSpentAmount, 2) }}</div>
                </div>
                <div class="bg-light p-3 rounded-circle text-info"><i class="fas fa-coins fa-2x"></i></div>
              </div>
            </div>
          </div>

          <div class="col-12 col-sm-6 col-lg-3 mb-3">
            <div class="stat-card-360 p-3">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <div class="text-muted small font-weight-bold text-uppercase">Total Orders</div>
                  <div class="h4 font-weight-bold text-dark mb-0">{{ $totalOrdersCount }} <small class="text-muted">({{ $deliveredOrdersCount }} delivered)</small></div>
                </div>
                <div class="bg-light p-3 rounded-circle text-primary"><i class="fas fa-shopping-bag fa-2x"></i></div>
              </div>
            </div>
          </div>

          <div class="col-12 col-sm-6 col-lg-3 mb-3">
            <div class="stat-card-360 p-3">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <div class="text-muted small font-weight-bold text-uppercase">Avg Order Value (AOV)</div>
                  <div class="h4 font-weight-bold text-dark mb-0">₹{{ number_format($avgOrderValue, 2) }}</div>
                </div>
                <div class="bg-light p-3 rounded-circle text-warning"><i class="fas fa-chart-line fa-2x"></i></div>
              </div>
            </div>
          </div>
        </div>

        <!-- 360° Profile Tabbed Interface -->
        <div class="card card-outline card-success shadow-sm">
          <div class="card-header p-0 border-bottom-0">
            <ul class="nav nav-tabs" id="customer360Tabs" role="tablist">
              <li class="nav-item">
                <a class="nav-link active py-3 px-4" id="overview-tab" data-toggle="tab" href="#overview" role="tab" aria-controls="overview" aria-selected="true">
                  <i class="fas fa-user-circle mr-2 text-success"></i>Overview & Settings
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link py-3 px-4" id="orders-tab" data-toggle="tab" href="#orders" role="tab" aria-controls="orders" aria-selected="false">
                  <i class="fas fa-shopping-cart mr-2 text-info"></i>Order History ({{ $totalOrdersCount }})
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link py-3 px-4" id="addresses-tab" data-toggle="tab" href="#addresses" role="tab" aria-controls="addresses" aria-selected="false">
                  <i class="fas fa-map-marked-alt mr-2 text-warning"></i>Saved Addresses ({{ count($savedAddresses) }})
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link py-3 px-4" id="cart-tab" data-toggle="tab" href="#cart" role="tab" aria-controls="cart" aria-selected="false">
                  <i class="fas fa-shopping-basket mr-2 text-primary"></i>Live Active Cart ({{ count($liveCartItems) }})
                </a>
              </li>
            </ul>
          </div>

          <div class="card-body">
            <div class="tab-content" id="customer360TabContent">

              <!-- TAB 1: Overview & Settings -->
              <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview-tab">
                <div class="row">
                  <!-- Left Side: Profile Info Card -->
                  <div class="col-md-5 mb-3">
                    <div class="border rounded p-4 text-center bg-light">
                      <div class="profile-avatar-circle mb-3">
                        {{ strtoupper(substr($customer->name, 0, 1)) }}
                      </div>
                      <h4 class="font-weight-bold mb-1">{{ $customer->name }}</h4>
                      <p class="text-muted mb-2"><i class="fas fa-phone text-success mr-1"></i> {{ $customer->phone }}</p>
                      @if($customer->email)
                        <p class="text-muted small mb-3"><i class="fas fa-envelope text-info mr-1"></i> {{ $customer->email }}</p>
                      @endif

                      <div class="border-top pt-3 text-left">
                        <div class="d-flex justify-content-between py-2 border-bottom">
                          <span class="text-muted">Account Created:</span>
                          <span class="font-weight-bold">{{ $customer->created_at ?? 'N/A' }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-2 border-bottom">
                          <span class="text-muted">Favorite Fulfillment Hub:</span>
                          <span class="font-weight-bold text-success">{{ $favoriteStore }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-2 border-bottom">
                          <span class="text-muted">Preferred Payment:</span>
                          <span class="font-weight-bold">{{ $preferredPayment }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-2">
                          <span class="text-muted">Cancelled Orders:</span>
                          <span class="font-weight-bold text-danger">{{ $cancelledOrdersCount }}</span>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Right Side: Edit Settings & Wallet Form -->
                  <div class="col-md-7 mb-3">
                    <div class="border rounded p-4">
                      <h5 class="font-weight-bold text-dark mb-3"><i class="fas fa-cog text-success mr-2"></i>Manage Profile & Admin Controls</h5>

                      <form action="/admin/customers/{{ $customer->id }}/update-status" method="POST">
                        @csrf
                        <div class="form-row">
                          <div class="form-group col-md-6">
                            <label class="font-weight-bold">Account Status</label>
                            <select name="status" class="form-control">
                              <option value="Active" {{ ($customer->status ?? '') === 'Active' ? 'selected' : '' }}>Active</option>
                              <option value="Blocked" {{ ($customer->status ?? '') === 'Blocked' ? 'selected' : '' }}>Blocked</option>
                              <option value="Suspended" {{ ($customer->status ?? '') === 'Suspended' ? 'selected' : '' }}>Suspended</option>
                            </select>
                          </div>

                          <div class="form-group col-md-6 d-flex align-items-center pt-3">
                            <div class="custom-control custom-switch">
                              <input type="checkbox" class="custom-control-input" id="vipSwitch" name="is_vip" {{ !empty($customer->is_vip) ? 'checked' : '' }}>
                              <label class="custom-control-label font-weight-bold text-warning" for="vipSwitch">
                                <i class="fas fa-crown mr-1"></i> VIP Customer Flag
                              </label>
                            </div>
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="font-weight-bold text-success"><i class="fas fa-wallet mr-1"></i> Customer Wallet Cash (₹)</label>
                          <div class="input-group">
                            <div class="input-group-prepend">
                              <span class="input-group-text font-weight-bold bg-success text-white">₹</span>
                            </div>
                            <input type="number" step="0.01" min="0" name="wallet_balance" class="form-control font-weight-bold" value="{{ number_format($customer->wallet_balance ?? 0.00, 2, '.', '') }}" placeholder="0.00">
                          </div>
                          <small class="form-text text-muted">Directly update or credit wallet balance available for in-app checkout.</small>
                        </div>

                        <div class="form-group">
                          <label class="font-weight-bold">Internal Admin Notes</label>
                          <textarea name="notes" class="form-control" rows="3" placeholder="Special preferences, delivery instructions, or support notes...">{{ $customer->notes }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-success font-weight-bold"><i class="fas fa-save mr-1"></i> Save Profile Changes</button>
                      </form>
                    </div>
                  </div>
                </div>
              </div>

              <!-- TAB 2: Order History -->
              <div class="tab-pane fade" id="orders" role="tabpanel" aria-labelledby="orders-tab">
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <h5 class="font-weight-bold text-dark mb-0"><i class="fas fa-list-alt text-info mr-2"></i>Complete Customer Order History</h5>
                  <span class="badge badge-info px-3 py-2 font-weight-bold">{{ $totalOrdersCount }} Total Orders</span>
                </div>

                <div class="table-responsive">
                  <table class="table table-hover table-striped border">
                    <thead class="bg-dark text-white">
                      <tr>
                        <th>Order #</th>
                        <th>Store</th>
                        <th>Grand Total</th>
                        <th>Payment Method</th>
                        <th>Status</th>
                        <th>Order Date</th>
                        <th class="text-center">Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      @forelse($orders as $ord)
                      <tr>
                        <td class="font-weight-bold text-primary">
                          <a href="/admin/orders/{{ $ord->id }}" target="_blank" class="text-primary font-weight-bold">
                            {{ $ord->order_number }} <i class="fas fa-external-link-alt small ml-1"></i>
                          </a>
                        </td>
                        <td class="font-weight-bold small text-muted">{{ $ord->store_name ?? 'Main Krishnanagar Hub' }}</td>
                        <td class="text-success font-weight-bold">₹{{ number_format($ord->grand_total, 2) }}</td>
                        <td><span class="badge badge-light border"><i class="fas fa-credit-card text-muted mr-1"></i> {{ $ord->payment_method ?? 'COD' }}</span></td>
                        <td>
                          <span class="badge badge-{{ $ord->status === 'Delivered' ? 'success' : ($ord->status === 'Cancelled' ? 'danger' : 'info') }}">
                            {{ $ord->status }}
                          </span>
                        </td>
                        <td class="small text-muted">{{ $ord->created_at }}</td>
                        <td class="text-center">
                          <a href="/admin/orders/{{ $ord->id }}" class="btn btn-sm btn-outline-info font-weight-bold">
                            <i class="fas fa-eye mr-1"></i> View Order
                          </a>
                        </td>
                      </tr>
                      @if(!empty($ord->items))
                      <tr class="bg-light">
                        <td colspan="7" class="py-2 pl-4">
                          <div class="small text-muted font-weight-bold mb-1"><i class="fas fa-boxes mr-1"></i> Order Items Breakdown:</div>
                          <div class="d-flex flex-wrap">
                            @foreach($ord->items as $it)
                              <span class="badge badge-white border text-dark mr-2 mb-1 p-2">
                                <strong>{{ $it->product_name }}</strong> &times; {{ $it->quantity }} = <span class="text-success font-weight-bold">₹{{ number_format($it->total, 2) }}</span>
                              </span>
                            @endforeach
                          </div>
                        </td>
                      </tr>
                      @endif
                      @empty
                      <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                          <i class="fas fa-box-open fa-2x mb-2 text-secondary"></i>
                          <div>No order history found for this customer.</div>
                        </td>
                      </tr>
                      @endforelse
                    </tbody>
                  </table>
                </div>
              </div>

              <!-- TAB 3: Saved Addresses -->
              <div class="tab-pane fade" id="addresses" role="tabpanel" aria-labelledby="addresses-tab">
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <h5 class="font-weight-bold text-dark mb-0"><i class="fas fa-map-marked-alt text-warning mr-2"></i>Real Saved Delivery Locations</h5>
                  <span class="badge badge-warning text-dark px-3 py-2 font-weight-bold">{{ count($savedAddresses) }} Address(es)</span>
                </div>

                <div class="row">
                  @forelse($savedAddresses as $addr)
                  <div class="col-md-6 mb-3">
                    <div class="border rounded p-3 bg-white h-100 shadow-sm">
                      <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="font-weight-bold text-dark"><i class="fas fa-home text-success mr-2"></i> {{ $addr['type'] }}</span>
                        @if(!empty($addr['is_default']))
                          <span class="badge badge-success px-2 py-1">Primary Default</span>
                        @endif
                      </div>
                      <p class="text-dark font-weight-bold mb-1">{{ $addr['receiver_name'] ?? $customer->name }} <span class="text-muted font-weight-normal">({{ $addr['receiver_phone'] ?? $customer->phone }})</span></p>
                      <p class="text-muted small mb-2"><i class="fas fa-map-marker-alt text-danger mr-1"></i> {{ $addr['address'] }}</p>
                      <div class="small text-muted font-weight-bold">
                        <span>City: {{ $addr['city'] }}</span> | <span>Pincode: {{ $addr['pincode'] }}</span>
                      </div>
                      <div class="mt-2 pt-2 border-top">
                        <span class="badge badge-light border text-monospace"><i class="fas fa-compass text-info mr-1"></i> GPS: {{ $addr['latitude'] }}, {{ $addr['longitude'] }}</span>
                      </div>
                    </div>
                  </div>
                  @empty
                  <div class="col-12 py-4 text-center text-muted">
                    <i class="fas fa-map-pin fa-2x mb-2 text-secondary"></i>
                    <div>No saved delivery addresses found for this customer.</div>
                  </div>
                  @endforelse
                </div>
              </div>

              <!-- TAB 4: Live Cart -->
              <div class="tab-pane fade" id="cart" role="tabpanel" aria-labelledby="cart-tab">
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <h5 class="font-weight-bold text-dark mb-0"><i class="fas fa-shopping-basket text-primary mr-2"></i>Live Active Cart Items</h5>
                  <span class="badge badge-primary px-3 py-2 font-weight-bold">{{ count($liveCartItems) }} items in cart</span>
                </div>

                <div class="table-responsive">
                  <table class="table table-striped table-hover border mb-0">
                    <thead class="bg-light">
                      <tr>
                        <th>Product Title</th>
                        <th>Unit Price</th>
                        <th>Quantity</th>
                        <th class="text-right">Item Subtotal</th>
                      </tr>
                    </thead>
                    <tbody>
                      @php $cartTotal = 0; @endphp
                      @forelse($liveCartItems as $cItem)
                      @php $cartTotal += $cItem['total']; @endphp
                      <tr>
                        <td class="font-weight-bold">
                          <i class="fas fa-box text-success mr-2"></i> {{ $cItem['name'] }}
                          <span class="badge badge-light border ml-1">{{ $cItem['unit'] }}</span>
                        </td>
                        <td>₹{{ number_format($cItem['price'], 2) }}</td>
                        <td><span class="badge badge-info px-2 py-1">{{ $cItem['quantity'] }}</span></td>
                        <td class="text-right font-weight-bold text-success">₹{{ number_format($cItem['total'], 2) }}</td>
                      </tr>
                      @empty
                      <tr>
                        <td colspan="4" class="text-center py-4 text-muted">
                          <i class="fas fa-shopping-cart fa-2x mb-2 text-secondary"></i>
                          <div>No items currently in customer's live cart session.</div>
                        </td>
                      </tr>
                      @endforelse
                    </tbody>
                    @if(!empty($liveCartItems))
                    <tfoot>
                      <tr class="bg-light">
                        <td colspan="3" class="text-right font-weight-bold">Live Cart Total:</td>
                        <td class="text-right font-weight-bold text-success h5 mb-0">₹{{ number_format($cartTotal, 2) }}</td>
                      </tr>
                    </tfoot>
                    @endif
                  </table>
                </div>
              </div>

            </div>
          </div>
        </div>

      </div>
    </div>
  </div>

  <footer class="main-footer"><strong>Copyright &copy; 2026 Q-Commerce Admin | Customer 360° Engine.</strong></footer>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>
