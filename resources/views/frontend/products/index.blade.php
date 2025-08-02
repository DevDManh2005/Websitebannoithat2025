@extends('layouts.app')

@section('title', 'Tất cả sản phẩm')

@section('content')
<div class="container my-5">
    <div class="row">
        {{-- Cột bên trái: Bộ lọc --}}
        <div class="col-lg-3">
            @include('frontend.components.filter-sidebar', [
                'categories' => $categories,
                'brands' => $brands,
                'max_price' => $max_price
            ])
        </div>

        {{-- Cột bên phải: Danh sách sản phẩm --}}
        <div class="col-lg-9">
            <h1 class="fw-bold mb-3">Tất cả sản phẩm</h1>

            {{-- Thanh sắp xếp và thông tin --}}
            <div class="d-flex justify-content-between align-items-center mb-4 p-2 bg-light rounded">
                <span class="text-muted small">Hiển thị {{ $products->firstItem() }}-{{ $products->lastItem() }} trong tổng số {{ $products->total() }} sản phẩm</span>
                <form action="{{ url()->current() }}" method="GET" id="sort-form">
                    {{-- Giữ lại các tham số lọc hiện tại khi sắp xếp --}}
                    @foreach(request()->except(['sort', 'page']) as $key => $value)
                        @if(is_array($value))
                            @foreach($value as $v)
                                <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
                            @endforeach
                        @else
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endif
                    @endforeach
                    <select class="form-select form-select-sm" name="sort" style="width: auto;" onchange="document.getElementById('sort-form').submit();">
                        <option value="latest" @selected(request('sort') == 'latest')>Mới nhất</option>
                        <option value="price_asc" @selected(request('sort') == 'price_asc')>Giá: Thấp đến cao</option>
                        <option value="price_desc" @selected(request('sort') == 'price_desc')>Giá: Cao đến thấp</option>
                    </select>
                </form>
            </div>

            {{-- Lưới sản phẩm --}}
            @if($products->isNotEmpty())
                <div class="row g-4">
                    @foreach($products as $product)
                        <div class="col-6 col-md-4">
                            @include('frontend.components.product-card', ['product' => $product])
                        </div>
                    @endforeach
                </div>

                {{-- Phân trang --}}
                <div class="d-flex justify-content-center mt-5">
                    {{ $products->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <p>Không tìm thấy sản phẩm nào phù hợp với tiêu chí của bạn.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
