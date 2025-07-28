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
      <a href="{{ route('staff.dashboard') }}" class="list-group-item list-group-item-action {{ request()->routeIs('staff.dashboard') ? 'active' : '' }}">
        <i class="bi bi-speedometer2 me-2"></i> Tổng quan
      </a>
      <a href="#" class="list-group-item list-group-item-action">
        <i class="bi bi-bag me-2"></i> Đơn hàng
      </a>
      <a href="#" class="list-group-item list-group-item-action">
        <i class="bi bi-graph-up me-2"></i> Thống kê cá nhân
      </a>
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
            <a class="nav-link" href="#"><i class="bi bi-bell fs-5"></i></a>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" data-bs-toggle="dropdown">
              <img src="https://via.placeholder.com/30" class="rounded-circle me-2">
              {{ Auth::user()->name }}
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
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
