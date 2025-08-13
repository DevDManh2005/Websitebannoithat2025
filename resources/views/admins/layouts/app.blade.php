{{-- resources/views/admins/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel – @yield('title', 'Dashboard')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Bootstrap CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    {{-- Custom CSS for Admin Layout --}}
    <style>
        :root {
            --sidebar-width: 250px;
        }

        body {
            overflow-x: hidden;
        }

        #wrapper {
            display: flex;
        }

        #sidebar-wrapper {
            min-height: 100vh;
            width: var(--sidebar-width);
            margin-left: 0;
            transition: margin .25s ease-out;
            background-color: #f8f9fa;
        }

        #sidebar-wrapper .sidebar-heading {
            padding: .875rem 1.25rem;
            font-size: 1.2rem;
            font-weight: 700;
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
            margin-left: calc(-1 * var(--sidebar-width));
        }

        .list-group-item {
            border: 0;
        }

        .list-group-item.active {
            background: #0d6efd;
            color: #fff;
        }

        .nav-section-title {
            font-size: .75rem;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: #6c757d;
            padding: .5rem 1rem;
        }

        @media (max-width: 991.98px) {
            #sidebar-wrapper {
                position: fixed;
                z-index: 1030;
            }

            #wrapper.toggled #sidebar-wrapper {
                margin-left: 0;
            }

            #sidebarOverlay {
                display: none;
            }

            #wrapper.toggled #sidebarOverlay {
                display: block;
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, .25);
                z-index: 1029;
            }
        }
    </style>
    @stack('styles')
</head>

<body>
    <div id="wrapper">
        <div id="sidebarOverlay" onclick="document.getElementById('wrapper').classList.remove('toggled')"></div>

        <div class="border-end" id="sidebar-wrapper">
            <div class="sidebar-heading border-bottom">Admin Panel</div>
            <div class="list-group list-group-flush">

                <div class="nav-section-title">Tổng quan</div>
                <a href="{{ route('admin.dashboard') }}"
                    class="list-group-item list-group-item-action d-flex align-items-center p-3 {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2 me-2"></i> Tổng quan
                </a>

                <div class="nav-section-title">Bán hàng</div>
                <a href="{{ route('admin.orders.index') }}"
                    class="list-group-item list-group-item-action d-flex align-items-center p-3 {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                    <i class="bi bi-receipt me-2"></i> Quản lý Đơn hàng
                </a>

                <a href="{{ route('admin.products.index') }}"
                    class="list-group-item list-group-item-action d-flex align-items-center p-3 {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                    <i class="bi bi-box me-2"></i> Sản phẩm
                </a>

                <a href="{{ route('admin.reviews.index') }}"
                    class="list-group-item list-group-item-action d-flex align-items-center p-3 {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
                    <i class="bi bi-chat-square-text me-2"></i> Đánh giá sản phẩm
                </a>

                <a href="{{ route('admin.categories.index') }}"
                    class="list-group-item list-group-item-action d-flex align-items-center p-3 {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                    <i class="bi bi-graph-up me-2"></i> Danh mục sản phẩm
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
                    <i class="bi bi-ticket-percent me-2"></i> Voucher
                </a>

                <hr class="my-2">

                <div class="nav-section-title">Nội dung</div>
                <a href="{{ route('admin.blog-categories.index') }}"
                    class="list-group-item list-group-item-action d-flex align-items-center p-3 {{ request()->routeIs('admin.blog-categories.*') ? 'active' : '' }}">
                    <i class="bi bi-folder me-2"></i> Danh mục bài viết
                </a>

                <a href="{{ route('admin.blogs.index') }}"
                    class="list-group-item list-group-item-action d-flex align-items-center p-3 {{ request()->routeIs('admin.blogs.*') ? 'active' : '' }}">
                    <i class="bi bi-journal-text me-2"></i> Bài viết
                </a>

                @if(Route::has('admin.blog-comments.index'))
                    <a href="{{ route('admin.blog-comments.index') }}"
                        class="list-group-item list-group-item-action d-flex align-items-center p-3 {{ request()->routeIs('admin.blog-comments.*') ? 'active' : '' }}">
                        <i class="bi bi-chat-dots me-2"></i> Bình luận bài viết
                    </a>
                @endif

                <a href="{{ route('admin.slides.index') }}"
                    class="list-group-item list-group-item-action d-flex align-items-center p-3 {{ request()->routeIs('admin.slides.*') ? 'active' : '' }}">
                    <i class="bi bi-images me-2"></i> Slides
                </a>

                <hr class="my-2">

                <div class="nav-section-title">Người dùng & Phân quyền</div>
                <a href="{{ route('admin.users.index') }}"
                    class="list-group-item list-group-item-action d-flex align-items-center p-3 {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="bi bi-person-lines-fill me-2"></i> Người dùng
                </a>

                <a href="{{ route('admin.staffs.index') }}"
                    class="list-group-item list-group-item-action d-flex align-items-center p-3 {{ request()->routeIs('admin.staffs.*') ? 'active' : '' }}">
                    <i class="bi bi-people me-2"></i> Nhân viên
                </a>

                <a href="{{ route('admin.admins.index') }}"
                    class="list-group-item list-group-item-action d-flex align-items-center p-3 {{ request()->routeIs('admin.admins.*') ? 'active' : '' }}">
                    <i class="bi bi-person-badge me-2"></i> Admin
                </a>
                @if (Route::has('admin.permissions.index'))
                    <a href="{{ route('admin.permissions.index') }}"
                        class="list-group-item list-group-item-action d-flex align-items-center p-3 {{ request()->routeIs('admin.permissions.*') ? 'active' : '' }}">
                        <i class="bi bi-shield-lock me-2"></i> Quyền
                    </a>
                @endif
                <a href="{{ route('admin.route-permissions.index') }}"
                    class="list-group-item list-group-item-action d-flex align-items-center p-3 {{ request()->routeIs('admin.route-permissions.*') ? 'active' : '' }}">
                    <i class="bi bi-diagram-3 me-2"></i> Route → Quyền
                </a>

                <a href="{{ route('admin.settings.index') }}"
                    class="list-group-item list-group-item-action d-flex align-items-center p-3 {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                    <i class="bi bi-gear me-2"></i> Cài đặt
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
                                <img src="{{ optional(Auth::user()->profile)->avatar ? asset('storage/' . Auth::user()->profile->avatar) : 'https://via.placeholder.com/30' }}"
                                    class="rounded-circle me-2" alt="User Avatar" width="30" height="30">
                                {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="{{ route('profile.show') }}">Hồ sơ</a></li>
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

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const wrapper = document.getElementById('wrapper');
        const sidebarToggle = document.getElementById('sidebarToggle');
        sidebarToggle.addEventListener('click', function () {
            wrapper.classList.toggle('toggled');
        });
        // Đóng sidebar khi click link trên màn nhỏ
        document.querySelectorAll('#sidebar-wrapper a').forEach(a => {
            a.addEventListener('click', () => {
                if (window.innerWidth < 992) wrapper.classList.remove('toggled');
            });
        });
    </script>

    @stack('scripts')
</body>

</html>