@extends('admins.layouts.app')

@section('title', 'Chi tiết Đơn hàng #' . $order->order_code)

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Chi tiết Đơn hàng #{{ $order->order_code }}</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header">Sản phẩm</div>
                <div class="card-body">
                    @foreach($order->items as $item)
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-grow-1">
                                <strong>{{ $item->variant->product->name }}</strong>
                                <br>
                                <small class="text-muted">
                                    @php
                                        $attributes = (array)($item->variant->attributes ?? []); // Đảm bảo luôn là mảng, kể cả khi null hoặc không hợp lệ
                                    @endphp
                                    @if(is_array($attributes) && count($attributes) > 0)
                                        @foreach($attributes as $key => $value)
                                            {{ ucfirst($key) }}: {{ $value }}
                                        @endforeach
                                    @else
                                        Không có thuộc tính
                                    @endif
                                </small>
                            </div>
                            <div class="text-end">
                                {{ number_format($item->price) }} ₫ x {{ $item->quantity }}
                                <br>
                                <strong>{{ number_format($item->subtotal) }} ₫</strong>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header">Thông tin Khách hàng</div>
                <div class="card-body">
                    <p><strong>Tên:</strong> {{ $order->shipment->receiver_name }}</p>
                    <p><strong>Email:</strong> {{ $order->user->email }}</p>
                    <p><strong>Điện thoại:</strong> {{ $order->shipment->phone }}</p>
                    <p><strong>Địa chỉ:</strong> {{ $order->shipment->address }}, {{ $order->shipment->ward }}, {{ $order->shipment->district }}, {{ $order->shipment->city }}</p>
                    @if($order->shipment->tracking_code)
                        <p><strong>Mã vận đơn GHTK:</strong> {{ $order->shipment->tracking_code }}</p>
                        {{-- Bạn có thể thêm link tra cứu GHTK tại đây --}}
                        {{-- <a href="https://customer.ghtk.vn/track/{{ $order->shipment->tracking_code }}" target="_blank" class="btn btn-sm btn-outline-info">Tra cứu GHTK</a> --}}
                    @endif
                </div>
            </div>
            <div class="card shadow mb-4">
                <div class="card-header">Cập nhật Trạng thái</div>
                <div class="card-body">
                    <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="mb-3">
                            <label for="order_status" class="form-label">Trạng thái đơn hàng</label>
                            <select name="status" id="order_status" class="form-select" {{ $order->status == 'delivered' ? 'disabled' : '' }}>
                                <option value="pending" @selected($order->status == 'pending')>Chờ xử lý</option>
                                <option value="processing" @selected($order->status == 'processing')>Đang xử lý</option>
                                <option value="shipped_to_shipper" @selected($order->status == 'shipped_to_shipper')>Đã giao cho shipper</option>
                                <option value="shipping" @selected($order->status == 'shipping')>Đang giao</option>
                                <option value="delivered" @selected($order->status == 'delivered')>Đã giao</option>
                                <option value="cancelled" @selected($order->status == 'cancelled')>Đã hủy</option>
                            </select>
                            @if($order->status == 'delivered')
                                <small class="text-muted">Không thể thay đổi trạng thái đơn hàng đã được nhận.</small>
                            @endif
                        </div>
                        <button type="submit" class="btn btn-primary" {{ $order->status == 'delivered' ? 'disabled' : '' }}>Cập nhật</button>
                    </form>

                    {{-- Logic hiển thị nút hủy và thông báo --}}
                    @if($order->status == 'cancelled')
                        <div class="alert alert-info mt-3">
                            Đơn hàng đã bị hủy.
                            @if($order->payment && in_array($order->payment->method, ['vnpay', 'momo']))
                                Vui lòng liên hệ hỗ trợ để xử lý hoàn tiền cho giao dịch online.
                            @endif
                        </div>
                    @elseif($order->status == 'delivered')
                        {{-- Thông báo cụ thể khi đơn hàng đã được xác nhận nhận bởi khách hàng --}}
                        <div class="alert alert-success mt-3">
                            Đơn hàng này đã được khách hàng xác nhận đã nhận.
                        </div>
                    @elseif($order->isCancellable())
                        <hr>
                        <form action="{{ route('admin.orders.cancel', $order) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này? Hành động này không thể hoàn tác.');">
                            @csrf
                            <button type="submit" class="btn btn-danger">Hủy đơn hàng</button>
                        </form>
                    @else
                        <div class="alert alert-warning mt-3">
                            Đơn hàng này không thể hủy được do trạng thái hiện tại (
                                @php
                                    $statusText = '';
                                    switch ($order->status) {
                                        case 'shipped_to_shipper': $statusText = 'Đã giao cho shipper'; break;
                                        case 'shipping': $statusText = 'Đang giao'; break;
                                        case 'delivered': $statusText = 'Đã giao'; break;
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
