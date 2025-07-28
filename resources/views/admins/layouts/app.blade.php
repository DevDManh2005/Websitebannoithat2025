{{-- resources/views/admins/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Admin Panel – @yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        #wrapper.toggled #sidebar-wrapper {
            margin-left: -250px;
        }

        #sidebar-wrapper {
            width: 250px;
        }
    </style>
</head>

<body>
    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <div class="border-end bg-white" id="sidebar-wrapper">
            <div class="sidebar-heading px-3 py-2">Admin Panel</div>
            <div class="list-group list-group-flush">
                <a href="{{ route('admin.dashboard') }}"
                    class="list-group-item list-group-item-action {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2 me-2"></i> Tổng quan
                </a>

                <a href="{{ route('admin.orders.index') }}"
                    class="list-group-item list-group-item-action {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                    <i class="bi bi-receipt me-2"></i> Quản lý Đơn hàng
                </a>

                <a href="{{ route('admin.staffs.index') }}"
                    class="list-group-item list-group-item-action {{ request()->routeIs('admin.staffs.*') ? 'active' : '' }}">
                    <i class="bi bi-people me-2"></i> Quản lý Nhân viên
                </a>

                <a href="{{ route('admin.admins.index') }}"
                    class="list-group-item list-group-item-action {{ request()->routeIs('admin.admins.*') ? 'active' : '' }}">
                    <i class="bi bi-person-badge me-2"></i> Quản lý Admin
                </a>

                <a href="{{ route('admin.users.index') }}"
                    class="list-group-item list-group-item-action {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="bi bi-person-lines-fill me-2"></i> Quản lý Người dùng
                </a>
                <a href="{{ route('admin.categories.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                    <i class="bi bi-graph-up me-2"></i> Danh Mục
                </a>
                <a href="{{ route('admin.brands.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.brands.*') ? 'active' : '' }}">
                    <i class="bi bi-bag me-2"></i> Hãng
                </a>
                <a href="{{ route('admin.suppliers.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.suppliers.*') ? 'active' : '' }}">
                    <i class="bi bi-box-seam me-2"></i> Nhà cung cấp
                </a>

                <a href="{{ route('admin.products.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                    <i class="bi bi-box me-2"></i> Sản Phẩm
                </a>

                <a href="{{ route('admin.inventories.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.inventories.*') ? 'active' : '' }}">
                    <i class="bi bi-archive me-2"></i> Kho
                </a>

            </div>
        </div>
        <!-- /#sidebar-wrapper -->

        <!-- Page Content -->
        <div id="page-content-wrapper" class="flex-fill">
            <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
                <div class="container-fluid">
                    <button class="btn btn-outline-primary" id="sidebarToggle"><i class="bi bi-list"></i></button>

                    <form class="d-flex ms-3">
                        <input class="form-control" type="search" placeholder="Tìm kiếm..." aria-label="Search">
                    </form>

                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item me-3">
                            <a class="nav-link position-relative" href="#">
                                <i class="bi bi-bell fs-5"></i>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">3</span>
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" data-bs-toggle="dropdown">
                                <img src="https://via.placeholder.com/30" class="rounded-circle me-2">
                                {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="#">Hồ sơ</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
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
    @stack('scripts')
    <script>
        document.getElementById('sidebarToggle').addEventListener('click', function () {
            document.getElementById('wrapper').classList.toggle('toggled');
        });
    </script>
</body>

</html>
