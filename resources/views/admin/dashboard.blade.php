<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Q-Commerce Admin | Dashboard</title>
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- Theme style AdminLTE 3 -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  <style>
    .brand-link { background-color: #0c831f !important; }
    .btn-primary { background-color: #0c831f; border-color: #0c831f; }
    .btn-primary:hover { background-color: #085d15; border-color: #085d15; }
  </style>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="/" class="nav-link">Home</a>
      </li>
    </ul>
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <a class="btn btn-outline-success btn-sm" href="/import-database" target="_blank" onclick="return confirm('Import/Reset Database schema and seed sample products?')">
          <i class="fas fa-database mr-1"></i> Import Database Tables
        </a>
      </li>
    </ul>
  </nav>

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="/admin" class="brand-link text-center">
      <span class="brand-text font-weight-bold text-white"><i class="fas fa-bolt mr-2"></i>Q-Commerce Admin</span>
    </a>
    <div class="sidebar">
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
          <li class="nav-item">
            <a href="/admin" class="nav-link active">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>Dashboard</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="/admin/products" class="nav-link">
              <i class="nav-icon fas fa-box"></i>
              <p>Products</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="/import-database" class="nav-link text-warning" onclick="return confirm('Import Database Tables?')">
              <i class="nav-icon fas fa-file-import"></i>
              <p>Import Database</p>
            </a>
          </li>
        </ul>
      </nav>
    </div>
  </aside>

  <!-- Content Wrapper -->
  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 font-weight-bold">Dashboard</h1>
          </div>
        </div>
      </div>
    </div>

    <!-- Main content -->
    <div class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-lg-4 col-6">
            <div class="small-box bg-success">
              <div class="inner">
                <h3>{{ $totalProducts ?? 0 }}</h3>
                <p>Total Products</p>
              </div>
              <div class="icon"><i class="fas fa-shopping-bag"></i></div>
            </div>
          </div>
          <div class="col-lg-4 col-6">
            <div class="small-box bg-info">
              <div class="inner">
                <h3>{{ $totalCategories ?? 0 }}</h3>
                <p>Categories</p>
              </div>
              <div class="icon"><i class="fas fa-th-large"></i></div>
            </div>
          </div>
          <div class="col-lg-4 col-6">
            <div class="small-box bg-warning">
              <div class="inner">
                <h3>{{ $totalOrders ?? 0 }}</h3>
                <p>Total Orders</p>
              </div>
              <div class="icon"><i class="fas fa-shopping-cart"></i></div>
            </div>
          </div>
        </div>

        <!-- Database Auto Import Action Card -->
        <div class="card card-outline card-success">
          <div class="card-header">
            <h3 class="card-title font-weight-bold"><i class="fas fa-database text-success mr-2"></i>Database Setup & Import</h3>
          </div>
          <div class="card-body">
            <p>Click below to create and populate all MySQL tables (Stores, Categories, Products, Orders) automatically.</p>
            <a href="/import-database" class="btn btn-success font-weight-bold" onclick="return confirm('Import Database Tables?')">
              <i class="fas fa-download mr-1"></i> Import / Reset Database Tables Now
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <footer class="main-footer">
    <strong>Copyright &copy; 2026 Q-Commerce Admin.</strong> All rights reserved.
  </footer>
</div>

<!-- jQuery & AdminLTE -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>
