@props(['order'])

<div class="order-summary-card">
    {{-- Header --}}
    <div class="card-section-header">
        <div>
            <h6 class="order-code">Đơn hàng: <strong>#{{ $order->order_code }}</strong></h6>
            <small class="order-date">Ngày đặt: {{ $order->created_at->format('d/m/Y') }}</small>
        </div>
        @php
            // Sử dụng các class "soft badge" mới
            $statusConfig = [
                'pending'          => ['class' => 'badge-soft-warning',  'text' => 'Đang chờ'],
                'processing'       => ['class' => 'badge-soft-info',     'text' => 'Đang xử lý'],
                'shipped_to_shipper' => ['class' => 'badge-soft-secondary', 'text' => 'Đã giao vận chuyển'],
                'shipping'         => ['class' => 'badge-soft-primary',  'text' => 'Đang giao'],
                'delivered'        => ['class' => 'badge-soft-success',  'text' => 'Đã giao'],
                'received'        => ['class' => 'badge-soft-success',  'text' => 'Đã nhận'],
                'cancelled'        => ['class' => 'badge-soft-danger',   'text' => 'Đã hủy'],
            ];
            $status = $statusConfig[$order->status] ?? ['class' => 'badge-soft-secondary', 'text' => 'Không xác định'];
        @endphp
        <span class="badge {{ $status['class'] }}">{{ $status['text'] }}</span>
    </div>

    {{-- Body --}}
    <div class="card-section-body">
        @foreach($order->items->take(2) as $item)
            <div class="item-preview-row {{ !$loop->last ? 'mb-2' : '' }}">
                <img src="{{ optional($item->variant->product->images->where('is_primary', true)->first())->image_url_path ?? 'https://placehold.co/50x50' }}"
                     alt="{{ $item->variant->product->name }}"
                     class="item-thumbnail">
                <div class="item-info">
                    <p class="item-name">{{ $item->variant->product->name }}</p>
                    <small class="item-quantity">Số lượng: {{ $item->quantity }}</small>
                </div>
            </div>
        @endforeach
        @if($order->items->count() > 2)
            <p class="more-items-text">+ {{ $order->items->count() - 2 }} sản phẩm khác...</p>
        @endif
    </div>

    {{-- Footer --}}
    <div class="card-section-footer">
        <div class="total-amount">
            Tổng tiền: <span class="amount-value">{{ number_format($order->final_amount) }} ₫</span>
        </div>
        <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-brand btn-sm">Xem chi tiết</a>
    </div>
</div>

@once
@push('styles')
<style>
    .order-summary-card {
        background: var(--card, #fff);
        border: 1px solid rgba(0,0,0,.08);
        border-radius: var(--radius, 12px);
        box-shadow: 0 4px 12px rgba(0,0,0,.04);
        margin-bottom: 1.25rem;
    }
    .card-section-header, .card-section-body, .card-section-footer {
        padding: 1rem 1.25rem;
    }
    .card-section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid rgba(0,0,0,.06);
    }
    .order-code { margin: 0; font-size: 1rem; color: var(--text); }
    .order-date { color: var(--muted); }

    /* Soft Badges */
    .badge-soft-primary { background-color: rgba(13, 110, 253, 0.1); color: #0d6efd; }
    .badge-soft-secondary { background-color: rgba(108, 117, 125, 0.1); color: #6c757d; }
    .badge-soft-success { background-color: rgba(25, 135, 84, 0.1); color: #198754; }
    .badge-soft-danger { background-color: rgba(220, 53, 69, 0.1); color: #dc3545; }
    .badge-soft-warning { background-color: rgba(255, 193, 7, 0.1); color: #755d00; }
    .badge-soft-info { background-color: rgba(13, 202, 240, 0.1); color: #087990; }
    
    .item-preview-row {
        display: flex;
        align-items: center;
    }
    .item-thumbnail {
        width: 50px;
        height: 50px;
        border-radius: 8px;
        object-fit: cover;
        flex-shrink: 0;
    }
    .item-info {
        margin-left: 0.75rem;
        flex-grow: 1;
        min-width: 0;
    }
    .item-name {
        margin-bottom: 0;
        font-size: 0.9rem;
        font-weight: 500;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .item-quantity {
        color: var(--muted);
    }
    .more-items-text {
        color: var(--muted);
        font-size: 0.85rem;
        text-align: center;
        margin-top: 0.75rem;
        margin-bottom: 0;
        font-style: italic;
    }
    
    .card-section-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: rgba(0,0,0,.02);
        border-top: 1px solid rgba(0,0,0,.06);
    }
    .total-amount {
        font-weight: 600;
        color: var(--text);
    }
    .total-amount .amount-value {
        color: var(--brand);
        font-size: 1.1rem;
    }
</style>
@endpush
@endonce