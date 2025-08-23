@php
    // Logic này có thể giữ lại hoặc chuyển vào View Composer nếu muốn
    $productActive = request()->routeIs(['products.*', 'product.*', 'category.*']);

    // Biến $supportUnread sẽ tự động được cung cấp bởi View Composer
@endphp

<ul class="navbar-nav mx-auto mb-2 mb-lg-0 align-items-lg-center main-navbar-nav">
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }} {{ $textColor ?? '' }}"
            href="{{ route('home') }}">Trang chủ</a>
    </li>

    <li class="nav-item dropdown mega">
        <a class="nav-link" href="#categories" id="open-category-modal">
            Sản phẩm
        </a>
        @include('frontend.components.category-mega', ['categories' => $sharedCategoriesTree ?? collect()])
    </li>

    <li class="nav-item dropdown">
        <a class="nav-link {{ request()->routeIs('blog.*') ? 'active' : '' }}" href="#blog-categories"
            id="open-blog-modal">
            Tin tức
        </a>
        @include('frontend.components.blog-category-menu')
    </li>

    <li class="nav-item">
        <a class="nav-link position-relative {{ request()->routeIs('support.*') ? 'active' : '' }} {{ $textColor ?? '' }}"
            href="{{ route('support.index') }}">
            Hỗ trợ
            @if(auth()->check() && isset($supportOpenTickets) && $supportOpenTickets > 0)
                <span class="notification-pill">{{ $supportOpenTickets }}</span>
            @endif
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="{{ route('about') }}">Giới thiệu</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('contact') }}">Liên hệ</a>
    </li>
</ul>

@once
    @push('styles')
        <style>
            .main-navbar-nav .nav-link {
                font-weight: 600;
                color: var(--text, #2B2623);
                padding: 0.5rem 1rem;
                position: relative;
                transition: color .2s ease;
            }

            /* Style cho link trên header trong suốt (trang chủ) */
            .header-home .main-navbar-nav .nav-link {
                color: #fff;
                text-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
            }

            .header-home.is-scrolled .main-navbar-nav .nav-link {
                color: #fff;
            }

            .main-navbar-nav .nav-link:hover {
                color: var(--brand);
            }

            .header-home .main-navbar-nav .nav-link:hover {
                color: #fff;
                /* Giữ màu trắng khi hover trên nền tối */
            }

            .main-navbar-nav .nav-link::after {
                content: '';
                position: absolute;
                bottom: -5px;
                left: 50%;
                transform: translateX(-50%) scaleX(0);
                width: 60%;
                height: 2px;
                background-color: var(--brand);
                transition: transform .25s ease-out;
            }

            .header-home .main-navbar-nav .nav-link::after {
                background-color: #fff;
            }

            .main-navbar-nav .nav-link.active::after,
            .main-navbar-nav .nav-link:hover::after {
                transform: translateX(-50%) scaleX(1);
            }

            .main-navbar-nav .nav-link.active {
                color: var(--brand);
            }

            .header-home .main-navbar-nav .nav-link.active {
                color: #fff;
            }

            /* Notification Pill */
            .notification-pill {
                position: absolute;
                top: 0;
                right: 0;
                transform: translate(40%, -40%);
                background-color: var(--brand);
                color: white;
                font-size: 0.65rem;
                font-weight: 700;
                border-radius: 99px;
                min-width: 18px;
                height: 18px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: 0 5px;
                border: 2px solid var(--card, #fff);
                /* Tạo khoảng cách với chữ */
            }

            .header-home .notification-pill {
                border-color: transparent;
            }
        </style>
    @endpush
@endonce