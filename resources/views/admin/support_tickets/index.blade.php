<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Q-Commerce Admin | Support Tickets</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  <style>
    .brand-link { background-color: #0c831f !important; }
    .btn-success { background-color: #0c831f; border-color: #0c831f; }
  </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
      <li class="nav-item"><a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a></li>
      <li class="nav-item d-none d-sm-inline-block"><a href="/admin" class="nav-link">Dashboard</a></li>
      <li class="nav-item d-none d-sm-inline-block"><a href="/admin/support-tickets" class="nav-link font-weight-bold text-success">Support Tickets</a></li>
    </ul>
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <form method="POST" action="/logout">
          @csrf
          <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-sign-out-alt mr-1"></i>Logout</button>
        </form>
      </li>
    </ul>
  </nav>

  @include('admin.layouts.sidebar')

  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2 align-items-center">
          <div class="col-sm-6">
            <h1 class="m-0 font-weight-bold text-dark">
              <i class="fas fa-headset text-success mr-2"></i>Support Tickets & Callbacks
            </h1>
          </div>
          <div class="col-sm-6 text-right">
            <span class="badge badge-success px-3 py-2" style="font-size: 14px;">Total Tickets: {{ $tickets->total() }}</span>
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

        <div class="card card-outline card-success shadow-sm">
          <div class="card-header bg-white py-3">
            <div class="row align-items-center">
              <!-- Filter Pills -->
              <div class="col-md-7 mb-2 mb-md-0">
                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                  <a href="{{ request()->fullUrlWithQuery(['status' => 'all']) }}" class="btn btn-sm {{ request('status', 'all') == 'all' ? 'btn-success font-weight-bold' : 'btn-outline-secondary' }}">
                    All
                  </a>
                  <a href="{{ request()->fullUrlWithQuery(['status' => 'pending']) }}" class="btn btn-sm {{ request('status') == 'pending' ? 'btn-warning font-weight-bold text-dark' : 'btn-outline-secondary' }}">
                    Pending
                  </a>
                  <a href="{{ request()->fullUrlWithQuery(['status' => 'in_progress']) }}" class="btn btn-sm {{ request('status') == 'in_progress' ? 'btn-info font-weight-bold' : 'btn-outline-secondary' }}">
                    In Progress
                  </a>
                  <a href="{{ request()->fullUrlWithQuery(['status' => 'resolved']) }}" class="btn btn-sm {{ request('status') == 'resolved' ? 'btn-success font-weight-bold' : 'btn-outline-secondary' }}">
                    Resolved
                  </a>
                  <a href="{{ request()->fullUrlWithQuery(['status' => 'closed']) }}" class="btn btn-sm {{ request('status') == 'closed' ? 'btn-secondary font-weight-bold' : 'btn-outline-secondary' }}">
                    Closed
                  </a>
                </div>
              </div>

              <!-- Search Input -->
              <div class="col-md-5">
                <form method="GET" action="/admin/support-tickets">
                  <input type="hidden" name="status" value="{{ request('status', 'all') }}">
                  <div class="input-group">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Search ticket #, name, phone, category..." value="{{ request('search') }}">
                    <div class="input-group-append">
                      <button type="submit" class="btn btn-sm btn-success">
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
                  <th>Ticket #</th>
                  <th>Date & Time</th>
                  <th>Customer</th>
                  <th>Store</th>
                  <th>Category / Sub-category</th>
                  <th>Order Info</th>
                  <th>Description</th>
                  <th>Executive Remarks</th>
                  <th>Status</th>
                  <th class="text-center">Action</th>
                </tr>
              </thead>
              <tbody>
                @forelse($tickets as $tkt)
                  <tr>
                    <td>
                      <span class="font-weight-bold text-success">{{ $tkt->ticket_number }}</span>
                    </td>
                    <td style="white-space: nowrap;">
                      <small class="text-muted"><i class="far fa-clock mr-1"></i>{{ \Carbon\Carbon::parse($tkt->created_at)->format('d M Y, h:i A') }}</small>
                    </td>
                    <td>
                      <strong>{{ $tkt->user_name ?? 'Customer' }}</strong><br>
                      <small class="text-muted"><i class="fas fa-phone-alt mr-1"></i>{{ $tkt->user_phone }}</small>
                    </td>
                    <td>
                      <span class="badge badge-light border border-secondary px-2 py-1">
                        {{ $tkt->store_name ?? 'All Stores' }}
                      </span>
                    </td>
                    <td>
                      <strong class="text-dark">{{ $tkt->category }}</strong>
                      @if($tkt->sub_category)
                        <br><small class="text-muted"><i class="fas fa-angle-right mr-1"></i>{{ $tkt->sub_category }}</small>
                      @endif
                    </td>
                    <td>
                      @if($tkt->order_number || $tkt->order_id)
                        <span class="badge badge-info px-2 py-1">#{{ $tkt->order_number ?? $tkt->order_id }}</span>
                      @else
                        <span class="text-muted small"><em>N/A</em></span>
                      @endif
                    </td>
                    <td style="max-width: 200px;">
                      <small>{{ $tkt->description ?? 'No extra description' }}</small>
                    </td>
                    <td style="max-width: 200px;">
                      @if($tkt->admin_remarks)
                        <small class="text-info font-weight-bold"><i class="fas fa-comment-alt mr-1"></i>{{ $tkt->admin_remarks }}</small>
                      @else
                        <span class="text-muted small"><em>No remarks yet</em></span>
                      @endif
                    </td>
                    <td>
                      @if($tkt->status === 'pending')
                        <span class="badge badge-warning px-2 py-1 text-dark">Pending</span>
                      @elseif($tkt->status === 'in_progress')
                        <span class="badge badge-info px-2 py-1">In Progress</span>
                      @elseif($tkt->status === 'resolved')
                        <span class="badge badge-success px-2 py-1">Resolved</span>
                      @elseif($tkt->status === 'closed')
                        <span class="badge badge-secondary px-2 py-1">Closed</span>
                      @endif
                    </td>
                    <td class="text-center">
                      <button type="button" class="btn btn-xs btn-outline-success font-weight-bold" data-toggle="modal" data-target="#editTicketModal{{ $tkt->id }}">
                        <i class="fas fa-edit mr-1"></i>Take Action
                      </button>

                      <!-- Action Modal -->
                      <div class="modal fade" id="editTicketModal{{ $tkt->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                          <div class="modal-content text-left">
                            <form method="POST" action="/admin/support-tickets/{{ $tkt->id }}/status">
                              @csrf
                              <div class="modal-header bg-success text-white">
                                <h5 class="modal-title font-weight-bold">Update Support Ticket {{ $tkt->ticket_number }}</h5>
                                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                                </button>
                              </div>
                              <div class="modal-body">
                                <div class="form-group">
                                  <label class="font-weight-bold">Ticket Status</label>
                                  <select name="status" class="form-control font-weight-bold">
                                    <option value="pending" {{ $tkt->status == 'pending' ? 'selected' : '' }}>Pending 🟡</option>
                                    <option value="in_progress" {{ $tkt->status == 'in_progress' ? 'selected' : '' }}>In Progress 🔵</option>
                                    <option value="resolved" {{ $tkt->status == 'resolved' ? 'selected' : '' }}>Resolved 🟢</option>
                                    <option value="closed" {{ $tkt->status == 'closed' ? 'selected' : '' }}>Closed ⚪</option>
                                  </select>
                                </div>
                                <div class="form-group">
                                  <label class="font-weight-bold">Executive Remarks / Action Notes</label>
                                  <textarea name="admin_remarks" class="form-control" rows="3" placeholder="Enter resolution notes, callback timestamp, or status update...">{{ $tkt->admin_remarks }}</textarea>
                                </div>
                              </div>
                              <div class="modal-footer bg-light justify-content-between">
                                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-sm btn-success font-weight-bold">Save Changes</button>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="10" class="text-center py-5 text-muted">
                      <i class="fas fa-headset fa-3x mb-3 d-block text-secondary"></i>
                      <h5>No Support Tickets Found</h5>
                      <p class="small">Support requests raised from the mobile app will appear here.</p>
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>

          @if($tickets->hasPages())
            <div class="card-footer bg-white d-flex justify-content-center">
              {{ $tickets->links() }}
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>

  <footer class="main-footer text-sm">
    <strong>Copyright &copy; {{ date('Y') }} <a href="#">SonarbanglaMart</a>.</strong> All rights reserved.
  </footer>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>
