{{-- resources/views/layouts/partials/_nav_actions.blade.php (Phiên bản self-contained) --}}

<div class="d-flex align-items-center ms-lg-3 nav-actions">
    {{-- Search --}}
    <a href="#" class="nav-link search-toggle-btn" title="Tìm kiếm" aria-label="Mở tìm kiếm">
        <i class="bi bi-search fs-5"></i>
        <span class="action-label d-lg-none ">Tìm kiếm</span>
    </a>
    {{-- Auth --}}
    @guest
        {{-- Desktop buttons --}}
        <div class="d-none d-lg-flex align-items-center ms-3">
            <a href="{{ route('login.form') }}" class="btn btn-sm btn-outline-primary">Đăng nhập</a>
            <a href="{{ route('register.form') }}" class="btn btn-sm btn-primary ms-2">Đăng ký</a>
        </div>
        {{-- Mobile links --}}
        <div class="d-lg-none nav-actions-mobile-auth">
            <a href="{{ route('login.form') }}" class="nav-link">
                <i class="bi bi-box-arrow-in-right fs-5"></i>
                <span class="action-label ms-2">Đăng nhập</span>
            </a>
            <a href="{{ route('register.form') }}" class="nav-link">
                <i class="bi bi-person-plus fs-5"></i>
                <span class="action-label ms-2">Đăng ký</span>
            </a>
        </div>
    @else
        {{-- Logged-in user dropdown --}}
        <div class="dropdown ms-lg-3">
            <a href="#" class="nav-link dropdown-toggle ms-3" data-bs-toggle="dropdown" aria-expanded="false"
                aria-label="Tài khoản">
                <i class="bi bi-person fs-5 "></i>
                <span class="action-label d-lg-none ms-2">Tài khoản</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
                @php $roleName = Auth::user()->role->name ?? null; @endphp
                @if($roleName === 'admin')
                    <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">Trang quản trị</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                @elseif($roleName === 'staff')
                    <li><a class="dropdown-item" href="{{ route('staff.dashboard') }}">Trang nhân viên</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                @endif
                <li><a class="dropdown-item" href="{{ route('profile.show') }}">Tài khoản của tôi</a></li>
                <li><a class="dropdown-item" href="{{ route('orders.index') }}">Đơn hàng</a></li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li>
                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="dropdown-item">Đăng xuất</button>
                    </form>
                </li>
            </ul>
        </div>
    @endguest

    {{-- Wishlist --}}
    <a href="{{ route('wishlist.index') }}" class="nav-link ms-lg-3 position-relative ms-3" aria-label="Yêu thích">
        <i class="bi bi-heart fs-5"></i>
        {{-- <span class="action-label d-lg-none ms-2">Yêu thích</span> --}}
        @auth
            <span id="wishlist-count"
                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                style="font-size:.6em; {{ ($sharedWishlistItemCount ?? 0) > 0 ? '' : 'display:none;' }}">
                {{ $sharedWishlistItemCount ?? 0 }}
            </span>
        @endauth
    </a>

    {{-- Cart --}}
    <a href="{{ route('cart.index') }}" class="nav-link ms-lg-3 position-relative ms-3" aria-label="Giỏ hàng">
        <i class="bi bi-cart fs-5"></i>
        {{-- <span class="action-label d-lg-none ms-2">Giỏ hàng</span> --}}
        <span id="cart-count-badge"
            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
            style="font-size:.6em; {{ ($sharedCartItemCount ?? 0) > 0 ? '' : 'display:none;' }}">
            {{ $sharedCartItemCount ?? 0 }}
        </span>
    </a>
</div>

{{-- Directive @once đảm bảo CSS và JS chỉ được thêm vào trang một lần duy nhất --}}
@once
    @push('styles')
        <style>
            :root {
    --brand: #A20E38;
    --text: #333333;
}

/* Desktop Styles */
.nav-actions .nav-link {
    color: var(--text);
}

.nav-actions .nav-link:hover,
.nav-actions .nav-link.position-relative:hover,
.nav-actions .nav-link.position-relative:hover i {
    color: var(--brand);
}

/* Desktop buttons */
.btn-outline-primary:hover {
    background-color: var(--brand) !important;
    color: #fff !important;
    border-color: var(--brand) !important;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    background-color: #8E0D30 !important;
    border-color: #8E0D30 !important;
    color: #fff !important;
    transition: all 0.3s ease;
}

/* Mobile links */
.nav-actions-mobile-auth .nav-link:hover,
.nav-actions-mobile-auth .nav-link:hover .action-label,
.nav-actions-mobile-auth .nav-link:hover i {
    background-color: #f8f9fa;
    color: var(--brand);
    transition: all 0.3s ease;
}

.nav-actions-mobile-auth .nav-link:active,
.nav-actions-mobile-auth .nav-link:active .action-label,
.nav-actions-mobile-auth .nav-link:active i {
    background-color: #e9ecef;
    color: var(--brand);
    transition: all 0.2s ease;
}

/* Dropdown items */
.dropdown-item:hover,
.dropdown-item:active {
    background-color: #f8f9fa;
    color: var(--brand);
    transition: all 0.3s ease;
}

/* Header-specific styles */
.header-home .nav-actions .nav-link,
.header-home .nav-actions .nav-link:hover {
    color: var(--brand); /* Đảm bảo màu thương hiệu */
}

.header-home.is-scrolled .nav-actions .nav-link,
.header-home.is-scrolled .nav-actions .nav-link:hover {
    color: var(--brand);
}

/* Responsive Styles */
@media (max-width: 991.98px) {
    .navbar-collapse .nav-actions {
        flex-direction: column;
        align-items: flex-start !important;
        width: 100%;
        margin: 1rem 0 0;
        padding-top: 1rem;
        border-top: 1px solid rgba(0, 0, 0, 0.1);
    }

    .navbar-collapse .nav-actions .nav-link {
        padding: 0.8rem 0;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    .navbar-collapse .nav-actions .nav-link:last-child {
        border-bottom: none;
    }

    .navbar-collapse .nav-actions .dropdown,
    .navbar-collapse .nav-actions-mobile-auth {
        width: 100%;
    }

    .navbar-collapse .nav-actions .dropdown .nav-link {
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    .navbar-collapse .nav-actions .dropdown-toggle::after {
        margin-left: auto;
    }
}
        </style>
    @endpush
@endonce