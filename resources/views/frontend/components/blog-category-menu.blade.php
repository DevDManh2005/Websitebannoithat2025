@php
    /** @var \Illuminate\Support\Collection $blogCategories */
    // Biến $blogCategories đã được cung cấp bởi View Composer trong AppServiceProvider
@endphp

{{-- HTML cho Cửa Sổ Chuyên Mục Tin Tức --}}
<div class="blog-modal-overlay" id="blog-modal-overlay" aria-hidden="true">
    <div class="blog-modal-content" role="dialog" aria-modal="true">
        <div class="blog-modal-header">
            <h5 class="blog-modal-title">Chuyên mục tin tức</h5>
            <button type="button" class="btn-close-modal" id="blog-modal-close" aria-label="Đóng">&times;</button>
        </div>
        <div class="blog-modal-body">
            <div class="blog-category-list">
                <a href="{{ route('blog.index') }}"
                   class="blog-category-link {{ request()->routeIs('blog.index') && !request('danh-muc') ? 'active' : '' }}">
                    <i class="bi bi-files"></i>
                    <span>Tất cả bài viết</span>
                </a>
                @if(isset($blogCategories) && $blogCategories->isNotEmpty())
                    @foreach($blogCategories as $category)
                        <a class="blog-category-link {{ request('danh-muc') === $category->slug ? 'active' : '' }}"
                           href="{{ route('blog.index', ['danh-muc' => $category->slug]) }}"
                           title="{{ $category->name }}">
                            <i class="bi bi-tag"></i>
                            <span>{{ $category->name }}</span>
                        </a>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>

{{-- CSS cho Cửa Sổ Modal --}}
@once
<style>
    /* ===== Component: Blog Category Modal ===== */
    .blog-modal-overlay {
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
    .blog-modal-overlay.active {
        opacity: 1;
        visibility: visible;
    }

    .blog-modal-content {
        background: var(--card, #fff);
        border-radius: var(--radius, 12px);
        box-shadow: 0 20px 50px rgba(0,0,0,0.2);
        width: min(450px, 92vw);
        max-height: 80vh;
        display: flex;
        flex-direction: column;
        transform: scale(0.95);
        transition: transform .3s cubic-bezier(.2, .9, .3, 1);
    }
    .blog-modal-overlay.active .blog-modal-content {
        transform: scale(1);
    }

    .blog-modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid rgba(0,0,0, .07);
        flex-shrink: 0;
    }
    .blog-modal-title {
        font-weight: 700;
        margin: 0;
    }
    /* Dùng lại style nút close đã có */
    .btn-close-modal {
        background: none; border: 0;
        font-size: 2rem; line-height: 1;
        color: var(--muted); opacity: 0.7;
        transition: transform .2s, color .2s;
    }
    .btn-close-modal:hover {
        color: var(--brand);
        transform: rotate(90deg);
    }

    .blog-modal-body {
        padding: 1rem;
        overflow-y: auto;
    }
    .blog-category-list {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }
    .blog-category-link {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.7rem 0.85rem;
        border-radius: 8px;
        color: var(--text);
        font-weight: 500;
        text-decoration: none;
        transition: background .15s ease, color .15s ease;
    }
    .blog-category-link:hover {
        background: rgba(var(--brand-rgb, 162, 14, 56), .07);
        color: var(--brand);
    }
    .blog-category-link.active {
        background: rgba(var(--brand-rgb, 162, 14, 56), .1);
        color: var(--brand);
        font-weight: 600;
    }
    .blog-category-link i { font-size: 1.1rem; }
</style>
@endonce

{{-- ===== SCRIPT ===== --}}
@once
    @push('scripts-page')
    <script>
  // ===== Blog Category Modal Trigger =====
    (function() {
        const openBtn = document.getElementById('open-blog-modal');
        const closeBtn = document.getElementById('blog-modal-close');
        const overlay = document.getElementById('blog-modal-overlay');

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