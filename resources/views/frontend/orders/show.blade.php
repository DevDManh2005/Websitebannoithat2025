@extends('layouts.app')

@section('title', 'Chi tiết đơn hàng #' . $order->order_code)

@section('content')
    {{-- =================== HERO / BREADCRUMB =================== --}}
    <section class="order-hero position-relative overflow-hidden mb-5">
        <img src="https://anphonghouse.com/wp-content/uploads/2018/06/hinh-nen-noi-that-dep-full-hd-so-43-0.jpg" alt="Order Banner" class="hero-bg">
        <div class="hero-overlay"></div>
        <div class="container position-relative" data-aos="fade-down">
            <div class="row align-items-center" style="min-height: 220px;">
                <div class="col-12 text-center">
                    <h1 class="fw-bold text-white mb-2">Chi Tiết Đơn Hàng</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center mb-0">
                            <li class="breadcrumb-item"><a class="hero-bc-link" href="{{ route('home') }}">Trang chủ</a></li>
                            <li class="breadcrumb-item"><a class="hero-bc-link" href="{{ route('orders.index') }}">Đơn hàng của tôi</a></li>
                            <li class="breadcrumb-item active text-white-50" aria-current="page">#{{ $order->order_code }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <div class="hero-bottom wave-sep"></div>
    </section>

    {{-- ===== Helper: chuẩn hoá URL ảnh (URL tuyệt đối / storage / public) ===== --}}
    @php
        use Illuminate\Support\Facades\Storage;
        use Illuminate\Support\Str;

        /** @var \Closure $resolveImage */
        $resolveImage = function ($product) {
            // Ưu tiên ảnh primary, fallback ảnh đầu tiên
            $img = optional($product->images)->firstWhere('is_primary', 1)
                 ?? optional($product->images)->first();

            $raw = $img->image_url ?? null;
            if (!$raw) {
                return 'https://via.placeholder.com/80';
            }

            // 1) Nếu là URL tuyệt đối -> dùng luôn
            if (Str::startsWith($raw, ['http://', 'https://'])) {
                return $raw;
            }

            // 2) Chuẩn hoá path: bỏ prefix "storage/" hoặc "public/"
            $normalized = ltrim(str_replace(['storage/', 'public/'], '', $raw), '/');

            // 3) Trả về URL public (symlink storage) — KHÔNG check exists để tránh rớt URL
            // (Các trang khác của bạn cũng hiển thị OK mà không cần exists)
            if ($normalized) {
                return Storage::url($normalized); // /storage/...
            }

            // 4) Fallback: có thể file đang nằm sẵn trong /public
            return asset($raw);
        };

        // Cờ thanh toán online (VNPAY)
        $paidOnline = ($order->is_paid ?? false) && (($order->payment_method ?? '') === 'vnpay');
    @endphp

    <div class="container my-5">
        {{-- Alerts --}}
        @if(session('success')) <div class="alert alert-success shadow-sm" data-aos="fade-up">{{ session('success') }}</div> @endif
        @if(session('error'))   <div class="alert alert-danger  shadow-sm" data-aos="fade-up">{{ session('error') }}</div> @endif

        {{-- =================== ORDER HEADER =================== --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4" data-aos="fade-right">
            <div class="card-body p-4 p-md-5">
                @php
                    $statusConfig = [
                        'pending'            => ['class' => 'bg-warning text-dark', 'text' => 'Đang chờ xử lý'],
                        'processing'         => ['class' => 'bg-info text-dark',    'text' => 'Đang xử lý'],
                        'shipped_to_shipper' => ['class' => 'bg-secondary',          'text' => 'Đã giao cho shipper'],
                        'shipping'           => ['class' => 'bg-primary',            'text' => 'Đang giao'],
                        'delivered'          => ['class' => 'bg-success',            'text' => 'Đã giao (Chờ bạn xác nhận)'],
                        'received'           => ['class' => 'bg-success',            'text' => 'Đã nhận thành công'],
                        'cancelled'          => ['class' => 'bg-danger',             'text' => 'Đã hủy'],
                    ];
                    $status = $statusConfig[$order->status] ?? ['class' => 'bg-light text-dark', 'text' => 'Không xác định'];

                    // timeline map
                    $steps = [
                        'pending'            => 'Đặt hàng',
                        'processing'         => 'Xác nhận',
                        'shipped_to_shipper' => 'Bàn giao',
                        'shipping'           => 'Đang giao',
                        'delivered'          => 'Đã giao',
                        'received'           => 'Hoàn tất',
                    ];
                    $keys = array_keys($steps);
                    $currentIdx = array_search($order->status, $keys);
                @endphp

                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-light text-dark fw-semibold rounded-3">Mã đơn: #{{ $order->order_code }}</span>
                            <span class="badge {{ $status['class'] }}">{{ $status['text'] }}</span>
                            @if($paidOnline)
                                <span class="badge bg-success">ĐÃ THANH TOÁN ONLINE</span>
                            @endif
                        </div>
                        <small class="text-muted d-block mt-2">
                            <i class="bi bi-calendar3 me-1"></i>{{ optional($order->created_at)->format('d/m/Y H:i') }}
                            @if(optional($order->shipment)->tracking_code)
                                <span class="ms-3"><i class="bi bi-truck me-1"></i>Mã vận đơn:
                                    <span class="text-primary fw-semibold">{{ $order->shipment->tracking_code }}</span> (GHN)
                                </span>
                            @endif
                        </small>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" onclick="window.print()">
                            <i class="bi bi-printer me-1"></i> In
                        </button>
                        <a href="{{ route('orders.index') }}" class="btn btn-light btn-sm rounded-pill">Quay lại</a>
                    </div>
                </div>

                {{-- timeline --}}
                <div class="order-steps mt-4">
                    @foreach($steps as $k => $label)
                        @php $i = array_search($k, $keys); $done = $currentIdx !== false && $i !== false && $i <= $currentIdx; @endphp
                        <div class="step {{ $done ? 'done' : '' }}">
                            <span class="dot"></span>
                            <span class="label">{{ $label }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="row g-4 g-lg-5">
            {{-- =================== LEFT: ITEMS + ADDRESS =================== --}}
            <div class="col-lg-8">
                {{-- Items --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4" data-aos="fade-right" data-aos-delay="50">
                    <div class="card-header bg-white border-0 px-4 pt-4">
                        <h5 class="mb-0 fw-bold">Sản phẩm trong đơn hàng</h5>
                    </div>
                    <div class="card-body p-4 pt-0">
                        @foreach($order->items as $item)
                            @php
                                $product  = $item->variant->product;
                                $imageUrl = $resolveImage($product);
                            @endphp
                            <div class="d-flex align-items-center py-3 border-top">
                                <div class="rounded-3 overflow-hidden flex-shrink-0 img-hover-zoom" style="width:72px;height:72px;">
                                    <img src="{{ $imageUrl }}" alt="{{ $product->name }}" class="w-100 h-100 object-fit-cover">
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">
                                        <a href="{{ route('product.show', $product->slug) }}" class="text-decoration-none text-dark fw-semibold">
                                            {{ $product->name }}
                                        </a>
                                    </h6>
                                    <small class="text-muted">
                                        @forelse((array)$item->variant->attributes as $key => $value)
                                            {{ ucfirst($key) }}: {{ $value }}@if(!$loop->last), @endif
                                        @empty
                                            Sản phẩm gốc
                                        @endforelse
                                    </small>
                                    <div class="mt-1 xsmall text-muted">Số lượng: {{ $item->quantity }}</div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold text-danger">{{ number_format($item->subtotal) }} ₫</div>
                                    <small class="text-muted">{{ number_format($item->price) }} ₫ / sp</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Shipping info --}}
                <div class="card border-0 shadow-sm rounded-4" data-aos="fade-right" data-aos-delay="100">
                    <div class="card-header bg-white border-0 px-4 pt-4">
                        <h5 class="mb-0 fw-bold">Thông tin giao hàng</h5>
                    </div>
                    <div class="card-body p-4 pt-0">
                        <div class="row g-4">
                            <div class="col-md-7">
                                <div class="small text-muted">Người nhận</div>
                                <div class="fw-semibold">{{ optional($order->shipment)->receiver_name }} — {{ optional($order->shipment)->phone }}</div>

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
                                <div class="fw-semibold">{{ optional($order->shipment)->carrier_name ?? 'GHN' }}</div>
                                @if(optional($order->shipment)->expected_date)
                                    <div class="xsmall text-muted">Dự kiến: {{ \Carbon\Carbon::parse($order->shipment->expected_date)->format('d/m/Y') }}</div>
                                @endif

                                <hr>
                                <div class="small text-muted">Thanh toán</div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Phương thức</span>
                                    <span class="fw-semibold">{{ $paidOnline ? 'VNPAY' : 'Thanh toán khi nhận (COD)' }}</span>
                                </div>
                                <div class="d-flex justify-content-between mt-2">
                                    <span class="text-muted">Trạng thái</span>
                                    <span class="fw-semibold">
                                        @if($paidOnline)
                                            Đã thanh toán online
                                            @if(!empty($order->paid_at))
                                                <span class="text-muted xsmall">({{ \Carbon\Carbon::parse($order->paid_at)->format('d/m/Y H:i') }})</span>
                                            @endif
                                        @else
                                            Chưa thanh toán
                                        @endif
                                    </span>
                                </div>
                                @if($paidOnline && !empty($order->payment_ref))
                                    <div class="xsmall text-muted mt-1">
                                        Mã giao dịch VNPAY: <span class="text-primary">{{ $order->payment_ref }}</span>
                                    </div>
                                @endif

                                {{-- Nút thanh toán lại nếu chưa thanh toán và đơn còn hợp lệ --}}
                                @if(!$paidOnline && in_array($order->status, ['pending','processing']))
                                    <a href="{{ route('payment.vnpay.create', $order) }}" class="btn btn-primary w-100 rounded-pill mt-3">
                                        Thanh toán online (VNPAY)
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- =================== RIGHT: SUMMARY + ACTIONS =================== --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 sticky-lg-top" style="top: 96px;" data-aos="fade-left">
                    <div class="card-header bg-white border-0 px-4 pt-4">
                        <h5 class="mb-0 fw-bold">Tổng cộng</h5>
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
                            <span class="text-danger">{{ number_format($order->final_amount) }} ₫</span>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            @if(method_exists($order, 'isCancellable') && $order->isCancellable())
                                <div class="alert alert-info small p-2">Nếu đã thanh toán online, vui lòng liên hệ Admin để được hoàn tiền sau khi hủy.</div>
                                <form action="{{ route('orders.cancel', $order) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này?');">
                                    @csrf
                                    <button type="submit" class="btn btn-danger w-100 rounded-pill">Hủy đơn hàng</button>
                                </form>
                            @elseif(method_exists($order, 'isReceivableByCustomer') && $order->isReceivableByCustomer())
                                <form action="{{ route('orders.markAsReceived', $order) }}" method="POST" onsubmit="return confirm('Xác nhận bạn đã nhận được đúng và đủ sản phẩm?');">
                                    @csrf
                                    <button type="submit" class="btn btn-success w-100 rounded-pill">Đã nhận được hàng</button>
                                </form>
                            @elseif($order->status == 'received')
                                <div class="alert alert-success">Đã xác nhận nhận hàng. Cảm ơn bạn!</div>
                            @elseif($order->status == 'cancelled')
                                <div class="alert alert-secondary">Đơn hàng này đã được hủy.</div>
                            @else
                                <div class="alert alert-light">Đơn hàng đang được xử lý, hiện chưa có hành động khả dụng.</div>
                            @endif

                            <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary w-100 rounded-pill">Quay lại danh sách</a>
                        </div>
                    </div>
                </div>
            </div>
            {{-- /RIGHT --}}
        </div>
    </div>
@endsection

@push('styles')
<style>
/* ===== HERO sáng + overlay ===== */
.order-hero{ background:#fff; }
.order-hero .hero-bg{
    position:absolute; inset:0; width:100%; height:100%; object-fit:cover; transform:scale(1.03);
    filter: brightness(0.7);
}
.order-hero .hero-overlay{ position:absolute; inset:0; background:linear-gradient(180deg, rgba(0,0,0,.35), rgba(0,0,0,.35)); }
.wave-sep{
    position:absolute; left:0; right:0; bottom:-1px; height:28px;
    background: radial-gradient(36px 11px at 50% 0, #fff 98%, transparent 100%) repeat-x;
    background-size:36px 18px;
}
.hero-bc-link{ color:#f8f9fa; text-decoration:none; }
.hero-bc-link:hover{ text-decoration:underline; }

/* ===== Card & text ===== */
.rounded-4{ border-radius:1rem !important; }
.border-dashed{ border-top:1px dashed #dee2e6; }
.xsmall{ font-size:.825rem; }

/* ===== Steps (timeline) ===== */
.order-steps{ display:flex; gap:1.25rem; flex-wrap:wrap; }
.order-steps .step{ display:flex; align-items:center; gap:.5rem; color:#6c757d; }
.order-steps .step .dot{
    width:10px; height:10px; border-radius:50%; background:#ced4da; display:inline-block;
}
.order-steps .step.done{ color:#198754; font-weight:600; }
.order-steps .step.done .dot{ background:#198754; }

/* ===== Image hover ===== */
.img-hover-zoom img{ transition: transform .35s ease; display:block; }
.img-hover-zoom:hover img{ transform: scale(1.06); }

/* ===== Sticky fix: chỉ sticky từ lg trở lên (Bootstrap) ===== */
@media (max-width: 991.98px){
    .sticky-lg-top{ position:static !important; }
}
</style>
@endpush
