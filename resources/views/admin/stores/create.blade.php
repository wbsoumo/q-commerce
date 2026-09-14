<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Q-Commerce Admin | Create Store & Manager</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
      <li class="nav-item"><a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a></li>
      <li class="nav-item d-none d-sm-inline-block"><a href="/admin/stores" class="nav-link">Stores</a></li>
      <li class="nav-item d-none d-sm-inline-block"><a href="#" class="nav-link active font-weight-bold">Create Store</a></li>
    </ul>
  </nav>

  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="/admin" class="brand-link text-center" style="background:#0c831f">
      <span class="brand-text font-weight-bold text-white"><i class="fas fa-bolt mr-2"></i>Q-Commerce</span>
    </a>
    <div class="sidebar">
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column">
          <li class="nav-item"><a href="/admin" class="nav-link"><i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p></a></li>
          <li class="nav-item"><a href="/admin/stores" class="nav-link active"><i class="nav-icon fas fa-store"></i><p>Stores & Managers</p></a></li>
        </ul>
      </nav>
    </div>
  </aside>

  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <h1 class="m-0 font-weight-bold">Add New Store & Create Manager Account</h1>
      </div>
    </div>

    <div class="content">
      <div class="container-fluid">
        <div class="card card-success">
          <div class="card-header"><h3 class="card-title font-weight-bold">Store & Manager Information</h3></div>
          <form action="/admin/stores/store" method="POST">
            @csrf
            <div class="card-body">
              <h5 class="text-success font-weight-bold mb-3"><i class="fas fa-store mr-1"></i> Store Details</h5>
              <div class="row">
                <div class="col-md-6 form-group">
                  <label>Store Name</label>
                  <input type="text" name="name" class="form-control" placeholder="e.g. Krishnanagar Branch" required>
                </div>
                <div class="col-md-6 form-group">
                  <label>Store Code</label>
                  <input type="text" name="code" class="form-control" placeholder="e.g. STR-KRN-03" required>
                </div>
              </div>
              <div class="form-group">
                <label>Address</label>
                <input type="text" name="address" class="form-control" placeholder="Street address" required>
              </div>
              <div class="row">
                <div class="col-md-6 form-group">
                  <label>City</label>
                  <input type="text" name="city" class="form-control" value="Krishnanagar" required>
                </div>
                <div class="col-md-6 form-group">
                  <label>Pincode</label>
                  <input type="text" name="pincode" class="form-control" value="741101" required>
                </div>
              </div>

              <hr>

              <h5 class="text-primary font-weight-bold mb-3"><i class="fas fa-user-shield mr-1"></i> Store Manager Portal Account</h5>
              <div class="row">
                <div class="col-md-4 form-group">
                  <label>Manager Full Name</label>
                  <input type="text" name="manager_name" class="form-control" placeholder="Manager Name" required>
                </div>
                <div class="col-md-4 form-group">
                  <label>Manager Email (Login ID)</label>
                  <input type="email" name="manager_email" class="form-control" placeholder="manager@blinkit.com" required>
                </div>
                <div class="col-md-4 form-group">
                  <label>Manager Password</label>
                  <input type="password" name="manager_password" class="form-control" placeholder="******" required>
                </div>
              </div>
            </div>
            <div class="card-footer">
              <button type="submit" class="btn btn-success font-weight-bold"><i class="fas fa-save mr-1"></i> Save Store & Create Manager</button>
              <a href="/admin/stores" class="btn btn-default">Cancel</a>
            </div>
          </form>
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
