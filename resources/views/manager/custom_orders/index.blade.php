<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Custom & Bulk Orders | {{ $store->name ?? 'Branch' }}</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  <style>
    .brand-banner { background: linear-gradient(135deg, #007bff 0%, #0056b3 100%); }
    .manager-sidebar { background-color: #1e293b !important; }
    .img-preview-thumb {
      max-height: 55px;
      max-width: 70px;
      object-fit: cover;
      border-radius: 6px;
      cursor: pointer;
      border: 1px solid #dee2e6;
      transition: transform 0.2s;
    }
    .img-preview-thumb:hover {
      transform: scale(1.08);
      border-color: #17a2b8;
    }
  </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light border-bottom shadow-sm">
    <ul class="navbar-nav">
      <li class="nav-item"><a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a></li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="#" class="nav-link active font-weight-bold text-primary">
          <i class="fas fa-store mr-1"></i> {{ $store->name ?? 'Store Branch' }} Manager Portal
        </a>
      </li>
    </ul>
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <form action="/logout" method="POST" class="d-inline">
          @csrf
          <button type="submit" class="btn btn-outline-danger btn-sm font-weight-bold"><i class="fas fa-sign-out-alt mr-1"></i> Logout</button>
        </form>
      </li>
    </ul>
  </nav>

  @include('manager.layouts.sidebar')

  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid d-flex justify-content-between align-items-center">
        <div>
          <h1 class="m-0 font-weight-bold text-dark"><i class="fas fa-file-invoice text-info mr-2"></i>Custom & Bulk Order Requests</h1>
          <p class="text-muted mb-0"><i class="fas fa-store text-primary mr-1"></i> {{ $store->name ?? 'Branch' }}</p>
        </div>
        <div>
          <span class="badge badge-info px-3 py-2" style="font-size: 14px;">Total Branch Requests: {{ $customOrders->total() }}</span>
        </div>
      </div>
    </div>

    <div class="content">
      <div class="container-fluid">
        
        @if(session('success'))
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
        @endif

        @if(session('error'))
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle mr-2"></i>{{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
        @endif

        <div class="card card-outline card-info shadow-sm">
          <div class="card-header bg-white py-3">
            <div class="row align-items-center">
              <!-- Filter Pills -->
              <div class="col-md-7 mb-2 mb-md-0">
                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                  <a href="{{ request()->fullUrlWithQuery(['status' => 'all']) }}" class="btn btn-sm {{ request('status', 'all') == 'all' ? 'btn-info font-weight-bold' : 'btn-outline-secondary' }}">
                    All
                  </a>
                  <a href="{{ request()->fullUrlWithQuery(['status' => 'pending']) }}" class="btn btn-sm {{ request('status') == 'pending' ? 'btn-warning font-weight-bold text-dark' : 'btn-outline-secondary' }}">
                    Pending
                  </a>
                  <a href="{{ request()->fullUrlWithQuery(['status' => 'approved']) }}" class="btn btn-sm {{ request('status') == 'approved' ? 'btn-success font-weight-bold' : 'btn-outline-secondary' }}">
                    Approved
                  </a>
                  <a href="{{ request()->fullUrlWithQuery(['status' => 'completed']) }}" class="btn btn-sm {{ request('status') == 'completed' ? 'btn-primary font-weight-bold' : 'btn-outline-secondary' }}">
                    Completed
                  </a>
                  <a href="{{ request()->fullUrlWithQuery(['status' => 'rejected']) }}" class="btn btn-sm {{ request('status') == 'rejected' ? 'btn-danger font-weight-bold' : 'btn-outline-secondary' }}">
                    Rejected
                  </a>
                </div>
              </div>

              <!-- Search Input -->
              <div class="col-md-5">
                <form method="GET" action="/manager/custom-orders">
                  <input type="hidden" name="status" value="{{ request('status', 'all') }}">
                  <div class="input-group">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Search by customer name, phone, address..." value="{{ request('search') }}">
                    <div class="input-group-append">
                      <button type="submit" class="btn btn-sm btn-info">
                        <i class="fas fa-search"></i>
                      </button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>

          <div class="card-body p-0 table-responsive">
            <table class="table table-hover table-striped align-middle mb-0">
              <thead class="bg-light">
                <tr>
                  <th>Request ID</th>
                  <th>Date & Time</th>
                  <th>Customer Details</th>
                  <th>Order Type</th>
                  <th>Image / List</th>
                  <th>Delivery Address</th>
                  <th>Remarks / Requirements</th>
                  <th>Status</th>
                  <th class="text-center">Action</th>
                </tr>
              </thead>
              <tbody>
                @forelse($customOrders as $order)
                  @php
                    $rawPath = ltrim($order->image_path ?? '', '/');
                    $imgUrl = $order->image_path 
                      ? (str_starts_with($order->image_path, 'http') ? $order->image_path : asset($rawPath))
                      : null;
                    $adminFallbackUrl = $rawPath ? "https://admin.sbmartquick.com/" . $rawPath : null;
                  @endphp
                  <tr>
                    <td>
                      <span class="font-weight-bold text-primary">#REQ-{{ $order->id }}</span>
                    </td>
                    <td style="white-space: nowrap;">
                      <small class="text-muted"><i class="far fa-clock mr-1"></i>{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y, h:i A') }}</small>
                    </td>
                    <td>
                      <strong>{{ $order->user_name ?? 'N/A' }}</strong><br>
                      <small class="text-muted"><i class="fas fa-phone-alt mr-1"></i>{{ $order->user_phone ?? 'N/A' }}</small>
                    </td>
                    <td>
                      <span class="badge badge-light border border-secondary px-2 py-1">
                        {{ $order->order_type }}
                      </span>
                    </td>
                    <td>
                      @if($imgUrl)
                        <div class="d-flex align-items-center">
                          <img src="{{ $imgUrl }}" 
                               class="img-preview-thumb mr-2" 
                               data-toggle="modal" 
                               data-target="#imageModalMgr{{ $order->id }}" 
                               alt="Order List"
                               onerror="this.onerror=null; this.src='{{ $adminFallbackUrl }}';">
                          <button type="button" class="btn btn-xs btn-outline-info font-weight-bold" data-toggle="modal" data-target="#imageModalMgr{{ $order->id }}">
                            <i class="fas fa-eye mr-1"></i>View Photo
                          </button>
                        </div>

                        <!-- Full Image Modal -->
                        <div class="modal fade" id="imageModalMgr{{ $order->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                          <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                            <div class="modal-content">
                              <div class="modal-header bg-light">
                                <h5 class="modal-title font-weight-bold">Order Image #REQ-{{ $order->id }}</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                                </button>
                              </div>
                              <div class="modal-body text-center bg-dark p-2">
                                <img src="{{ $imgUrl }}" 
                                     class="img-fluid rounded" 
                                     style="max-height: 80vh;" 
                                     alt="Order Image"
                                     onerror="this.onerror=null; this.src='{{ $adminFallbackUrl }}';">
                              </div>
                              <div class="modal-footer bg-light justify-content-between">
                                <a href="{{ $imgUrl }}" target="_blank" class="btn btn-sm btn-info">
                                  <i class="fas fa-external-link-alt mr-1"></i>Open Full Size
                                </a>
                                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                              </div>
                            </div>
                          </div>
                        </div>
                      @else
                        <span class="text-muted small"><em>No image attached</em></span>
                      @endif
                    </td>
                    <td style="max-width: 200px;">
                      <small>{{ $order->address ?? 'N/A' }}</small>
                      @if($order->latitude && $order->longitude)
                        <br>
                        <a href="https://maps.google.com/?q={{ $order->latitude }},{{ $order->longitude }}" target="_blank" class="badge badge-secondary mt-1">
                          <i class="fas fa-map-marker-alt mr-1"></i>View Maps
                        </a>
                      @endif
                    </td>
                    <td style="max-width: 250px;">
                      <small>{{ $order->remarks ?? '-' }}</small>
                    </td>
                    <td>
                      @if($order->status === 'pending')
                        <span class="badge badge-warning px-2 py-1 text-dark">Pending</span>
                      @elseif($order->status === 'approved')
                        <span class="badge badge-success px-2 py-1">Approved</span>
                      @elseif($order->status === 'completed')
                        <span class="badge badge-primary px-2 py-1">Completed</span>
                      @elseif($order->status === 'rejected')
                        <span class="badge badge-danger px-2 py-1">Rejected</span>
                      @endif
                    </td>
                    <td class="text-center">
                      <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle font-weight-bold" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                          Update Status
                        </button>
                        <div class="dropdown-menu dropdown-menu-right shadow">
                          <form method="POST" action="/manager/custom-orders/{{ $order->id }}/status">
                            @csrf
                            <input type="hidden" name="status" value="pending">
                            <button type="submit" class="dropdown-item text-warning font-weight-bold"><i class="fas fa-hourglass-half mr-2"></i>Mark Pending</button>
                          </form>
                          <form method="POST" action="/manager/custom-orders/{{ $order->id }}/status">
                            @csrf
                            <input type="hidden" name="status" value="approved">
                            <button type="submit" class="dropdown-item text-success font-weight-bold"><i class="fas fa-check mr-2"></i>Approve Request</button>
                          </form>
                          <form method="POST" action="/manager/custom-orders/{{ $order->id }}/status">
                            @csrf
                            <input type="hidden" name="status" value="completed">
                            <button type="submit" class="dropdown-item text-primary font-weight-bold"><i class="fas fa-flag-checkered mr-2"></i>Mark Completed</button>
                          </form>
                          <div class="dropdown-divider"></div>
                          <form method="POST" action="/manager/custom-orders/{{ $order->id }}/status">
                            @csrf
                            <input type="hidden" name="status" value="rejected">
                            <button type="submit" class="dropdown-item text-danger font-weight-bold"><i class="fas fa-times mr-2"></i>Reject Request</button>
                          </form>
                        </div>
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="9" class="text-center py-5 text-muted">
                      <i class="fas fa-inbox fa-3x mb-3 d-block text-secondary"></i>
                      <h5>No Custom / Bulk Orders for {{ $store->name ?? 'this Branch' }}</h5>
                      <p class="small">When users in your store's coverage area request custom items, they will appear here.</p>
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>

          @if($customOrders->hasPages())
            <div class="card-footer bg-white d-flex justify-content-center">
              {{ $customOrders->links() }}
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>

  <footer class="main-footer text-sm">
    <strong>Copyright &copy; {{ date('Y') }} <a href="#">{{ $store->name ?? 'SonarbanglaMart' }}</a>.</strong> All rights reserved.
  </footer>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>
