@extends('layouts.app')

@section('title', 'Lịch sử mua hàng')

@section('content')
    {{-- =================== HERO / BREADCRUMB =================== --}}
    <section class="support-hero position-relative overflow-hidden mb-5">
        <img src="https://images.unsplash.com/photo-1524758631624-e2822e304c36?q=80&w=1600&auto=format&fit=crop" alt="Orders Banner" class="hero-bg">
        <div class="hero-overlay"></div>
        <div class="container position-relative" data-aos="fade-down">
            <div class="row align-items-center" style="min-height: 220px;">
                <div class="col-12 text-center">
                    <h1 class="fw-bold text-white mb-2">Đơn Hàng Của Tôi</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center mb-0">
                            <li class="breadcrumb-item"><a class="hero-bc-link" href="{{ route('home') }}">Trang chủ</a></li>
                            <li class="breadcrumb-item active text-white-50" aria-current="page">Lịch sử mua hàng</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <div class="hero-bottom wave-sep"></div>
    </section>

    <div class="container my-5">
        <div class="row g-4">
            {{-- =================== LEFT MENU =================== --}}
            <div class="col-lg-3">
                <div class="profile-menu card-glass rounded-4" data-aos="fade-right">
                    <div class="p-4">
                        <div class="text-center mb-4">
                            @php
                                $user = Auth::user();
                                $avatar_path = optional($user->profile)->avatar;
                                $is_url = $avatar_path && \Illuminate\Support\Str::startsWith($avatar_path, 'http');
                                $avatar_url = $is_url ? $avatar_path : ($avatar_path ? asset('storage/' . $avatar_path) : 'https://via.placeholder.com/150');
                            @endphp
                            <img src="{{ $avatar_url }}" alt="Avatar" class="profile-avatar rounded-circle mb-3">
                            <h5 class="mb-0">{{ $user->name }}</h5>
                            <small class="text-muted">{{ $user->email }}</small>
                        </div>

                        <div class="list-group list-group-flush">
                            <a href="{{ route('profile.show') }}" class="list-group-item list-group-item-action">
                                <i class="bi bi-person-circle me-2"></i>Thông tin hồ sơ
                            </a>
                            <a href="{{ route('orders.index') }}" class="list-group-item list-group-item-action active">
                                <i class="bi bi-box-seam me-2"></i>Đơn hàng của tôi
                            </a>
                            <a href="{{ route('wishlist.index') }}" class="list-group-item list-group-item-action">
                                <i class="bi bi-heart me-2"></i>Danh sách yêu thích
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- =================== RIGHT CONTENT =================== --}}
            <div class="col-lg-9">
                <div class="card card-glass rounded-4" data-aos="fade-left" data-aos-delay="100">
                    <div class="card-header bg-transparent p-3 p-md-4 border-0">
                        {{-- Status filter pills --}}
                        <ul class="nav nav-pills nav-fill flex-nowrap overflow-auto gap-2">
                            @php
                                $statuses = [
                                    'all'        => 'Tất cả',
                                    'pending'    => 'Chờ xử lý',
                                    'processing' => 'Đang xử lý',
                                    'shipping'   => 'Đang giao',
                                    'delivered'  => 'Đã giao',
                                    'received'   => 'Đã nhận',
                                    'cancelled'  => 'Đã hủy',
                                ];
                            @endphp
                            @foreach($statuses as $statusKey => $statusValue)
                                <li class="nav-item">
                                    <a class="nav-link rounded-pill px-3 {{ $currentStatus == $statusKey ? 'active' : '' }}"
                                       href="{{ route('orders.index', ['status' => $statusKey]) }}">{{ $statusValue }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="card-body p-0">
                        @if(session('success')) <div class="alert alert-success mx-3 mt-3" data-aos="fade-up">{{ session('success') }}</div> @endif
                        @if(session('error'))   <div class="alert alert-danger mx-3 mt-3" data-aos="fade-up">{{ session('error') }}</div> @endif

                        @forelse($orders as $order)
                            <div class="order-item p-3 p-md-4 border-top">
                                {{-- Header row --}}
                                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="order-hash badge badge-soft-brand fw-semibold">#{{ $order->order_code }}</span>
                                        <small class="text-muted">
                                            <i class="bi bi-calendar3 me-1"></i>{{ $order->created_at->format('d/m/Y H:i') }}
                                        </small>
                                    </div>
                                    <span class="badge {{ \App\Models\Order::getStatusClass($order->status) }}">
                                        {{ \App\Models\Order::getStatusText($order->status) }}
                                    </span>
                                </div>

                                {{-- Items --}}
                                <div class="order-items">
                                    @foreach($order->items as $item)
                                        @if($item->variant && $item->variant->product)
                                            <div class="d-flex align-items-center py-2">
                                                <a href="{{ route('product.show', $item->variant->product->slug) }}"
                                                   class="rounded-3 overflow-hidden flex-shrink-0 img-hover-zoom"
                                                   style="width:60px;height:60px;">
                                                    <img src="{{ optional($item->variant->product->primaryImage)->image_url_path ?? 'https://placehold.co/80x80' }}"
                                                         class="w-100 h-100 object-fit-cover" alt="{{ $item->variant->product->name }}">
                                                </a>
                                                <div class="ms-3 flex-grow-1">
                                                    <p class="mb-0 small fw-semibold text-truncate-2">
                                                        {{ $item->variant->product->name }}
                                                    </p>
                                                    <small class="text-muted">SL: {{ $item->quantity }}</small>
                                                </div>
                                                <div class="ms-auto text-end">
                                                    <span class="text-muted small">{{ number_format($item->price) }} ₫</span>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>

                                <hr class="my-3">

                                {{-- Footer row --}}
                                <div class="d-flex flex-wrap justify-content-end align-items-center gap-3">
                                    <span class="text-muted">Tổng tiền:</span>
                                    <strong class="fs-5 text-brand">{{ number_format($order->final_amount) }} ₫</strong>

                                    @if($order->status === 'delivered')
                                        <form action="{{ route('orders.receive', $order) }}" method="POST" onsubmit="return confirm('Xác nhận bạn đã nhận đủ hàng?');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success rounded-pill">
                                                Xác nhận đã nhận hàng
                                            </button>
                                        </form>
                                    @endif

                                    @php $firstItemProduct = $order->items->first()?->variant?->product; @endphp
                                    @if($firstItemProduct && in_array($order->status, ['delivered', 'received']))
                                        <a href="{{ route('product.show', $firstItemProduct->slug) }}#reviews-content" class="btn btn-sm btn-brand rounded-pill">
                                            <i class="bi bi-pencil-square me-1"></i>Viết đánh giá
                                        </a>
                                    @endif

                                    <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-brand rounded-pill">
                                        Xem chi tiết
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="text-center p-5" data-aos="fade-up">
                                <i class="bi bi-box2" style="font-size: 3rem;"></i>
                                <p class="mt-3 text-muted">Bạn chưa có đơn hàng nào trong mục này.</p>
                                <a href="{{ route('products.index') }}" class="btn btn-brand rounded-pill mt-3">Tiếp tục mua sắm</a>
                            </div>
                        @endforelse
                    </div>

                    @if($orders->hasPages())
                        <div class="card-footer bg-transparent border-0 pt-3">
                            {{ $orders->withQueryString()->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    /* =================== Hero Section =================== */
    .support-hero {
        background: var(--bg);
    }
    .support-hero .hero-bg {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        transform: scale(1.03);
        filter: brightness(0.7);
    }
    .support-hero .hero-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(0, 0, 0, 0.35), rgba(0, 0, 0, 0.35));
    }
    .wave-sep {
        position: absolute;
        left: 0;
        right: 0;
        bottom: -1px;
        height: 28px;
        background: radial-gradient(36px 11px at 50% 0, var(--bg) 98%, transparent 100%) repeat-x;
        background-size: 36px 18px;
    }
    .hero-bc-link {
        color: var(--sand);
        text-decoration: none;
        transition: color 0.2s ease;
    }
    .hero-bc-link:hover {
        color: var(--brand);
    }
    .breadcrumb-item.active {
        color: var(--muted);
    }

    /* =================== Profile Menu =================== */
    .profile-avatar {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border: 4px solid var(--card);
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.12);
        transition: transform 0.25s ease;
    }
    .profile-avatar:hover {
        transform: scale(1.05);
    }
    .profile-menu .list-group-item {
        border: none;
        padding: 0.9rem 1.1rem;
        font-weight: 500;
        color: var(--text);
        background: transparent;
        border-radius: 0.6rem;
        transition: background 0.2s ease, color 0.2s ease;
    }
    .profile-menu .list-group-item + .list-group-item {
        margin-top: 0.25rem;
    }
    .profile-menu .list-group-item.active {
        background-color: var(--brand);
        color: #fff;
    }
    .profile-menu .list-group-item:not(.active):hover {
        background: var(--sand);
        color: var(--brand);
    }

    /* =================== Card and Order Items =================== */
    .card-glass {
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.92), rgba(255, 255, 255, 0.98));
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        border: 1px solid rgba(15, 23, 42, 0.04);
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }
    .card-glass:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
    }
    .order-item {
        background: var(--card);
        transition: background 0.2s ease, transform 0.25s ease;
    }
    .order-item:hover {
        background: var(--bg);
        transform: translateY(-2px);
    }
    .order-item:not(:first-child) {
        border-top: 1px solid var(--sand) !important;
    }
    .order-hash {
        border-radius: 0.6rem;
        background: rgba(var(--brand-rgb), 0.1);
        color: var(--brand);
    }

    /* =================== Nav Pills =================== */
    .nav-pills .nav-link {
        color: var(--muted);
        font-weight: 500;
        white-space: nowrap;
        transition: background 0.2s ease, color 0.2s ease, box-shadow 0.2s ease;
    }
    .nav-pills .nav-link.active {
        background-color: var(--brand);
        color: #fff;
        box-shadow: 0 2px 6px rgba(var(--brand-rgb), 0.25);
    }
    .nav-pills .nav-link:not(.active):hover {
        background: var(--sand);
        color: var(--brand);
    }

    /* =================== Buttons and Badges =================== */
    .btn-brand {
        background-color: var(--brand);
        border-color: var(--brand);
        color: #fff;
        padding: 0.5rem 1rem;
        transition: transform 0.15s ease, box-shadow 0.15s ease, background 0.15s ease;
    }
    .btn-brand:hover {
        background-color: var(--brand-600);
        border-color: var(--brand-600);
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
    }
    .btn-outline-brand {
        color: var(--brand);
        border-color: var(--brand);
        padding: 0.45rem 0.9rem;
        transition: background 0.15s ease, color 0.15s ease;
    }
    .btn-outline-brand:hover {
        background-color: var(--brand);
        border-color: var(--brand);
        color: #fff;
    }
    .btn-success {
        background-color: #28a745;
        border-color: #28a745;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .btn-success:hover {
        background-color: #218838;
        border-color: #1e7e34;
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
    }
    .badge-soft-brand {
        background: rgba(var(--brand-rgb), 0.1);
        color: var(--brand);
        font-size: 0.85rem;
    }
    .text-brand {
        color: var(--brand) !important;
    }
    .text-muted {
        color: var(--muted);
    }

    /* =================== Links and Images =================== */
    a {
        color: var(--brand);
        text-decoration: none;
    }
    a:hover {
        color: var(--brand-600);
    }
    .img-hover-zoom img {
        transition: transform 0.35s ease;
        display: block;
    }
    .img-hover-zoom:hover img {
        transform: scale(1.06);
    }
    .text-truncate-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .rounded-4 {
        border-radius: 1rem !important;
    }

    /* =================== Responsive Design =================== */
    @media (max-width: 991px) {
        .support-hero {
            min-height: 180px;
        }
        .support-hero h1 {
            font-size: 2rem;
        }
        .col-lg-3, .col-lg-9 {
            flex: 0 0 100%;
            max-width: 100%;
        }
        .profile-avatar {
            width: 80px;
            height: 80px;
        }
        .card {
            padding: 1.5rem;
        }
        .order-item .order-items .d-flex.align-items-center {
            gap: 1rem;
        }
    }

    @media (max-width: 767px) {
        .support-hero {
            min-height: 160px;
        }
        .support-hero h1 {
            font-size: 1.8rem;
        }
        .container {
            padding-left: 15px;
            padding-right: 15px;
        }
        .card {
            padding: 1rem;
        }
        .card-body {
            padding: 0 !important;
        }
        .order-item {
            padding: 1rem;
        }
        .btn-sm {
            padding: 0.35rem 0.7rem;
            font-size: 0.85rem;
        }
        .profile-avatar {
            width: 70px;
            height: 70px;
        }
        .order-item .order-items .d-flex.align-items-center {
            gap: 0.75rem;
        }
        .bi-box2 {
            font-size: 2.5rem;
        }
    }

    @media (max-width: 575px) {
        .support-hero {
            min-height: 140px;
        }
        .support-hero h1 {
            font-size: 1.6rem;
        }
        .breadcrumb {
            font-size: 0.85rem;
        }
        .card {
            padding: 0.75rem;
        }
        .order-item {
            padding: 0.75rem;
        }
        .btn-sm {
            padding: 0.3rem 0.6rem;
            font-size: 0.8rem;
        }
        .profile-avatar {
            width: 60px;
            height: 60px;
        }
        .order-item .order-items .d-flex.align-items-center {
            flex-direction: column;
            align-items: flex-start !important;
            gap: 0.75rem;
        }
        .order-item .order-items .ms-3 {
            margin-left: 0 !important;
        }
        .order-item .order-items .ms-auto {
            margin-left: auto !important;
        }
        .nav-pills .nav-link {
            font-size: 0.9rem;
            padding: 0.3rem 0.7rem;
        }
        .order-hash, .badge {
            font-size: 0.8rem;
        }
        .bi-box2 {
            font-size: 2rem;
        }
    }
</style>
@endpush