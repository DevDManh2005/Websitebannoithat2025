@php
    // Đảm bảo biến $review luôn tồn tại để tránh lỗi
    $review = $review ?? null;
@endphp

@if($review && $review->user)
<div class="d-flex mb-4">
    <div class="flex-shrink-0">
        <img src="{{ optional($review->user->profile)->avatar_url ?? 'https://placehold.co/50x50' }}" alt="{{ $review->user->name }}" class="rounded-circle" width="50" height="50">
    </div>
    <div class="flex-grow-1 ms-3">
        <div class="d-flex justify-content-between align-items-center">
            <strong>{{ $review->user->name }}</strong>
            <small class="text-muted">{{ $review->created_at->format('d/m/Y') }}</small>
        </div>
        <div class="mb-1">
            @include('frontend.components.star-rating', ['rating' => $review->rating])
        </div>
        <p class="mb-1">{{ $review->review }}</p>
        @if($review->image)
            <a href="{{ asset('storage/' . $review->image) }}" target="_blank">
                <img src="{{ asset('storage/' . $review->image) }}" alt="Ảnh đánh giá" class="img-thumbnail mt-2" width="100">
            </a>
        @endif
    </div>
</div>
@endif
