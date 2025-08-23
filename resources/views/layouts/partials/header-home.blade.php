{{-- resources/views/layouts/partials/header-home.blade.php --}}
<header class="header-home navbar-transparent" style="position: absolute; top: 0; left: 0; width: 100%; z-index: 9999;">
    <nav class="navbar navbar-expand-lg navbar-dark py-3" aria-label="Main navigation">
        <div class="container">

            {{-- Brand / Logo --}}
            <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}" aria-label="{{ $settings['site_name'] ?? 'Home' }}">
                @if(!empty($settings['logo_light']))
                    <img src="{{ asset('storage/' . $settings['logo_light']) }}"
                         alt="{{ $settings['site_name'] ?? 'Logo' }}"
                         style="height: 100px; max-height:100px; width:auto;"
                         loading="lazy">
                @else
                    <span class="fs-4 fw-bold text-white">{{ $settings['site_name'] ?? 'EternaHome' }}</span>
                @endif
            </a>

            {{-- Toggler (mobile) --}}
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#main-navbar"
                    aria-controls="main-navbar" aria-expanded="false" aria-label="Mở menu">
                <span class="navbar-toggler-icon"></span>
            </button>

            {{-- Nav links + actions (kept via partials) --}}
            <div class="collapse navbar-collapse" id="main-navbar">
                {{-- _nav_links expects $textColor param in some places; default to text-white --}}
                @include('layouts.partials._nav_links', ['textColor' => $textColor ?? 'text-white'])

                {{-- _nav_actions expects $iconColor param; default to text-white --}}
                @include('layouts.partials._nav_actions', ['iconColor' => $iconColor ?? 'text-white'])
            </div>
        </div>
    </nav>

    {{-- Scoped styles to ensure header-home link/icon colors play nicely with :root variables.
         These do not change functionality — chỉ dùng biến màu để đồng bộ giao diện. --}}
    <style>
      /* Đảm bảo chữ trắng trên nền trong suốt, hover dùng sắc của brand */
      .header-home .navbar-brand .fs-4,
      .header-home .nav-link,
      .header-home .navbar-nav .nav-link {
        color: rgba(255,255,255,0.96) !important;
      }

      .header-home .nav-link:hover,
      .header-home .nav-link:focus,
      .header-home .nav-link.active {
        color: var(--brand) !important; /* dùng color chủ đạo (biến) khi hover/active */
        text-decoration: none;
      }

      /* Buttons / icons in header actions */
      .header-home .navbar-nav .nav-link .bi,
      .header-home .navbar-nav .nav-link i {
        color: rgba(255,255,255,0.95);
      }
      .header-home .navbar-nav .nav-link:hover .bi,
      .header-home .navbar-nav .nav-link:focus .bi {
        color: var(--brand);
      }

      /* Toggler icon: giữ behavior của navbar-dark, nhưng đảm bảo nhìn tốt trên ảnh nền */
      .header-home .navbar-toggler {
        border-color: rgba(255,255,255,0.12);
      }
      .header-home .navbar-toggler-icon {
        filter: none; /* giữ biểu tượng mặc định của bootstrap-dark */
      }

      /* Responsive: giảm kích thước logo trên mobile để tránh chiếm quá nhiều chỗ */
      @media (max-width: 576px) {
        .header-home .navbar-brand img { height: 62px; max-height: 62px; }
      }
    </style>
</header>
