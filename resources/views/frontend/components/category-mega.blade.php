@php
  /** @var \Illuminate\Support\Collection|\Illuminate\Database\Eloquent\Collection|array $categories */
  $roots = collect($categories ?? []);
@endphp

<div class="dropdown-menu catmega" id="catmega" aria-labelledby="productsDropdown" role="menu">
  <div class="catmega-inner">
    <div class="catmega-grid">
      @foreach($roots as $root)
        <div class="catmega-col">
          <a href="{{ route('products.index', ['categories[]' => $root->id]) }}"
             class="catmega-title" role="menuitem">
            {{ $root->name }}
          </a>

          @if($root->children && $root->children->isNotEmpty())
            <ul class="catmega-list">
              @foreach($root->children->take(12) as $child)
                <li>
                  <a class="catmega-link"
                     href="{{ route('products.index', ['categories[]' => $child->id]) }}"
                     role="menuitem">
                    {{ $child->name }}
                  </a>
                </li>
              @endforeach
            </ul>
          @endif
        </div>
      @endforeach
    </div>
  </div>
</div>
