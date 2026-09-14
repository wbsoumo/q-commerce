<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Q-Commerce Admin | Inventory Transaction Logs</title>
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
      <li class="nav-item d-none d-sm-inline-block"><a href="/admin/inventory/transactions" class="nav-link active font-weight-bold">Inventory Transactions</a></li>
    </ul>
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <button type="button" class="btn btn-success btn-sm font-weight-bold" data-toggle="modal" data-target="#adjustModal">
          <i class="fas fa-sliders-h mr-1"></i> Manual Stock Adjustment
        </button>
      </li>
    </ul>
  </nav>

  @include('admin.layouts.sidebar')

  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid d-flex justify-content-between align-items-center">
        <h1 class="m-0 font-weight-bold">Stock Movement & Inventory Transaction Audit</h1>
        <button type="button" class="btn btn-success font-weight-bold" data-toggle="modal" data-target="#adjustModal">
          <i class="fas fa-plus mr-1"></i> Stock Adjustment
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

        <div class="card card-outline card-primary">
          <div class="card-header"><h3 class="card-title font-weight-bold"><i class="fas fa-list mr-2"></i>Transaction Audit Trail</h3></div>
          <div class="card-body p-0">
            <table class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th>#ID</th>
                  <th>Product</th>
                  <th>Store</th>
                  <th>Change Qty</th>
                  <th>Prev Stock</th>
                  <th>New Stock</th>
                  <th>Type</th>
                  <th>Reason</th>
                  <th>Ref ID</th>
                  <th>Timestamp</th>
                </tr>
              </thead>
              <tbody>
                @forelse($transactions as $trx)
                <tr>
                  <td>{{ $trx->id }}</td>
                  <td class="font-weight-bold">{{ $trx->product_name ?? 'N/A' }}</td>
                  <td>{{ $trx->store_name ?? 'Global' }}</td>
                  <td class="{{ $trx->quantity > 0 ? 'text-success font-weight-bold' : 'text-danger font-weight-bold' }}">
                    {{ $trx->quantity > 0 ? '+'.$trx->quantity : $trx->quantity }}
                  </td>
                  <td>{{ $trx->previous_stock }}</td>
                  <td class="font-weight-bold">{{ $trx->new_stock }}</td>
                  <td>
                    <span class="badge badge-{{ in_array($trx->transaction_type, ['STOCK_IN', 'ORDER_CANCELLED']) ? 'success' : 'warning' }}">
                      {{ $trx->transaction_type }}
                    </span>
                  </td>
                  <td>{{ $trx->reason ?? '-' }}</td>
                  <td>{{ $trx->reference_id ?? '-' }}</td>
                  <td>{{ $trx->created_at }}</td>
                </tr>
                @empty
                <tr><td colspan="10" class="text-center py-4">No inventory transactions logged yet.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
          <div class="card-footer clearfix">
            {{ $transactions->links('pagination::bootstrap-4') }}
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Adjustment Modal -->
  <div class="modal fade" id="adjustModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <form action="/admin/inventory/adjust" method="POST">
          @csrf
          <div class="modal-header bg-success text-white">
            <h5 class="modal-title font-weight-bold"><i class="fas fa-sliders-h mr-1"></i> Manual Stock Adjustment</h5>
            <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label>Select Product</label>
              <select name="product_id" class="form-control" required>
                @foreach($products as $p)
                  <option value="{{ $p->id }}">{{ $p->name }} (Stock: {{ $p->stock }})</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label>Select Store</label>
              <select name="store_id" class="form-control">
                <option value="">Global Product Stock</option>
                @foreach($stores as $st)
                  <option value="{{ $st->id }}">{{ $st->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label>Quantity Change (+ or -)</label>
              <input type="number" name="quantity" class="form-control" placeholder="e.g. +50 or -5" required>
            </div>
            <div class="form-group">
              <label>Transaction Type</label>
              <select name="transaction_type" class="form-control">
                <option value="STOCK_IN">STOCK_IN (Restock)</option>
                <option value="STOCK_OUT">STOCK_OUT (Disposed)</option>
                <option value="STOCK_ADJUSTMENT">STOCK_ADJUSTMENT</option>
                <option value="DAMAGED">DAMAGED</option>
                <option value="EXPIRED">EXPIRED</option>
              </select>
            </div>
            <div class="form-group">
              <label>Reason / Notes</label>
              <input type="text" name="reason" class="form-control" placeholder="Reason for inventory edit" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-success font-weight-bold">Save Stock Adjustment</button>
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
