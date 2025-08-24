{{-- resources/views/layouts/partials/header.blade.php --}}

@php
    $isHomePage = request()->routeIs('home');
@endphp

<header class="@if($isHomePage) header-home navbar-transparent @else header-internal sticky-top shadow-sm @endif"
        @if($isHomePage) style="position: absolute; top: 0; left: 0; width: 100%; z-index: 1030;" @endif
        role="banner">

    {{-- Top bar: Chỉ hiển thị ở các trang trong --}}
    @if(!$isHomePage)
        <div class="top-bar py-2 d-none d-lg-block bg-light border-bottom">
            <div class="container d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center text-secondary small">
                    <div class="me-4 d-flex align-items-center">
                        <i class="bi bi-geo-alt-fill me-2 text-brand" aria-hidden="true"></i>
                        <span>{{ $settings['contact_address'] ?? 'Địa chỉ liên hệ' }}</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="bi bi-telephone-fill me-2 text-brand" aria-hidden="true"></i>
                        <span>{{ $settings['contact_phone'] ?? '1900 1234' }}</span>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    <span class="small me-3 text-secondary">Theo dõi:</span>
                    <a href="{{ $settings['social_facebook'] ?? '#' }}" class="text-secondary me-2" aria-label="Facebook" rel="noopener noreferrer" target="_blank">
                        <i class="bi bi-facebook fs-5" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
        </div>
    @endif

    {{-- Main navbar --}}
    <nav class="navbar navbar-expand-lg py-3 @if($isHomePage) navbar-dark @else navbar-light bg-white @endif" aria-label="Main navigation">
        <div class="container">
            {{-- Brand / Logo --}}
            <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}" aria-label="{{ $settings['site_name'] ?? 'Home' }}">
                @if($isHomePage)
                    {{-- Logic hiển thị logo cho trang chủ (có đổi logo khi cuộn) --}}
                    @if(!empty($settings['logo_light']))
                        <img src="{{ asset('storage/' . $settings['logo_light']) }}" alt="Logo" class="logo-light" style="height: 100px; max-height:100px; width:auto;">
                    @endif
                    @if(!empty($settings['logo_dark']))
                        <img src="{{ asset('storage/' . $settings['logo_dark']) }}" alt="Logo" class="logo-dark" style="height: 100px; max-height:100px; width:auto; display: none;">
                    @else
                        <span class="fs-4 fw-bold text-white logo-text">{{ $settings['site_name'] ?? 'EternaHome' }}</span>
                    @endif
                @else
                    {{-- Logic hiển thị logo cho các trang trong --}}
                    @if(!empty($settings['logo_dark']))
                        <img src="{{ asset('storage/' . $settings['logo_dark']) }}" alt="Logo" style="height: 50px; max-height:50px; width:auto;">
                    @else
                        <span class="fs-4 fw-bold text-dark">{{ $settings['site_name'] ?? 'EternaHome' }}</span>
                    @endif
                @endif
            </a>

            {{-- Toggler (mobile) --}}
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#main-navbar" aria-controls="main-navbar" aria-expanded="false" aria-label="Mở menu">
                <span class="navbar-toggler-icon"></span>
            </button>

            {{-- Nav links + actions --}}
            <div class="collapse navbar-collapse" id="main-navbar">
                @include('layouts.partials._nav_links', ['textColor' => $isHomePage ? 'text-white' : ''])
                @include('layouts.partials._nav_actions', ['iconColor' => $isHomePage ? 'text-white' : ''])
            </div>
        </div>
    </nav>
</header>

{{-- Gộp CSS của cả 2 header vào đây --}}
<style>
    :root {
        --brand: #A20E38;
        --text: #2B2623;
        --card: #FFFFFF;
        --muted: #7D726C;
        --radius: 12px;
    }

    /* === CSS CHUNG === */
    /* Nav links mặc định */
    .nav-actions .nav-link {
        color: var(--text);
    }

    /* Nav links hover */
    .nav-actions .nav-link:hover,
    .nav-actions .nav-link.position-relative:hover,
    .nav-actions .nav-link.position-relative:hover i {
        color: var(--brand) !important;
    }

    /* Desktop buttons mặc định */
    .btn-outline-primary {
        background-color: transparent;
        border-color: var(--brand);
        color: var(--brand);
    }

    .btn-primary {
        background-color: var(--brand);
        border-color: var(--brand);
        color: #fff;
    }

    /* Desktop buttons hover */
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

    /* Mobile links mặc định */
    .nav-actions-mobile-auth .nav-link {
        color: var(--text);
    }

    .nav-actions-mobile-auth .nav-link i,
    .nav-actions-mobile-auth .nav-link .action-label {
        color: var(--text);
    }

    /* Mobile links hover */
    .nav-actions-mobile-auth .nav-link:hover,
    .nav-actions-mobile-auth .nav-link:hover .action-label,
    .nav-actions-mobile-auth .nav-link:hover i {
        background-color: #f8f9fa;
        color: var(--brand) !important;
        transition: all 0.3s ease;
    }

    /* Mobile links active (cho tương tác chạm) */
    .nav-actions-mobile-auth .nav-link:active,
    .nav-actions-mobile-auth .nav-link:active .action-label,
    .nav-actions-mobile-auth .nav-link:active i {
        background-color: #e9ecef;
        color: var(--brand) !important;
        transition: all 0.2s ease;
    }

    /* Dropdown items mặc định */
    .dropdown-item {
        color: var(--text);
    }

    /* Dropdown items hover và active */
    .dropdown-item:hover,
    .dropdown-item:active {
        background-color: #f8f9fa;
        color: var(--brand) !important;
        transition: all 0.3s ease;
    }

    /* === CSS CHO HEADER-INTERNAL === */
    .header-internal .top-bar a {
        color: var(--muted);
    }

    .header-internal .top-bar a:hover {
        color: var(--brand) !important;
    }

    .header-internal .navbar .nav-link.active {
        color: var(--brand) !important;
    }

    /* === CSS CHO HEADER-HOME === */
    .header-home {
        transition: background-color 0.4s ease-in-out, box-shadow 0.4s ease-in-out;
    }

    .header-home .logo-dark {
        display: none;
    }

    .header-home .logo-light {
        display: block;
    }

    .header-home .nav-link,
    .header-home .logo-text,
    .header-home .nav-link .bi,
    .header-home .nav-link i {
        color: rgba(255, 255, 255, 0.96) !important;
    }

    .header-home .nav-link:hover,
    .header-home .nav-link:hover .bi,
    .header-home .nav-link:hover i {
        color: var(--brand) !important;
    }

    .header-home .navbar-toggler {
        border-color: rgba(255, 255, 255, 0.2);
    }

    .header-home .navbar-toggler-icon {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 0.9%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
    }

    /* Header-home khi cuộn */
    .header-home.is-scrolled {
        position: fixed;
        background-color: var(--card);
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        animation: slideDown 0.5s ease-out;
    }

    @keyframes slideDown {
        from { transform: translateY(-100%); }
        to { transform: translateY(0); }
    }

    .header-home.is-scrolled .logo-light {
        display: none;
    }

    .header-home.is-scrolled .logo-dark {
        display: block;
    }

    .header-home.is-scrolled .nav-link,
    .header-home.is-scrolled .logo-text,
    .header-home.is-scrolled .nav-link .bi,
    .header-home.is-scrolled .nav-link i {
        color: var(--text) !important;
    }

    .header-home.is-scrolled .nav-link:hover,
    .header-home.is-scrolled .nav-link:hover .bi,
    .header-home.is-scrolled .nav-link:hover i {
        color: var(--brand) !important;
    }

    .header-home.is-scrolled .navbar-toggler {
        border-color: rgba(0, 0, 0, 0.1);
    }

    .header-home.is-scrolled .navbar-toggler-icon {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%2843, 38, 35, 0.8%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
    }

    /* === CSS RESPONSIVE === */
    @media (max-width: 991.98px) {
        .header-home .navbar-brand img,
        .header-home.is-scrolled .navbar-brand img {
            height: 70px !important;
            max-height: 70px !important;
        }

        .header-internal .navbar-brand img {
            height: 45px !important;
            max-height: 45px !important;
        }

        .header-home .navbar-collapse {
            background-color: var(--card);
            padding: 1rem 1.5rem;
            margin-top: 0.5rem;
            border-radius: var(--radius);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .header-home .navbar-collapse .nav-link,
        .header-home .navbar-collapse .nav-link .bi,
        .header-home .navbar-collapse .nav-link i {
            color: var(--text) !important;
        }

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

    @media (max-width: 576px) {
        .header-home .navbar-brand img,
        .header-home.is-scrolled .navbar-brand img {
            height: 60px !important;
            max-height: 60px !important;
        }

        .header-internal .navbar-brand img {
            height: 40px !important;
            max-height: 40px !important;
        }
    }
</style>