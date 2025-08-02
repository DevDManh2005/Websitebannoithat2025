@extends('admins.layouts.app')

@section('title', 'Quản lý Đơn hàng')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Danh sách Đơn hàng</h1>

    <div class="card shadow mb-4">
        <div class="card-body">
            {{-- Thanh điều hướng trạng thái --}}
            <ul class="nav nav-pills mb-3">
                <li class="nav-item">
                    <a class="nav-link {{ $currentStatus == 'all' ? 'active' : '' }}" href="{{ route('admin.orders.index', ['status' => 'all']) }}">Tất cả</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $currentStatus == 'pending' ? 'active' : '' }}" href="{{ route('admin.orders.index', ['status' => 'pending']) }}">Chờ xử lý</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $currentStatus == 'processing' ? 'active' : '' }}" href="{{ route('admin.orders.index', ['status' => 'processing']) }}">Đang xử lý</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $currentStatus == 'shipped_to_shipper' ? 'active' : '' }}" href="{{ route('admin.orders.index', ['status' => 'shipped_to_shipper']) }}">Đã giao cho shipper</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $currentStatus == 'shipping' ? 'active' : '' }}" href="{{ route('admin.orders.index', ['status' => 'shipping']) }}">Đang giao</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $currentStatus == 'delivered' ? 'active' : '' }}" href="{{ route('admin.orders.index', ['status' => 'delivered']) }}">Đã giao</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $currentStatus == 'cancelled' ? 'active' : '' }}" href="{{ route('admin.orders.index', ['status' => 'cancelled']) }}">Đã hủy</a>
                </li>
            </ul>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Mã ĐH</th>
                            <th>Khách hàng</th>
                            <th>Ngày đặt</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                            <tr>
                                <td>{{ $order->order_code }}</td>
                                <td>{{ $order->user->name ?? 'N/A' }}</td>
                                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ number_format($order->final_amount) }} ₫</td>
                                <td>
                                    @php
                                        $statusConfig = [
                                            'pending' => ['class' => 'bg-warning text-dark', 'text' => 'Chờ xử lý'],
                                            'processing' => ['class' => 'bg-info text-dark', 'text' => 'Đang xử lý'],
                                            'shipped_to_shipper' => ['class' => 'bg-secondary', 'text' => 'Đã giao cho shipper'],
                                            'shipping' => ['class' => 'bg-primary', 'text' => 'Đang giao'],
                                            'delivered' => ['class' => 'bg-success', 'text' => 'Đã giao'],
                                            'cancelled' => ['class' => 'bg-danger', 'text' => 'Đã hủy'],
                                        ];
                                        $status = $statusConfig[$order->status] ?? ['class' => 'bg-light text-dark', 'text' => 'Không xác định'];
                                    @endphp
                                    <span class="badge {{ $status['class'] }}">{{ $status['text'] }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-info btn-sm">Xem</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Không có đơn hàng nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center">
                {{ $orders->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection