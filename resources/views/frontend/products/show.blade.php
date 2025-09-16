@extends('layouts.app')

@section('title', $product->name)

@section('content')
    {{-- BREADCRUMB --}}
    <div class="py-3 card-glass border-bottom">
        <div class="container" data-aos="fade-in"></div>
    </div>

@php
    use Illuminate\Support\Str;

    // Lấy tất cả reviews đã duyệt, tách root reviews và build replies map
    $allApproved = $product->approvedReviews()->with('user')->latest()->get();
    $rootReviews = $allApproved->filter(fn($r) => !Str::startsWith($r->review, '[reply:#'));
    $rootCount = $rootReviews->count();

    $repliesMap = [];
    foreach ($allApproved as $r) {
        if (Str::startsWith($r->review, '[reply:#')) {
            if (preg_match('/^\[reply:#(\d+)\]/', $r->review, $m)) {
                $parentId = (int) $m[1];
                $repliesMap[$parentId][] = $r;
            }
        }
    }

    // role/permission an toàn
    $roleName = optional(optional(auth()->user())->role)->name ?? '';
    $isStaffOrAdmin = auth()->check() && in_array($roleName, ['admin','nhanvien'], true);

    // quyền reply
    $canReply = $isStaffOrAdmin || ($userHasPurchased ?? false);
    $currentUserId = auth()->id();

    // Kiểm tra user đã có review gốc chưa (loại trừ replies)
    $hasReviewed = false;
    if (auth()->check()) {
        $hasReviewed = $allApproved
            ->where('user_id', auth()->id())
            ->filter(fn($r) => !Str::startsWith($r->review, '[reply:#'))
            ->isNotEmpty();
    }

    // chuẩn bị related fallback (nếu controller không truyền $relatedProducts)
    $related = $relatedProducts ?? collect();
    if ($related->isEmpty() && $product->categories->isNotEmpty()) {
        $catIds = $product->categories->pluck('id')->toArray();
        $related = \App\Models\Product::with(['images','variants'])
            ->where('is_active', 1)
            ->where('id', '!=', $product->id)
            ->whereHas('categories', function($q) use ($catIds) {
                $q->whereIn('categories.id', $catIds);
            })
            ->orderBy('created_at', 'desc')
            ->limit(4)
            ->get();
    }
@endphp

<div class="container my-5">
    <div class="row g-4">
        {{-- GALLERY --}}
        <div class="col-lg-6" data-aos="fade-right">
            @includeIf('frontend.products.partials.gallery', ['product' => $product])
        </div>

        {{-- INFO / ACTIONS --}}
        <div class="col-lg-6" data-aos="fade-left" data-aos-delay="100">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <h1 class="fw-bold mb-0 text-dark pe-3">{{ $product->name }}</h1>
                <div class="ms-3">
                    <x-wishlist-icon :productId="$product->id" :isWishlisted="$isWished" />
                </div>
            </div>

            <div class="d-flex align-items-center mb-3">
                @includeIf('frontend.components.star-rating', ['rating' => $product->average_rating])
                <a href="#reviews-section" class="ms-3 text-muted small text-decoration-none border-start ps-3">
                    {{ $rootCount }} đánh giá
                </a>
            </div>

            <div class="product-price-box card-glass p-4 rounded-4 mb-4">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge badge-soft-brand text-uppercase fw-semibold">Giá</span>
                    <h3 class="text-brand fw-bolder mb-0" id="product-price-display">Vui lòng chọn thuộc tính</h3>
                </div>
                <small class="text-muted d-block mt-2">Mã SP: <span id="product-sku">N/A</span></small>
            </div>

            <form id="action-form" class="product-actions">
                @csrf
                <input type="hidden" name="product_variant_id" id="selected-variant-id">
                @includeIf('frontend.products.partials.actions', ['product' => $product])
            </form>
        </div>
    </div>

    {{-- DESCRIPTION --}}
    <div class="row mt-5" data-aos="fade-up" id="description-section">
        <div class="col-12">
            <div class="card-glass p-4 rounded-4">
                <h5 class="fw-bold mb-3">Mô tả sản phẩm</h5>
                <div class="content">{!! $product->description !!}</div>
            </div>
        </div>
    </div>

    {{-- REVIEWS --}}
    <div class="row mt-4" data-aos="fade-up" id="reviews-section">
        <div class="col-12">
            <h4 class="mb-3">Đánh giá ({{ $rootCount }})</h4>

            @forelse($rootReviews as $review)
                @includeIf('frontend.products.partials.review-item', [
                    'review' => $review,
                    'repliesMap' => $repliesMap,
                    'canReply' => $canReply,
                    'currentUserId' => $currentUserId,
                ])
            @empty
                <p class="mb-0">Chưa có đánh giá nào cho sản phẩm này.</p>
            @endforelse

            {{-- Form đánh giá gốc --}}
            @auth
                @if (!$hasReviewed || $isStaffOrAdmin)
                    @includeIf('frontend.components.review-form', ['product' => $product])
                @else
                    <div class="alert alert-info mt-4">Bạn đã đánh giá sản phẩm này. Bạn có thể trả lời, sửa hoặc xóa đánh giá của mình.</div>
                @endif
            @else
                <div class="alert alert-info mt-4">
                    Vui lòng <a href="{{ route('login.form') }}">đăng nhập</a> để đánh giá.
                </div>
            @endauth
        </div>
    </div>

    {{-- RELATED --}}
    @if($related->isNotEmpty())
        <div class="row mt-4" data-aos="fade-up" id="related-section">
            <div class="col-12">
                <div class="card-glass p-4 rounded-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0">Sản phẩm liên quan</h5>
                    </div>
                    <div class="row g-3 g-md-4">
                        @foreach ($related as $rp)
                            @includeIf('frontend.products.partials.card', ['product' => $rp])
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('styles')
@endpush

@push('scripts-page')
@endpush