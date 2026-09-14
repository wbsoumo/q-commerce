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
      <div class="container-fluid">
        <h1 class="m-0 font-weight-bold">Customer Profile & Order History</h1>
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
          <div class="col-md-4">
            <div class="card card-primary card-outline">
              <div class="card-body box-profile text-center">
                <h3 class="profile-username font-weight-bold">{{ $customer->name }}</h3>
                <p class="text-muted"><i class="fas fa-phone mr-1"></i> {{ $customer->phone }}</p>
                <form action="/admin/customers/{{ $customer->id }}/update-status" method="POST" class="text-left mt-3">
                  @csrf
                  <div class="form-group">
                    <label>Account Status</label>
                    <select name="status" class="form-control">
                      <option value="Active" {{ $customer->status === 'Active' ? 'selected' : '' }}>Active</option>
                      <option value="Blocked" {{ $customer->status === 'Blocked' ? 'selected' : '' }}>Blocked</option>
                      <option value="Suspended" {{ $customer->status === 'Suspended' ? 'selected' : '' }}>Suspended</option>
                    </select>
                  </div>
                  <div class="custom-control custom-switch mb-3">
                    <input type="checkbox" class="custom-control-input" id="vipSwitch" name="is_vip" {{ !empty($customer->is_vip) ? 'checked' : '' }}>
                    <label class="custom-control-label font-weight-bold text-warning" for="vipSwitch"><i class="fas fa-crown mr-1"></i> VIP Customer</label>
                  </div>
                  <div class="form-group">
                    <label>Customer Notes</label>
                    <textarea name="notes" class="form-control" rows="3" placeholder="Admin notes on customer">{{ $customer->notes }}</textarea>
                  </div>
                  <button type="submit" class="btn btn-primary btn-block font-weight-bold">Update Profile</button>
                </form>
              </div>
            </div>
          </div>

          <div class="col-md-8">
            <div class="card card-outline card-success">
              <div class="card-header"><h3 class="card-title font-weight-bold"><i class="fas fa-shopping-bag mr-2"></i>Order History & Total Spending</h3></div>
              <div class="card-body p-0">
                <table class="table table-striped">
                  <thead>
                    <tr><th>Order #</th><th>Total Amount</th><th>Status</th><th>Date</th><th>Action</th></tr>
                  </thead>
                  <tbody>
                    @forelse($orders as $ord)
                    <tr>
                      <td class="font-weight-bold">{{ $ord->order_number }}</td>
                      <td class="text-success font-weight-bold">₹{{ $ord->grand_total }}</td>
                      <td><span class="badge badge-info">{{ $ord->status }}</span></td>
                      <td>{{ $ord->created_at }}</td>
                      <td><a href="/admin/orders/{{ $ord->id }}" class="btn btn-xs btn-primary font-weight-bold">View Timeline</a></td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-3">No past orders found for this customer.</td></tr>
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
