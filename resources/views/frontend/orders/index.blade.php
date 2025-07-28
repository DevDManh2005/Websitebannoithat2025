@extends('layouts.app')

@section('content')
<div class="container my-4">
    <h1 class="mb-4">Lịch sử mua hàng</h1>

    <div class="card">
        <div class="card-body">
            {{-- Thanh điều hướng trạng thái --}}
            <ul class="nav nav-pills mb-3">
                <li class="nav-item">
                    <a class="nav-link {{ $currentStatus == 'all' ? 'active' : '' }}" href="{{ route('orders.index', ['status' => 'all']) }}">Tất cả</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $currentStatus == 'pending' ? 'active' : '' }}" href="{{ route('orders.index', ['status' => 'pending']) }}">Chờ xử lý</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $currentStatus == 'processing' ? 'active' : '' }}" href="{{ route('orders.index', ['status' => 'processing']) }}">Đang xử lý</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $currentStatus == 'shipped_to_shipper' ? 'active' : '' }}" href="{{ route('orders.index', ['status' => 'shipped_to_shipper']) }}">Đã giao cho shipper</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $currentStatus == 'shipping' ? 'active' : '' }}" href="{{ route('orders.index', ['status' => 'shipping']) }}">Đang giao</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $currentStatus == 'delivered' ? 'active' : '' }}" href="{{ route('orders.index', ['status' => 'delivered']) }}">Đã giao</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $currentStatus == 'cancelled' ? 'active' : '' }}" href="{{ route('orders.index', ['status' => 'cancelled']) }}">Đã hủy</a>
                </li>
            </ul>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @if($orders->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Mã đơn hàng</th>
                                <th>Ngày đặt</th>
                                <th>Tổng tiền</th>
                                <th>Trạng thái</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                                <tr>
                                    <td><strong>{{ $order->order_code }}</strong></td>
                                    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                    <td>{{ number_format($order->final_amount) }} ₫</td>
                                    <td>
                                        @php
                                            $badgeClass = '';
                                            $statusText = '';
                                            switch ($order->status) {
                                                case 'pending': $badgeClass = 'bg-warning text-dark'; $statusText = 'Chờ xử lý'; break;
                                                case 'processing': $badgeClass = 'bg-info text-dark'; $statusText = 'Đang xử lý'; break;
                                                case 'shipped_to_shipper': $badgeClass = 'bg-secondary'; $statusText = 'Đã giao cho shipper'; break;
                                                case 'shipping': $badgeClass = 'bg-primary'; $statusText = 'Đang giao'; break;
                                                case 'delivered': $badgeClass = 'bg-success'; $statusText = 'Đã giao'; break;
                                                case 'cancelled': $badgeClass = 'bg-danger'; $statusText = 'Đã hủy'; break;
                                                default: $badgeClass = 'bg-secondary'; $statusText = 'Không xác định'; break;
                                            }
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">{{ $statusText }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-primary">Xem chi tiết</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $orders->links() }}
                </div>
            @else
                <p class="text-center">Bạn chưa có đơn hàng nào.</p>
            @endif
        </div>
    </div>
</div>
@endsection