<aside class="main-sidebar manager-sidebar sidebar-dark-primary elevation-4 position-fixed">
  <a href="/manager/dashboard" class="brand-link text-center brand-banner border-0">
    <span class="brand-text font-weight-bold text-white"><i class="fas fa-shopping-basket mr-2"></i>Branch Manager</span>
  </a>
  <div class="sidebar">
    <div class="user-panel mt-3 pb-3 mb-3 d-flex border-bottom border-secondary">
      <div class="image text-white font-weight-bold pl-2">
        <i class="fas fa-user-circle fa-2x text-info"></i>
      </div>
      <div class="info">
        <a href="#" class="d-block font-weight-bold text-light">{{ auth()->user()->name ?? 'Store Manager' }}</a>
        <span class="badge badge-info mt-1"><i class="fas fa-map-marker-alt mr-1"></i> {{ $store->city ?? 'Branch' }}</span>
      </div>
    </div>
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
        <li class="nav-item">
          <a href="/manager/dashboard" class="nav-link {{ request()->is('manager/dashboard*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-tachometer-alt text-primary"></i><p>Dashboard</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="/manager/inventory" class="nav-link {{ request()->is('manager/inventory*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-boxes text-info"></i><p>Live Inventory & Pricing</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="/manager/orders" class="nav-link {{ request()->is('manager/orders*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-shopping-cart text-warning"></i><p>Store Orders</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="/manager/deliveries" class="nav-link {{ request()->is('manager/deliveries*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-motorcycle text-success"></i><p>Delivery Dispatch</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="/manager/settings" class="nav-link {{ request()->is('manager/settings*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-cog text-light"></i><p>Branch Operations</p>
          </a>
        </li>
        <li class="nav-header">ACCOUNT</li>
        <li class="nav-item">
          <form action="/logout" method="POST" id="mgr-logout">
            @csrf
            <a href="#" class="nav-link text-danger" onclick="event.preventDefault(); document.getElementById('mgr-logout').submit();">
              <i class="nav-icon fas fa-power-off"></i><p>Logout Session</p>
            </a>
          </form>
        </li>
      </ul>
    </nav>
  </div>
</aside>
