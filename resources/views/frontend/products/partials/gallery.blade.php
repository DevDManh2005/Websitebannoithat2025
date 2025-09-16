<div class="product-gallery">
    @php $images = $product->images ?? collect(); @endphp

    <div class="main-image mb-3">
        @if($images->isNotEmpty())
            <img src="{{ asset($images->first()->url ?? $images->first()->path) }}" class="img-fluid rounded" alt="{{ $product->name }}">
        @else
            <img src="{{ asset('images/product-placeholder.png') }}" class="img-fluid rounded" alt="no image">
        @endif
    </div>

    @if($images->count() > 1)
        <div class="thumbnails d-flex gap-2">
            @foreach($images as $img)
                <button class="thumbnail-btn border-0 bg-transparent p-0" type="button" onclick="document.querySelector('.main-image img').src='{{ asset($img->url ?? $img->path) }}'">
                    <img src="{{ asset($img->thumb ?? $img->url ?? $img->path) }}" width="64" height="64" class="rounded" alt="thumb">
                </button>
            @endforeach
        </div>
    @endif
</div>