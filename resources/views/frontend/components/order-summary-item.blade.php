@props(['order'])

<div class="card shadow-sm border-0 mb-3">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <div>
            <h6 class="mb-0">Đơn hàng: <strong>#{{ $order->order_code }}</strong></h6>
            <small class="text-muted">Ngày đặt: {{ $order->created_at->format('d/m/Y') }}</small>
        </div>
        @php
            $statusConfig = [
                'pending' => ['class' => 'bg-warning text-dark', 'text' => 'Đang chờ'],
                'processing' => ['class' => 'bg-info text-dark', 'text' => 'Đang xử lý'],
                'shipped_to_shipper' => ['class' => 'bg-secondary', 'text' => 'Đã giao vận chuyển'],
                'shipping' => ['class' => 'bg-primary', 'text' => 'Đang giao'],
                'delivered' => ['class' => 'bg-success', 'text' => 'Đã giao'],
                'received' => ['class' => 'bg-success', 'text' => 'Đã nhận'],
                'cancelled' => ['class' => 'bg-danger', 'text' => 'Đã hủy'],
            ];
            $status = $statusConfig[$order->status] ?? ['class' => 'bg-light text-dark', 'text' => 'Không xác định'];
        @endphp
        <span class="badge {{ $status['class'] }}">{{ $status['text'] }}</span>
    </div>
    <div class="card-body">
        @foreach($order->items->take(2) as $item)
            <div class="d-flex align-items-center {{ !$loop->last ? 'mb-2' : '' }}">
                <img src="{{ optional($item->variant->product->images->where('is_primary', true)->first())->image_url_path ?? 'https://placehold.co/50x50' }}" alt="{{ $item->variant->product->name }}" class="rounded" width="50" height="50">
                <div class="ms-3 flex-grow-1">
                    <p class="mb-0 small">{{ $item->variant->product->name }}</p>
                    <small class="text-muted">Số lượng: {{ $item->quantity }}</small>
                </div>
            </div>
        @endforeach
        @if($order->items->count() > 2)
            <p class="text-muted small text-center mt-2 mb-0">và {{ $order->items->count() - 2 }} sản phẩm khác...</p>
        @endif
    </div>
    <div class="card-footer bg-white d-flex justify-content-between align-items-center">
        <span class="fw-bold">Tổng tiền: <span class="text-danger">{{ number_format($order->final_amount) }} ₫</span></span>
        <a href="{{ route('orders.show', $order) }}" class="btn btn-primary btn-sm">Xem chi tiết</a>
    </div>
</div>
