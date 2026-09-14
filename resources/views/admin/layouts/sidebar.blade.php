<aside class="main-sidebar sidebar-dark-primary elevation-4 position-fixed">
  <a href="/admin" class="brand-link text-center" style="background:#0c831f">
    <span class="brand-text font-weight-bold text-white"><i class="fas fa-bolt mr-2"></i>Q-Commerce</span>
  </a>
  <div class="sidebar">
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        @if(auth()->user() && auth()->user()->role === 'store_manager')
          <!-- Store Manager Menu -->
          <li class="nav-item">
            <a href="/admin/store-manager" class="nav-link {{ request()->is('admin/store-manager*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-user-cog"></i><p>Store Manager Portal</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="/admin/stores/{{ auth()->user()->store_id }}/settings" class="nav-link {{ request()->is('admin/stores/*/settings') ? 'active' : '' }}">
              <i class="nav-icon fas fa-cog"></i><p>Branch Settings</p>
            </a>
          </li>
        @else
          <!-- Full Super Admin Menu -->
          <li class="nav-item">
            <a href="/admin" class="nav-link {{ request()->is('admin') && !request()->is('admin/*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="/admin/stores" class="nav-link {{ request()->is('admin/stores*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-store"></i><p>Stores & Hours</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="/admin/products" class="nav-link {{ request()->is('admin/products*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-boxes"></i><p>Product Catalog</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="/admin/store-manager" class="nav-link {{ request()->is('admin/store-manager*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-user-cog"></i><p>Store Manager Portal</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="/admin/inventory/transactions" class="nav-link {{ request()->is('admin/inventory/transactions*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-history"></i><p>Inventory History</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="/admin/inventory/alerts" class="nav-link text-warning {{ request()->is('admin/inventory/alerts*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-exclamation-triangle"></i><p>Stock Alerts</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="/admin/customers" class="nav-link {{ request()->is('admin/customers*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-users"></i><p>Customers</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="/admin/staff" class="nav-link {{ request()->is('admin/staff*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-users-cog"></i><p>Staff & Roles</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="/admin/deliveries" class="nav-link {{ request()->is('admin/deliveries*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-motorcycle"></i><p>Deliveries & Dispatch</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="/admin/delivery-zones" class="nav-link {{ request()->is('admin/delivery-zones*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-map-marked-alt"></i><p>Delivery Zones</p>
            </a>
          </li>
          <li class="nav-header">SYSTEM</li>
          <li class="nav-item">
            <a href="/import-database" class="nav-link text-warning" onclick="return confirm('Import/Reset Database Tables?')">
              <i class="nav-icon fas fa-file-import"></i><p>Import Database</p>
            </a>
          </li>
        @endif
        <li class="nav-item mt-2">
          <form action="/logout" method="POST" id="logout-form">
            @csrf
            <a href="#" class="nav-link text-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
              <i class="nav-icon fas fa-sign-out-alt"></i><p>Logout</p>
            </a>
          </form>
        </li>
      </ul>
    </nav>
  </div>
</aside>
