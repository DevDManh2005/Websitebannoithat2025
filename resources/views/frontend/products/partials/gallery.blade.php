// filepath: [gallery.blade.php](http://_vscodecontentref_/0)
@php
    use Illuminate\Support\Str;
    $images = $product->images ?? collect();
    $srcFor = fn($p) => Str::startsWith($p, ['http://','https://']) ? $p : (strpos($p, 'storage/') === 0 ? asset($p) : asset('storage/'.$p));
@endphp

<div class="product-gallery">
    <div class="main-image mb-3">
        @if($images->isNotEmpty())
            <img src="{{ $srcFor($images->first()->url ?? $images->first()->path) }}" class="img-fluid rounded" alt="{{ $product->name }}">
        @else
            <img src="{{ asset('images/product-placeholder.png') }}" class="img-fluid rounded" alt="no image">
        @endif
    </div>

    @if($images->count() > 1)
        <div class="thumbnails d-flex gap-2">
            @foreach($images as $img)
                <button class="thumbnail-btn border-0 bg-transparent p-0" type="button" onclick="document.querySelector('.main-image img').src='{{ $srcFor($img->url ?? $img->path) }}'">
                    <img src="{{ $srcFor($img->thumb ?? $img->url ?? $img->path) }}" width="64" height="64" class="rounded" alt="thumb">
                </button>
            @endforeach
        </div>
    @endif
</div>