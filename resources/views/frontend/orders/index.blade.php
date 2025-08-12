@extends('layouts.app')

@section('title', 'Lịch sử mua hàng')

@section('content')
    {{-- =================== HERO / BREADCRUMB =================== --}}
    <section class="orders-hero position-relative overflow-hidden mb-5">
        <img src="https://anphonghouse.com/wp-content/uploads/2018/06/hinh-nen-noi-that-dep-full-hd-so-43-0.jpg" alt="Orders Banner" class="hero-bg">
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
                <div class="profile-menu card border-0 shadow-sm rounded-4" data-aos="fade-right">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            @php
                                $user = Auth::user();
                                $avatar_path = optional($user->profile)->avatar;
                                $is_url = $avatar_path && Str::startsWith($avatar_path, 'http');
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
                        </div>
                    </div>
                </div>
            </div>

            {{-- =================== RIGHT CONTENT =================== --}}
            <div class="col-lg-9">
                <div class="card border-0 shadow-sm rounded-4" data-aos="fade-left" data-aos-delay="100">
                    <div class="card-header bg-white p-3 p-md-4 border-0">
                        {{-- Status filter pills --}}
                        <ul class="nav nav-pills nav-fill flex-nowrap overflow-auto gap-2">
                            @php
                                $statuses = [
                                    'all' => 'Tất cả',
                                    'pending' => 'Chờ xử lý',
                                    'processing' => 'Đang xử lý',
                                    'shipping' => 'Đang giao',
                                    'delivered' => 'Hoàn thành',
                                    'cancelled' => 'Đã hủy',
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
                        @if(session('success'))
                            <div class="alert alert-success mx-3 mt-3">{{ session('success') }}</div>
                        @endif
                        @if(session('error'))
                            <div class="alert alert-danger mx-3 mt-3">{{ session('error') }}</div>
                        @endif

                        @forelse($orders as $order)
                            <div class="order-item p-3 p-md-4 border-top">
                                {{-- Header row --}}
                                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="order-hash badge bg-light text-dark fw-semibold">#{{ $order->order_code }}</span>
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
                                    @endforeach
                                </div>

                                <hr class="my-3">

                                {{-- Footer row --}}
                                <div class="d-flex flex-wrap justify-content-end align-items-center gap-3">
                                    <span class="text-muted">Tổng tiền:</span>
                                    <strong class="fs-5 text-danger">{{ number_format($order->final_amount) }} ₫</strong>
                                    <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-primary rounded-pill">
                                        Xem chi tiết
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="text-center p-5">
                                <i class="bi bi-box2" style="font-size: 3rem; color: #ccc;"></i>
                                <p class="mt-3 text-muted">Bạn chưa có đơn hàng nào trong mục này.</p>
                            </div>
                        @endforelse
                    </div>

                    @if($orders->hasPages())
                        <div class="card-footer bg-white border-0 pt-3">
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
/* ===== HERO (sáng + overlay) ===== */
.orders-hero{ background:#fff; }
.orders-hero .hero-bg{
    position:absolute; inset:0; width:100%; height:100%; object-fit:cover; transform:scale(1.03);
    filter: brightness(0.7);
}
.orders-hero .hero-overlay{ position:absolute; inset:0; background:linear-gradient(180deg, rgba(0,0,0,.35), rgba(0,0,0,.35)); }
.wave-sep{
    position:absolute; left:0; right:0; bottom:-1px; height:28px;
    background: radial-gradient(36px 11px at 50% 0, #fff 98%, transparent 100%) repeat-x;
    background-size:36px 18px;
}
.hero-bc-link{ color:#f8f9fa; text-decoration:none; }
.hero-bc-link:hover{ text-decoration:underline; }

/* ===== Profile menu ===== */
.profile-avatar{
    width: 100px; height: 100px; object-fit: cover;
    border: 4px solid #fff; box-shadow: 0 0 15px rgba(0,0,0,0.12);
}
.profile-menu .list-group-item{
    border: none; padding: .9rem 1.1rem; font-weight: 500; color: #495057;
    border-radius: .6rem;
}
.profile-menu .list-group-item + .list-group-item{ margin-top:.25rem; }
.profile-menu .list-group-item.active{
    background-color:#A20E38; color:#fff;
}
.profile-menu .list-group-item:not(.active):hover{ background:#f8f9fa; }

/* ===== Pills ===== */
.nav-pills .nav-link{ color:#6c757d; font-weight:500; white-space:nowrap; }
.nav-pills .nav-link.active{
    background-color:#A20E38; color:#fff;
    box-shadow: 0 2px 6px rgba(162,14,56,.25);
}

/* ===== Order card ===== */
.rounded-4{ border-radius:1rem !important; }
.order-item{ background:#fff; transition: background .2s ease; }
.order-item:not(:first-child){ border-top:1px solid #f0f2f4 !important; }
.order-item:hover{ background:#fafbfc; }
.order-hash{ border-radius: .6rem; }

/* ===== Image hover ===== */
.img-hover-zoom img{ transition: transform .35s ease; display:block; }
.img-hover-zoom:hover img{ transform: scale(1.06); }

/* Helpers */
.text-truncate-2{
    display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;
}
</style>
@endpush
