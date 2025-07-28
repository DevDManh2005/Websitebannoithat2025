@extends('layouts.app')

@section('content')
<div class="container my-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="{{ route('profile.show') }}">Tài khoản</a></li>
            <li class="breadcrumb-item active" aria-current="page">Danh sách yêu thích</li>
        </ol>
    </nav>

    <h1 class="mb-4">Sản phẩm yêu thích</h1>

    @if($wishlistProducts->isNotEmpty())
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
            @foreach($wishlistProducts as $product)
                <div class="col">
                    <div class="card h-100 shadow-sm position-relative">
                        @php
                            $primaryImage = $product->images->first();
                            $mainVariant = $product->variants->first();
                            
                            $imageUrl = $primaryImage->image_url ?? 'https://via.placeholder.com/300x200';
                            if ($primaryImage && !Str::startsWith($primaryImage->image_url, 'http')) {
                                $imageUrl = asset('storage/' . $primaryImage->image_url);
                            }
                        @endphp

                        {{-- Nút xóa khỏi danh sách yêu thích --}}
                        <button class="btn btn-danger btn-sm rounded-circle position-absolute top-0 end-0 m-2 toggle-wishlist-btn active"
                                data-product-id="{{ $product->id }}"
                                title="Xóa khỏi danh sách yêu thích">
                            <i class="fas fa-heart"></i>
                        </button>

                        <a href="{{ route('product.show', $product->slug) }}">
                            <img src="{{ $imageUrl }}" class="card-img-top" alt="{{ $product->name }}">
                        </a>
                        <div class="card-body">
                            <h5 class="card-title">
                                <a href="{{ route('product.show', $product->slug) }}" class="text-decoration-none text-dark">{{ Str::limit($product->name, 50) }}</a>
                            </h5>
                            <p class="card-text text-danger fw-bold">
                                @if($mainVariant)
                                    @if($mainVariant->sale_price > 0 && $mainVariant->sale_price < $mainVariant->price)
                                        <span class="text-muted text-decoration-line-through">{{ number_format($mainVariant->price) }} ₫</span>
                                        {{ number_format($mainVariant->sale_price) }} ₫
                                    @else
                                        {{ number_format($mainVariant->price) }} ₫
                                    @endif
                                @else
                                    Liên hệ
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-5">
            <p>Danh sách yêu thích của bạn đang trống.</p>
            <a href="{{ url('/') }}" class="btn btn-primary">Tiếp tục mua sắm</a>
        </div>
    @endif
</div>
@endsection

@section('scripts')
{{-- Sử dụng lại script đã có để nút yêu thích hoạt động --}}
@include('frontend.partials.wishlist-script')
@endsection
