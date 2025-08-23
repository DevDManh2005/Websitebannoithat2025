@php
  /** @var \Illuminate\Support\Collection|\Illuminate\Database\Eloquent\Collection|array $categories */
  $roots = collect($categories ?? []);
@endphp

{{-- HTML cho Cửa Sổ Danh Mục --}}
<div class="category-modal-overlay" id="category-modal-overlay" aria-hidden="true">
  <div class="category-modal-content" role="dialog" aria-modal="true">
    <div class="category-modal-header">
      <a href="{{ route('products.index') }}" class="category-modal-title-link">
        <h5 class="category-modal-title">
          <span>Tất Cả Sản Phẩm</span>
          <i class="bi bi-box-arrow-up-right"></i>
        </h5>
      </a>
      <button type="button" class="btn-close-modal" id="category-modal-close" aria-label="Đóng">&times;</button>
    </div>
    <div class="category-modal-body">
      <ul class="category-accordion-list" role="list">
        @foreach($roots as $root)
      <li>
        {{-- Nút bấm cho danh mục cha --}}
        <a href="#collapse-{{ $root->id }}" class="accordion-trigger collapsed" data-bs-toggle="collapse"
        role="button" aria-expanded="false" aria-controls="collapse-{{ $root->id }}">
        <span class="accordion-trigger-name">{{ $root->name }}</span>
        <i class="bi bi-chevron-down accordion-trigger-icon"></i>
        </a>
        {{-- Danh sách danh mục con (sẽ xổ xuống) --}}
        <div class="collapse accordion-panel" id="collapse-{{ $root->id }}">
        <ul class="accordion-child-list" role="list">
          @if($root->children && $root->children->isNotEmpty())
        @foreach($root->children as $child)
        <li>
        <a href="{{ route('products.index', ['categories[]' => $child->id]) }}">
        {{ $child->name }}
        </a>
        </li>
      @endforeach
      @endif
          {{-- Link xem tất cả trong danh mục cha --}}
          <li>
          <a href="{{ route('products.index', ['categories[]' => $root->id]) }}" class="all-items-link">
            Xem tất cả <i class="bi bi-arrow-right-short"></i>
          </a>
          </li>
        </ul>
        </div>
      </li>
    @endforeach
      </ul>
    </div>
  </div>
</div>

{{-- CSS cho Cửa Sổ Danh Mục --}}
@once
  <style>
    /* CSS cho link ở tiêu đề Modal */
    .category-modal-header .category-modal-title-link {
    text-decoration: none;
    /* Bỏ gạch chân mặc định */
    }

    .category-modal-header .category-modal-title {
    display: inline-flex;
    /* Giúp icon và chữ canh thẳng hàng */
    align-items: center;
    gap: 0.5rem;
    color: var(--text);
    /* Giữ màu chữ gốc */
    transition: color .2s ease;
    }

    /* Style cho icon */
    .category-modal-header .category-modal-title i {
    font-size: 0.8em;
    /* Kích thước icon nhỏ hơn chữ một chút */
    opacity: 0;
    /* Mặc định ẩn icon đi */
    transform: translate(-5px, -5px);
    transition: all .25s ease;
    }

    /* Hiệu ứng khi hover */
    .category-modal-header .category-modal-title-link:hover .category-modal-title {
    color: var(--brand);
    /* Đổi màu chữ khi hover */
    }

    .category-modal-header .category-modal-title-link:hover .category-modal-title i {
    opacity: 1;
    /* Hiện icon lên */
    transform: translate(0, 0);
    /* Cho icon trượt nhẹ ra */
    }

    /* ===== Component: Category Modal ===== */
    .category-modal-overlay {
    position: fixed;
    inset: 0;
    z-index: 1070;
    background: rgba(30, 25, 22, 0.5);
    backdrop-filter: blur(8px) saturate(1.2);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    visibility: hidden;
    transition: opacity .3s ease, visibility .3s;
    }

    .category-modal-overlay.active {
    opacity: 1;
    visibility: visible;
    }

    .category-modal-content {
    background: var(--card, #fff);
    border-radius: var(--radius, 12px);
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.2);
    width: min(600px, 92vw);
    max-height: 80vh;
    display: flex;
    flex-direction: column;
    transform: scale(0.95);
    transition: transform .3s cubic-bezier(.2, .9, .3, 1);
    }

    .category-modal-overlay.active .category-modal-content {
    transform: scale(1);
    }

    .category-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid rgba(0, 0, 0, .07);
    flex-shrink: 0;
    }

    .category-modal-title {
    font-weight: 700;
    margin: 0;
    }

    .btn-close-modal {
    background: none;
    border: 0;
    font-size: 2rem;
    line-height: 1;
    color: var(--muted);
    opacity: 0.7;
    transition: transform .2s, color .2s;
    }

    .btn-close-modal:hover {
    color: var(--brand);
    transform: rotate(90deg);
    }

    .category-modal-body {
    padding: 1rem;
    overflow-y: auto;
    }

    .category-accordion-list {
    list-style: none;
    margin: 0;
    padding: 0;
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
    }

    /* --- Accordion Item --- */
    .accordion-trigger {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
    padding: 0.8rem 1rem;
    border-radius: 8px;
    font-weight: 600;
    color: var(--text);
    text-decoration: none;
    transition: background .2s ease, color .2s ease;
    }

    .accordion-trigger:hover {
    background: rgba(var(--brand-rgb, 162, 14, 56), .05);
    }

    .accordion-trigger:not(.collapsed) {
    background: rgba(var(--brand-rgb, 162, 14, 56), .08);
    color: var(--brand);
    }

    .accordion-trigger-icon {
    font-size: 1rem;
    transition: transform .3s cubic-bezier(.2, .9, .3, 1);
    }

    .accordion-trigger:not(.collapsed) .accordion-trigger-icon {
    transform: rotate(180deg);
    }

    .accordion-panel {
    padding: 0.5rem 1rem 1rem 2rem;
    border-left: 2px solid var(--brand);
    margin-left: 1rem;
    }

    .accordion-child-list {
    list-style: none;
    margin: 0;
    padding: 0;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    }

    .accordion-child-list a {
    color: var(--muted);
    text-decoration: none;
    transition: color .2s;
    }

    .accordion-child-list a:hover {
    color: var(--brand);
    }

    .accordion-child-list .all-items-link {
    font-weight: 600;
    color: var(--brand);
    }
  </style>
@endonce

{{-- ===== SCRIPT ===== --}}
@once
  @push('scripts-page')
    <script>
    // ===== Category Modal Trigger =====
    (function () {
    const openBtn = document.getElementById('open-category-modal');
    const closeBtn = document.getElementById('category-modal-close');
    const overlay = document.getElementById('category-modal-overlay');

    if (!openBtn || !closeBtn || !overlay) return;

    const openModal = (e) => {
      e.preventDefault();
      overlay.classList.add('active');
      overlay.setAttribute('aria-hidden', 'false');
    };

    const closeModal = () => {
      overlay.classList.remove('active');
      overlay.setAttribute('aria-hidden', 'true');
    };

    openBtn.addEventListener('click', openModal);
    closeBtn.addEventListener('click', closeModal);
    overlay.addEventListener('click', (e) => {
      if (e.target === overlay) closeModal();
    });
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && overlay.classList.contains('active')) {
      closeModal();
      }
    });
    })();
    </script>
  @endpush
@endonce