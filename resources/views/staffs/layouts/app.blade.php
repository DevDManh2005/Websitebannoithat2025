<!doctype html>
<html lang="vi">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Staff • EternaHome</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">

  <style>
    :root {
      --side: 240px
    }

    body {
      background: #f7f8fb
    }

    .sidebar {
      width: var(--side);
      min-height: 100vh;
      background: #fff;
      border-right: 1px solid #eef0f4
    }

    .sidebar .brand {
      font-weight: 700
    }

    .sidebar .nav-link {
      border-radius: .6rem;
      color: #334155;
      font-weight: 500
    }

    .sidebar .nav-link.active,
    .sidebar .nav-link:hover {
      background: #eef2ff;
      color: #4338ca
    }

    .content {
      margin-left: var(--side);
      min-height: 100vh
    }

    .topbar {
      position: sticky;
      top: 0;
      z-index: 5;
      background: #fff;
      border-bottom: 1px solid #eef0f4
    }

    .card.clean {
      border: 0;
      border-radius: 1rem;
      box-shadow: 0 8px 26px rgba(15, 23, 42, .06)
    }

    .chip {
      display: inline-block;
      padding: .25rem .6rem;
      border-radius: 999px;
      background: #fff1f2;
      color: #be123c;
      border: 1px dashed #fecdd3;
      font-size: .85rem;
      margin: .25rem .35rem .35rem 0
    }
  </style>

  @yield('styles')
</head>

<body>
  @php
  // Helper an toàn: chỉ tạo link khi route staff.* tồn tại
  $r = fn($name, $params = []) => \Illuminate\Support\Facades\Route::has($name) ? route($name, $params) : '#';
  $is = fn($pattern) => request()->routeIs($pattern) ? 'active' : '';
  $mods = collect($viewableModules ?? []);
@endphp

  <aside class="sidebar position-fixed p-3">
    <div class="d-flex align-items-center justify-content-between mb-4">
      <a href="{{ $r('staff.dashboard') }}" class="text-decoration-none brand">EternaHome</a>
      <i class="ri-shield-user-line fs-4 text-primary"></i>
    </div>

    <nav class="nav flex-column gap-1">
      <a class="nav-link {{ $is('staff.dashboard') }}" href="{{ $r('staff.dashboard') }}">
        <i class="ri-dashboard-3-line me-2"></i> Bảng điều khiển
      </a>

      @if($mods->contains('orders'))
      <a class="nav-link {{ $is('staff.orders.*') }}" href="{{ $r('staff.orders.index') }}">
      <i class="ri-bill-line me-2"></i> Đơn hàng
      </a>
    @endif

      @if($mods->contains('products'))
      <a class="nav-link {{ $is('staff.products.*') }}" href="{{ $r('staff.products.index') }}">
      <i class="ri-price-tag-3-line me-2"></i> Sản phẩm
      </a>
    @endif

      @if($mods->contains('inventories'))
      <a class="nav-link {{ $is('staff.inventories.*') }}" href="{{ $r('staff.inventories.index') }}">
      <i class="ri-archive-2-line me-2"></i> Tồn kho
      </a>
    @endif

      @if($mods->contains('categories'))
      <a class="nav-link {{ $is('staff.categories.*') }}" href="{{ $r('staff.categories.index') }}">
      <i class="ri-folder-2-line me-2"></i> Danh mục
      </a>
    @endif

      @if($mods->contains('brands'))
      <a class="nav-link {{ $is('staff.brands.*') }}" href="{{ $r('staff.brands.index') }}">
      <i class="ri-vip-crown-line me-2"></i> Thương hiệu
      </a>
    @endif

      @if($mods->contains('suppliers'))
      <a class="nav-link {{ $is('staff.suppliers.*') }}" href="{{ $r('staff.suppliers.index') }}">
      <i class="ri-team-line me-2"></i> Nhà cung cấp
      </a>
    @endif

      @if($mods->contains('vouchers'))
      <a class="nav-link {{ $is('staff.vouchers.*') }}" href="{{ $r('staff.vouchers.index') }}">
      <i class="ri-ticket-2-line me-2"></i> Voucher
      </a>
    @endif

      @if($mods->contains('reviews'))
      <a class="nav-link {{ $is('staff.reviews.*') }}" href="{{ $r('staff.reviews.index') }}">
      <i class="ri-star-smile-line me-2"></i> Đánh giá
      </a>
    @endif

      @if($mods->contains('slides'))
      <a class="nav-link {{ $is('staff.slides.*') }}" href="{{ $r('staff.slides.index') }}">
      <i class="ri-slideshow-2-line me-2"></i> Slides
      </a>
    @endif

      @if($mods->contains('blog-categories') || $mods->contains('blogs'))
      <div class="mt-3 small text-uppercase text-secondary fw-bold ms-2">Blog</div>
      @if($mods->contains('blog-categories'))
      <a class="nav-link {{ $is('staff.blog-categories.*') }}" href="{{ $r('staff.blog-categories.index') }}">
      <i class="ri-price-tag-2-line me-2"></i> Chuyên mục
      </a>
    @endif
      @if($mods->contains('blogs'))
      <a class="nav-link {{ $is('staff.blogs.*') }}" href="{{ $r('staff.blogs.index') }}">
      <i class="ri-article-line me-2"></i> Bài viết
      </a>
    @endif
      @if($mods->contains('banners'))
      <a class="nav-link {{ $is('staff.banners.*') }}" href="{{ $r('staff.banners.index') }}">
      <i class="ri-image-2-line me-2"></i> Banners
      </a>
    @endif
      @if($mods->contains('pages'))
      <a class="nav-link {{ $is('staff.pages.*') }}" href="{{ $r('staff.pages.index') }}">
      <i class="ri-file-text-line me-2"></i> Trang nội dung
      </a>
    @endif

      <div class="mt-3 small text-uppercase text-secondary fw-bold ms-2">Hệ thống</div>
      @if($mods->contains('settings'))
      <a class="nav-link {{ $is('staff.settings.*') }}" href="{{ $r('staff.settings.index') }}">
      <i class="ri-settings-3-line me-2"></i> Cấu hình
      </a>
    @endif
      @if($mods->contains('users'))
      <a class="nav-link {{ $is('staff.users.*') }}" href="{{ $r('staff.users.index') }}">
      <i class="ri-user-3-line me-2"></i> Người dùng
      </a>
    @endif
      @if($mods->contains('staffs'))
      <a class="nav-link {{ $is('staff.staffs.*') }}" href="{{ $r('staff.staffs.index') }}">
      <i class="ri-shield-user-line me-2"></i> Nhân viên
      </a>
    @endif

    @endif
    </nav>
  </aside>

  <div class="content">
    <header class="topbar py-2 px-3 d-flex align-items-center justify-content-between">
      <div>@yield('breadcrumb')</div>
      <div class="d-flex align-items-center gap-3">
        <span class="text-secondary small d-none d-md-inline">Xin chào, {{ auth()->user()->name ?? 'Staff' }}</span>
        <a class="btn btn-sm btn-outline-secondary" href="{{ route('logout') }}"
          onclick="event.preventDefault();document.getElementById('logout-form').submit();">
          <i class="ri-logout-circle-r-line me-1"></i> Đăng xuất
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
      </div>
    </header>

    <main class="p-4">
      @yield('content')
    </main>

    <footer class="py-3 text-center text-secondary small">
      © {{ date('Y') }} EternaHome • Staff
    </footer>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  @yield('scripts')
</body>

</html>