@extends('layouts.app')

@section('title', 'Danh sách yêu thích')

@section('content')
<div class="container my-5">
    <div class="text-center mb-5">
        <h1 class="fw-bold">Sản phẩm yêu thích</h1>
        <p class="text-muted">Đây là những sản phẩm bạn đã lưu lại.</p>
    </div>

    @if($wishlistProducts->isNotEmpty())
        <div class="row g-4">
            @foreach($wishlistProducts as $product)
                <div class="col-6 col-md-4 col-lg-3">
                    {{-- Sử dụng lại component thẻ sản phẩm --}}
                    @include('frontend.components.product-card', ['product' => $product])
                </div>
            @endforeach
        </div>

        {{-- Phân trang --}}
        <div class="d-flex justify-content-center mt-5">
            {{ $wishlistProducts->links() }}
        </div>
    @else
        <div class="text-center py-5">
            <i class="bi bi-heart fs-1 text-muted"></i>
            <h4 class="mt-3">Danh sách yêu thích của bạn đang trống</h4>
            <p class="text-muted">Hãy khám phá thêm các sản phẩm và lưu lại những món đồ bạn yêu thích nhé!</p>
            <a href="{{ route('home') }}" class="btn btn-primary mt-3">Bắt đầu mua sắm</a>
        </div>
    @endif
</div>
@endsection
