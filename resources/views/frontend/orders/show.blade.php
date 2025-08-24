@extends('layouts.app')

@section('title', 'Chi tiết đơn hàng #' . $order->order_code)

@section('content')
    <section class="support-hero position-relative overflow-hidden mb-5">
        <img src="https://images.unsplash.com/photo-1524758631624-e2822e304c36?q=80&w=1600&auto=format&fit=crop"
            alt="Order Banner" class="hero-bg">
        <div class="hero-overlay"></div>
        <div class="container position-relative" data-aos="fade-down">
            <div class="row align-items-center" style="min-height: 220px;">
                <div class="col-12 text-center">
                    <h1 class="fw-bold text-white mb-2">Chi Tiết Đơn Hàng</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center mb-0">
                            <li class="breadcrumb-item"><a class="hero-bc-link" href="{{ route('home') }}">Trang chủ</a>
                            </li>
                            <li class="breadcrumb-item"><a class="hero-bc-link" href="{{ route('orders.index') }}">Đơn hàng
                                    của tôi</a></li>
                            <li class="breadcrumb-item active text-white-50" aria-current="page">#{{ $order->order_code }}
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <div class="hero-bottom wave-sep"></div>
    </section>

    @php
        use Illuminate\Support\Facades\Storage;
        use Illuminate\Support\Str;

        $resolveImage = function ($product) {
            $img = optional($product->images)->firstWhere('is_primary', 1) ?? optional($product->images)->first();
            $raw = $img->image_url ?? null;
            if (!$raw)
                return 'https://via.placeholder.com/80';
            if (Str::startsWith($raw, ['http://', 'https://']))
                return $raw;
            $normalized = ltrim(str_replace(['storage/', 'public/'], '', $raw), '/');
            return $normalized ? Storage::url($normalized) : asset($raw);
        };

        $isPaid = (($order->payment_status ?? 'unpaid') === 'paid') || ($order->is_paid ?? false);
        $isOnline = (($order->payment_method ?? '') === 'vnpay');
    @endphp

    <div class="container my-5">
        @if(session('success'))
        <div class="alert alert-success shadow-sm" data-aos="fade-up">{{ session('success') }}</div> @endif
        @if(session('error'))
        <div class="alert alert-danger shadow-sm" data-aos="fade-up">{{ session('error') }}</div> @endif

        {{-- HEADER --}}
        <div class="card card-glass rounded-4 mb-4" data-aos="fade-right">
            <div class="card-body p-4 p-md-5">
                @php
                    $statusConfig = [
                        'pending' => ['class' => 'badge-soft-brand', 'text' => 'Đang chờ xử lý'],
                        'processing' => ['class' => 'bg-info text-dark', 'text' => 'Đang xử lý'],
                        'shipping' => ['class' => 'bg-primary', 'text' => 'Đang giao'],
                        'delivered' => ['class' => 'bg-success', 'text' => 'Đã giao (chờ xác nhận)'],
                        'received' => ['class' => 'bg-success', 'text' => 'Đã nhận thành công'],
                        'cancelled' => ['class' => 'bg-danger', 'text' => 'Đã hủy'],
                    ];
                    $status = $statusConfig[$order->status] ?? ['class' => 'bg-light text-dark', 'text' => 'Không xác định'];

                    $steps = [
                        'pending' => 'Đặt hàng',
                        'processing' => 'Xác nhận',
                        'shipping' => 'Đang giao',
                        'delivered' => 'Đã giao',
                        'received' => 'Hoàn tất',
                    ];
                    $keys = array_keys($steps);
                    $currentIdx = array_search($order->status, $keys);
                @endphp

                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge badge-soft-brand fw-semibold rounded-pill">Mã đơn:
                                #{{ $order->order_code }}</span>
                            <span class="badge {{ $status['class'] }}">{{ $status['text'] }}</span>
                            @if($isPaid && $isOnline)
                                <span class="badge bg-success">ĐÃ THANH TOÁN ONLINE</span>
                            @endif
                        </div>
                        <small class="text-muted d-block mt-2">
                            <i class="bi bi-calendar3 me-1"></i>{{ optional($order->created_at)->format('d/m/Y H:i') }}
                            @if(optional($order->shipment)->tracking_code)
                                <span class="ms-3"><i class="bi bi-truck me-1"></i>Mã vận đơn:
                                    <span class="text-brand fw-semibold">{{ $order->shipment->tracking_code }}</span>
                                    @if(optional($order->shipment)->carrier_name)
                                        <span class="text-muted">({{ $order->shipment->carrier_name }})</span>
                                    @endif
                                </span>
                            @endif
                        </small>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-outline-brand btn-sm rounded-pill" onclick="window.print()">
                            <i class="bi bi-printer me-1"></i> In
                        </button>
                        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill">Quay
                            lại</a>
                    </div>
                </div>

                {{-- timeline --}}
                <div class="order-steps mt-4">
                    @foreach($steps as $k => $label)
                        @php $i = array_search($k, $keys);
                        $done = $currentIdx !== false && $i !== false && $i <= $currentIdx; @endphp
                        <div class="step {{ $done ? 'done' : '' }}">
                            <span class="dot"></span>
                            <span class="label">{{ $label }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="row g-4 g-lg-5">
            {{-- LEFT: ITEMS --}}
            <div class="col-lg-8">
                <div class="card card-glass rounded-4 mb-4" data-aos="fade-right" data-aos-delay="50">
                    <div class="card-header bg-transparent border-0 px-4 pt-4">
                        <h5 class="mb-0 fw-bold text-brand">Sản phẩm trong đơn hàng</h5>
                    </div>
                    <div class="card-body p-4 pt-0">
                        @foreach($order->items as $item)
                            @php
                                $product = $item->variant->product;
                                $imageUrl = $resolveImage($product);
                            @endphp
                            <div class="d-flex align-items-center py-3 border-top">
                                <div class="rounded-3 overflow-hidden flex-shrink-0 img-hover-zoom"
                                    style="width:72px;height:72px;">
                                    <img src="{{ $imageUrl }}" alt="{{ $product->name }}" class="w-100 h-100 object-fit-cover">
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">
                                        <a href="{{ route('product.show', $product->slug) }}"
                                            class="text-decoration-none text-dark fw-semibold">
                                            {{ $product->name }}
                                        </a>
                                    </h6>
                                    <small class="text-muted">
                                        @forelse((array) $item->variant->attributes as $key => $value)
                                            {{ ucfirst($key) }}: {{ $value }}@if(!$loop->last), @endif
                                        @empty
                                            Sản phẩm gốc
                                        @endforelse
                                    </small>
                                    <div class="mt-1 xsmall text-muted">Số lượng: {{ $item->quantity }}</div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold text-brand">{{ number_format($item->subtotal) }} ₫</div>
                                    <small class="text-muted">{{ number_format($item->price) }} ₫ / sp</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Shipping info --}}
                <div class="card card-glass rounded-4" data-aos="fade-right" data-aos-delay="100">
                    <div class="card-header bg-transparent border-0 px-4 pt-4 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-brand">Thông tin giao hàng</h5>
                        @if(in_array($order->status, ['pending', 'processing']))
                            <button type="button" class="btn btn-sm btn-outline-brand rounded-pill" id="edit-address-btn">
                                <i class="bi bi-pencil-square me-1"></i>Chỉnh sửa
                            </button>
                        @endif
                    </div>
                    <div class="card-body p-4 pt-0">
                        <div id="address-display">
                            <div class="row g-4">
                                <div class="col-md-7">
                                    <div class="small text-muted">Người nhận</div>
                                    <div class="fw-semibold">{{ optional($order->shipment)->receiver_name }} —
                                        {{ optional($order->shipment)->phone }}</div>
                                    <div class="small text-muted mt-3">Địa chỉ</div>
                                    <div>
                                        {{ optional($order->shipment)->address }},
                                        {{ optional($order->shipment)->ward }},
                                        {{ optional($order->shipment)->district }},
                                        {{ optional($order->shipment)->city }}
                                    </div>
                                    @if(!empty($order->note))
                                        <div class="small text-muted mt-3">Ghi chú</div>
                                        <div>{{ $order->note }}</div>
                                    @endif
                                </div>
                                <div class="col-md-5">
                                    <div class="small text-muted">Vận chuyển</div>
                                    <div class="fw-semibold">
                                        {{ optional($order->shipment)->carrier_name ?? 'GIAOHANG ETERNA' }}</div>
                                    @if(optional($order->shipment)->expected_date)
                                        <div class="xsmall text-muted">Dự kiến:
                                            {{ \Carbon\Carbon::parse($order->shipment->expected_date)->format('d/m/Y') }}</div>
                                    @endif
                                    <hr>
                                    <div class="small text-muted">Thanh toán</div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted">Phương thức</span>
                                        <span
                                            class="fw-semibold">{{ $isOnline ? 'VNPAY' : 'Thanh toán khi nhận (COD)' }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mt-2">
                                        <span class="text-muted">Trạng thái</span>
                                        <span class="fw-semibold">{{ $isPaid ? 'Đã thanh toán' : 'Chưa thanh toán' }}</span>
                                    </div>
                                    @if($isOnline && !empty($order->payment_ref))
                                        <div class="xsmall text-muted mt-1">
                                            Mã giao dịch VNPAY: <span class="text-brand">{{ $order->payment_ref }}</span>
                                        </div>
                                    @endif
                                    @if(!$isPaid && $isOnline && in_array($order->status, ['pending', 'processing']))
                                        <a href="{{ route('payment.vnpay.create', $order) }}"
                                            class="btn btn-brand w-100 rounded-pill mt-3">
                                            Thanh toán online (VNPAY)
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div id="address-edit-form" style="display:none;">
                            <form action="{{ route('orders.update-address', $order) }}" method="POST"
                                id="update-address-form">
                                @csrf
                                @method('PATCH')
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="receiver_name" class="form-label">Họ và tên người nhận</label>
                                        <input type="text" class="form-control form-control-modern" name="receiver_name"
                                            value="{{ old('receiver_name', optional($order->shipment)->receiver_name) }}"
                                            required>
                                    </div>
                                    <div class="col-md-6">
    <label for="phone" class="form-label">Số điện thoại</label>
    <input type="text" 
           class="form-control form-control-modern @error('phone') is-invalid @enderror" 
           name="phone" 
           value="{{ old('phone', optional($order->shipment)->phone ?? optional($user->profile)->phone) }}" 
           required>

    @error('phone')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

                                    <div class="col-md-4">
                                        <label for="province_id" class="form-label">Tỉnh/Thành <span
                                                class="text-danger">*</span></label>
                                        <input type="hidden" name="city" id="province_name_input_edit"
                                            value="{{ old('city', optional($order->shipment)->city) }}">
                                        <select class="form-select form-control-modern" id="province_id_edit"
                                            name="province_id" required></select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="district_id" class="form-label">Quận/Huyện <span
                                                class="text-danger">*</span></label>
                                        <input type="hidden" name="district" id="district_name_input_edit"
                                            value="{{ old('district', optional($order->shipment)->district) }}">
                                        <select class="form-select form-control-modern" id="district_id_edit"
                                            name="district_id" required></select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="ward_code" class="form-label">Phường/Xã <span
                                                class="text-danger">*</span></label>
                                        <input type="hidden" name="ward" id="ward_name_input_edit"
                                            value="{{ old('ward', optional($order->shipment)->ward) }}">
                                        <select class="form-select form-control-modern" id="ward_code_edit" name="ward_code"
                                            required></select>
                                    </div>
                                    <div class="col-12">
                                        <label for="address" class="form-label">Địa chỉ cụ thể (số nhà, tên
                                            đường...)</label>
                                        <input type="text" class="form-control form-control-modern" id="address_edit"
                                            name="address" value="{{ old('address', optional($order->shipment)->address) }}"
                                            required placeholder="Ví dụ: 123 Nguyễn Văn Linh">
                                    </div>
                                </div>
                                <div class="d-flex gap-2 mt-4">
                                    <button type="submit" class="btn btn-brand rounded-pill">Lưu thay đổi</button>
                                    <button type="button" class="btn btn-outline-secondary rounded-pill"
                                        id="cancel-edit-btn">Hủy</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- RIGHT: SUMMARY + ACTIONS --}}
            <div class="col-lg-4">
                <div class="card card-glass rounded-4 sticky-lg-top" style="top: 96px;" data-aos="fade-left">
                    <div class="card-header bg-transparent border-0 px-4 pt-4">
                        <h5 class="mb-0 fw-bold text-brand">Tổng cộng</h5>
                    </div>
                    <div class="card-body p-4 pt-0">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tạm tính</span>
                            <span>{{ number_format($order->total_amount) }} ₫</span>
                        </div>
                        @if(($order->discount ?? 0) > 0)
                            <div class="d-flex justify-content-between text-success mb-2">
                                <span>Giảm giá (Voucher)</span>
                                <span>-{{ number_format($order->discount) }} ₫</span>
                            </div>
                        @endif
                        <div class="d-flex justify-content-between mb-2">
                            <span>Phí vận chuyển</span>
                            <span>{{ number_format(optional($order->shipment)->shipping_fee ?? 0) }} ₫</span>
                        </div>
                        <hr class="border-dashed">
                        <div class="d-flex justify-content-between align-items-center fw-bold fs-5">
                            <span>Thành tiền</span>
                            <span class="text-brand">{{ number_format($order->final_amount) }} ₫</span>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            @if(in_array($order->status, ['pending', 'processing']))
                                <div class="alert alert-info small p-2">Nếu đã thanh toán online, vui lòng liên hệ Admin để hỗ
                                    trợ hoàn tiền sau khi hủy.</div>
                                <form action="{{ route('orders.cancel', $order) }}" method="POST"
                                    onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này?');">
                                    @csrf
                                    <button type="submit" class="btn btn-danger w-100 rounded-pill">Hủy đơn hàng</button>
                                </form>
                            @elseif($order->status === 'delivered')
                               <form action="{{ route('orders.receive', $order->id) }}" method="POST" onsubmit="return confirm('Xác nhận bạn đã nhận đủ hàng?');">
                            @csrf
                            <button type="submit" class="btn btn-success w-100 rounded-pill">Đã nhận được hàng</button>
                            </form>
                            @elseif($order->status === 'received')
                                <div class="alert alert-success">Đã xác nhận nhận hàng. Cảm ơn bạn!</div>
                            @elseif($order->status === 'cancelled')
                                <div class="alert alert-secondary">Đơn hàng này đã được hủy.</div>
                            @else
                                <div class="alert alert-light">Đơn hàng đang được xử lý, hiện chưa có hành động khả dụng.</div>
                            @endif

                            <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary w-100 rounded-pill">Quay
                                lại danh sách</a>
                        </div>
                    </div>
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

    /* =================== Card and Elements =================== */
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
    .rounded-4 {
        border-radius: 1rem !important;
    }
    .border-dashed {
        border-top: 1px dashed #dee2e6;
    }
    .xsmall {
        font-size: 0.825rem;
    }

    /* =================== Order Steps =================== */
    .order-steps {
        display: flex;
        gap: 1.25rem;
        flex-wrap: wrap;
    }
    .order-steps .step {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--muted);
        transition: color 0.2s ease;
    }
    .order-steps .step .dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #ced4da;
        display: inline-block;
        transition: background 0.2s ease;
    }
    .order-steps .step.done {
        color: #28a745;
        font-weight: 600;
    }
    .order-steps .step.done .dot {
        background: #28a745;
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
    .btn-outline-secondary {
        color: var(--muted);
        border-color: var(--muted);
        transition: background 0.15s ease, color 0.15s ease;
    }
    .btn-outline-secondary:hover {
        background-color: var(--muted);
        border-color: var(--muted);
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
    .btn-danger {
        background-color: #dc3545;
        border-color: #dc3545;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .btn-danger:hover {
        background-color: #c82333;
        border-color: #bd2130;
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

    /* =================== Form Controls =================== */
    .form-control-modern,
    .form-select.form-control-modern {
        border-radius: 0.8rem;
        border: 1px solid #e9ecef;
        background: #fff;
        font-size: 1rem;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }
    .form-control-modern:focus,
    .form-select.form-control-modern:focus {
        border-color: var(--brand);
        box-shadow: 0 0 0 0.2rem var(--ring);
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

    /* =================== Responsive Design =================== */
    @media (max-width: 991px) {
        .support-hero {
            min-height: 180px;
        }
        .support-hero h1 {
            font-size: 2rem;
        }
        .col-lg-8, .col-lg-4 {
            flex: 0 0 100%;
            max-width: 100%;
        }
        .sticky-lg-top {
            position: static !important;
        }
        .card-body {
            padding: 1.5rem;
        }
        .order-steps {
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
        .card-body {
            padding: 1rem;
        }
        .btn-sm {
            padding: 0.35rem 0.7rem;
            font-size: 0.85rem;
        }
        .order-steps .step .dot {
            width: 8px;
            height: 8px;
        }
        .order-steps .step .label {
            font-size: 0.9rem;
        }
        .img-hover-zoom {
            width: 60px !important;
            height: 60px !important;
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
        .card-body {
            padding: 0.75rem;
        }
        .btn-sm {
            padding: 0.3rem 0.6rem;
            font-size: 0.8rem;
        }
        .badge, .badge-soft-brand {
            font-size: 0.8rem;
        }
        .order-steps {
            gap: 0.75rem;
        }
        .order-steps .step .dot {
            width: 6px;
            height: 6px;
        }
        .order-steps .step .label {
            font-size: 0.85rem;
        }
        .img-hover-zoom {
            width: 50px !important;
            height: 50px !important;
        }
        .xsmall {
            font-size: 0.75rem;
        }
        .alert {
            font-size: 0.85rem;
            padding: 0.5rem;
        }
        .form-control-modern,
        .form-select.form-control-modern {
            font-size: 0.9rem;
        }
    }
</style>
@endpush

@push('scripts-page')
<script>
    // GHN address select logic
    (function () {
        const orderStatus = "{{ $order->status }}";
        if (!['pending', 'processing'].includes(orderStatus)) return;

        const editBtn = document.getElementById('edit-address-btn');
        const cancelBtn = document.getElementById('cancel-edit-btn');
        const displayDiv = document.getElementById('address-display');
        const editFormDiv = document.getElementById('address-edit-form');
        const form = document.getElementById('update-address-form');

        if (!editBtn || !cancelBtn || !displayDiv || !editFormDiv || !form) return;

        // Populate initial values
        const provinceSelect = document.getElementById('province_id_edit');
        const districtSelect = document.getElementById('district_id_edit');
        const wardSelect = document.getElementById('ward_code_edit');
        const provinceNameInput = document.getElementById('province_name_input_edit');
        const districtNameInput = document.getElementById('district_name_input_edit');
        const wardNameInput = document.getElementById('ward_name_input_edit');

        let saved = {
            province: provinceNameInput?.value || '',
            district: districtNameInput?.value || '',
            ward: wardNameInput?.value || ''
        };

        const fetchJson = async (url) => {
            try {
                const r = await fetch(url, { headers: { 'Accept': 'application/json' } });
                return r.ok ? r.json() : [];
            }
            catch { return []; }
        };
        const renderOptions = (select, list, placeholder, valKey, textKey, pickedText = '') => {
            select.innerHTML = `<option value="">${placeholder}</option>`;
            if (!Array.isArray(list)) return;
            let pickedVal = null;
            for (const item of list) {
                const opt = new Option(item[textKey], item[valKey]);
                if (pickedText && item[textKey] === pickedText) { opt.selected = true; pickedVal = item[valKey]; }
                select.add(opt);
            }
            if (pickedVal) {
                select.value = pickedVal;
                setTimeout(() => select.dispatchEvent(new Event('change', { bubbles: true })), 0);
            }
        };

        const loadProvinces = async () => {
            const provinces = await fetchJson('{{ route("address.provinces") }}');
            renderOptions(provinceSelect, provinces, 'Chọn Tỉnh/Thành', 'ProvinceID', 'ProvinceName', saved.province);
        };

        provinceSelect.addEventListener('change', async function () {
            if (provinceNameInput) provinceNameInput.value = this.selectedIndex > 0 ? this.options[this.selectedIndex].text : '';
            renderOptions(districtSelect, [], 'Vui lòng chờ...', 'DistrictID', 'DistrictName');
            renderOptions(wardSelect, [], 'Chọn Phường/Xã', 'WardCode', 'WardName');
            if (this.value) {
                const districts = await fetchJson(`{{ route("address.districts") }}?province_id=${this.value}`);
                renderOptions(districtSelect, districts, 'Chọn Quận/Huyện', 'DistrictID', 'DistrictName', saved.district);
            }
            saved.district = '';
            saved.ward = '';
        });

        districtSelect.addEventListener('change', async function () {
            if (districtNameInput) districtNameInput.value = this.selectedIndex > 0 ? this.options[this.selectedIndex].text : '';
            renderOptions(wardSelect, [], 'Vui lòng chờ...', 'WardCode', 'WardName');
            if (this.value) {
                const wards = await fetchJson(`{{ route("address.wards") }}?district_id=${this.value}`);
                renderOptions(wardSelect, wards, 'Chọn Phường/Xã', 'WardCode', 'WardName', saved.ward);
            }
            saved.ward = '';
        });

        wardSelect.addEventListener('change', function () {
            if (wardNameInput) wardNameInput.value = this.selectedIndex > 0 ? this.options[this.selectedIndex].text : '';
        });

        editBtn.addEventListener('click', () => {
            displayDiv.style.display = 'none';
            editFormDiv.style.display = 'block';
            loadProvinces();
        });

        cancelBtn.addEventListener('click', () => {
            editFormDiv.style.display = 'none';
            displayDiv.style.display = 'block';
        });

        // AOS init
        if (typeof AOS !== 'undefined') {
            AOS.init({
                duration: 600,
                once: true,
                offset: 80
            });
        }
    })();
</script>
@endpush