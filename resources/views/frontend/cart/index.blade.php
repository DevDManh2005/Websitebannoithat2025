@extends('layouts.app')

@section('content')
<div class="container my-4">
    <h1 class="mb-4">Giỏ hàng của bạn</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($cartItems->isNotEmpty())
        <div class="row">
            {{-- Danh sách sản phẩm trong giỏ --}}
            <div class="col-lg-8">
                @foreach($cartItems as $item)
                    <div class="card mb-3">
                        <div class="row g-0">
                            <div class="col-md-2">
                                @php
                                    $image = $item->variant->product->images->first();
                                    $imageUrl = $image->image_url ?? 'https://via.placeholder.com/150';
                                    if ($image && !Str::startsWith($image->image_url, 'http')) {
                                        $imageUrl = asset('storage/' . $image->image_url);
                                    }
                                    $price = $item->variant->sale_price > 0 && $item->variant->sale_price < $item->variant->price
                                           ? $item->variant->sale_price
                                           : $item->variant->price;
                                @endphp
                                <img src="{{ $imageUrl }}" class="img-fluid rounded-start" alt="{{ $item->variant->product->name }}">
                            </div>
                            <div class="col-md-10">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $item->variant->product->name }}</h5>
                                    <p class="card-text">
                                        <small class="text-muted">
                                            @foreach($item->variant->attributes as $key => $value)
                                                {{ ucfirst($key) }}: {{ $value }}
                                            @endforeach
                                        </small>
                                    </p>
                                    <p class="card-text fw-bold">{{ number_format($price) }} ₫</p>
                                    
                                    <div class="d-flex justify-content-between align-items-center">
                                        {{-- Form cập nhật số lượng --}}
                                        <form action="{{ route('cart.update', $item->id) }}" method="POST" class="d-flex align-items-center">
                                            @csrf
                                            @method('PATCH')
                                            <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" class="form-control" style="width: 70px;">
                                            <button type="submit" class="btn btn-outline-primary btn-sm ms-2">Cập nhật</button>
                                        </form>
                                        
                                        {{-- Form xóa sản phẩm --}}
                                        <form action="{{ route('cart.remove', $item->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                                <i class="fas fa-trash"></i> Xóa
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Tóm tắt đơn hàng --}}
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Tóm tắt đơn hàng</h5>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <span>Tạm tính</span>
                            <strong>{{ number_format($totalPrice) }} ₫</strong>
                        </div>
                        <div class="d-grid mt-3">
                            {{-- CẬP NHẬT LẠI ĐƯỜNG DẪN Ở ĐÂY --}}
                            <a href="{{ route('checkout.index') }}" class="btn btn-primary">Tiến hành thanh toán</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-5">
            <p>Giỏ hàng của bạn đang trống.</p>
            <a href="{{ url('/') }}" class="btn btn-primary">Tiếp tục mua sắm</a>
        </div>
    @endif
</div>
@endsection
