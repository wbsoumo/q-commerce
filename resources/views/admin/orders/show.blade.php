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
                <p><strong>Customer:</strong> {{ $order->user_name }} ({{ $order->user_phone }})</p>
                <p><strong>Address:</strong> {{ $order->delivery_address }}</p>
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
                <form action="/admin/orders/{{ $order->id }}/update-status" method="POST">
                  @csrf
                  <div class="form-group">
                    <label>Update Status</label>
                    <select name="status" class="form-control font-weight-bold">
                      @foreach(['Pending', 'Confirmed', 'Preparing', 'Ready for Pickup', 'Out for Delivery', 'Delivered', 'Cancelled', 'Refunded'] as $st)
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
