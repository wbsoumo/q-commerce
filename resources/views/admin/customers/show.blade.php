<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Customer Profile | {{ $customer->name }}</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
      <li class="nav-item"><a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a></li>
      <li class="nav-item d-none d-sm-inline-block"><a href="/admin/customers" class="nav-link">Customers</a></li>
      <li class="nav-item d-none d-sm-inline-block"><a href="#" class="nav-link active font-weight-bold">{{ $customer->name }} Profile</a></li>
    </ul>
  </nav>

  @include('admin.layouts.sidebar')

  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid d-flex justify-content-between align-items-center">
        <div>
          <h1 class="m-0 font-weight-bold">Customer 360° Profile View</h1>
          <p class="text-muted mb-0">Detailed activity, active cart, saved locations, and complete order history for {{ $customer->name }}</p>
        </div>
        <a href="/admin/customers" class="btn btn-outline-secondary font-weight-bold"><i class="fas fa-arrow-left mr-1"></i> Back to Customers</a>
      </div>
    </div>

    <div class="content">
      <div class="container-fluid">
        @if(session('success'))
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
          </div>
        @endif

        <!-- Quick Summary Stats Widgets -->
        <div class="row">
          <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
              <div class="inner">
                <h3>{{ $totalOrdersCount }}</h3>
                <p>Total Orders Placed</p>
              </div>
              <div class="icon"><i class="fas fa-shopping-cart"></i></div>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
              <div class="inner">
                <h3>₹{{ number_format($totalSpentAmount, 2) }}</h3>
                <p>Lifetime Spending</p>
              </div>
              <div class="icon"><i class="fas fa-wallet"></i></div>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
              <div class="inner">
                <h3>{{ $deliveredOrdersCount }}</h3>
                <p>Successfully Delivered</p>
              </div>
              <div class="icon"><i class="fas fa-check-circle"></i></div>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
              <div class="inner">
                <h3>{{ count($savedAddresses) }}</h3>
                <p>Saved Delivery Addresses</p>
              </div>
              <div class="icon"><i class="fas fa-map-marker-alt"></i></div>
            </div>
          </div>
        </div>

        <div class="row">
          <!-- Left Column: Profile Card & Status Management -->
          <div class="col-md-4">
            <div class="card card-primary card-outline shadow-sm">
              <div class="card-body box-profile text-center">
                <div class="text-center mb-3">
                  <div class="d-inline-flex align-items-center justify-content-center bg-primary text-white rounded-circle font-weight-bold" style="width: 72px; height: 72px; fontSize: 28px;">
                    {{ strtoupper(substr($customer->name, 0, 1)) }}
                  </div>
                </div>
                <h3 class="profile-username font-weight-bold mb-1">{{ $customer->name }}</h3>
                <p class="text-muted"><i class="fas fa-phone text-primary mr-1"></i> {{ $customer->phone }}</p>

                <div class="border-top pt-3">
                  <form action="/admin/customers/{{ $customer->id }}/update-status" method="POST" class="text-left">
                    @csrf
                    <div class="form-group">
                      <label class="font-weight-bold">Account Status</label>
                      <select name="status" class="form-control">
                        <option value="Active" {{ $customer->status === 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Blocked" {{ $customer->status === 'Blocked' ? 'selected' : '' }}>Blocked</option>
                        <option value="Suspended" {{ $customer->status === 'Suspended' ? 'selected' : '' }}>Suspended</option>
                      </select>
                    </div>
                    <div class="custom-control custom-switch mb-3">
                      <input type="checkbox" class="custom-control-input" id="vipSwitch" name="is_vip" {{ !empty($customer->is_vip) ? 'checked' : '' }}>
                      <label class="custom-control-label font-weight-bold text-warning" for="vipSwitch"><i class="fas fa-crown mr-1"></i> VIP Customer Flag</label>
                    </div>
                    <div class="form-group">
                      <label class="font-weight-bold">Internal Admin Notes</label>
                      <textarea name="notes" class="form-control" rows="3" placeholder="Special preferences or delivery instructions...">{{ $customer->notes }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block font-weight-bold"><i class="fas fa-save mr-1"></i> Save Profile Settings</button>
                  </form>
                </div>
              </div>
            </div>

            <!-- Saved Addresses Card -->
            <div class="card card-outline card-warning shadow-sm">
              <div class="card-header font-weight-bold">
                <i class="fas fa-map-marked-alt text-warning mr-2"></i> Saved Addresses ({{ count($savedAddresses) }})
              </div>
              <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                  @foreach($savedAddresses as $addr)
                  <li class="list-group-item">
                    <div class="d-flex justify-content-between align-items-center">
                      <span class="font-weight-bold text-dark"><i class="fas fa-home text-muted mr-1"></i> {{ $addr['type'] }}</span>
                      @if($addr['is_default'])
                        <span class="badge badge-success">Default</span>
                      @endif
                    </div>
                    <p class="text-muted small mb-1 mt-1">{{ $addr['address'] }}</p>
                    <span class="badge badge-light text-monospace"><i class="fas fa-compass text-info mr-1"></i> GPS: {{ $addr['latitude'] }}, {{ $addr['longitude'] }}</span>
                  </li>
                  @endforeach
                </ul>
              </div>
            </div>
          </div>

          <!-- Right Column: Live Cart, Order History & Preferences -->
          <div class="col-md-8">
            <!-- 1. Live Active Cart Items Card -->
            <div class="card card-outline card-success shadow-sm mb-4">
              <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title font-weight-bold text-success mb-0"><i class="fas fa-shopping-cart mr-2"></i>Live Active Cart (In-App Session)</h3>
                <span class="badge badge-success font-weight-bold px-3 py-2">{{ count($liveCartItems) }} items in cart</span>
              </div>
              <div class="card-body p-0">
                <table class="table table-striped table-hover mb-0">
                  <thead>
                    <tr>
                      <th>Product</th>
                      <th>Unit Price</th>
                      <th>Quantity</th>
                      <th>Item Subtotal</th>
                    </tr>
                  </thead>
                  <tbody>
                    @php $cartTotal = 0; @endphp
                    @forelse($liveCartItems as $cItem)
                    @php $cartTotal += $cItem['total']; @endphp
                    <tr>
                      <td class="font-weight-bold">
                        <i class="fas fa-box text-muted mr-2"></i> {{ $cItem['name'] }}
                        <span class="badge badge-light ml-1">{{ $cItem['unit'] }}</span>
                      </td>
                      <td>₹{{ number_format($cItem['price'], 2) }}</td>
                      <td><span class="badge badge-info">{{ $cItem['quantity'] }}</span></td>
                      <td class="font-weight-bold text-success">₹{{ number_format($cItem['total'], 2) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center py-3 text-muted">No items currently in customer's live cart.</td></tr>
                    @endforelse
                  </tbody>
                  @if(!empty($liveCartItems))
                  <tfoot>
                    <tr class="bg-light">
                      <td colspan="3" class="text-right font-weight-bold">Live Cart Total:</td>
                      <td class="font-weight-bold text-success font-size-16">₹{{ number_format($cartTotal, 2) }}</td>
                    </tr>
                  </tfoot>
                  @endif
                </table>
              </div>
            </div>

            <!-- 2. Order History Card -->
            <div class="card card-outline card-info shadow-sm">
              <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title font-weight-bold"><i class="fas fa-history mr-2"></i>Complete Order History</h3>
                <span class="badge badge-info font-weight-bold px-3 py-2">{{ count($orders) }} orders</span>
              </div>
              <div class="card-body p-0">
                <table class="table table-striped table-hover mb-0">
                  <thead>
                    <tr>
                      <th>Order #</th>
                      <th>Grand Total</th>
                      <th>Payment</th>
                      <th>Status</th>
                      <th>Order Date</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($orders as $ord)
                    <tr>
                      <td class="font-weight-bold text-primary">{{ $ord->order_number }}</td>
                      <td class="text-success font-weight-bold">₹{{ number_format($ord->grand_total, 2) }}</td>
                      <td><span class="badge badge-light"><i class="fas fa-credit-card mr-1"></i> {{ $ord->payment_method ?? 'COD' }}</span></td>
                      <td>
                        <span class="badge badge-{{ $ord->status === 'Delivered' ? 'success' : ($ord->status === 'Cancelled' ? 'danger' : 'info') }}">
                          {{ $ord->status }}
                        </span>
                      </td>
                      <td class="small text-muted">{{ $ord->created_at }}</td>
                      <td>
                        <a href="/admin/orders/{{ $ord->id }}" class="btn btn-xs btn-outline-info font-weight-bold"><i class="fas fa-eye mr-1"></i> View Order Details</a>
                      </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-4 text-muted">No past order history found for this customer.</td></tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <footer class="main-footer"><strong>Copyright &copy; 2026 Q-Commerce Admin.</strong></footer>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>
