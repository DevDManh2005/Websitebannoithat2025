<!-- Header cố định trên cùng -->
<header class="sticky-top">
    <!-- Tầng trên của Header -->
    <nav class="navbar navbar-expand-lg bg-body-tertiary border-bottom shadow-sm py-3">
        <div class="container">
            <!-- Logo hoặc tên trang -->
            <a class="navbar-brand" href="{{ route('home') }}">
                {{-- Logo cho nền sáng (logo tối) --}}
                @if(!empty($settings['logo_dark']))
                    <img src="{{ asset('storage/' . $settings['logo_dark']) }}" alt="{{ $settings['site_name'] ?? 'Logo' }}" height="50" class="logo-dark">
                @else
                    <span class="logo-dark">{{ $settings['site_name'] ?? 'EternaHome' }}</span>
                @endif

                {{-- Logo cho nền tối (logo sáng) --}}
                @if(!empty($settings['logo_light']))
                    <img src="{{ asset('storage/' . $settings['logo_light']) }}" alt="{{ $settings['site_name'] ?? 'Logo' }}" height="50" class="logo-light">
                @else
                     <span class="logo-light">{{ $settings['site_name'] ?? 'EternaHome' }}</span>
                @endif
            </a>
            <!-- Nút mở rộng menu trên mobile -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#main-navbar"
                aria-controls="main-navbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <!-- Nội dung menu khi mở rộng -->
            <div class="collapse navbar-collapse" id="main-navbar">
                <!-- Danh sách menu chính -->
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}"
                            href="{{ route('home') }}">Trang Chủ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('products.index') ? 'active' : '' }}" href="{{ route('products.index') }}">Sản Phẩm</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Bài Viết</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Giới Thiệu</a>
                    </li>
                </ul>
                <!-- Các nút hành động bên phải -->
                <div class="d-flex align-items-center">
                    <button type="button" class="btn nav-link" id="search-toggle-btn">
                        <i class="bi bi-search fs-5"></i>
                    </button>
                    <button class="btn nav-link ms-2" id="theme-toggle-btn" type="button">
                        <i class="bi bi-sun-fill theme-icon-light"></i>
                        <i class="bi bi-moon-fill theme-icon-dark"></i>
                    </button>
                    @guest
                        <a href="{{ route('login.form') }}" class="btn btn-outline-primary btn-sm ms-3">Đăng nhập</a>
                        <a href="{{ route('register.form') }}" class="btn btn-primary btn-sm ms-2">Đăng ký</a>
                    @else
                        <div class="dropdown ms-3">
                            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="bi bi-person fs-5"></i></a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                @if(in_array(Auth::user()->role->name, ['admin', 'staff']))
                                    <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">Trang quản trị</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                @endif
                                <li><a class="dropdown-item" href="{{ route('profile.show') }}">Tài khoản của tôi</a></li>
                                <li><a class="dropdown-item" href="{{ route('orders.index') }}">Đơn hàng</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">@csrf<button type="submit" class="dropdown-item">Đăng xuất</button></form>
                                </li>
                            </ul>
                        </div>
                    @endguest
                    <a href="{{ route('wishlist.index') }}" class="nav-link ms-3 position-relative">
                        <i class="bi bi-heart fs-5"></i>
                        @auth
                            <span id="wishlist-count" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6em; {{ $sharedWishlistItemCount > 0 ? '' : 'display: none;' }}">
                                {{ $sharedWishlistItemCount }}
                            </span>
                        @endauth
                    </a>
                    <a href="{{ route('cart.index') }}" class="nav-link ms-3 position-relative">
                        <i class="bi bi-cart fs-5"></i>
                        @auth
                            @if($sharedCartItemCount > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6em;">{{ $sharedCartItemCount }}</span>
                            @endif
                        @endauth
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Tầng dưới của Header (Menu Danh mục) -->
    <nav class="navbar navbar-expand-lg bg-body-tertiary border-bottom d-none d-lg-block">
        <div class="container">
            <ul class="navbar-nav w-100 justify-content-center">
                @foreach($sharedCategories as $category)
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="{{ route('category.show', $category->slug) }}" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ $category->name }}
                    </a>
                    @if($category->children->isNotEmpty())
                         @include('frontend.components.category-menu', ['categories' => $category->children])
                    @endif
                </li>
                @endforeach
            </ul>
        </div>
    </nav>
</header>

<!-- Khung tìm kiếm -->
<div id="search-overlay" class="search-overlay">
    <div class="container">
        <button type="button" class="btn-close-search" id="search-close-btn">&times;</button>
        <form action="{{ route('search') }}" method="GET" class="search-form-overlay">
            <input type="text" class="form-control-overlay" name="q" placeholder="Bàn ghế, Giường..." autocomplete="off">
            <div class="form-text-overlay mt-3">Tìm kiếm phổ biến: <a href="#">Bàn ghế</a>, <a href="#">Giường ngủ</a>, <a href="#">Kệ tivi</a></div>
        </form>
    </div>
</div>
