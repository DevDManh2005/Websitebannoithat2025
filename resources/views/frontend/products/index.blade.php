@extends('layouts.app')

@section('content')
<div class="container my-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Trang chủ</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $category->name }}</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-3">
            <h4>Bộ lọc</h4>
            <hr>
            {{-- Nơi dành cho các bộ lọc sản phẩm sau này --}}
        </div>
        <div class="col-lg-9">
            <h1 class="mb-4">{{ $category->name }}</h1>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                @forelse($products as $product)
                    <div class="col">
                        <div class="card h-100 shadow-sm position-relative">
                            @php
                                $primaryImage = $product->images->first();
                                $mainVariant = $product->variants->first();
                                $isWishlisted = in_array($product->id, $wishlistProductIds);
                                
                                // Xử lý đường dẫn ảnh
                                $imageUrl = $primaryImage->image_url ?? 'https://via.placeholder.com/300x200';
                                if ($primaryImage && !Str::startsWith($primaryImage->image_url, 'http')) {
                                    $imageUrl = asset('storage/' . $primaryImage->image_url);
                                }
                            @endphp

                            <button class="btn btn-outline-danger btn-sm rounded-circle position-absolute top-0 end-0 m-2 toggle-wishlist-btn {{ $isWishlisted ? 'active' : '' }}"
                                    data-product-id="{{ $product->id }}"
                                    title="Thêm vào danh sách yêu thích">
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
                @empty
                    <div class="col-12">
                        <p class="text-center">Chưa có sản phẩm nào trong danh mục này.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-4 d-flex justify-content-center">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@include('frontend.partials.wishlist-script')
@endsection
