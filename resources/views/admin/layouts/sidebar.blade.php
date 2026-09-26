<aside class="main-sidebar sidebar-dark-primary elevation-4 position-fixed" style="z-index: 1038;">
  <a href="/admin" class="brand-link text-center border-bottom border-secondary" style="background:#0c831f; padding: 12px 15px;">
    <span class="brand-text font-weight-bold text-white"><i class="fas fa-bolt mr-2 text-warning"></i>Q-Commerce Admin</span>
  </a>
  
  <div class="sidebar">
    <nav class="mt-2 pb-4">
      <ul class="nav nav-pills nav-sidebar flex-column nav-child-indent" data-widget="treeview" role="menu" data-accordion="false">
        @if(auth()->user() && auth()->user()->role === 'store_manager')
          <!-- Store Manager Menu -->
          <li class="nav-header text-uppercase text-muted small font-weight-bold">Store Operations</li>
          <li class="nav-item">
            <a href="/admin/store-manager" class="nav-link {{ request()->is('admin/store-manager*') ? 'active bg-success' : '' }}">
              <i class="nav-icon fas fa-chart-pie text-light"></i><p>Dashboard</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="/admin/stores/{{ auth()->user()->store_id }}/settings" class="nav-link {{ request()->is('admin/stores/*/settings') ? 'active bg-success' : '' }}">
              <i class="nav-icon fas fa-cog text-light"></i><p>Branch Settings</p>
            </a>
          </li>
        @else
          <!-- Full Super Admin Menu Organized by Operational Priority -->
          
          <!-- Priority 1: Overview -->
          <li class="nav-header text-uppercase text-muted small font-weight-bold">Overview</li>
          <li class="nav-item">
            <a href="/admin" class="nav-link {{ request()->is('admin') && !request()->is('admin/*') ? 'active bg-success' : '' }}">
              <i class="nav-icon fas fa-chart-line text-light"></i><p>Dashboard</p>
            </a>
          </li>

          <!-- Priority 2: Daily Operations & Fulfillment -->
          <li class="nav-header text-uppercase text-muted small font-weight-bold">Operations</li>
          <li class="nav-item">
            <a href="/admin/orders" class="nav-link {{ request()->is('admin/orders*') ? 'active bg-success' : '' }}">
              <i class="nav-icon fas fa-shopping-bag text-warning"></i>
              <p>Orders</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="/admin/customers" class="nav-link {{ request()->is('admin/customers*') ? 'active bg-success' : '' }}">
              <i class="nav-icon fas fa-users text-light"></i><p>Customers</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="/admin/deliveries" class="nav-link {{ request()->is('admin/deliveries*') ? 'active bg-success' : '' }}">
              <i class="nav-icon fas fa-motorcycle text-light"></i><p>Deliveries</p>
            </a>
          </li>

          <!-- Priority 3: Catalog & Inventory Management -->
          <li class="nav-header text-uppercase text-muted small font-weight-bold">Catalog & Inventory</li>
          <li class="nav-item">
            <a href="/admin/categories" class="nav-link {{ request()->is('admin/categories*') ? 'active bg-success' : '' }}">
              <i class="nav-icon fas fa-th-large text-light"></i><p>Categories</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="/admin/products" class="nav-link {{ request()->is('admin/products*') ? 'active bg-success' : '' }}">
              <i class="nav-icon fas fa-boxes text-light"></i><p>Products</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="/admin/store-manager" class="nav-link {{ request()->is('admin/store-manager*') ? 'active bg-success' : '' }}">
              <i class="nav-icon fas fa-cubes text-light"></i><p>Inventory</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="/admin/inventory/alerts" class="nav-link {{ request()->is('admin/inventory/alerts*') ? 'active bg-success' : '' }}">
              <i class="nav-icon fas fa-exclamation-triangle text-warning"></i><p>Stock Alerts</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="/admin/inventory/transactions" class="nav-link {{ request()->is('admin/inventory/transactions*') ? 'active bg-success' : '' }}">
              <i class="nav-icon fas fa-history text-light"></i><p>Stock Logs</p>
            </a>
          </li>

          <!-- Priority 4: Marketing & App Customization -->
          <li class="nav-header text-uppercase text-muted small font-weight-bold">Marketing & App</li>
          <li class="nav-item">
            <a href="/admin/sliders" class="nav-link {{ request()->is('admin/sliders*') ? 'active bg-success' : '' }}">
              <i class="nav-icon fas fa-images text-light"></i><p>Sliders</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="/admin/homepage-customizer" class="nav-link {{ request()->is('admin/homepage-customizer*') ? 'active bg-success' : '' }}">
              <i class="nav-icon fas fa-desktop text-light"></i><p>Home Design</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="/admin/coupons" class="nav-link {{ request()->is('admin/coupons*') ? 'active bg-success' : '' }}">
              <i class="nav-icon fas fa-ticket-alt text-light"></i><p>Coupons</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="/admin/notifications" class="nav-link {{ request()->is('admin/notifications*') ? 'active bg-success' : '' }}">
              <i class="nav-icon fas fa-bell text-light"></i><p>Notifications</p>
            </a>
          </li>

          <!-- Priority 5: Stores & System Settings -->
          <li class="nav-header text-uppercase text-muted small font-weight-bold">Store & System</li>
          <li class="nav-item">
            <a href="/admin/stores" class="nav-link {{ request()->is('admin/stores*') && !request()->is('admin/stores/*/settings') ? 'active bg-success' : '' }}">
              <i class="nav-icon fas fa-store text-light"></i><p>Stores</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="/admin/delivery-zones" class="nav-link {{ request()->is('admin/delivery-zones*') ? 'active bg-success' : '' }}">
              <i class="nav-icon fas fa-map-marked-alt text-light"></i><p>Coverage</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="/admin/staff" class="nav-link {{ request()->is('admin/staff*') ? 'active bg-success' : '' }}">
              <i class="nav-icon fas fa-user-shield text-light"></i><p>Staff</p>
            </a>
          </li>
        @endif

        <li class="nav-header text-uppercase text-muted small font-weight-bold mt-2">Account</li>
        <li class="nav-item mb-3">
          <form action="/logout" method="POST" id="logout-form">
            @csrf
            <a href="#" class="nav-link text-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
              <i class="nav-icon fas fa-sign-out-alt"></i><p class="font-weight-bold">Logout</p>
            </a>
          </form>
        </li>
      </ul>
    </nav>
  </div>
</aside>
