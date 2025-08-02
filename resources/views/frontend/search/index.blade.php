@extends('layouts.app')

@section('title', 'Kết quả tìm kiếm cho "' . e($query) . '"')

@section('content')
<div class="container my-5">
    <div class="text-center mb-5">
        <h1 class="fw-bold">Kết quả tìm kiếm</h1>
        <p class="text-muted">Đã tìm thấy {{ $products->count() }} sản phẩm cho từ khóa: "<strong>{{ e($query) }}</strong>"</p>
    </div>

    @if($products->isNotEmpty())
        <div class="row g-4">
            @foreach($products as $product)
                <div class="col-6 col-md-4 col-lg-3">
                    {{-- Sử dụng lại component thẻ sản phẩm --}}
                    @include('frontend.components.product-card', ['product' => $product])
                </div>
            @endforeach
        </div>

        {{-- Phân trang (nếu bạn có phân trang trong controller) --}}
        <div class="d-flex justify-content-center mt-5">
            {{-- {{ $products->links() }} --}}
        </div>
    @else
        <div class="text-center py-5">
            <i class="bi bi-search fs-1 text-muted"></i>
            <h4 class="mt-3">Không tìm thấy sản phẩm nào</h4>
            <p class="text-muted">Vui lòng thử lại với một từ khóa khác.</p>
            <a href="{{ route('home') }}" class="btn btn-primary mt-3">Quay lại trang chủ</a>
        </div>
    @endif
</div>
@endsection
