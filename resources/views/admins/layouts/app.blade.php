<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel – @yield('title', 'Dashboard')</title>

    {{-- Bootstrap CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    {{-- Custom CSS for Admin Layout --}}
    <style>
        body {
            overflow-x: hidden;
        }

        #wrapper {
            display: flex;
        }

        #sidebar-wrapper {
            min-height: 100vh;
            width: 250px;
            margin-left: 0;
            transition: margin .25s ease-out;
            background-color: #f8f9fa;
        }

        #sidebar-wrapper .sidebar-heading {
            padding: 0.875rem 1.25rem;
            font-size: 1.2rem;
            font-weight: bold;
        }

        #sidebar-wrapper .list-group {
            width: 100%;
        }

        #page-content-wrapper {
            flex: 1;
            min-width: 0;
            width: 100%;
        }

        #wrapper.toggled #sidebar-wrapper {
            margin-left: -250px;
        }

        .list-group-item {
            border: 0;
        }
    </style>
</head>

<body>
    <div id="wrapper">
        <div class="border-end" id="sidebar-wrapper">
            <div class="sidebar-heading border-bottom">Admin Panel</div>
            <div class="list-group list-group-flush">
                <a href="{{ route('admin.dashboard') }}"
                    class="list-group-item list-group-item-action d-flex align-items-center p-3 {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2 me-2"></i> Tổng quan
                </a>
                <a href="{{ route('admin.orders.index') }}"
                    class="list-group-item list-group-item-action d-flex align-items-center p-3 {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                    <i class="bi bi-receipt me-2"></i> Quản lý Đơn hàng
                </a>
                <a href="{{ route('admin.products.index') }}"
                    class="list-group-item list-group-item-action d-flex align-items-center p-3 {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                    <i class="bi bi-box me-2"></i> Sản Phẩm
                </a>
                <a href="{{ route('admin.reviews.index') }}"
                    class="list-group-item list-group-item-action d-flex align-items-center p-3 {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
                    <i class="bi bi-chat-square-text me-2"></i> Quản lý Đánh giá
                </a>
                <a href="{{ route('admin.categories.index') }}"
                    class="list-group-item list-group-item-action d-flex align-items-center p-3 {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                    <i class="bi bi-graph-up me-2"></i> Danh Mục
                </a>
                <a href="{{ route('admin.brands.index') }}"
                    class="list-group-item list-group-item-action d-flex align-items-center p-3 {{ request()->routeIs('admin.brands.*') ? 'active' : '' }}">
                    <i class="bi bi-bag me-2"></i> Hãng
                </a>
                <a href="{{ route('admin.suppliers.index') }}"
                    class="list-group-item list-group-item-action d-flex align-items-center p-3 {{ request()->routeIs('admin.suppliers.*') ? 'active' : '' }}">
                    <i class="bi bi-box-seam me-2"></i> Nhà cung cấp
                </a>
                <a href="{{ route('admin.inventories.index') }}"
                    class="list-group-item list-group-item-action d-flex align-items-center p-3 {{ request()->routeIs('admin.inventories.*') ? 'active' : '' }}">
                    <i class="bi bi-archive me-2"></i> Kho
                </a>
                <a href="{{ route('admin.vouchers.index') }}"
                    class="list-group-item list-group-item-action d-flex align-items-center p-3 {{ request()->routeIs('admin.vouchers.*') ? 'active' : '' }}">
                    <i class="bi bi-ticket-percent me-2"></i> Quản lý Voucher
                </a>
                <hr class="my-2">

                <a href="{{ route('admin.users.index') }}"
                    class="list-group-item list-group-item-action d-flex align-items-center p-3 {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="bi bi-person-lines-fill me-2"></i> Quản lý Người dùng
                </a>
                <a href="{{ route('admin.staffs.index') }}"
                    class="list-group-item list-group-item-action d-flex align-items-center p-3 {{ request()->routeIs('admin.staffs.*') ? 'active' : '' }}">
                    <i class="bi bi-people me-2"></i> Quản lý Nhân viên
                </a>
                <a href="{{ route('admin.admins.index') }}"
                    class="list-group-item list-group-item-action d-flex align-items-center p-3 {{ request()->routeIs('admin.admins.*') ? 'active' : '' }}">
                    <i class="bi bi-person-badge me-2"></i> Quản lý Admin
                </a>
                <a href="{{ route('admin.settings.index') }}"
                    class="list-group-item list-group-item-action d-flex align-items-center p-3 {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                    <i class="bi bi-gear me-2"></i> Cài đặt
                </a>
                <a href="{{ route('admin.slides.index') }}"
                    class="list-group-item list-group-item-action d-flex align-items-center p-3 {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                    <i class="bi bi-gear me-2"></i> Quản lý Slide
                </a>
            </div>
        </div>
        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
                <div class="container-fluid">
                    <button class="btn btn-outline-primary" id="sidebarToggle"><i class="bi bi-list"></i></button>

                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown"
                                role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <img src="https://via.placeholder.com/30" class="rounded-circle me-2" alt="User Avatar">
                                {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="#">Hồ sơ</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Đăng xuất</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>

            <main class="container-fluid p-4">
                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('sidebarToggle').addEventListener('click', function () {
            document.getElementById('wrapper').classList.toggle('toggled');
        });
    </script>
    @stack('scripts')
</body>

</html>