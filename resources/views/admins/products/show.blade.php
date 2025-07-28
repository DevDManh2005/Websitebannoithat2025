@extends('layouts.app')

@section('content')
<div class="container my-4">
    {{-- Hiển thị thông báo thành công (nếu có) --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Trang chủ</a></li>
            @if($product->category)
            <li class="breadcrumb-item">
                <a href="{{ route('category.show', $product->category->slug) }}">{{ $product->category->name }}</a>
            </li>
            @endif
            <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-6">
            @php
                $primaryImage = $product->images->where('is_primary', true)->first();
                $mainImageUrl = $primaryImage ? (Str::startsWith($primaryImage->image_url, 'http') ? $primaryImage->image_url : asset('storage/' . $primaryImage->image_url)) : 'https://placehold.co/600x400';
            @endphp
            <div class="mb-3">
                <img id="main-product-image" src="{{ $mainImageUrl }}" class="img-fluid rounded" alt="{{ $product->name }}">
            </div>
            @if($product->images->where('is_primary', false)->count() > 0)
            <div class="d-flex">
                @foreach($product->images as $image)
                    @php
                        $thumbnailUrl = $image->image_url;
                        if (!Str::startsWith($thumbnailUrl, 'http')) {
                            $thumbnailUrl = asset('storage/' . $thumbnailUrl);
                        }
                    @endphp
                    <div class="me-2" style="width: 80px; cursor: pointer;">
                        <img src="{{ $thumbnailUrl }}" class="img-thumbnail thumbnail-image" alt="Thumbnail" onclick="document.getElementById('main-product-image').src = this.src;">
                    </div>
                @endforeach
            </div>
            @endif
        </div>

        <div class="col-lg-6">
            <h1>{{ $product->name }}</h1>
            @if($product->brand)
            <p class="text-muted">Thương hiệu: <a href="#">{{ $product->brand->name }}</a></p>
            @endif

            @php
                $mainVariant = $product->variants->where('is_main_variant', true)->first() ?? $product->variants->first();
                // Kiểm tra Auth::check() trước khi truy cập Auth::user()
                $isWishlisted = Auth::check() && Auth::user()->wishlist->contains($product->id);

                // Chuẩn bị dữ liệu thuộc tính để hiển thị
                $attributeGroups = [];
                foreach ($product->variants as $variant) {
                    // Đảm bảo $variant->attributes luôn là một mảng, ngay cả khi null hoặc không hợp lệ
                    $currentAttributes = (array)($variant->attributes ?? []); 
                    foreach ($currentAttributes as $key => $value) { 
                        $attributeGroups[$key][] = $value;
                    }
                }
                foreach ($attributeGroups as $key => $values) {
                    $attributeGroups[$key] = array_unique($values);
                }
            @endphp
            
            <div id="product-price-display" class="fs-3 fw-bold text-danger mb-3">
                @if($mainVariant)
                    @if($mainVariant->sale_price > 0 && $mainVariant->sale_price < $mainVariant->price)
                        <span class="fs-5 text-muted text-decoration-line-through">{{ number_format($mainVariant->price) }} ₫</span>
                        {{ number_format($mainVariant->sale_price) }} ₫
                    @else
                        {{ number_format($mainVariant->price) }} ₫
                    @endif
                @else
                    Giá: Liên hệ
                @endif
            </div>
            <hr>

            {{-- FORM THÊM VÀO GIỎ HÀNG --}}
            <form action="{{ route('cart.add') }}" method="POST" id="add-to-cart-form">
                @csrf
                <input type="hidden" name="variant_id" id="selected-variant-id" value="{{ $mainVariant->id ?? '' }}">

                @foreach ($attributeGroups as $attributeName => $values)
                <div class="mb-3 variant-group">
                    <label class="form-label"><strong>{{ ucfirst($attributeName) }}:</strong></label>
                    <div class="d-flex flex-wrap">
                        @foreach ($values as $value)
                            <div class="me-2 mb-2">
                                <input type="radio" class="btn-check variant-option" name="attribute_{{ Str::slug($attributeName) }}" id="attr_{{ Str::slug($attributeName) }}_{{ Str::slug($value) }}" value="{{ $value }}" autocomplete="off">
                                <label class="btn btn-outline-secondary" for="attr_{{ Str::slug($attributeName) }}_{{ Str::slug($value) }}">{{ $value }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endforeach

                <div class="mb-3">
                    <label for="quantity" class="form-label"><strong>Số lượng:</strong></label>
                    <input type="number" name="quantity" id="quantity" class="form-control" value="1" min="1" style="width: 100px;">
                </div>

                <div class="d-flex align-items-center">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-cart-plus"></i> Thêm vào giỏ hàng
                    </button>
                    <button type="button" class="btn btn-outline-danger btn-lg ms-3 toggle-wishlist-btn {{ $isWishlisted ? 'active' : '' }}"
                            data-product-id="{{ $product->id }}"
                            title="Thêm vào danh sách yêu thích">
                        <i class="fas fa-heart"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-12">
            <h3>Mô tả sản phẩm</h3>
            <hr>
            <div class="product-description">{!! $product->description !!}</div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@include('frontend.partials.wishlist-script')

<script>
document.addEventListener('DOMContentLoaded', function() {
    const variantsData = @json($product->variants);
    const priceDisplay = document.getElementById('product-price-display');
    const selectedVariantInput = document.getElementById('selected-variant-id');
    const variantOptionRadios = document.querySelectorAll('.variant-option');

    function updatePriceAndVariant() {
        const selectedAttributes = {};
        let allOptionsSelected = true;

        document.querySelectorAll('.variant-group').forEach(group => {
            const selectedRadio = group.querySelector('.variant-option:checked');
            if (selectedRadio) {
                // Extract attribute name from the label's text content, removing ': '
                const attributeNameLabel = document.querySelector(`label[for="${selectedRadio.id}"]`).closest('.variant-group').querySelector('label.form-label strong').textContent;
                const attributeName = attributeNameLabel.replace(':', '').trim();
                selectedAttributes[attributeName] = selectedRadio.value;
            } else {
                allOptionsSelected = false;
            }
        });
        
        if (!allOptionsSelected) {
            // If not all options are selected, reset to main variant or default price
            // For now, we'll just return and keep the initial price
            // You might want to show a "Please select options" message here
            return;
        }

        const matchedVariant = variantsData.find(variant => {
            // Ensure variant.attributes is an array before comparing
            const variantAttrs = variant.attributes ?? {}; 

            // Check if the number of attributes matches
            if (Object.keys(variantAttrs).length !== Object.keys(selectedAttributes).length) {
                return false;
            }
            // Check if all selected attributes match a variant's attributes
            for (const key in selectedAttributes) {
                if (variantAttrs[key] !== selectedAttributes[key]) {
                    return false;
                }
            }
            return true;
        });

        if (matchedVariant) {
            selectedVariantInput.value = matchedVariant.id;
            
            const price = parseFloat(matchedVariant.price);
            const salePrice = parseFloat(matchedVariant.sale_price) || 0;

            const formatCurrency = (number) => new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(number).replace('₫', '₫');

            let newPriceHtml = '';
            if (salePrice > 0 && salePrice < price) {
                newPriceHtml = `<span class="fs-5 text-muted text-decoration-line-through">${formatCurrency(price)}</span> ${formatCurrency(salePrice)}`;
            } else {
                newPriceHtml = formatCurrency(price);
            }
            priceDisplay.innerHTML = newPriceHtml;
        } else {
            // If no variant matches the selected attributes, display a message or default price
            priceDisplay.innerHTML = '<span class="text-danger">Không tìm thấy biến thể phù hợp.</span>';
            selectedVariantInput.value = ''; // Clear selected variant ID
        }
    }

    variantOptionRadios.forEach(radio => {
        radio.addEventListener('change', updatePriceAndVariant);
    });

    // Automatically select the first option for each attribute group on load
    document.querySelectorAll('.variant-group').forEach(group => {
        const firstRadio = group.querySelector('.variant-option');
        if (firstRadio) {
            firstRadio.checked = true;
        }
    });
    updatePriceAndVariant(); // Call once on load to set initial price based on default selections
});
</script>