@extends('layouts.app')

@section('content')
<div class="container my-4">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <h1 class="mb-4">Chi tiết đơn hàng #{{ $order->order_code }}</h1>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">Sản phẩm trong đơn hàng</div>
                <div class="card-body">
                    @foreach($order->items as $item)
                        <div class="d-flex mb-3">
                            @php
                                $image = $item->variant->product->images->first();
                                $imageUrl = $image->image_url ?? 'https://via.placeholder.com/100';
                                if ($image && !Str::startsWith($image->image_url, 'http')) {
                                    $imageUrl = asset('storage/' . $image->image_url);
                                }
                            @endphp
                            <img src="{{ $imageUrl }}" alt="" style="width: 100px; height: 100px; object-fit: cover;" class="rounded">
                            <div class="ms-3 flex-grow-1">
                                <h6>{{ $item->variant->product->name }}</h6>
                                <small class="text-muted">
                                    @php
                                        $attributes = $item->variant->attributes ?? []; // Đảm bảo luôn là mảng, kể cả khi null
                                    @endphp
                                    @if(is_array($attributes) && count($attributes) > 0)
                                        @foreach($attributes as $key => $value)
                                            {{ ucfirst($key) }}: {{ $value }}
                                        @endforeach
                                    @else
                                        Không có thuộc tính
                                    @endif
                                </small>
                                <p class="mb-0">Số lượng: {{ $item->quantity }}</p>
                                <p class="fw-bold">{{ number_format($item->price) }} ₫</p>
                            </div>
                        </div>
                        @if(!$loop->last) <hr> @endif
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">Thông tin giao hàng</div>
                <div class="card-body">
                    <p><strong>Người nhận:</strong> {{ $order->shipment->receiver_name }}</p>
                    <p><strong>Điện thoại:</strong> {{ $order->shipment->phone }}</p>
                    <p><strong>Địa chỉ:</strong> {{ $order->shipment->address }}, {{ $order->shipment->ward }}, {{ $order->shipment->district }}, {{ $order->shipment->city }}</p>
                </div>
            </div>
            <div class="card mt-3">
                <div class="card-header">Tổng cộng</div>
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <span>Tạm tính:</span>
                        <span>{{ number_format($order->total_amount) }} ₫</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between fw-bold fs-5">
                        <span>Thành tiền:</span>
                        <span>{{ number_format($order->final_amount) }} ₫</span>
                    </div>
                </div>
            </div>

            {{-- Nút Hủy đơn hàng và nút "Đã nhận" --}}
            <div class="card mt-3">
                <div class="card-header">Hành động</div>
                <div class="card-body">
                    @if($order->status == 'cancelled')
                        <div class="alert alert-info">
                            Đơn hàng đã bị hủy.
                            @if($order->payment && in_array($order->payment->method, ['vnpay', 'momo']))
                                Vui lòng liên hệ qua số điện thoại hỗ trợ để được hướng dẫn hoàn tiền.
                            @endif
                        </div>
                    @elseif($order->status == 'delivered')
                        {{-- Thông báo cụ thể khi đơn hàng đã được xác nhận nhận --}}
                        <div class="alert alert-success">
                            Đơn hàng này đã được xác nhận đã nhận.
                        </div>
                    @elseif($order->isReceivableByCustomer())
                        {{-- Nút "Đã nhận" chỉ hiển thị khi đơn hàng đã giao cho shipper hoặc đang giao --}}
                        <form action="{{ route('orders.markDelivered', $order) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn đã nhận được đơn hàng này?');">
                            @csrf
                            <button type="submit" class="btn btn-success w-100">Đã nhận hàng</button>
                        </form>
                    @elseif($order->isCancellable())
                        {{-- Nút hủy chỉ hiển thị khi đơn hàng chờ hoặc đang xử lý --}}
                        <form action="{{ route('orders.cancel', $order) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này? Hành động này không thể hoàn tác.');">
                            @csrf
                            <button type="submit" class="btn btn-danger w-100">Hủy đơn hàng</button>
                        </form>
                    @else
                        {{-- Thông báo chung cho các trạng thái không thể hủy/nhận --}}
                        <div class="alert alert-warning">
                            Đơn hàng này không thể hủy hoặc xác nhận nhận hàng được do trạng thái hiện tại (
                                @php
                                    $statusText = '';
                                    switch ($order->status) {
                                        case 'shipped_to_shipper': $statusText = 'Đã giao cho shipper'; break;
                                        case 'shipping': $statusText = 'Đang giao'; break;
                                        case 'delivered': $statusText = 'Đã giao'; break;
                                        case 'cancelled': $statusText = 'Đã hủy'; break;
                                        default: $statusText = ucfirst($order->status); break;
                                    }
                                @endphp
                                {{ $statusText }}
                            ).
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection