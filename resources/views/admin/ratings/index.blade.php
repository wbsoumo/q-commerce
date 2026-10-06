<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Q-Commerce Admin | Customer Ratings & Reviews</title>
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
      <li class="nav-item d-none d-sm-inline-block"><a href="#" class="nav-link active font-weight-bold">Ratings & Reviews</a></li>
    </ul>
  </nav>

  @include('admin.layouts.sidebar')

  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid d-flex justify-content-between align-items-center">
        <h1 class="m-0 font-weight-bold"><i class="fas fa-star text-warning mr-2"></i>Customer Ratings & Reviews</h1>
      </div>
    </div>

    <div class="content">
      <div class="container-fluid">
        <!-- Filter Card -->
        <div class="card card-outline card-warning mb-3">
          <div class="card-body">
            <form method="GET" action="/admin/ratings" class="form-inline">
              <label class="mr-2 font-weight-bold"><i class="fas fa-filter mr-1"></i> Filter Ratings:</label>
              
              <select name="type" class="form-control mr-2" onchange="this.form.submit()">
                <option value="">-- All Review Types --</option>
                <option value="product" {{ request('type') == 'product' ? 'selected' : '' }}>Product Ratings Only</option>
                <option value="delivery" {{ request('type') == 'delivery' ? 'selected' : '' }}>Delivery Ratings Only</option>
              </select>

              <select name="store_id" class="form-control mr-2" onchange="this.form.submit()">
                <option value="">-- All Stores --</option>
                @foreach($stores as $s)
                  <option value="{{ $s->id }}" {{ request('store_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                @endforeach
              </select>

              <button type="submit" class="btn btn-warning font-weight-bold"><i class="fas fa-search mr-1"></i> Apply Filter</button>
              <a href="/admin/ratings" class="btn btn-secondary ml-2 font-weight-bold"><i class="fas fa-redo mr-1"></i> Reset</a>
            </form>
          </div>
        </div>

        <div class="card card-outline card-success">
          <div class="card-header"><h3 class="card-title font-weight-bold">Customer Ratings Feed ({{ count($ratings) }})</h3></div>
          <div class="card-body p-0">
            <table class="table table-striped align-middle">
              <thead>
                <tr>
                  <th>#ID</th>
                  <th>Order #</th>
                  <th>Customer Phone</th>
                  <th>Product Rating</th>
                  <th>Product Remarks</th>
                  <th>Delivery Rating</th>
                  <th>Delivery Remarks</th>
                  <th>Store</th>
                  <th>Date & Time</th>
                </tr>
              </thead>
              <tbody>
                @forelse($ratings as $r)
                  <tr>
                    <td class="font-weight-bold">#{{ $r->id }}</td>
                    <td><a href="/admin/orders/{{ $r->order_id ?? 1 }}" class="badge badge-info text-white font-weight-bold">Order #{{ $r->order_id ?? 'N/A' }}</a></td>
                    <td class="font-weight-bold">{{ $r->user_phone ?? 'N/A' }}</td>
                    <td>
                      <div class="text-warning font-weight-bold">
                        @for($i=1; $i<=5; $i++)
                          <i class="fas fa-star {{ $i <= $r->product_rating ? 'text-warning' : 'text-muted' }}"></i>
                        @endfor
                        <span class="ml-1 text-dark">({{ $r->product_rating }}/5)</span>
                      </div>
                    </td>
                    <td><small class="text-dark font-italic">{{ $r->product_remarks ?: 'No remark' }}</small></td>
                    <td>
                      <div class="text-warning font-weight-bold">
                        @for($i=1; $i<=5; $i++)
                          <i class="fas fa-star {{ $i <= $r->delivery_rating ? 'text-warning' : 'text-muted' }}"></i>
                        @endfor
                        <span class="ml-1 text-dark">({{ $r->delivery_rating }}/5)</span>
                      </div>
                    </td>
                    <td><small class="text-dark font-italic">{{ $r->delivery_remarks ?: 'No remark' }}</small></td>
                    <td><span class="badge badge-secondary">{{ $r->store_name ?? 'Main Store' }}</span></td>
                    <td class="small text-muted">{{ $r->created_at }}</td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="9" class="text-center p-4 text-muted font-weight-bold">No customer ratings or reviews found matching criteria.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>
