@extends('layouts.app')

@section('title', $product->name)

@section('content')
    {{-- =================== BREADCRUMB =================== --}}
    <div class="py-3 card-glass border-bottom">
        <div class="container" data-aos="fade-in">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                    @if ($category = $product->categories->first())
                        <li class="breadcrumb-item">
                            <a href="{{ route('category.show', $category->slug) }}">{{ $category->name }}</a>
                        </li>
                    @endif
                    <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
                </ol>
            </nav>
        </div>
    </div>

    @php
    use Illuminate\Support\Str;

    // Đếm chỉ review gốc (không tính phản hồi)
    $rootCount = $product->approvedReviews->filter(fn($r) => !Str::startsWith($r->review, '[reply:#'))->count();

    // Chuẩn bị dữ liệu review + replies
    $allApproved = $product->approvedReviews()->with('user')->latest()->get();
    $rootReviews = $allApproved->filter(fn($r) => !Str::startsWith($r->review, '[reply:#'));

    $repliesMap = [];
    foreach ($allApproved as $r) {
        if (Str::startsWith($r->review, '[reply:#')) {
            if (preg_match('/^\[reply:#(\d+)\]/', $r->review, $m)) {
                $parentId = (int) $m[1];
                $repliesMap[$parentId][] = $r;
            }
        }
    }

    // NOTE: đảm bảo biến $canReply tồn tại cho view (dùng khi hiển thị form trả lời)
    $isStaffOrAdmin = in_array(auth()->user()->role->name ?? '', ['admin','nhanvien']);
    $canReply = $isStaffOrAdmin || ($userHasPurchased ?? false);
    $currentUserId = auth()->id();
@endphp

    <div class="container my-5">
        {{-- =================== HÀNG 1: GALLERY + INFO/ACTIONS =================== --}}
        <div class="row g-4 g-lg-5">
            {{-- GALLERY --}}
            <div class="col-lg-6" data-aos="fade-right">
                <div class="product-gallery">
                    <div class="swiper main-image-swiper rounded-4 shadow-sm">
                        <div class="swiper-wrapper">
                            @forelse($product->images as $image)
                                <div class="swiper-slide">
                                    <img src="{{ $image->image_url_path }}" class="w-100 h-100 object-fit-cover"
                                        alt="{{ $product->name }}">
                                </div>
                            @empty
                                <div class="swiper-slide">
                                    <img src="https://placehold.co/800x800?text=No+Image"
                                        class="w-100 h-100 object-fit-cover" alt="No Image Available">
                                </div>
                            @endforelse
                        </div>
                        <div class="swiper-button-next"></div>
                        <div class="swiper-button-prev"></div>
                    </div>

                    @if ($product->images->count() > 1)
                        <div class="swiper thumbnail-swiper mt-3">
                            <div class="swiper-wrapper">
                                @foreach ($product->images as $image)
                                    <div class="swiper-slide">
                                        <img src="{{ $image->image_url_path }}" class="img-fluid rounded-3 border"
                                            alt="Thumbnail">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
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
                    @include('frontend.components.star-rating', ['rating' => $product->average_rating])
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

                    {{-- Thuộc tính / biến thể --}}
                    <div id="variant-options" class="mb-4">
                        @foreach ($attributeGroups as $name => $values)
                            <div class="mb-3">
                                <label class="form-label fw-semibold">{{ Str::ucfirst($name) }}:</label>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach ($values as $value)
                                        @php $id = Str::slug($name . '-' . $value); @endphp
                                        <input type="radio" class="btn-check variant-option" name="{{ $name }}"
                                            value="{{ $value }}" id="{{ $id }}" autocomplete="off">
                                        <label class="btn btn-outline-brand btn-variant"
                                            for="{{ $id }}">{{ $value }}</label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Số lượng --}}
                    <div class="d-flex align-items-center mb-4">
                        <label class="me-3 fw-semibold">Số lượng:</label>
                        <div class="input-group quantity-group">
                            <button type="button" class="btn btn-outline-brand" id="quantity-minus"
                                aria-label="Giảm số lượng">−</button>
                            <input type="number" class="form-control text-center form-control-modern" name="quantity"
                                id="quantity-selector" value="1" min="1" inputmode="numeric">
                            <button type="button" class="btn btn-outline-brand" id="quantity-plus"
                                aria-label="Tăng số lượng">+</button>
                        </div>
                    </div>

                    {{-- CTA --}}
                    @auth
                        <div class="d-grid gap-2 d-sm-flex">
                            <button type="button" class="btn btn-outline-brand btn-lg flex-grow-1 rounded-pill"
                                id="add-to-cart-btn" disabled>
                                <i class="bi bi-cart-plus me-2"></i>Thêm vào giỏ
                            </button>
                            <button type="button" class="btn btn-brand btn-lg flex-grow-1 rounded-pill" id="buy-now-btn"
                                disabled>
                                <i class="bi bi-bag-check-fill me-2"></i>Mua ngay
                            </button>
                        </div>
                    @else
                        <a href="{{ route('login.form') }}" class="btn btn-brand btn-lg mt-3 w-100 rounded-pill">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Đăng nhập để mua hàng
                        </a>
                    @endauth
                </form>
            </div>
        </div>

        {{-- =================== HÀNG 2: MÔ TẢ SẢN PHẨM =================== --}}
        <div class="row mt-5" data-aos="fade-up" id="description-section">
            <div class="col-12">
                <div class="card-glass p-4 rounded-4">
                    <h5 class="fw-bold mb-3">Mô tả sản phẩm</h5>
                    <div class="content">{!! $product->description !!}</div>
                </div>
            </div>
        </div>

       {{-- =================== HÀNG 3: ĐÁNH GIÁ =================== --}}
        <div class="row mt-4" data-aos="fade-up" id="reviews-section">
            <div class="col-12">
                {{-- NOTE: Hiển thị reviews gốc (rootReviews) và replies từ $repliesMap --}}
                <h4 class="mb-3">Đánh giá ({{ $rootReviews->count() }})</h4>

                @forelse($rootReviews as $review)
                    @php $isOwner = auth()->check() && auth()->id() === $review->user_id; @endphp
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-start">
                                <img src="{{ $review->user->avatar_url ?? asset('images/default-avatar.png') }}" class="rounded-circle me-3" width="48" height="48" alt="avatar">
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <strong>{{ $review->user->name ?? 'Người dùng' }}</strong>
                                            <small class="text-muted ms-2">{{ $review->created_at->diffForHumans() }}</small>
                                        </div>
                                        <div class="small text-muted">
                                            @auth
                                                @if($isOwner)
                                                    <a class="link-secondary" data-bs-toggle="collapse" href="#edit-review-{{ $review->id }}">Sửa</a>
                                                    <form class="d-inline ms-2" method="POST" action="{{ route('reviews.destroy', $review) }}" onsubmit="return confirm('Xoá đánh giá này?')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-link link-danger p-0 align-baseline">Xoá</button>
                                                    </form>
                                                @endif

                                                @if($canReply)
                                                    <a class="link-secondary ms-2" data-bs-toggle="collapse" href="#reply-review-{{ $review->id }}">Trả lời</a>
                                                @endif
                                            @endauth
                                        </div>
                                    </div>

                                    <div class="mt-2">
                                        {{-- Hiển thị nội dung, loại bỏ prefix reply nếu có --}}
                                        {!! nl2br(e(preg_replace('/^\[reply:#\d+\]/', '', $review->review))) !!}
                                    </div>

                                    {{-- Edit review --}}
                                    @if($isOwner)
                                        <div class="collapse mt-2" id="edit-review-{{ $review->id }}">
                                            <form method="POST" action="{{ route('reviews.update', $review) }}">
                                                @csrf @method('PATCH')
                                                <div class="mb-2">
                                                    <textarea name="review" class="form-control" rows="3">{{ preg_replace('/^\[reply:#\d+\]/', '', $review->review) }}</textarea>
                                                </div>
                                                <button class="btn btn-sm btn-outline-brand">Lưu</button>
                                            </form>
                                        </div>
                                    @endif

                                    {{-- Reply form --}}
                                    @auth
                                        @if($canReply)
                                            <div class="collapse mt-2" id="reply-review-{{ $review->id }}">
                                                <form method="POST" action="{{ route('reviews.reply', $product) }}">
                                                    @csrf
                                                    <input type="hidden" name="parent_id" value="{{ $review->id }}">
                                                    <div class="mb-2">
                                                        <textarea name="review" class="form-control" rows="2" placeholder="Viết trả lời..."></textarea>
                                                    </div>
                                                    <button class="btn btn-sm btn-variant">Gửi trả lời</button>
                                                </form>
                                            </div>
                                        @endif
                                    @endauth

                                    {{-- Replies --}}
                                    @if(!empty($repliesMap[$review->id] ?? []))
                                        <div class="mt-3 ps-4 border-start">
                                            @foreach($repliesMap[$review->id] as $rep)
                                                @php $repOwner = auth()->check() && auth()->id() === $rep->user_id; @endphp
                                                <div class="mb-3">
                                                    <div class="d-flex justify-content-between small">
                                                        <div>
                                                            <strong>{{ $rep->user->name ?? 'Nhân viên' }}</strong>
                                                            <small class="text-muted ms-2">{{ $rep->created_at->diffForHumans() }}</small>
                                                        </div>
                                                        <div>
                                                            @auth
                                                                @if($repOwner)
                                                                    <a class="link-secondary" data-bs-toggle="collapse" href="#edit-reply-{{ $rep->id }}">Sửa</a>
                                                                    <form class="d-inline ms-2" method="POST" action="{{ route('reviews.destroy', $rep) }}" onsubmit="return confirm('Xoá phản hồi này?')">
                                                                        @csrf @method('DELETE')
                                                                        <button type="submit" class="btn btn-link link-danger p-0 align-baseline">Xoá</button>
                                                                    </form>
                                                                @endif
                                                            @endauth
                                                        </div>
                                                    </div>

                                                    <div class="mt-1">
                                                        {!! nl2br(e(preg_replace('/^\[reply:#\d+\]/', '', $rep->review))) !!}
                                                    </div>

                                                    @if($repOwner)
                                                        <div class="collapse mt-1" id="edit-reply-{{ $rep->id }}">
                                                            <form method="POST" action="{{ route('reviews.update', $rep) }}">
                                                                @csrf @method('PATCH')
                                                                <div class="mb-2">
                                                                    <textarea name="review" class="form-control" rows="2">{{ preg_replace('/^\[reply:#\d+\]/', '', $rep->review) }}</textarea>
                                                                </div>
                                                                <button class="btn btn-sm btn-outline-brand">Lưu</button>
                                                            </form>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="mb-0">Chưa có đánh giá nào cho sản phẩm này.</p>
                @endforelse

                {{-- Form đánh giá gốc (user đã mua hoặc admin/staff) --}}
                @auth
                    @php $isStaffOrAdmin = in_array(auth()->user()->role->name ?? '', ['admin','nhanvien']); @endphp
                    @if($userHasPurchased || $isStaffOrAdmin)
                        @include('frontend.components.review-form', ['product' => $product])
                    @else
                        <div class="alert alert-warning mt-4">Bạn cần mua sản phẩm để đánh giá.</div>
                    @endif
                @else
                    <div class="alert alert-info mt-4">
                        Vui lòng <a href="{{ route('login.form') }}">đăng nhập</a> để đánh giá.
                    </div>
                @endauth
            </div>
        </div>

        {{-- =================== HÀNG 4: SẢN PHẨM LIÊN QUAN =================== --}}
        @if ($related->isNotEmpty())
            <div class="row mt-4" data-aos="fade-up" id="related-section">
                <div class="col-12">
                    <div class="card-glass p-4 rounded-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0">Sản phẩm liên quan</h5>
                            {{-- Nếu muốn kéo bằng Swiper, có thể thêm nút điều hướng ở đây --}}
                        </div>
                        <div class="row g-3 g-md-4">
                            @foreach ($related as $rp)
                                @php
                                    // Tính giá hiển thị tối thiểu từ variants (nếu có)
                                    $baseMin = optional($rp->variants)->min('price');
                                    $saleMin = optional($rp->variants)->where('sale_price', '>', 0)->min('sale_price');
                                    $hasSale = $saleMin && $baseMin && $saleMin < $baseMin;

                                    $img =
                                        optional($rp->primaryImage)->image_url_path ??
                                        ($rp->images->first()->image_url_path ??
                                            'https://placehold.co/600x600?text=No+Image');
                                @endphp
                                <div class="col-6 col-md-4 col-lg-3">
                                    <div class="card card-glass h-100 rounded-4 overflow-hidden">
                                        <a href="{{ route('product.show', $rp->slug) }}" class="d-block">
                                            <div class="ratio ratio-1x1">
                                                <img src="{{ $img }}" class="w-100 h-100 object-fit-cover"
                                                    alt="{{ $rp->name }}">
                                            </div>
                                        </a>
                                        <div class="card-body p-3">
                                            <a href="{{ route('product.show', $rp->slug) }}"
                                                class="text-decoration-none text-dark">
                                                <div class="fw-semibold text-truncate">{{ $rp->name }}</div>
                                            </a>
                                            <div class="d-flex align-items-center mt-1">
                                                @include('frontend.components.star-rating', [
                                                    'rating' => $rp->average_rating,
                                                ])
                                            </div>
                                            <div class="mt-2">
                                                @if ($baseMin)
                                                    @if ($hasSale)
                                                        <span
                                                            class="text-muted text-decoration-line-through me-1">{{ number_format($baseMin) }}
                                                            ₫</span>
                                                        <strong class="text-brand">{{ number_format($saleMin) }}
                                                            ₫</strong>
                                                    @else
                                                        <strong class="text-brand">{{ number_format($baseMin) }}
                                                            ₫</strong>
                                                    @endif
                                                @else
                                                    <span class="text-muted small">Xem chi tiết</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div> {{-- row --}}
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- =================== MODAL: CẢNH BÁO SỐ LƯỢNG > 5 =================== --}}
    @php
        use Illuminate\Support\Facades\Route as RouteFacade;
        $contactRoute = RouteFacade::has('contact') ? route('contact') : null;
    @endphp
    <div class="modal fade" id="qtyLimitModal" tabindex="-1" aria-labelledby="qtyLimitModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 shadow">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-brand" id="qtyLimitModalLabel">Thông báo số lượng lớn</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">
                        Bạn đang chọn mua <strong>trên 5 sản phẩm</strong>. Vui lòng
                        <span class="text-brand fw-semibold">liên hệ với chúng tôi</span>
                        để được tư vấn, hướng dẫn và nhận thêm ưu đãi.
                    </p>
                    @if ($contactRoute)
                        <p class="mt-2 mb-0"><a href="{{ $contactRoute }}" class="text-decoration-none">Đi tới trang
                                liên hệ</a></p>
                    @endif
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-brand rounded-pill" data-bs-dismiss="modal">Đã hiểu</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* =================== Breadcrumb =================== */
        .breadcrumb-item a {
            text-decoration: none;
            color: var(--sand);
            transition: color 0.2s ease;
        }

        .breadcrumb-item a:hover {
            color: var(--brand);
        }

        .breadcrumb-item.active {
            color: var(--muted);
        }

        /* =================== Gallery =================== */
        .product-gallery .main-image-swiper {
            height: 520px;
            background: #fff;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }

        .product-gallery .main-image-swiper:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        }

        .product-gallery .thumbnail-swiper {
            height: 96px;
            padding: 8px 0;
        }

        .product-gallery .thumbnail-swiper .swiper-slide {
            width: 25%;
            height: 100%;
            opacity: .6;
            transition: opacity .25s ease, transform .25s ease;
            cursor: pointer;
        }

        .product-gallery .thumbnail-swiper .swiper-slide:hover {
            opacity: 1;
            transform: translateY(-2px);
        }

        .product-gallery .thumbnail-swiper .swiper-slide-thumb-active {
            opacity: 1;
            transform: translateY(-2px);
            outline: 2px solid var(--brand);
            outline-offset: 2px;
            border-radius: .5rem;
        }

        .product-gallery .thumbnail-swiper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: .5rem;
        }

        .swiper-button-next,
        .swiper-button-prev {
            color: var(--brand);
            background: rgba(255, 255, 255, .9);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            transition: background .2s ease;
        }

        .swiper-button-next:hover,
        .swiper-button-prev:hover {
            background: var(--brand);
            color: #fff;
        }

        /* =================== Card and Price Box =================== */
        .card-glass {
            background: linear-gradient(180deg, rgba(255, 255, 255, .92), rgba(255, 255, 255, .98));
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            border: 1px solid rgba(15, 23, 42, .04);
            transition: transform .25s ease, box-shadow .25s ease;
        }

        .card-glass:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, .1);
        }

        .product-price-box {
            border: 2px solid var(--brand);
            transition: border-color .2s ease;
        }

        .product-price-box:hover {
            border-color: var(--brand-600);
        }

        .rounded-4 {
            border-radius: 1rem !important;
        }

        /* =================== Form and Button Styles =================== */
        .btn-brand {
            background-color: var(--brand);
            border-color: var(--brand);
            color: #fff;
            padding: .5rem 1rem;
            transition: transform .15s ease, box-shadow .15s ease, background .15s ease;
        }

        .btn-brand:hover {
            background-color: var(--brand-600);
            border-color: var(--brand-600);
            transform: translateY(-2px);
            box-shadow: 0 8px 24px var(--ring);
        }

        .btn-outline-brand {
            color: var(--brand);
            border-color: var(--brand);
            padding: .45rem .9rem;
            transition: background .15s ease, color .15s ease;
        }

        .btn-outline-brand:hover {
            background-color: var(--brand);
            border-color: var(--brand);
            color: #fff;
        }

        .btn-variant {
            border-radius: 999px;
            padding: .35rem .85rem;
            font-size: .9rem;
            transition: background .2s ease, color .2s ease, box-shadow .2s ease;
        }

        .btn-check:checked+.btn-variant {
            background: var(--brand);
            border-color: var(--brand);
            color: #fff;
            box-shadow: 0 6px 14px var(--ring);
        }

        .form-control-modern {
            border-radius: .8rem;
            border: 1px solid #e9ecef;
            background: #fff;
            font-size: 1rem;
        }

        .form-control-modern:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 .2rem var(--ring);
        }

        .quantity-group {
            width: 160px;
        }

        .quantity-group .form-control {
            border-left: 0;
            border-right: 0;
            border-radius: 0;
        }

        .text-brand {
            color: var(--brand);
        }

        .text-muted {
            color: var(--muted);
        }

        .badge-soft-brand {
            background: rgba(var(--brand-rgb), .1);
            color: var(--brand);
            font-size: .85rem;
        }

        /* =================== Responsive =================== */
        @media (max-width: 991px) {
            .col-lg-6 {
                flex: 0 0 100%;
                max-width: 100%;
            }

            .product-gallery .main-image-swiper {
                height: 400px;
            }

            .product-gallery .thumbnail-swiper {
                height: 80px;
            }

            .quantity-group {
                width: 140px;
            }

            .btn-lg {
                padding: .4rem .8rem;
                font-size: .9rem;
            }

            .card {
                padding: 1.5rem;
            }
        }

        @media (max-width: 767px) {
            .product-gallery .main-image-swiper {
                height: 320px;
            }

            .product-gallery .thumbnail-swiper {
                height: 70px;
            }

            .container {
                padding-left: 15px;
                padding-right: 15px;
            }

            .card {
                padding: 1rem;
            }

            .btn-lg {
                padding: .35rem .7rem;
                font-size: .85rem;
            }

            .quantity-group {
                width: 120px;
            }

            .btn-variant {
                padding: .3rem .7rem;
                font-size: .85rem;
            }

            .product-price-box h3 {
                font-size: 1.5rem;
            }

            .bi-star-fill,
            .bi-star-half,
            .bi-star {
                font-size: .9rem;
            }
        }

        @media (max-width: 575px) {
            .product-gallery .main-image-swiper {
                height: 280px;
            }

            .product-gallery .thumbnail-swiper {
                height: 60px;
            }

            .card {
                padding: .75rem;
            }

            .btn-lg {
                padding: .3rem .6rem;
                font-size: .8rem;
            }

            .quantity-group {
                width: 100px;
            }

            .btn-variant {
                padding: .25rem .6rem;
                font-size: .8rem;
            }

            .product-price-box h3 {
                font-size: 1.25rem;
            }

            .breadcrumb {
                font-size: .85rem;
            }
        }
    </style>
@endpush

@push('scripts-page')
    <script>
        const variantsData = {!! json_encode($product->variants) !!};
        const priceDisplay = document.getElementById('product-price-display');
        const skuDisplay = document.getElementById('product-sku');
        const selectedVariantInput = document.getElementById('selected-variant-id');
        const addToCartBtn = document.getElementById('add-to-cart-btn');
        const buyNowBtn = document.getElementById('buy-now-btn');
        const quantitySelector = document.getElementById('quantity-selector');
        const attributeGroupCount = parseInt("{{ count($attributeGroups) }}");

        function updateProductInfo() {
            const selectedOptions = {};
            document.querySelectorAll('.variant-option:checked').forEach(radio => {
                selectedOptions[radio.name] = radio.value;
            });
            if (Object.keys(selectedOptions).length < attributeGroupCount) return;

            const matchedVariant = variantsData.find(v => {
                if (!v.attributes || Object.keys(v.attributes).length !== Object.keys(selectedOptions).length)
                    return false;
                return Object.entries(selectedOptions).every(([key, value]) => v.attributes[key] === value);
            });

            const formatCurrency = num => new Intl.NumberFormat('vi-VN', {
                style: 'currency',
                currency: 'VND'
            }).format(num);

            if (matchedVariant) {
                selectedVariantInput.value = matchedVariant.id;
                skuDisplay.textContent = matchedVariant.sku;
                const price = parseFloat(matchedVariant.price);
                const salePrice = parseFloat(matchedVariant.sale_price) || 0;

                priceDisplay.innerHTML = (salePrice > 0 && salePrice < price) ?
                    `<span class="text-muted text-decoration-line-through me-2">${formatCurrency(price)}</span> ${formatCurrency(salePrice)}` :
                    formatCurrency(price);

                if (addToCartBtn) addToCartBtn.disabled = false;
                if (buyNowBtn) buyNowBtn.disabled = false;
            } else {
                selectedVariantInput.value = '';
                skuDisplay.textContent = 'N/A';
                priceDisplay.innerHTML = '<span class="text-brand">Phiên bản không có sẵn</span>';
                if (addToCartBtn) addToCartBtn.disabled = true;
                if (buyNowBtn) buyNowBtn.disabled = true;
            }
        }
        document.querySelectorAll('.variant-option').forEach(radio => radio.addEventListener('change', updateProductInfo));

        document.getElementById('quantity-minus')?.addEventListener('click', () => {
            let currentVal = parseInt(quantitySelector.value || '1', 10);
            if (currentVal > 1) quantitySelector.value = currentVal - 1;
        });
        document.getElementById('quantity-plus')?.addEventListener('click', () => {
            let currentVal = parseInt(quantitySelector.value || '1', 10);
            quantitySelector.value = currentVal + 1;
        });

        function showQtyLimitModal() {
            const el = document.getElementById('qtyLimitModal');
            if (window.bootstrap && bootstrap.Modal) {
                bootstrap.Modal.getOrCreateInstance(el).show();
            } else {
                alert('Bạn mua trên 5 sản phẩm. Vui lòng liên hệ với chúng tôi để được hướng dẫn và nhận thêm ưu đãi.');
            }
        }

        async function handleCartAction(url, formData) {
            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                return await response.json();
            } catch (error) {
                console.error('Lỗi:', error);
                return {
                    success: false,
                    message: 'Có lỗi xảy ra, vui lòng thử lại.'
                };
            }
        }

        addToCartBtn?.addEventListener('click', async function() {
            const qty = parseInt(quantitySelector.value || '1', 10);
            if (qty > 5) {
                showQtyLimitModal();
                return;
            }

            this.disabled = true;
            const formData = new FormData(document.getElementById('action-form'));
            const result = await handleCartAction('{{ route('cart.add') }}', formData);

            if (result.success) {
                const cartBadgeById = document.getElementById('cart-count');
                const cartBadge = cartBadgeById || document.querySelector('[data-cart-badge]');
                if (cartBadge) {
                    cartBadge.textContent = result.cart_count ?? cartBadge.textContent;
                    cartBadge.style.display = parseInt(cartBadge.textContent || '0', 10) > 0 ? 'inline-block' :
                        'none';
                }
                if (window.Swal) Swal.fire({
                    icon: 'success',
                    title: 'Đã thêm vào giỏ!',
                    showConfirmButton: false,
                    timer: 1400,
                    toast: true,
                    position: 'top-end'
                });
            } else {
                if (window.Swal) Swal.fire({
                    icon: 'error',
                    title: 'Thất bại',
                    text: result.message || 'Không thể thêm vào giỏ.'
                });
            }
            this.disabled = false;
        });

        buyNowBtn?.addEventListener('click', async function() {
            const qty = parseInt(quantitySelector.value || '1', 10);
            if (qty > 5) {
                showQtyLimitModal();
                return;
            }

            this.disabled = true;
            const formData = new FormData(document.getElementById('action-form'));
            const result = await handleCartAction('{{ route('cart.buyNow') }}', formData);

            if (result.success && result.redirect_url) {
                window.location.href = result.redirect_url;
            } else {
                if (window.Swal) Swal.fire({
                    icon: 'error',
                    title: 'Thất bại',
                    text: result.message || 'Không thể xử lý đơn hàng.'
                });
                this.disabled = false;
            }
        });

        // Swiper init
        if (document.querySelector('.main-image-swiper')) {
            const hasThumb = document.querySelectorAll('.thumbnail-swiper .swiper-slide').length > 0;
            const thumbSwiper = hasThumb ? new Swiper('.thumbnail-swiper', {
                spaceBetween: 10,
                slidesPerView: 4,
                freeMode: true,
                watchSlidesProgress: true,
                breakpoints: {
                    0: {
                        slidesPerView: 3
                    },
                    576: {
                        slidesPerView: 4
                    },
                    992: {
                        slidesPerView: 5
                    }
                }
            }) : null;

            new Swiper('.main-image-swiper', {
                spaceBetween: 10,
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev'
                },
                thumbs: hasThumb ? {
                    swiper: thumbSwiper
                } : undefined
            });
        }

        // AOS init
        if (typeof AOS !== 'undefined') AOS.init({
            duration: 600,
            once: true,
            offset: 80
        });
    </script>
@endpush
