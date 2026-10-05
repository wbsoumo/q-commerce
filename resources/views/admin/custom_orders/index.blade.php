@extends('admin.layouts.app')

@section('title', 'Custom & Bulk Order Requests')

@section('content')
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2 align-items-center">
        <div class="col-sm-6">
          <h1 class="m-0 font-weight-bold text-dark">
            <i class="fas fa-file-invoice text-info mr-2"></i>Custom & Bulk Order Requests
          </h1>
        </div>
        <div class="col-sm-6 text-right">
          <span class="badge badge-info px-3 py-2" style="font-size: 14px;">Total Requests: {{ $customOrders->total() }}</span>
        </div>
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
              <form method="GET" action="/admin/custom-orders">
                <input type="hidden" name="status" value="{{ request('status', 'all') }}">
                <div class="input-group">
                  <input type="text" name="search" class="form-control form-control-sm" placeholder="Search by name, phone, type, address..." value="{{ request('search') }}">
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
                    @if($order->image_path)
                      <a href="{{ asset($order->image_path) }}" target="_blank" data-toggle="lightbox">
                        <img src="{{ asset($order->image_path) }}" class="img-thumbnail" style="max-height: 50px; max-width: 60px; object-fit: cover;">
                      </a>
                    @else
                      <span class="text-muted small"><em>No image</em></span>
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
                      <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-toggle="dropdown">
                        Update Status
                      </button>
                      <div class="dropdown-menu dropdown-menu-right">
                        <form method="POST" action="/admin/custom-orders/{{ $order->id }}/status">
                          @csrf
                          <input type="hidden" name="status" value="pending">
                          <button type="submit" class="dropdown-item text-warning font-weight-bold"><i class="fas fa-hourglass-half mr-2"></i>Mark Pending</button>
                        </form>
                        <form method="POST" action="/admin/custom-orders/{{ $order->id }}/status">
                          @csrf
                          <input type="hidden" name="status" value="approved">
                          <button type="submit" class="dropdown-item text-success font-weight-bold"><i class="fas fa-check mr-2"></i>Approve Request</button>
                        </form>
                        <form method="POST" action="/admin/custom-orders/{{ $order->id }}/status">
                          @csrf
                          <input type="hidden" name="status" value="completed">
                          <button type="submit" class="dropdown-item text-primary font-weight-bold"><i class="fas fa-flag-checkered mr-2"></i>Mark Completed</button>
                        </form>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="/admin/custom-orders/{{ $order->id }}/status">
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
                    <h5>No Custom or Bulk Order Requests Found</h5>
                    <p class="small">When users request custom items from the app, they will appear here.</p>
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
@endsection
