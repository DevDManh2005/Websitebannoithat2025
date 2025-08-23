@props(['rating'])

@php
    $fullStars = floor($rating);
    $halfStar = ($rating - $fullStars) >= 0.5;
    $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
@endphp

<div class="star-rating">
    @for($i = 0; $i < $fullStars; $i++)
        <i class="bi bi-star-fill"></i>
    @endfor

    @if($halfStar)
        <i class="bi bi-star-half"></i>
    @endif

    @for($i = 0; $i < $emptyStars; $i++)
        <i class="bi bi-star"></i>
    @endfor
</div>

@once
@push('styles')
<style>
    .star-rating {
        display: inline-flex;
        gap: 2px;
        color: var(--brand, #A20E38); /* Màu mặc định nếu biến không tồn tại */
        font-size: 0.9rem; /* Có thể điều chỉnh kích thước ở đây */
    }
    /* Đảm bảo sao trống có màu nhạt hơn một chút để dễ phân biệt */
    .star-rating .bi-star {
        color: #ddd;
    }
</style>
@endpush
@endonce