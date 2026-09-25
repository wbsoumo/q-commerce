<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Order Timeline #{{ $order->order_number }}</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,600,700&display=fallback">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  <style>
    :root {
      --primary-green: #0c831f;
      --primary-hover: #096818;
    }
    .brand-link { background-color: var(--primary-green) !important; }
    .btn-success { background-color: var(--primary-green); border-color: var(--primary-green); }
    .btn-success:hover { background-color: var(--primary-hover); border-color: var(--primary-hover); }
  </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  <nav class="main-header navbar navbar-expand navbar-white navbar-light shadow-sm">
    <ul class="navbar-nav">
      <li class="nav-item"><a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a></li>
      <li class="nav-item d-none d-sm-inline-block"><a href="/admin" class="nav-link">Dashboard</a></li>
      <li class="nav-item d-none d-sm-inline-block"><a href="/admin/orders" class="nav-link">Orders</a></li>
      <li class="nav-item d-none d-sm-inline-block"><a href="#" class="nav-link active font-weight-bold text-success">Order #{{ $order->order_number }}</a></li>
    </ul>
  </nav>

  @include('admin.layouts.sidebar')

  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid d-flex justify-content-between align-items-center">
        <h1 class="m-0 font-weight-bold">Order Details & Lifecycle Timeline</h1>
        <span class="badge badge-lg badge-success p-2" style="font-size: 1.1rem;">Current Status: {{ $order->status }}</span>
      </div>
    </div>

    <div class="content">
      <div class="container-fluid">
        @if(session('success'))
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
          </div>
        @endif

        <div class="row">
          <!-- Order Info -->
          <div class="col-md-7">
            <div class="card card-outline card-success">
              <div class="card-header"><h3 class="card-title font-weight-bold"><i class="fas fa-receipt mr-2"></i>Order Summary</h3></div>
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <p class="mb-0"><strong>Customer:</strong> {{ $order->user_name }} ({{ $order->user_phone }})</p>
                  @if(($order->order_type ?? 'delivery') === 'pickup')
                    <span class="badge badge-warning p-2 font-weight-bold"><i class="fas fa-store mr-1"></i> STORE PICKUP ORDER</span>
                  @else
                    <span class="badge badge-info p-2 font-weight-bold"><i class="fas fa-truck mr-1"></i> HOME DELIVERY</span>
                  @endif
                </div>

                @if(!empty($order->is_for_someone_else) || !empty($order->receiver_name))
                  <div class="alert alert-warning p-2 small mb-3">
                    <i class="fas fa-user-friends mr-1"></i> <strong>Order for Someone Else:</strong><br>
                    <strong>Receiver Name:</strong> {{ $order->receiver_name ?? 'N/A' }} | <strong>Receiver Phone:</strong> {{ $order->receiver_phone ?? 'N/A' }}
                  </div>
                @endif

                @if(($order->order_type ?? 'delivery') === 'pickup')
                  <div class="alert alert-info p-2 small mb-3">
                    <i class="fas fa-clock mr-1"></i> <strong>Pickup Schedule & Store Window:</strong><br>
                    <strong>Scheduled Date:</strong> {{ $order->pickup_date ?? date('Y-m-d') }} | <strong>Time Slot:</strong> {{ $order->pickup_time ?? '10:00 AM - 11:00 AM' }}<br>
                    <span class="text-muted">Store Hours: {{ $order->pickup_details->store_opening_time ?? '06:00 AM' }} - {{ $order->pickup_details->store_closing_time ?? '11:00 PM' }}</span>
                  </div>
                @else
                  <p><strong>Delivery Address:</strong> {{ $order->delivery_address }}</p>
                @endif

                <p><strong>Payment Method:</strong> {{ $order->payment_method }}</p>
                <hr>
                <h5 class="font-weight-bold">Items Purchased</h5>
                <table class="table table-sm">
                  <thead>
                    <tr><th>Item</th><th>Price</th><th>Qty</th><th>Subtotal</th></tr>
                  </thead>
                  <tbody>
                    @foreach($items as $it)
                    <tr>
                      <td>{{ $it->product_name }}</td>
                      <td>₹{{ $it->price }}</td>
                      <td>{{ $it->quantity }}</td>
                      <td class="font-weight-bold">₹{{ $it->total }}</td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
                <div class="text-right mt-3">
                  <p class="mb-1">Subtotal: <strong>₹{{ $order->subtotal }}</strong></p>
                  <p class="mb-1">Delivery Fee: <strong>₹{{ $order->delivery_fee }}</strong></p>
                  <h4 class="text-success font-weight-bold">Grand Total: ₹{{ $order->grand_total }}</h4>
                </div>
              </div>
            </div>
          </div>

          <!-- Timeline & Lifecycle Update -->
          <div class="col-md-5">
            <div class="card card-outline card-primary">
              <div class="card-header"><h3 class="card-title font-weight-bold"><i class="fas fa-tasks mr-2"></i>Status Lifecycle Control</h3></div>
              <div class="card-body">
                
                @php
                  $currentStatus = $order->status ?? 'Pending';
                  $orderType = $order->order_type ?? 'delivery';
                  $isPickup = $orderType === 'pickup';
                @endphp

                <!-- Lifecycle Step Progress Header -->
                <div class="mb-3 text-center">
                  <div class="btn-group btn-group-toggle w-100 mb-2">
                    <span class="btn btn-xs {{ in_array($currentStatus, ['Pending', 'Confirmed', 'Packing', 'Processing', 'Preparing', 'Out for Delivery', 'Ready for Pickup', 'Delivered']) ? 'btn-success' : 'btn-light border' }}">1. Confirmed</span>
                    <span class="btn btn-xs {{ in_array($currentStatus, ['Packing', 'Processing', 'Preparing', 'Out for Delivery', 'Ready for Pickup', 'Delivered']) ? 'btn-success' : 'btn-light border' }}">2. Packing</span>
                    <span class="btn btn-xs {{ in_array($currentStatus, ['Out for Delivery', 'Ready for Pickup', 'Delivered']) ? 'btn-success' : 'btn-light border' }}">{{ $isPickup ? '3. Ready' : '3. Out for Delivery' }}</span>
                    <span class="btn btn-xs {{ $currentStatus === 'Delivered' ? 'btn-success' : 'btn-light border' }}">4. Delivered</span>
                  </div>
                </div>

                <!-- Next Stage Action Control (No Dropdown) -->
                @if($currentStatus === 'Pending' || $currentStatus === 'Confirmed')
                  <!-- Stage 1 -> Stage 2: Confirmed to Packing -->
                  <form action="/admin/orders/{{ $order->id }}/update-status" method="POST">
                    @csrf
                    <input type="hidden" name="status" value="Packing">
                    <div class="alert alert-info p-3 mb-3">
                      <i class="fas fa-box-open mr-2"></i> <strong>Next Step: Advance Order to Packing</strong><br>
                      <span class="small">Mark order as accepted and start preparing items in store.</span>
                    </div>
                    <button type="submit" class="btn btn-lg btn-block btn-primary font-weight-bold py-3 shadow-sm">
                      <i class="fas fa-boxes mr-2"></i> Advance to Packing 📦
                    </button>
                  </form>

                @elseif($currentStatus === 'Packing' || $currentStatus === 'Processing' || $currentStatus === 'Preparing')
                  <!-- Stage 2 -> Stage 3 -->
                  @if(!$isPickup)
                    <!-- Home Delivery: Prompt Delivery Partner Assignment for Out for Delivery -->
                    <form action="/admin/orders/{{ $order->id }}/update-status" method="POST">
                      @csrf
                      <input type="hidden" name="status" value="Out for Delivery">
                      
                      <div class="card card-outline card-warning mb-3">
                        <div class="card-header py-2 font-weight-bold text-dark">
                          <i class="fas fa-motorcycle text-warning mr-1"></i> Select Delivery Partner for Dispatch
                        </div>
                        <div class="card-body p-3">
                          @if($delivery && !empty($delivery->rider_name))
                            <div class="alert alert-success p-2 small mb-2">
                              <i class="fas fa-user-check mr-1"></i> <strong>Assigned Rider:</strong> {{ $delivery->rider_name }} ({{ $delivery->rider_phone }})
                            </div>
                          @else
                            <div class="alert alert-warning p-2 small mb-2">
                              <i class="fas fa-exclamation-triangle mr-1"></i> Assign a delivery partner to dispatch this order:
                            </div>
                          @endif

                          <div class="form-group mb-0">
                            <label class="small font-weight-bold">Delivery Executive</label>
                            <select name="delivery_partner_id" class="form-control font-weight-bold" required>
                              <option value="">-- Select Active Delivery Executive --</option>
                              @foreach($riders as $r)
                                <option value="{{ $r->id }}" {{ ($delivery->delivery_partner_id ?? null) == $r->id ? 'selected' : '' }}>
                                  {{ $r->name }} ({{ $r->phone }})
                                </option>
                              @endforeach
                            </select>
                          </div>
                        </div>
                      </div>

                      <button type="submit" class="btn btn-lg btn-block btn-warning text-dark font-weight-bold py-3 shadow-sm">
                        <i class="fas fa-motorcycle mr-2"></i> Dispatch: Out for Delivery 🛵
                      </button>
                    </form>
                  @else
                    <!-- Store Pickup: Mark Ready for Pickup -->
                    <form action="/admin/orders/{{ $order->id }}/update-status" method="POST">
                      @csrf
                      <input type="hidden" name="status" value="Ready for Pickup">
                      <div class="alert alert-purple p-3 mb-3" style="background-color: #f3e5f5; color: #6a1b9a;">
                        <i class="fas fa-store mr-2"></i> <strong>Next Step: Ready for Store Pickup</strong><br>
                        <span class="small">Notify customer that their pickup order is ready.</span>
                      </div>
                      <button type="submit" class="btn btn-lg btn-block font-weight-bold py-3 text-white shadow-sm" style="background-color: #6f42c1;">
                        <i class="fas fa-store mr-2"></i> Mark Ready for Store Pickup 🏬
                      </button>
                    </form>
                  @endif

                @elseif($currentStatus === 'Out for Delivery' || $currentStatus === 'Ready for Pickup')
                  <!-- Stage 3 -> Stage 4: Mark Delivered -->
                  @if(!$isPickup && $delivery && !empty($delivery->rider_name))
                    <div class="alert alert-success p-2 small mb-3">
                      <i class="fas fa-user-check mr-1"></i> <strong>Assigned Rider:</strong> {{ $delivery->rider_name }} ({{ $delivery->rider_phone }})
                    </div>
                  @endif

                  <form action="/admin/orders/{{ $order->id }}/update-status" method="POST">
                    @csrf
                    <input type="hidden" name="status" value="Delivered">
                    <div class="alert alert-success p-3 mb-3">
                      <i class="fas fa-check-circle mr-2"></i> <strong>Next Step: Complete Order</strong><br>
                      <span class="small">Mark order as delivered successfully.</span>
                    </div>
                    <button type="submit" class="btn btn-lg btn-block btn-success font-weight-bold py-3 shadow-sm">
                      <i class="fas fa-check-circle mr-2"></i> {{ $isPickup ? 'Mark Picked Up & Completed ✅' : 'Mark as Delivered ✅' }}
                    </button>
                  </form>

                @elseif($currentStatus === 'Delivered')
                  <!-- Order Delivered State -->
                  <div class="alert alert-success p-3 text-center mb-3">
                    <i class="fas fa-check-circle fa-2x d-block mb-2 text-success"></i>
                    <h5 class="font-weight-bold mb-1">Order Delivered Successfully!</h5>
                    <span class="small text-muted">This order has completed all lifecycle stages.</span>
                  </div>
                @else
                  <div class="alert alert-secondary p-3 text-center mb-3">
                    <h5 class="font-weight-bold mb-1">Status: {{ $currentStatus }}</h5>
                  </div>
                @endif

                @if($currentStatus !== 'Delivered' && $currentStatus !== 'Cancelled')
                  <hr>
                  <form action="/admin/orders/{{ $order->id }}/update-status" method="POST" onsubmit="return confirm('Are you sure you want to cancel this order?');">
                    @csrf
                    <input type="hidden" name="status" value="Cancelled">
                    <button type="submit" class="btn btn-sm btn-outline-danger btn-block font-weight-bold">
                      <i class="fas fa-times-circle mr-1"></i> Cancel Order
                    </button>
                  </form>
                @endif

                <hr>
                <h5 class="font-weight-bold"><i class="fas fa-history mr-1"></i> Order Status Audit History</h5>
                <div class="timeline mt-3">
                  @foreach($history as $h)
                    <div>
                      <i class="fas fa-check bg-blue"></i>
                      <div class="timeline-item">
                        <span class="time"><i class="fas fa-clock"></i> {{ $h->created_at }}</span>
                        <h3 class="timeline-header font-weight-bold text-primary">{{ $h->new_status }}</h3>
                        <div class="timeline-body p-2 text-muted">
                          {{ $h->reason ?? 'Status changed' }}
                        </div>
                      </div>
                    </div>
                  @endforeach
                </div>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>
