@php
    $fullStars = floor($rating);
    $halfStar = ($rating - $fullStars) >= 0.5;
    $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
@endphp

@for($i = 0; $i < $fullStars; $i++)
    <i class="bi bi-star-fill text-warning"></i>
@endfor

@if($halfStar)
    <i class="bi bi-star-half text-warning"></i>
@endif

@for($i = 0; $i < $emptyStars; $i++)
    <i class="bi bi-star text-warning"></i>
@endfor
