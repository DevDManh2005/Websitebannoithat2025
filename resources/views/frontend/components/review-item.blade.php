<div class="review-item d-flex flex-column "data-aos="fade-up">
    <div class="d-flex align-items-center mb-2">
        {{-- Avatar --}}
        <img src="{{ $review->user->profile->avatar_url }}?v={{ $review->user->profile->updated_at->timestamp }}"
             alt="{{ $review->user->name }}"
             class="rounded-circle me-3">

        <div>
            {{-- Tên người đánh giá --}}
            <strong>{{ $review->user->name }}</strong><br>
            {{-- Hiển thị sao --}}
            @include('frontend.components.star-rating', ['rating' => $review->rating])
        </div>
    </div>

    {{-- Nội dung đánh giá --}}
    <p>{{ $review->review }}</p>

    {{-- Ảnh nếu có --}}
    @if($review->image)
        <img src="{{ asset('storage/' . $review->image) }}"
             alt="Hình ảnh đánh giá"
             class="img-thumbnail">
    @endif
</div>

<style>/* === Review Item === */
.review-item {
    border-bottom: 1px solid #e9ecef;
    padding-bottom: 1rem;
    margin-bottom: 1.5rem;
}

.review-item:last-child {
    border-bottom: none;
    margin-bottom: 0;
}

.review-item img.rounded-circle {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border: 2px solid #dee2e6;
    margin-right: 12px;
}

.review-item strong {
    display: block;
    font-weight: 600;
    font-size: 1rem;
    color: #333;
}

.review-item .star-rating {
    font-size: 0.9rem;
    color: #ffc107; /* Vàng sao */
}

.review-item p {
    font-size: 0.95rem;
    color: #555;
    margin-top: 0.5rem;
    margin-bottom: 0.5rem;
}

.review-item .img-thumbnail {
    border-radius: 10px;
    border: 1px solid #ddd;
    margin-top: 0.75rem;
    max-width: 180px;
    transition: transform 0.3s ease;
}

.review-item .img-thumbnail:hover {
    transform: scale(1.03);
}
</style>