<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Order Timeline #{{ $order->order_number }}</title>
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
      <li class="nav-item d-none d-sm-inline-block"><a href="#" class="nav-link active font-weight-bold">Order #{{ $order->order_number }}</a></li>
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
                @if(($order->order_type ?? 'delivery') === 'delivery')
                  <div class="card card-outline card-warning mb-3">
                    <div class="card-header font-weight-bold py-2"><i class="fas fa-motorcycle text-warning mr-1"></i> Delivery Partner Assignment</div>
                    <div class="card-body p-3">
                      @if($delivery && !empty($delivery->rider_name))
                        <div class="alert alert-success p-2 small mb-2">
                          <i class="fas fa-user-check mr-1"></i> <strong>Assigned Rider:</strong> {{ $delivery->rider_name }} ({{ $delivery->rider_phone }})<br>
                          <span class="text-muted">Status: {{ $delivery->delivery_status }}</span>
                        </div>
                      @else
                        <div class="alert alert-danger p-2 small mb-2">
                          <i class="fas fa-exclamation-triangle mr-1"></i> Delivery partner will be assigned soon...
                        </div>
                      @endif

                      <form action="/admin/deliveries/assign" method="POST">
                        @csrf
                        <input type="hidden" name="delivery_id" value="{{ $delivery->id ?? '' }}">
                        <div class="form-group mb-2">
                          <label class="small font-weight-bold">Select & Assign Delivery Executive</label>
                          <select name="delivery_partner_id" class="form-control form-control-sm font-weight-bold" required>
                            <option value="">-- Choose Active Rider --</option>
                            @foreach($riders as $r)
                              <option value="{{ $r->id }}" {{ ($delivery->delivery_partner_id ?? null) == $r->id ? 'selected' : '' }}>
                                {{ $r->name }} ({{ $r->phone }})
                              </option>
                            @endforeach
                          </select>
                        </div>
                        <button type="submit" class="btn btn-sm btn-warning btn-block font-weight-bold">
                          <i class="fas fa-motorcycle mr-1"></i> Assign Delivery Executive
                        </button>
                      </form>
                    </div>
                  </div>
                @endif

                <form action="/admin/orders/{{ $order->id }}/update-status" method="POST">
                  @csrf
                  <div class="form-group">
                    <label>Update Order Status</label>
                    <select name="status" class="form-control font-weight-bold">
                      @foreach(['Pending', 'Confirmed', 'Preparing', 'Out for Delivery', 'Delivered', 'Cancelled', 'Refunded'] as $st)
                        <option value="{{ $st }}" {{ $order->status === $st ? 'selected' : '' }}>{{ $st }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="form-group">
                    <label>Status Change Reason / Notes</label>
                    <input type="text" name="reason" class="form-control" placeholder="Optional notes for status update">
                  </div>
                  <button type="submit" class="btn btn-primary btn-block font-weight-bold"><i class="fas fa-sync mr-1"></i> Transition Order Status</button>
                </form>

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
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>
