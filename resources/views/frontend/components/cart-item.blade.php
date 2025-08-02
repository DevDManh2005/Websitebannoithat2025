@props(['item'])

<div class="row align-items-center mb-3">
    <div class="col-2 col-md-1">
        <img src="{{ optional($item->variant->product->images->where('is_primary', true)->first())->image_url_path ?? 'https://placehold.co/80x80' }}" alt="{{ $item->variant->product->name }}" class="img-fluid rounded">
    </div>
    <div class="col-10 col-md-5">
        <a href="{{ route('product.show', $item->variant->product->slug) }}" class="text-dark text-decoration-none">{{ $item->variant->product->name }}</a>
        <div class="small text-muted">
            @foreach((array)$item->variant->attributes as $key => $value)
                {{ ucfirst($key) }}: {{ $value }}@if(!$loop->last), @endif
            @endforeach
        </div>
    </div>
    <div class="col-6 col-md-2 mt-2 mt-md-0">
        @php
            $price = $item->variant->sale_price > 0 && $item->variant->sale_price < $item->variant->price
                ? $item->variant->sale_price
                : $item->variant->price;
        @endphp
        <span class="fw-bold">{{ number_format($price) }} ₫</span>
        @if($price < $item->variant->price)
            <small class="text-muted text-decoration-line-through">{{ number_format($item->variant->price) }} ₫</small>
        @endif
    </div>
    <div class="col-6 col-md-2 mt-2 mt-md-0">
        <form action="{{ route('cart.update', $item->id) }}" method="POST">
            @csrf
            @method('PATCH')
            <input type="number" name="quantity" class="form-control form-control-sm" value="{{ $item->quantity }}" min="1" onchange="this.form.submit()">
        </form>
    </div>
    <div class="col-12 col-md-2 text-md-end mt-2 mt-md-0">
        <form action="{{ route('cart.remove', $item->id) }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-outline-danger">Xóa</button>
        </form>
    </div>
</div>
