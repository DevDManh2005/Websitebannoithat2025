@php
    // Đảm bảo biến $rating luôn tồn tại, nếu không có thì mặc định là 0
    $rating = $rating ?? 0;
@endphp

<div class="rating-stars">
    @for ($i = 1; $i <= 5; $i++)
        @if ($i <= $rating)
            <i class="bi bi-star-fill text-warning"></i>
        @elseif ($i - 0.5 <= $rating)
            <i class="bi bi-star-half text-warning"></i>
        @else
            <i class="bi bi-star text-warning"></i>
        @endif
    @endfor
</div>
