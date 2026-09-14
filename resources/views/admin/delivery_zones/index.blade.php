<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Q-Commerce Admin | Delivery Zones & Fee Engine</title>
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
      <li class="nav-item d-none d-sm-inline-block"><a href="/admin/delivery-zones" class="nav-link active font-weight-bold">Delivery Zones</a></li>
    </ul>
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <button type="button" class="btn btn-success btn-sm font-weight-bold" data-toggle="modal" data-target="#zoneModal">
          <i class="fas fa-plus mr-1"></i> Add Delivery Zone
        </button>
      </li>
    </ul>
  </nav>

  <aside class="main-sidebar sidebar-dark-primary elevation-4 position-fixed">
    <a href="/admin" class="brand-link text-center" style="background:#0c831f">
      <span class="brand-text font-weight-bold text-white"><i class="fas fa-bolt mr-2"></i>Q-Commerce</span>
    </a>
    <div class="sidebar">
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column">
          <li class="nav-item"><a href="/admin" class="nav-link"><i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p></a></li>
          <li class="nav-item"><a href="/admin/delivery-zones" class="nav-link active"><i class="nav-icon fas fa-map-marked-alt"></i><p>Delivery Zones</p></a></li>
        </ul>
      </nav>
    </div>
  </aside>

  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid d-flex justify-content-between align-items-center">
        <h1 class="m-0 font-weight-bold">Delivery Zones & Fee Rules Engine</h1>
        <button type="button" class="btn btn-success font-weight-bold" data-toggle="modal" data-target="#zoneModal">
          <i class="fas fa-plus mr-1"></i> Add Zone Rule
        </button>
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

        <div class="card card-outline card-success">
          <div class="card-header"><h3 class="card-title font-weight-bold"><i class="fas fa-layer-group mr-2"></i>Active Store Delivery Zones</h3></div>
          <div class="card-body p-0">
            <table class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th>#ID</th>
                  <th>Zone Name</th>
                  <th>Store</th>
                  <th>Type</th>
                  <th>Radius / Pincodes</th>
                  <th>Base Delivery Fee</th>
                  <th>Min Order</th>
                  <th>Free Threshold</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                @forelse($zones as $z)
                <tr>
                  <td>{{ $z->id }}</td>
                  <td class="font-weight-bold">{{ $z->zone_name }}</td>
                  <td>{{ $z->store_name }}</td>
                  <td><span class="badge badge-info">{{ strtoupper($z->zone_type) }}</span></td>
                  <td>
                    @if($z->zone_type === 'radius')
                      {{ $z->radius_km }} km
                    @else
                      {{ $z->pincodes }}
                    @endif
                  </td>
                  <td class="text-success font-weight-bold">₹{{ $z->base_delivery_fee }}</td>
                  <td>₹{{ $z->min_order_amount }}</td>
                  <td><span class="badge badge-success">Free above ₹{{ $z->free_delivery_threshold }}</span></td>
                  <td><span class="badge badge-success">Active</span></td>
                </tr>
                @empty
                <tr><td colspan="9" class="text-center py-4">No custom delivery zones created yet. Default store settings apply.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Zone Modal -->
  <div class="modal fade" id="zoneModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <form action="/admin/delivery-zones/store" method="POST">
          @csrf
          <div class="modal-header bg-success text-white">
            <h5 class="modal-title font-weight-bold"><i class="fas fa-map-pin mr-1"></i> Create Delivery Zone</h5>
            <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label>Select Store</label>
              <select name="store_id" class="form-control" required>
                @foreach($stores as $st)
                  <option value="{{ $st->id }}">{{ $st->name }} ({{ $st->city }})</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label>Zone Name</label>
              <input type="text" name="zone_name" class="form-control" placeholder="e.g. Krishnanagar 5km Inner Circle" required>
            </div>
            <div class="form-group">
              <label>Zone Type</label>
              <select name="zone_type" class="form-control">
                <option value="radius">Radius Based (km)</option>
                <option value="pincode">PIN-Code Based</option>
              </select>
            </div>
            <div class="form-group">
              <label>Radius (km)</label>
              <input type="number" step="0.1" name="radius_km" class="form-control" value="5.0">
            </div>
            <div class="form-group">
              <label>Pincodes (Comma separated if PIN-Code based)</label>
              <input type="text" name="pincodes" class="form-control" placeholder="741101, 741102">
            </div>
            <div class="row">
              <div class="col-md-6 form-group">
                <label>Base Fee (₹)</label>
                <input type="number" step="0.01" name="base_delivery_fee" class="form-control" value="15.00" required>
              </div>
              <div class="col-md-6 form-group">
                <label>Free Delivery Above (₹)</label>
                <input type="number" step="0.01" name="free_delivery_threshold" class="form-control" value="299.00" required>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-success font-weight-bold">Save Delivery Zone</button>
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
</body>
</html>
