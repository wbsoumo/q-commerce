<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Customer Management & Insights | Q-Commerce Admin</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  <style>
    :root {
      --brand-green: #28a745;
      --brand-green-dark: #1e7e34;
      --brand-green-light: #e6f4ea;
    }
    .btn-brand-green { background-color: var(--brand-green); color: #fff; border-color: var(--brand-green); }
    .btn-brand-green:hover { background-color: var(--brand-green-dark); color: #fff; border-color: var(--brand-green-dark); }
    .customer-card { transition: all 0.2s ease-in-out; border-radius: 8px; border-left: 4px solid var(--brand-green); }
    .customer-card:hover { transform: translateY(-3px); box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
    .table-modern th { background-color: #1e293b; color: #ffffff; border: none; vertical-align: middle; }
    .table-modern td { vertical-align: middle; }
    .avatar-sm {
      width: 38px; height: 38px; border-radius: 50%;
      background: var(--brand-green-light); color: var(--brand-green-dark);
      display: inline-flex; align-items: center; justify-content: center;
      font-weight: 700; font-size: 16px; border: 1px solid #a7f3d0;
      flex-shrink: 0;
    }
    .small-box .icon {
      font-size: 55px; opacity: 0.25; top: 10px; right: 15px; transition: all .3s linear;
    }
    .small-box:hover .icon { font-size: 60px; opacity: 0.35; }
  </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
      <li class="nav-item"><a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a></li>
      <li class="nav-item d-none d-sm-inline-block"><a href="/admin" class="nav-link">Dashboard</a></li>
      <li class="nav-item d-none d-sm-inline-block"><a href="/admin/customers" class="nav-link active font-weight-bold text-success"><i class="fas fa-users mr-1"></i> Customer Directory</a></li>
    </ul>
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <button class="btn btn-brand-green btn-sm font-weight-bold" data-toggle="modal" data-target="#createCustomerModal">
          <i class="fas fa-user-plus mr-1"></i> Onboard New Customer
        </button>
      </li>
    </ul>
  </nav>

  @include('admin.layouts.sidebar')

  <div class="content-wrapper">
    <!-- Header -->
    <div class="content-header">
      <div class="container-fluid d-flex justify-content-between align-items-center flex-wrap">
        <div class="mb-2">
          <h1 class="m-0 font-weight-bold text-dark"><i class="fas fa-users text-success mr-2"></i>Customer Management & Insights</h1>
          <p class="text-muted small mb-0">View registered user accounts, wallet cash balances, lifetime spent analytics, and customer 360 profiles.</p>
        </div>
        <div class="mb-2">
          <button class="btn btn-brand-green font-weight-bold" data-toggle="modal" data-target="#createCustomerModal">
            <i class="fas fa-user-plus mr-1"></i> Onboard Customer
          </button>
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
        @if(session('error'))
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle mr-2"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
          </div>
        @endif

        <!-- Summary Metric Cards -->
        <div class="row">
          <div class="col-12 col-sm-6 col-lg-3 mb-3">
            <div class="small-box bg-white border shadow-sm customer-card h-100">
              <div class="inner pl-3 pt-3">
                <h3 class="text-success font-weight-bold mb-0">{{ number_format($totalCustomersCount ?? 0) }}</h3>
                <p class="text-muted font-weight-bold mb-2">Registered Accounts</p>
              </div>
              <div class="icon text-success"><i class="fas fa-users"></i></div>
              <a href="/admin/customers" class="small-box-footer bg-light text-success font-weight-bold">View Directory <i class="fas fa-arrow-circle-right ml-1"></i></a>
            </div>
          </div>

          <div class="col-12 col-sm-6 col-lg-3 mb-3">
            <div class="small-box bg-white border shadow-sm customer-card h-100" style="border-left-color: #10b981;">
              <div class="inner pl-3 pt-3">
                <h3 class="text-success font-weight-bold mb-0">{{ number_format($activeCustomersCount ?? 0) }}</h3>
                <p class="text-muted font-weight-bold mb-2">Active Customers</p>
              </div>
              <div class="icon text-success"><i class="fas fa-user-check"></i></div>
              <a href="/admin/customers?status=Active" class="small-box-footer bg-light text-success font-weight-bold">Filter Active <i class="fas fa-arrow-circle-right ml-1"></i></a>
            </div>
          </div>

          <div class="col-12 col-sm-6 col-lg-3 mb-3">
            <div class="small-box bg-white border shadow-sm customer-card h-100" style="border-left-color: #f59e0b;">
              <div class="inner pl-3 pt-3">
                <h3 class="text-warning font-weight-bold mb-0" style="color: #d97706 !important;">{{ number_format($vipCustomersCount ?? 0) }}</h3>
                <p class="text-muted font-weight-bold mb-2">VIP Members</p>
              </div>
              <div class="icon text-warning"><i class="fas fa-crown"></i></div>
              <a href="/admin/customers?vip_status=vip" class="small-box-footer bg-light text-warning font-weight-bold">Filter VIP <i class="fas fa-arrow-circle-right ml-1"></i></a>
            </div>
          </div>

          <div class="col-12 col-sm-6 col-lg-3 mb-3">
            <div class="small-box bg-white border shadow-sm customer-card h-100" style="border-left-color: #3b82f6;">
              <div class="inner pl-3 pt-3">
                <h3 class="text-info font-weight-bold mb-0" style="font-size: 24px;">₹{{ number_format($totalWalletLiability ?? 0, 2) }}</h3>
                <p class="text-muted font-weight-bold mb-2">Total Wallet Liabilities</p>
              </div>
              <div class="icon text-info"><i class="fas fa-wallet"></i></div>
              <a href="/admin/customers?sort_by=highest_wallet" class="small-box-footer bg-light text-info font-weight-bold">Sort Wallet Cash <i class="fas fa-arrow-circle-right ml-1"></i></a>
            </div>
          </div>
        </div>

        <!-- Filter & Search Bar -->
        <div class="card card-outline card-success mb-3 shadow-sm">
          <div class="card-header bg-light py-2">
            <h3 class="card-title font-weight-bold text-dark mb-0"><i class="fas fa-filter text-success mr-1"></i> Customer Search & Advanced Filters</h3>
          </div>
          <div class="card-body py-3">
            <form action="/admin/customers" method="GET" class="form-row align-items-center">
              <div class="col-12 col-sm-6 col-lg-3 mb-2">
                <div class="input-group">
                  <div class="input-group-prepend"><span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span></div>
                  <input type="text" name="search" class="form-control" placeholder="Search Name, Phone, Email..." value="{{ request('search') }}">
                </div>
              </div>

              <div class="col-6 col-sm-3 col-lg-2 mb-2">
                <select name="status" class="form-control" onchange="this.form.submit()">
                  <option value="">All Account Statuses</option>
                  <option value="Active" {{ request('status') == 'Active' ? 'selected' : '' }}>Active</option>
                  <option value="Blocked" {{ request('status') == 'Blocked' ? 'selected' : '' }}>Blocked</option>
                  <option value="Suspended" {{ request('status') == 'Suspended' ? 'selected' : '' }}>Suspended</option>
                </select>
              </div>

              <div class="col-6 col-sm-3 col-lg-2 mb-2">
                <select name="vip_status" class="form-control" onchange="this.form.submit()">
                  <option value="">All Tiers</option>
                  <option value="vip" {{ request('vip_status') == 'vip' ? 'selected' : '' }}>VIP Members</option>
                  <option value="regular" {{ request('vip_status') == 'regular' ? 'selected' : '' }}>Regular Members</option>
                </select>
              </div>

              <div class="col-6 col-sm-4 col-lg-2 mb-2">
                <select name="sort_by" class="form-control" onchange="this.form.submit()">
                  <option value="id_desc" {{ ($sortBy ?? '') == 'id_desc' ? 'selected' : '' }}>Newest Joined</option>
                  <option value="most_spent" {{ ($sortBy ?? '') == 'most_spent' ? 'selected' : '' }}>Highest Lifetime Spent</option>
                  <option value="most_orders" {{ ($sortBy ?? '') == 'most_orders' ? 'selected' : '' }}>Most Orders Placed</option>
                  <option value="highest_wallet" {{ ($sortBy ?? '') == 'highest_wallet' ? 'selected' : '' }}>Highest Wallet Cash</option>
                  <option value="oldest" {{ ($sortBy ?? '') == 'oldest' ? 'selected' : '' }}>Oldest Accounts</option>
                </select>
              </div>

              <div class="col-6 col-sm-2 col-lg-1 mb-2">
                <select name="per_page" class="form-control font-weight-bold" onchange="this.form.submit()" title="Items per page">
                  <option value="15" {{ ($perPage ?? '15') == '15' ? 'selected' : '' }}>15 / page</option>
                  <option value="30" {{ ($perPage ?? '15') == '30' ? 'selected' : '' }}>30 / page</option>
                  <option value="50" {{ ($perPage ?? '15') == '50' ? 'selected' : '' }}>50 / page</option>
                  <option value="100" {{ ($perPage ?? '15') == '100' ? 'selected' : '' }}>100 / page</option>
                  <option value="all" {{ ($perPage ?? '15') == 'all' ? 'selected' : '' }}>All</option>
                </select>
              </div>

              <div class="col-12 col-sm-6 col-lg-2 mb-2 d-flex">
                <button type="submit" class="btn btn-brand-green btn-block font-weight-bold" title="Search"><i class="fas fa-search mr-1"></i> Filter</button>
                @if(request('search') || request('status') || request('vip_status') || request('sort_by') || request('per_page'))
                  <a href="/admin/customers" class="btn btn-secondary ml-1" title="Reset Filters"><i class="fas fa-undo"></i></a>
                @endif
              </div>
            </form>
          </div>
        </div>

        <!-- Customer Directory Table Card -->
        <div class="card card-outline card-success shadow-sm">
          <div class="card-header bg-dark d-flex justify-content-between align-items-center">
            <h3 class="card-title font-weight-bold text-white mb-0"><i class="fas fa-address-book text-success mr-2"></i> Registered Customer Directory</h3>
          </div>
          <div class="card-body p-0 table-responsive">
            <table class="table table-hover table-striped table-modern mb-0">
              <thead>
                <tr>
                  <th style="width: 50px;" class="text-nowrap">#ID</th>
                  <th class="text-nowrap">Customer Profile</th>
                  <th class="text-nowrap">Contact Info</th>
                  <th class="text-nowrap">Account Status</th>
                  <th class="text-nowrap">Tier Flag</th>
                  <th class="text-nowrap">Wallet Cash</th>
                  <th class="text-nowrap">Orders</th>
                  <th class="text-nowrap">Lifetime Spend</th>
                  <th style="width: 140px;" class="text-center text-nowrap">360° Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse($customers as $c)
                <tr>
                  <td class="align-middle font-weight-bold text-muted text-nowrap">#{{ $c->id }}</td>
                  <td class="align-middle">
                    <div class="d-flex align-items-center text-nowrap">
                      <div class="avatar-sm mr-2">
                        {{ strtoupper(substr($c->name ?? 'C', 0, 1)) }}
                      </div>
                      <div>
                        <a href="/admin/customers/{{ $c->id }}" class="font-weight-bold text-dark" title="View Customer 360 Profile">
                          {{ $c->name }}
                        </a>
                        <div class="small text-muted">Joined: {{ isset($c->created_at) ? date('M d, Y', strtotime($c->created_at)) : 'N/A' }}</div>
                      </div>
                    </div>
                  </td>
                  <td class="align-middle text-nowrap">
                    <div class="font-weight-bold text-dark"><i class="fas fa-phone text-success mr-1"></i> {{ $c->phone }}</div>
                    @if(!empty($c->email))
                      <div class="small text-muted"><i class="fas fa-envelope text-info mr-1"></i> {{ $c->email }}</div>
                    @endif
                  </td>
                  <td class="align-middle text-nowrap">
                    <span class="badge badge-{{ ($c->status ?? '') === 'Active' ? 'success' : 'danger' }} px-2 py-1">
                      {{ $c->status ?? 'Active' }}
                    </span>
                  </td>
                  <td class="align-middle text-nowrap">
                    @if(!empty($c->is_vip))
                      <span class="badge badge-warning text-dark font-weight-bold px-2 py-1"><i class="fas fa-crown mr-1"></i> VIP Member</span>
                    @else
                      <span class="badge badge-light border text-muted">Regular</span>
                    @endif
                  </td>
                  <td class="align-middle text-nowrap">
                    <span class="badge badge-success font-weight-bold px-2 py-1" style="font-size: 13px;">
                      <i class="fas fa-wallet mr-1"></i> ₹{{ number_format($c->wallet_balance ?? 0, 2) }}
                    </span>
                    <button type="button" class="btn btn-link btn-xs text-success p-0 ml-1" title="Quick Wallet Credit / Adjustment" onclick="openQuickWalletModal({{ $c->id }}, '{{ addslashes($c->name) }}', {{ $c->wallet_balance ?? 0 }})">
                      <i class="fas fa-plus-circle"></i>
                    </button>
                  </td>
                  <td class="align-middle text-nowrap">
                    <span class="badge badge-info font-weight-bold px-2 py-1">{{ $c->total_orders ?? 0 }} orders</span>
                  </td>
                  <td class="align-middle text-success font-weight-bold text-nowrap">
                    ₹{{ number_format($c->total_spent ?? 0, 2) }}
                  </td>
                  <td class="align-middle text-center text-nowrap">
                    <div class="dropdown">
                      <button class="btn btn-sm btn-outline-success dropdown-toggle font-weight-bold px-2 py-1" type="button" data-toggle="dropdown" aria-expanded="false">
                        Actions
                      </button>
                      <div class="dropdown-menu dropdown-menu-right shadow-sm">
                        <a class="dropdown-item font-weight-bold text-dark" href="/admin/customers/{{ $c->id }}">
                          <i class="fas fa-user-shield text-success mr-2"></i> View 360° Profile
                        </a>
                        <a class="dropdown-item font-weight-bold text-dark" href="javascript:void(0);" onclick="openQuickWalletModal({{ $c->id }}, '{{ addslashes($c->name) }}', {{ $c->wallet_balance ?? 0 }})">
                          <i class="fas fa-wallet text-info mr-2"></i> Quick Wallet Credit / Debit
                        </a>
                      </div>
                    </div>
                  </td>
                </tr>
                @empty
                <tr>
                  <td colspan="9" class="text-center py-5 text-muted">
                    <i class="fas fa-users-slash fa-3x mb-3 text-secondary"></i>
                    <h5>No customer records match your filter criteria.</h5>
                    <button class="btn btn-brand-green btn-sm font-weight-bold mt-2" data-toggle="modal" data-target="#createCustomerModal">
                      <i class="fas fa-user-plus mr-1"></i> Onboard First Customer
                    </button>
                  </td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>
          @if(method_exists($customers, 'links'))
            <div class="card-footer bg-light d-flex justify-content-between align-items-center flex-wrap">
              <div class="my-1">Showing {{ $customers->firstItem() ?? 0 }} to {{ $customers->lastItem() ?? 0 }} of {{ $customers->total() }} customers</div>
              <div class="my-1">{{ $customers->links('pagination::bootstrap-4') }}</div>
            </div>
          @endif
        </div>

      </div>
    </div>
  </div>

  <!-- Onboard New Customer Modal -->
  <div class="modal fade" id="createCustomerModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <form action="/admin/customers/store" method="POST">
          @csrf
          <div class="modal-header bg-dark text-white">
            <h5 class="modal-title font-weight-bold"><i class="fas fa-user-plus text-success mr-2"></i> Onboard New Customer Account</h5>
            <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label class="font-weight-bold">Full Customer Name <span class="text-danger">*</span></label>
              <input type="text" name="name" class="form-control" placeholder="e.g. Soumojit Saha" required>
            </div>
            <div class="form-group">
              <label class="font-weight-bold">Phone Number (Login ID) <span class="text-danger">*</span></label>
              <input type="text" name="phone" class="form-control" placeholder="e.g. 9876543210" required>
            </div>
            <div class="form-group">
              <label class="font-weight-bold">Email Address (Optional)</label>
              <input type="email" name="email" class="form-control" placeholder="e.g. customer@example.com">
            </div>
            <div class="form-row">
              <div class="form-group col-md-6">
                <label class="font-weight-bold">Account Status</label>
                <select name="status" class="form-control">
                  <option value="Active">Active</option>
                  <option value="Blocked">Blocked</option>
                  <option value="Suspended">Suspended</option>
                </select>
              </div>
              <div class="form-group col-md-6">
                <label class="font-weight-bold">Initial Wallet Cash (₹)</label>
                <input type="number" step="0.01" min="0" name="wallet_balance" class="form-control" value="0.00">
              </div>
            </div>
            <div class="custom-control custom-switch mb-3">
              <input type="checkbox" class="custom-control-input" id="newVipSwitch" name="is_vip">
              <label class="custom-control-label font-weight-bold text-warning" for="newVipSwitch"><i class="fas fa-crown mr-1"></i> Flag as VIP Member</label>
            </div>
            <div class="form-group">
              <label class="font-weight-bold">Internal Admin Notes</label>
              <textarea name="notes" class="form-control" rows="2" placeholder="Special preferences or onboarding notes..."></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-brand-green font-weight-bold"><i class="fas fa-check-circle mr-1"></i> Onboard Customer</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Quick Wallet Credit / Adjustment Modal -->
  <div class="modal fade" id="quickWalletModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <form action="/admin/customers/quick-credit" method="POST">
          @csrf
          <input type="hidden" name="customer_id" id="walletCustomerId">
          <div class="modal-header bg-dark text-white">
            <h5 class="modal-title font-weight-bold"><i class="fas fa-wallet text-success mr-2"></i> Quick Wallet Cash Adjustment</h5>
            <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
          </div>
          <div class="modal-body">
            <div class="alert alert-light border">
              Customer: <strong id="walletCustomerName" class="text-dark">Customer Name</strong><br>
              Current Wallet Balance: <strong id="walletCurrentBalance" class="text-success">₹0.00</strong>
            </div>

            <div class="form-group">
              <label class="font-weight-bold">Transaction Type</label>
              <select name="type" class="form-control font-weight-bold">
                <option value="credit">➕ Credit / Add Cash to Wallet</option>
                <option value="debit">➖ Debit / Deduct Cash from Wallet</option>
              </select>
            </div>

            <div class="form-group">
              <label class="font-weight-bold">Amount (₹) <span class="text-danger">*</span></label>
              <input type="number" step="0.01" min="0.01" name="amount" class="form-control font-weight-bold" placeholder="e.g. 100.00" required>
            </div>

            <div class="form-group">
              <label class="font-weight-bold">Reason / Audit Note</label>
              <input type="text" name="reason" class="form-control" placeholder="e.g. Promotional Cashback or Refund for Order #1002">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-brand-green font-weight-bold">Process Adjustment</button>
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
<script>
  function openQuickWalletModal(id, name, currentBalance) {
    $('#walletCustomerId').val(id);
    $('#walletCustomerName').text(name);
    $('#walletCurrentBalance').text('₹' + parseFloat(currentBalance).toFixed(2));
    $('#quickWalletModal').modal('show');
  }
</script>
</body>
</html>
