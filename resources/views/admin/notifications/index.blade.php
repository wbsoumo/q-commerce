@extends('admin.layouts.app')

@section('title', 'Push Notification Center - SB Mart Admin')

@section('content')
<div class="content-wrapper p-4" style="background: #f4f6f9;">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="font-weight-bold text-dark mb-1"><i class="fas fa-bell text-warning mr-2"></i>Push Notification Engine</h2>
            <p class="text-muted small mb-0">Manage Firebase FCM settings, broadcast notifications with images, and view delivery logs.</p>
        </div>
        <div>
            <span class="badge badge-success px-3 py-2 font-weight-normal" style="font-size: 13px;">
                <i class="fas fa-mobile-alt mr-1"></i> Registered Devices: <strong>{{ $totalTokens }}</strong>
            </span>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="fas fa-exclamation-triangle mr-2"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Main Navigation Tabs -->
    <div class="card card-primary card-outline card-outline-tabs shadow-sm border-0">
        <div class="card-header p-0 border-bottom-0 bg-white">
            <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active font-weight-bold" id="tab-send-tab" data-toggle="pill" href="#tab-send" role="tab" aria-controls="tab-send" aria-selected="true">
                        <i class="fas fa-paper-plane mr-2 text-primary"></i>Send Notification
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link font-weight-bold" id="tab-settings-tab" data-toggle="pill" href="#tab-settings" role="tab" aria-controls="tab-settings" aria-selected="false">
                        <i class="fas fa-key mr-2 text-warning"></i>Firebase JSON Config
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link font-weight-bold" id="tab-logs-tab" data-toggle="pill" href="#tab-logs" role="tab" aria-controls="tab-logs" aria-selected="false">
                        <i class="fas fa-history mr-2 text-info"></i>Audit Logs ({{ $logs->total() }})
                    </a>
                </li>
            </ul>
        </div>

        <div class="card-body p-4">
            <div class="tab-content" id="custom-tabs-four-tabContent">
                <!-- TAB 1: Send Notification Form -->
                <div class="tab-pane fade show active" id="tab-send" role="tabpanel" aria-labelledby="tab-send-tab">
                    <form action="/admin/notifications/send" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-8">
                                <div class="card border border-light shadow-none bg-light p-3 rounded mb-3">
                                    <h5 class="font-weight-bold text-dark mb-3"><i class="fas fa-bullhorn text-success mr-2"></i>Compose Notification</h5>
                                    
                                    <div class="form-group">
                                        <label class="font-weight-bold">Notification Target *</label>
                                        <div class="d-flex align-items-center">
                                            <div class="custom-control custom-radio mr-4">
                                                <input class="custom-control-input" type="radio" id="targetAll" name="target_type" value="all" checked onclick="toggleTargetInput(false)">
                                                <label for="targetAll" class="custom-control-label font-weight-normal">Broadcast to All Users</label>
                                            </div>
                                            <div class="custom-control custom-radio">
                                                <input class="custom-control-input" type="radio" id="targetSpecific" name="target_type" value="specific_user" onclick="toggleTargetInput(true)">
                                                <label for="targetSpecific" class="custom-control-label font-weight-normal">Specific User Phone</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group" id="specificPhoneGroup" style="display: none;">
                                        <label class="font-weight-bold">Target Mobile Number *</label>
                                        <input type="text" name="target_phone" class="form-control" placeholder="e.g. +918016222991 or 8016222991">
                                        <small class="text-muted">Enter registered 10-digit number or with +91 country code.</small>
                                    </div>

                                    <div class="form-group">
                                        <label class="font-weight-bold">Title *</label>
                                        <input type="text" name="title" id="notifTitle" class="form-control" placeholder="e.g. ⚡ Mega Diwali Sale - 50% Off On Essentials!" required>
                                    </div>

                                    <div class="form-group">
                                        <label class="font-weight-bold">Message Body *</label>
                                        <textarea name="body" id="notifBody" class="form-control" rows="3" placeholder="e.g. Order your favourite groceries & daily needs now with 10-min instant delivery." required></textarea>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-7">
                                            <div class="form-group">
                                                <label class="font-weight-bold">Banner / Image URL (Optional)</label>
                                                <input type="url" name="image_url" id="notifImage" class="form-control" placeholder="https://example.com/images/banner.jpg">
                                                <small class="text-muted">Displays rich image in notification tray.</small>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label class="font-weight-bold">Order ID Deep-Link (Optional)</label>
                                                <input type="text" name="order_id" class="form-control" placeholder="e.g. ORD10085">
                                                <small class="text-muted">Directs user to specific order screen on click.</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-success btn-lg btn-block font-weight-bold shadow-sm">
                                    <i class="fas fa-paper-plane mr-2"></i> Dispatch Notification Now
                                </button>
                            </div>

                            <!-- Live Preview Column -->
                            <div class="col-md-4">
                                <div class="card shadow-sm border border-secondary" style="border-radius: 18px; overflow: hidden;">
                                    <div class="card-header bg-dark text-white text-center py-2 font-weight-bold small">
                                        <i class="fas fa-mobile-alt mr-1"></i> Live Notification Tray Preview
                                    </div>
                                    <div class="card-body p-3 bg-light" style="min-height: 280px;">
                                        <div class="p-3 bg-white shadow-sm border" style="border-radius: 12px;">
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="badge badge-success p-1 mr-2" style="border-radius: 6px;"><i class="fas fa-shopping-bag"></i></span>
                                                <strong class="text-dark small">SB Mart</strong>
                                                <span class="text-muted ml-auto extra-small" style="font-size: 10px;">now</span>
                                            </div>
                                            <h6 class="font-weight-bold text-dark mb-1" id="previewTitle">Notification Title Preview</h6>
                                            <p class="text-secondary small mb-2" id="previewBody">Notification message body text will appear here as you type...</p>
                                            <div id="previewImageContainer" style="display: none;">
                                                <img id="previewImage" src="" class="img-fluid rounded" style="max-height: 120px; width: 100%; object-fit: cover;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- TAB 2: Firebase JSON Settings -->
                <div class="tab-pane fade" id="tab-settings" role="tabpanel" aria-labelledby="tab-settings-tab">
                    <form action="/admin/notifications/settings" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="card border border-light shadow-none bg-light p-3 rounded">
                            <h5 class="font-weight-bold text-dark mb-3"><i class="fas fa-key text-warning mr-2"></i>Firebase Service Account JSON Credentials</h5>
                            
                            <div class="form-group">
                                <label class="font-weight-bold">Firebase Project ID</label>
                                <input type="text" name="project_id" class="form-control" value="{{ $settings->project_id ?? '' }}" placeholder="e.g. sb-mart-qcommerce">
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">Upload Service Account JSON File</label>
                                <input type="file" name="service_account_file" class="form-control-file border bg-white p-2 rounded" accept=".json">
                                <small class="text-muted">Downloaded from Firebase Console -> Project Settings -> Service Accounts -> Generate new private key.</small>
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">Or Paste Service Account JSON Content Below</label>
                                <textarea name="service_account_json" class="form-control font-monospace" rows="8" placeholder='{"type": "service_account", "project_id": "...", "private_key": "..."}'>{{ $settings->service_account_json ?? '' }}</textarea>
                            </div>

                            <button type="submit" class="btn btn-primary font-weight-bold shadow-sm">
                                <i class="fas fa-save mr-2"></i> Save Firebase Configuration
                            </button>
                        </div>
                    </form>
                </div>

                <!-- TAB 3: Audit Logs -->
                <div class="tab-pane fade" id="tab-logs" role="tabpanel" aria-labelledby="tab-logs-tab">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered bg-white">
                            <thead class="thead-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Title</th>
                                    <th>Message</th>
                                    <th>Target</th>
                                    <th>Order ID</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                    <tr>
                                        <td>{{ $log->id }}</td>
                                        <td class="font-weight-bold text-dark">{{ $log->title }}</td>
                                        <td class="small text-secondary">{{ Str::limit($log->body, 60) }}</td>
                                        <td>
                                            @if($log->target_type === 'all')
                                                <span class="badge badge-primary">Broadcast (All)</span>
                                            @else
                                                <span class="badge badge-info">{{ $log->target_phone }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $log->order_id ?? '-' }}</td>
                                        <td>
                                            @if($log->status === 'sent')
                                                <span class="badge badge-success">Sent</span>
                                            @else
                                                <span class="badge badge-warning">{{ ucfirst($log->status) }}</span>
                                            @endif
                                        </td>
                                        <td class="small text-muted">{{ $log->created_at }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">No notifications dispatched yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $logs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleTargetInput(isSpecific) {
        document.getElementById('specificPhoneGroup').style.display = isSpecific ? 'block' : 'none';
    }

    // Dynamic Live Preview
    const notifTitle = document.getElementById('notifTitle');
    const notifBody = document.getElementById('notifBody');
    const notifImage = document.getElementById('notifImage');

    const previewTitle = document.getElementById('previewTitle');
    const previewBody = document.getElementById('previewBody');
    const previewImage = document.getElementById('previewImage');
    const previewImageContainer = document.getElementById('previewImageContainer');

    if (notifTitle) {
        notifTitle.addEventListener('input', function() {
            previewTitle.innerText = this.value || 'Notification Title Preview';
        });
    }

    if (notifBody) {
        notifBody.addEventListener('input', function() {
            previewBody.innerText = this.value || 'Notification message body text will appear here as you type...';
        });
    }

    if (notifImage) {
        notifImage.addEventListener('input', function() {
            if (this.value.trim().length > 0) {
                previewImage.src = this.value.trim();
                previewImageContainer.style.display = 'block';
            } else {
                previewImageContainer.style.display = 'none';
            }
        });
    }
</script>
@endsection
