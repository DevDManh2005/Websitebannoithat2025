@extends('layouts.app')

@section('title', 'Chi tiết đơn hàng #' . $order->order_code)

@section('content')
<div class="container my-4">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="mb-0">Chi tiết đơn hàng #{{ $order->order_code }}</h1>
        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">Quay lại danh sách</a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            @php
                $statusConfig = [
                    'pending' => ['class' => 'bg-warning text-dark', 'text' => 'Đang chờ xử lý'],
                    'processing' => ['class' => 'bg-info text-dark', 'text' => 'Đang xử lý'],
                    'shipped_to_shipper' => ['class' => 'bg-secondary', 'text' => 'Đã giao cho shipper'],
                    'shipping' => ['class' => 'bg-primary', 'text' => 'Đang giao'],
                    'delivered' => ['class' => 'bg-success', 'text' => 'Đã giao (Chờ bạn xác nhận)'],
                    'received' => ['class' => 'bg-success', 'text' => 'Đã nhận thành công'],
                    'cancelled' => ['class' => 'bg-danger', 'text' => 'Đã hủy'],
                ];
                $status = $statusConfig[$order->status] ?? ['class' => 'bg-light text-dark', 'text' => 'Không xác định'];
            @endphp
            <p class="mb-0"><strong>Trạng thái đơn hàng:</strong> <span class="badge {{ $status['class'] }}">{{ $status['text'] }}</span></p>
            @if(optional($order->shipment)->tracking_code)
                <p class="mb-0 mt-2"><strong>Mã vận đơn:</strong> <span class="text-primary fw-bold">{{ $order->shipment->tracking_code }}</span> (Vận chuyển bởi GHN)</p>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light"><h5 class="mb-0">Sản phẩm trong đơn hàng</h5></div>
                <div class="card-body">
                    @foreach($order->items as $item)
                        <div class="d-flex align-items-center mb-3">
                            @php
                                $primaryImage = $item->variant->product->images->where('is_primary', true)->first();
                                $imageUrl = $primaryImage && Storage::disk('public')->exists($primaryImage->image_url) ? asset('storage/' . $primaryImage->image_url) : 'https://via.placeholder.com/80';
                            @endphp
                            <img src="{{ $imageUrl }}" alt="{{ $item->variant->product->name }}" style="width: 80px; height: 80px; object-fit: cover;" class="rounded me-3">
                            <div class="flex-grow-1">
                                <h6>{{ $item->variant->product->name }}</h6>
                                <small class="text-muted">
                                    @forelse((array)$item->variant->attributes as $key => $value)
                                        {{ ucfirst($key) }}: {{ $value }}@if(!$loop->last), @endif
                                    @empty
                                        Sản phẩm gốc
                                    @endforelse
                                </small>
                                <p class="mb-0">Số lượng: {{ $item->quantity }}</p>
                            </div>
                            <div class="text-end">
                                <p class="fw-bold mb-0">{{ number_format($item->subtotal) }} ₫</p>
                                <small class="text-muted">{{ number_format($item->price) }} ₫ / sản phẩm</small>
                            </div>
                        </div>
                        @if(!$loop->last) <hr> @endif
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light"><h5 class="mb-0">Thông tin giao hàng</h5></div>
                <div class="card-body">
                    <p class="mb-1"><strong>Người nhận:</strong> {{ $order->shipment->receiver_name }}</p>
                    <p class="mb-1"><strong>Điện thoại:</strong> {{ $order->shipment->phone }}</p>
                    <p class="mb-0"><strong>Địa chỉ:</strong> {{ $order->shipment->address }}, {{ $order->shipment->ward }}, {{ $order->shipment->district }}, {{ $order->shipment->city }}</p>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                 <div class="card-header bg-light"><h5 class="mb-0">Tổng cộng</h5></div>
                 <div class="card-body">
                     <div class="d-flex justify-content-between"><span>Tạm tính:</span> <span>{{ number_format($order->total_amount) }} ₫</span></div>
                     @if($order->discount > 0)
                     <div class="d-flex justify-content-between text-success">
                         <span>Giảm giá (Voucher):</span>
                         <span>-{{ number_format($order->discount) }} ₫</span>
                     </div>
                     @endif
                     <div class="d-flex justify-content-between"><span>Phí vận chuyển:</span> <span>{{ number_format($order->shipment->shipping_fee) }} ₫</span></div>
                     <hr>
                     <div class="d-flex justify-content-between fw-bold fs-5"><span class="text-dark">Thành tiền:</span> <span class="text-danger">{{ number_format($order->final_amount) }} ₫</span></div>
                 </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-light"><h5 class="mb-0">Hành động</h5></div>
                <div class="card-body text-center">
                    @if($order->isCancellable())
                        <div class="alert alert-info small p-2">Nếu đã thanh toán online, vui lòng liên hệ Admin để được hoàn tiền sau khi hủy.</div>
                        <form action="{{ route('orders.cancel', $order) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này?');">
                            @csrf
                            <button type="submit" class="btn btn-danger w-100">Hủy đơn hàng</button>
                        </form>
                    @elseif($order->isReceivableByCustomer())
                        <form action="{{ route('orders.markAsReceived', $order) }}" method="POST" onsubmit="return confirm('Xác nhận bạn đã nhận được đúng và đủ sản phẩm?');">
                            @csrf
                            <button type="submit" class="btn btn-success w-100">Đã nhận được hàng</button>
                        </form>
                    @elseif($order->status == 'received')
                         <div class="alert alert-success">Đã xác nhận nhận hàng. Cảm ơn bạn!</div>
                    @elseif($order->status == 'cancelled')
                         <div class="alert alert-secondary">Đơn hàng này đã được hủy.</div>
                    @else
                         <div class="alert alert-light">Đơn hàng đang trong quá trình xử lý, bạn chưa thể thực hiện hành động.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection