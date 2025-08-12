<header class="navbar-transparent" style="position: absolute; top: 0; left: 0; width: 100%; z-index: 9999;">
    <nav class="navbar navbar-expand-lg navbar-dark py-3">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                @if(!empty($settings['logo_light']))
                    <img src="{{ asset('storage/' . $settings['logo_light']) }}" alt="{{ $settings['site_name'] ?? 'Logo' }}" style="height: 100px;" loading="lazy">
                @else
                    <span class="fs-4 fw-bold text-white">{{ $settings['site_name'] ?? 'EternaHome' }}</span>
                @endif
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#main-navbar" aria-controls="main-navbar" aria-expanded="false" aria-label="Mở menu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="main-navbar">
                @include('layouts.partials._nav_links', ['textColor' => 'text-white'])
                @include('layouts.partials._nav_actions', ['iconColor' => 'text-white'])
            </div>
        </div>
    </nav>
</header>
