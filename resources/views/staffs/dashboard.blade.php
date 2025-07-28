<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Staff Panel – @yield('title')</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    #wrapper.toggled #sidebar-wrapper { margin-left: -220px; }
    #sidebar-wrapper { width: 220px; }
  </style>
</head>
<body>
<div class="d-flex" id="wrapper">
  <!-- Sidebar -->
  <div class="border-end bg-light" id="sidebar-wrapper">
    <div class="sidebar-heading px-3 py-2">Staff Panel</div>
    <div class="list-group list-group-flush">
      {{-- Tổng quan luôn hiện --}}
      <a href="{{ route('staff.dashboard') }}"
         class="list-group-item list-group-item-action {{ request()->routeIs('staff.dashboard') ? 'active' : '' }}">
        <i class="bi bi-speedometer2 me-2"></i> Tổng quan
      </a>

      {{-- Đơn hàng --}}
      @if(Auth::user()->hasPermission('orders', 'view'))
      <a href="{{ route('staff.orders.index') }}" 
         class="list-group-item list-group-item-action {{ request()->routeIs('staff.orders.*') ? 'active' : '' }}">
        <i class="bi bi-bag me-2"></i> Đơn hàng
      </a>
      @endif

      {{-- Thống kê cá nhân --}}
      @if(Auth::user()->hasPermission('reports', 'view_personal'))
      <a href="{{ route('staff.reports.personal') }}"
         class="list-group-item list-group-item-action {{ request()->routeIs('staff.reports.personal') ? 'active' : '' }}">
        <i class="bi bi-graph-up me-2"></i> Thống kê cá nhân
      </a>
      @endif

      {{-- Ví dụ thêm: Quản lý sản phẩm --}}
      @if(Auth::user()->hasPermission('products', 'manage'))
      <a href="{{ route('staff.products.index') }}"
         class="list-group-item list-group-item-action {{ request()->routeIs('staff.products.*') ? 'active' : '' }}">
        <i class="bi bi-box-seam me-2"></i> Sản phẩm
      </a>
      @endif
    </div>
  </div>
  <!-- /#sidebar-wrapper -->

  <!-- Page Content -->
  <div id="page-content-wrapper" class="flex-fill">
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
      <div class="container-fluid">
        <button class="btn btn-outline-primary" id="sidebarToggle"><i class="bi bi-list"></i></button>
        <ul class="navbar-nav ms-auto">
          <li class="nav-item me-3">
            @if(Auth::user()->hasPermission('notifications', 'view'))
            <a class="nav-link" href="{{ route('staff.notifications.index') }}">
              <i class="bi bi-bell fs-5"></i>
              @if($unread = Auth::user()->unreadNotifications->count())
                <span class="badge bg-danger">{{ $unread }}</span>
              @endif
            </a>
            @endif
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" data-bs-toggle="dropdown">
              <img src="{{ Auth::user()->profile->avatar ?? 'https://via.placeholder.com/30' }}"
                   class="rounded-circle me-2" width="30" height="30">
              {{ Auth::user()->name }}
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="{{ route('staff.profile.edit') }}">Hồ sơ cá nhân</a></li>
              <li><hr class="dropdown-divider"></li>
              <li>
                <form action="{{ route('logout') }}" method="POST">
                  @csrf
                  <button class="dropdown-item">Đăng xuất</button>
                </form>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </nav>

    <div class="container-fluid p-4">
      @yield('content')
    </div>
  </div>
  <!-- /#page-content-wrapper -->
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  document.getElementById('sidebarToggle').addEventListener('click', function() {
    document.getElementById('wrapper').classList.toggle('toggled');
  });
</script>
</body>
</html>
