{{-- resources/views/layouts/partials/header-internal.blade.php --}}
<header class="header-internal sticky-top shadow-sm" role="banner">
    {{-- Top bar (desktop only) --}}
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

    {{-- Main navbar --}}
    <nav class="navbar navbar-expand-lg navbar-light bg-white py-3" role="navigation" aria-label="Main navigation">
        <div class="container">
            {{-- Brand / Logo --}}
            <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}" aria-label="{{ $settings['site_name'] ?? 'Home' }}">
                @if(!empty($settings['logo_dark']))
                    <img src="{{ asset('storage/' . $settings['logo_dark']) }}"
                         alt="{{ $settings['site_name'] ?? 'Logo' }}"
                         height="50"
                         loading="lazy"
                         style="max-height:50px; width:auto;">
                @else
                    <span class="fs-4 fw-bold text-dark">{{ $settings['site_name'] ?? 'EternaHome' }}</span>
                @endif
            </a>

            {{-- Toggler (mobile) --}}
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#main-navbar"
                    aria-controls="main-navbar" aria-expanded="false" aria-label="Mở menu">
                <span class="navbar-toggler-icon"></span>
            </button>

            {{-- Nav links + actions (partials giữ nguyên) --}}
            <div class="collapse navbar-collapse" id="main-navbar">
                @include('layouts.partials._nav_links', ['textColor' => ''])
                @include('layouts.partials._nav_actions', ['iconColor' => ''])
            </div>
        </div>
    </nav>

    {{-- Scoped styles — không thay đổi chức năng, chỉ đồng bộ màu / hover / accessibility --}}
    <style>
      /* Sử dụng biến màu từ :root (ví dụ --brand) để đồng bộ giao diện */
      .header-internal .text-brand { color: var(--brand) !important; }
      .header-internal .text-brand:hover { color: var(--brand-600) !important; }

      /* Top-bar: giữ tông sáng nhưng dùng brand cho icon */
      .header-internal .top-bar { background: var(--card); }
      .header-internal .top-bar .text-secondary { color: var(--muted) !important; }

      /* Navbar link hover/active dùng brand để đồng bộ */
      .header-internal .navbar .nav-link {
        color: rgba(33,37,41,0.9);
      }
      .header-internal .navbar .nav-link:hover,
      .header-internal .navbar .nav-link:focus,
      .header-internal .navbar .nav-link.active {
        color: var(--brand) !important;
        text-decoration: none;
      }

      /* Brand logo spacing */
      .header-internal .navbar-brand { display: inline-flex; align-items: center; gap: .5rem; }

      /* Toggler border subtle */
      .header-internal .navbar-toggler { border-color: rgba(0,0,0,.08); }

      /* Ensure social icons contrast on top-bar */
      .header-internal .top-bar a { color: rgba(33,37,41,0.7); }
      .header-internal .top-bar a:hover { color: var(--brand) !important; }

      /* Responsive tweaks */
      @media (max-width: 576px) {
        .header-internal .navbar-brand img { height: 44px; max-height:44px; }
        .header-internal .top-bar { display: none !important; } /* kept desktop-only as before */
      }
    </style>
</header>
