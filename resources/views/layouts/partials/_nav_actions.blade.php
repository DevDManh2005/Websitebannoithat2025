<div class="d-flex align-items-center ms-lg-3 nav-actions">
    {{-- Search --}}
    <a href="#" class="nav-link search-toggle-btn" title="Tìm kiếm" aria-label="Mở tìm kiếm">
        <i class="bi bi-search"></i>
        <span class="action-label d-lg-none ms-2">Tìm kiếm</span>
    </a>
    {{-- Auth --}}
    @guest
        {{-- Desktop buttons --}}
        <div class="d-none d-lg-flex align-items-center ms-3 gap-2">
            <a href="{{ route('login.form') }}" class="btn btn-sm btn-outline-primary">Đăng nhập</a>
            <a href="{{ route('register.form') }}" class="btn btn-sm btn-primary">Đăng ký</a>
        </div>
        {{-- Mobile links --}}
        <div class="d-lg-none nav-actions-mobile-auth">
            <a href="{{ route('login.form') }}" class="nav-link">
                <i class="bi bi-box-arrow-in-right"></i>
                <span class="action-label ms-2">Đăng nhập</span>
            </a>
            <a href="{{ route('register.form') }}" class="nav-link">
                <i class="bi bi-person-plus"></i>
                <span class="action-label ms-2">Đăng ký</span>
            </a>
        </div>
    @else
        {{-- Logged-in user dropdown --}}
        <div class="dropdown ms-lg-3">
            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Tài khoản">
                <i class="bi bi-person"></i>
                <span class="action-label d-lg-none ms-2">Tài khoản</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
                @php $roleName = Auth::user()->role->name ?? null; @endphp
                @if($roleName === 'admin')
                    <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">Trang quản trị</a></li>
                    <li><hr class="dropdown-divider"></li>
                @elseif($roleName === 'staff')
                    <li><a class="dropdown-item" href="{{ route('staff.dashboard') }}">Trang nhân viên</a></li>
                    <li><hr class="dropdown-divider"></li>
                @endif
                <li><a class="dropdown-item" href="{{ route('profile.show') }}">Tài khoản của tôi</a></li>
                <li><a class="dropdown-item" href="{{ route('orders.index') }}">Đơn hàng</a></li>
                <li><hr class="dropdown-divider"></li>
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
    <a href="{{ route('wishlist.index') }}" class="nav-link ms-lg-3 position-relative" aria-label="Yêu thích">
        <i class="bi bi-heart"></i>
        @auth
            <span id="wishlist-count" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="{{ ($sharedWishlistItemCount ?? 0) > 0 ? '' : 'display:none;' }}">
                {{ $sharedWishlistItemCount ?? 0 }}
            </span>
        @endauth
    </a>

    {{-- Cart --}}
    <a href="{{ route('cart.index') }}" class="nav-link ms-lg-3 position-relative" aria-label="Giỏ hàng">
        <i class="bi bi-cart"></i>
        <span id="cart-count-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="{{ ($sharedCartItemCount ?? 0) > 0 ? '' : 'display:none;' }}">
            {{ $sharedCartItemCount ?? 0 }}
        </span>
    </a>
</div>

@once
    @push('styles')
    <style>
        /* --- CSS CHO NAV ACTIONS COMPONENT --- */
        :root {
            --text: #333;
            --brand: #A20E38;
            --primary: #A20E38;
            --primary-dark: #A20E38;
        }

        /* Desktop Styles */
        .nav-actions { gap: 0.75rem; }
        .nav-actions .nav-link {
            color: var(--text);
            font-size: clamp(0.9rem, 2vw, 1rem);
            display: flex;
            align-items: center;
            transition: color 0.2s ease;
        }
        .header-home .nav-actions .nav-link {
            color: #fff;
        }
        .header-home.is-scrolled .nav-actions .nav-link {
            color: var(--text);
        }
        .nav-actions .nav-link:hover, .nav-actions .nav-link:focus {
            color: var(--brand);
        }
        .header-home .nav-actions .nav-link:hover, .header-home .nav-actions .nav-link:focus {
            color: #fff;
        }
        .header-home.is-scrolled .nav-actions .nav-link:hover, .header-home.is-scrolled .nav-actions .nav-link:focus {
            color: var(--brand);
        }
        .nav-actions .bi {
            font-size: clamp(1.2rem, 2.5vw, 1.4rem);
        }
        .nav-actions .badge {
            font-size: clamp(0.5rem, 1.5vw, 0.6rem);
            padding: 0.25em 0.5em;
            line-height: 1;
        }
        .nav-actions .btn-sm {
            font-size: clamp(0.8rem, 2vw, 0.9rem);
            padding: 0.3rem 0.75rem;
        }
        .nav-actions-mobile-auth .nav-link {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .dropdown-menu {
            font-size: clamp(0.85rem, 2vw, 0.95rem);
            min-width: 180px;
        }
        .dropdown-item:hover, .dropdown-item:focus {
            background-color: #f8e8ec;
            color: var(--brand);
        }

        /* Responsive Styles */
        @media (max-width: 991.98px) {
            .navbar-collapse .nav-actions {
                flex-direction: column;
                align-items: flex-start !important;
                width: 100%;
                margin-left: 0 !important;
                margin-top: 0.75rem;
                padding-top: 0.75rem;
                border-top: 1px solid rgba(0,0,0,0.1);
            }
            .navbar-collapse .nav-actions .nav-link {
                font-size: clamp(0.85rem, 2vw, 0.9rem);
                padding: 0.6rem 0;
                width: 100%;
                border-bottom: 1px solid rgba(0,0,0,0.05);
            }
            .navbar-collapse .nav-actions .nav-link:last-child {
                border-bottom: none;
            }
            .navbar-collapse .nav-actions .bi {
                font-size: clamp(1.1rem, 2vw, 1.2rem);
            }
            .navbar-collapse .nav-actions .badge {
                font-size: clamp(0.45rem, 1.5vw, 0.55rem);
            }
            .navbar-collapse .nav-actions-mobile-auth {
                width: 100%;
            }
            .navbar-collapse .nav-actions-mobile-auth .nav-link {
                border-bottom: 1px solid rgba(0,0,0,0.05);
            }
            .navbar-collapse .nav-actions .dropdown {
                width: 100%;
            }
            .navbar-collapse .nav-actions .dropdown .nav-link {
                border-bottom: 1px solid rgba(0,0,0,0.05);
            }
            .navbar-collapse .nav-actions .dropdown-toggle::after {
                margin-left: auto;
            }
            .dropdown-menu {
                font-size: clamp(0.8rem, 2vw, 0.9rem);
                min-width: 100%;
            }
        }

        @media (max-width: 767.98px) {
            .nav-actions { gap: 0.5rem; }
            .nav-actions .nav-link {
                font-size: clamp(0.8rem, 2vw, 0.85rem);
            }
            .nav-actions .bi {
                font-size: clamp(1rem, 2vw, 1.1rem);
            }
            .nav-actions .badge {
                font-size: clamp(0.4rem, 1.5vw, 0.5rem);
                padding: 0.2em 0.4em;
            }
            .navbar-collapse .nav-actions {
                margin-top: 0.5rem;
                padding-top: 0.5rem;
            }
            .navbar-collapse .nav-actions .nav-link {
                padding: 0.5rem 0;
            }
            .dropdown-menu {
                font-size: clamp(0.75rem, 2vw, 0.85rem);
            }
        }

        @media (max-width: 575.98px) {
            .nav-actions { gap: 0.4rem; }
            .nav-actions .nav-link {
                font-size: clamp(0.75rem, 2vw, 0.8rem);
            }
            .nav-actions .bi {
                font-size: clamp(0.9rem, 2vw, 1rem);
            }
            .nav-actions .badge {
                font-size: clamp(0.35rem, 1.5vw, 0.45rem);
                padding: 0.15em 0.35em;
            }
            .navbar-collapse .nav-actions {
                margin-top: 0.4rem;
                padding-top: 0.4rem;
            }
            .navbar-collapse .nav-actions .nav-link {
                padding: 0.4rem 0;
            }
            .dropdown-menu {
                font-size: clamp(0.7rem, 2vw, 0.8rem);
            }
        }
    </style>
    @endpush
@endonce