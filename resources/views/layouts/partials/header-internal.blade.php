<header class="sticky-top shadow-sm">
    <div class="top-bar py-2 d-none d-lg-block bg-light border-bottom">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center text-secondary">
                <div class="me-4 d-flex align-items-center">
                    <i class="bi bi-geo-alt-fill me-2 text-primary"></i>
                    <span class="small">{{ $settings['contact_address'] ?? 'Địa chỉ liên hệ' }}</span>
                </div>
                <div class="d-flex align-items-center">
                    <i class="bi bi-telephone-fill me-2 text-primary"></i>
                    <span class="small">{{ $settings['contact_phone'] ?? '1900 1234' }}</span>
                </div>
            </div>
            <div class="d-flex align-items-center">
                <span class="small me-3 text-secondary">Theo dõi:</span>
                <a href="{{ $settings['social_facebook'] ?? '#' }}" class="text-secondary me-2" aria-label="Facebook">
                    <i class="bi bi-facebook fs-5"></i>
                </a>
            </div>
        </div>
    </div>

    <nav class="navbar navbar-expand-lg navbar-light bg-white py-3">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                @if(!empty($settings['logo_dark']))
                    <img src="{{ asset('storage/' . $settings['logo_dark']) }}" alt="{{ $settings['site_name'] ?? 'Logo' }}" height="50" loading="lazy">
                @else
                    <span class="fs-4 fw-bold text-dark">{{ $settings['site_name'] ?? 'EternaHome' }}</span>
                @endif
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#main-navbar" aria-controls="main-navbar" aria-expanded="false" aria-label="Mở menu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="main-navbar">
                @include('layouts.partials._nav_links', ['textColor' => ''])
                @include('layouts.partials._nav_actions', ['iconColor' => ''])
            </div>
        </div>
    </nav>
</header>
