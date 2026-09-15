<aside class="main-sidebar sidebar-dark-primary elevation-4 position-fixed" style="z-index: 1038;">
  <a href="/admin" class="brand-link text-center border-bottom border-secondary" style="background:#0c831f; padding: 12px 15px;">
    <span class="brand-text font-weight-bold text-white"><i class="fas fa-bolt mr-2 text-warning"></i>Q-Commerce Admin</span>
  </a>
  
  <div class="sidebar">
    <nav class="mt-2 pb-4">
      <ul class="nav nav-pills nav-sidebar flex-column nav-child-indent" data-widget="treeview" role="menu" data-accordion="false">
        @if(auth()->user() && auth()->user()->role === 'store_manager')
          <!-- Store Manager Menu -->
          <li class="nav-header text-uppercase text-muted small font-weight-bold">Store Management</li>
          <li class="nav-item">
            <a href="/admin/store-manager" class="nav-link {{ request()->is('admin/store-manager*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-user-cog text-info"></i><p>Manager Dashboard</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="/admin/stores/{{ auth()->user()->store_id }}/settings" class="nav-link {{ request()->is('admin/stores/*/settings') ? 'active' : '' }}">
              <i class="nav-icon fas fa-sliders-h text-warning"></i><p>Branch Settings</p>
            </a>
          </li>
        @else
          <!-- Full Super Admin Menu -->
          <li class="nav-header text-uppercase text-muted small font-weight-bold">Main Dashboard</li>
          <li class="nav-item">
            <a href="/admin" class="nav-link {{ request()->is('admin') && !request()->is('admin/*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-chart-line text-info"></i><p>Analytics Overview</p>
            </a>
          </li>

          <li class="nav-header text-uppercase text-muted small font-weight-bold">App & Store Customizer</li>
          <li class="nav-item">
            <a href="/admin/homepage-customizer" class="nav-link {{ request()->is('admin/homepage-customizer*') ? 'active bg-success' : '' }}">
              <i class="nav-icon fas fa-desktop text-success"></i>
              <p>App Home Customizer <span class="right badge badge-success">Live</span></p>
            </a>
          </li>
          <li class="nav-item">
            <a href="/admin/stores" class="nav-link {{ request()->is('admin/stores*') && !request()->is('admin/stores/*/settings') ? 'active' : '' }}">
              <i class="nav-icon fas fa-store text-primary"></i><p>Stores & Dark Hubs</p>
            </a>
          </li>

          <li class="nav-header text-uppercase text-muted small font-weight-bold">Catalog & Inventory</li>
          <li class="nav-item">
            <a href="/admin/categories" class="nav-link {{ request()->is('admin/categories*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-th-large text-warning"></i><p>Categories</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="/admin/products" class="nav-link {{ request()->is('admin/products*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-boxes text-info"></i><p>Product Catalog</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="/admin/store-manager" class="nav-link {{ request()->is('admin/store-manager*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-cubes text-teal"></i><p>Store Inventory Matrix</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="/admin/inventory/transactions" class="nav-link {{ request()->is('admin/inventory/transactions*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-history text-secondary"></i><p>Inventory Audit Logs</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="/admin/inventory/alerts" class="nav-link {{ request()->is('admin/inventory/alerts*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-exclamation-triangle text-danger"></i><p>Low Stock Alerts</p>
            </a>
          </li>

          <li class="nav-header text-uppercase text-muted small font-weight-bold">Logistics & Users</li>
          <li class="nav-item">
            <a href="/admin/customers" class="nav-link {{ request()->is('admin/customers*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-users text-primary"></i><p>Customers</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="/admin/staff" class="nav-link {{ request()->is('admin/staff*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-user-shield text-indigo"></i><p>Staff & Roles</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="/admin/deliveries" class="nav-link {{ request()->is('admin/deliveries*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-motorcycle text-success"></i><p>Deliveries & Riders</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="/admin/delivery-zones" class="nav-link {{ request()->is('admin/delivery-zones*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-map-marked-alt text-purple"></i><p>Delivery Coverage</p>
            </a>
          </li>
        @endif

        <li class="nav-header text-uppercase text-muted small font-weight-bold mt-2">Account</li>
        <li class="nav-item mb-3">
          <form action="/logout" method="POST" id="logout-form">
            @csrf
            <a href="#" class="nav-link text-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
              <i class="nav-icon fas fa-sign-out-alt"></i><p class="font-weight-bold">Logout System</p>
            </a>
          </form>
        </li>
      </ul>
    </nav>
  </div>
</aside>
