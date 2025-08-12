@php
  /** @var \Illuminate\Support\Collection|array $categories */
  $categories = collect($categories ?? []);
  $level = $level ?? 0;
@endphp

@if($categories->isNotEmpty())
  <ul class="dropdown-menu cmenu level-{{ $level }}">
    @foreach($categories as $cat)
      @php $hasChildren = $cat->children && $cat->children->isNotEmpty(); @endphp

      <li class="{{ $hasChildren ? 'dropdown-submenu position-relative' : '' }}">
        <a class="dropdown-item d-flex justify-content-between align-items-center"
           href="{{ route('products.index', ['categories[]' => $cat->id]) }}">
          <span class="text-truncate">{{ $cat->name }}</span>

          @if($hasChildren)
            <i class="bi bi-chevron-right small opacity-75 cm-caret ms-2"></i>
          @endif
        </a>

        @if($hasChildren)
          @include('frontend.components.category-menu', [
            'categories' => $cat->children,
            'level' => $level + 1
          ])
        @endif
      </li>
    @endforeach
  </ul>
@endif

@once
@push('styles')
<style>
/* ---------- Fly-out dropdown multi-level (scoped) ---------- */
.cmenu{
  --bg: #fff;
  --line: rgba(17,24,39,.10);
  --hover: rgba(193,18,107,.08);
  --radius: 12px;

  min-width: 260px;
  padding: 8px;
  border-radius: var(--radius);
  background: var(--bg);
  border: 1px solid var(--line);
  box-shadow: 0 12px 28px rgba(2,6,23,.12);
}

.cmenu.level-1{ min-width: 240px; }
.cmenu.level-2{ min-width: 220px; }
.cmenu.level-3{ min-width: 200px; }

.cmenu .dropdown-item{
  padding: .5rem .75rem;
  border-radius: 8px;
  font-weight: 500;
  line-height: 1.25;
}
.cmenu .dropdown-item:hover{
  background: var(--hover);
  color: #c1126b;
}

.dropdown-submenu > .dropdown-menu{
  top: 0;
  left: 100%;
  margin-left: .25rem;
  margin-right: .25rem;
}

.dropdown-submenu:hover > .dropdown-menu{
  display: block;
}

.cmenu .cm-caret{ transition: transform .15s ease; }
.dropdown-submenu:hover > a .cm-caret{ transform: translateX(2px); }

/* Auto-truncate long names */
.cmenu .dropdown-item > span{ inline-size: 100%; overflow: hidden; text-overflow: ellipsis; }

/* Mobile: convert fly-out to collapsible blocks */
@media (max-width: 991.98px){
  .cmenu{
    min-width: 100%;
    box-shadow: none;
    border: 0;
    padding: 6px 6px 10px;
  }
  .dropdown-submenu > .dropdown-menu{
    position: static;
    inset: auto;
    float: none;
    display: none;         /* collapsed by default */
    margin: .25rem 0 0 0;
    padding-left: .5rem;
    border: 0;
    box-shadow: none;
  }
  .dropdown-submenu > .dropdown-menu.show{ display: block; }
  .cmenu .cm-caret{ transform: rotate(90deg); } /* point down initially */
  .dropdown-submenu > .dropdown-item.active .cm-caret{ transform: rotate(0deg); }
}
</style>
@endpush

@push('scripts-page')
<script>
/* Mobile: click caret (hoặc click lần đầu vào link có con) để expand/collapse */
document.addEventListener('click', function (e) {
  const isMobile = window.matchMedia('(max-width: 991.98px)').matches;

  // Click vào caret
  const caret = e.target.closest('.cm-caret');
  if (caret && isMobile) {
    e.preventDefault();
    const li = caret.closest('.dropdown-submenu');
    const sub = li && li.querySelector(':scope > .dropdown-menu');
    if (sub) {
      sub.classList.toggle('show');
      li.querySelector(':scope > .dropdown-item')?.classList.toggle('active');
    }
    return;
  }

  // Click vào link có con: lần 1 mở submenu, lần 2 mới đi link
  const link = e.target.closest('.dropdown-submenu > .dropdown-item');
  if (link && isMobile) {
    const li  = link.parentElement;
    const sub = li && li.querySelector(':scope > .dropdown-menu');
    if (sub && !sub.classList.contains('show')) {
      e.preventDefault();
      sub.classList.add('show');
      link.classList.add('active');
    }
  }
});
</script>
@endpush
@endonce
