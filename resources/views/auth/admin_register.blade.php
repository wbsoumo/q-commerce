<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Q-Commerce | Temp Admin Registration</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  <style>
    body.login-page {
      background: linear-gradient(135deg, #0c831f 0%, #064e12 100%);
    }
    .card-primary.card-outline {
      border-top: 3px solid #0c831f;
    }
  </style>
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="card card-outline card-primary shadow-lg">
    <div class="card-header text-center py-4" style="background: #0c831f;">
      <a href="#" class="h1 font-weight-bold text-white"><i class="fas fa-user-plus mr-2"></i>Q-Commerce</a>
      <div class="text-white-50 small mt-1 font-weight-bold">TEMP ADMIN REGISTRATION / RESET</div>
    </div>
    <div class="card-body login-card-body p-4">
      <p class="login-box-msg font-weight-bold text-secondary">Create or Reset Super Admin Account</p>

      @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <i class="fas fa-exclamation-triangle mr-1"></i> {{ session('error') }}
          <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
      @endif

      <form action="/admin/register" method="POST">
        @csrf
        <div class="input-group mb-3">
          <input type="text" name="name" class="form-control" placeholder="Admin Name" value="Super Admin" required autofocus>
          <div class="input-group-append">
            <div class="input-group-text"><span class="fas fa-user"></span></div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="email" name="email" class="form-control" placeholder="Admin Email" value="admin@blinkit.com" required>
          <div class="input-group-append">
            <div class="input-group-text"><span class="fas fa-envelope"></span></div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" name="password" class="form-control" placeholder="New Password" value="admin123" required>
          <div class="input-group-append">
            <div class="input-group-text"><span class="fas fa-lock"></span></div>
          </div>
        </div>
        <div class="row mb-3">
          <div class="col-12">
            <button type="submit" class="btn btn-success btn-block font-weight-bold" style="background:#0c831f">
              <i class="fas fa-user-check mr-1"></i> Register / Set Password & Login
            </button>
          </div>
        </div>
      </form>

      <div class="text-center mt-3">
        <a href="/admin/login" class="text-secondary font-weight-bold">
          <i class="fas fa-arrow-left mr-1"></i> Back to Admin Login
        </a>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>
