
@php
    use App\Models\BlogCategory;

    $categories = $categories
        ?? BlogCategory::with('children.children.children')
            ->whereNull('parent_id')
            ->where('is_active', 1)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

    $activeSlug = $activeSlug ?? request('danh-muc');

    function renderCategoryTree($categories, $activeSlug, $level = 0) {
        foreach ($categories as $c) {
            $count = $c->blogs_count ?? null;
            $isActive = $activeSlug === $c->slug;
            $hasChildren = $c->children && $c->children->count() > 0;

            $containsActive = $hasChildren && $c->children->contains(function($child) use ($activeSlug) {
                return $child->slug === $activeSlug 
                    || ($child->children && $child->children->contains(fn($gc) => $gc->slug === $activeSlug));
            });

            $expanded = $isActive || $containsActive;
            $padding = 1.25 + $level * 1;

            echo '<div class="blog-cat-item">';

            echo '<div class="blog-cat-row">';
            echo '<a href="'.route('blog.index',['danh-muc'=>$c->slug]).'" class="blog-category-link '.($isActive?'active':'').'" style="padding-left:'.$padding.'rem">';
            echo '<i class="bi bi-tag me-2"></i>';
            echo '<span class="flex-grow-1">'.$c->name.'</span>';
            if($count){ echo '<span class="badge badge-soft-brand ms-2">'.$count.'</span>'; }
            echo '</a>';

            if($hasChildren){
                echo '<button class="btn-ghost-toggle ms-1 toggle-subcats" type="button" aria-expanded="'.($expanded?'true':'false').'">';
                echo '<i class="bi '.($expanded?'bi-chevron-down':'bi-chevron-right').'"></i>';
                echo '</button>';
            }
            echo '</div>';

            if($hasChildren){
                echo '<div class="subcat-list" style="display:'.($expanded?'flex':'none').'">';
                renderCategoryTree($c->children, $activeSlug, $level+1);
                echo '</div>';
            }

            echo '</div>';
        }
    }
@endphp

<div class="card-glass shadow-elevated rounded-4 blog-side">
    <div class="p-3 p-lg-4">
        <h5 class="fw-bold mb-3 d-flex align-items-center gap-2 text-brand">
            <i class="bi bi-list-task text-brand"></i> Chuyên mục
        </h5>

        <a href="{{ route('blog.index') }}" 
           class="blog-category-link {{ empty($activeSlug) ? 'active' : '' }}">
            <i class="bi bi-files me-2"></i> Tất cả bài viết
        </a>

        {!! renderCategoryTree($categories, $activeSlug) !!}
    </div>

    <style>
        /* Blog category row */
        .blog-cat-row { 
            display: flex; 
            align-items: center; 
            gap: 0.5rem; 
        }
        
        /* Category link */
        .blog-category-link {
            display: flex; 
            align-items: center; 
            flex-grow: 1;
            padding: 0.55rem 0.75rem; 
            border-radius: 8px;
            color: var(--text); 
            text-decoration: none;
            transition: background 0.15s ease, color 0.15s ease;
        }
        .blog-category-link:hover,
        .blog-category-link.active { 
            background: rgba(162, 14, 56, .1); 
            color: var(--brand); 
        }
        .blog-category-link .bi { 
            transition: transform 0.15s ease; 
        }
        .blog-category-link:hover .bi,
        .blog-category-link.active .bi { 
            transform: translateX(2px); 
        }

        /* Subcategory list */
        .subcat-list { 
            display: none; 
            flex-direction: column; 
            border-left: 2px dashed rgba(162, 14, 56, .15); 
            margin-left: 0.8rem; 
            padding-left: 0.5rem;
            transition: opacity 0.2s ease;
        }
        .subcat-list[style*="display: flex"] {
            opacity: 1;
        }
        .subcat-list[style*="display: none"] {
            opacity: 0;
        }

        /* Toggle button */
        .btn-ghost-toggle {
            border: none; 
            background: transparent; 
            cursor: pointer;
            padding: 0.5rem;
            font-size: 1rem;
            color: var(--muted);
            transition: color 0.15s ease;
        }
        .btn-ghost-toggle:hover {
            color: var(--brand);
        }
        .btn-ghost-toggle .bi {
            transition: transform 0.2s ease;
        }

        /* Responsive tweaks */
        @media (max-width: 991px) {
            /* Tablet */
            .blog-side .p-3.p-lg-4 {
                padding: 1rem !important;
            }
            .blog-category-link {
                padding: 0.5rem 0.7rem;
                font-size: 0.95rem;
            }
            .badge-soft-brand {
                font-size: 0.75rem;
                padding: 0.2rem 0.5rem;
            }
            .btn-ghost-toggle {
                padding: 0.4rem;
                font-size: 0.95rem;
            }
        }

        @media (max-width: 767px) {
            /* Mobile */
            .blog-side .p-3.p-lg-4 {
                padding: 0.9rem !important;
            }
            .blog-category-link {
                padding: 0.45rem 0.65rem;
                font-size: 0.9rem;
            }
            .badge-soft-brand {
                font-size: 0.7rem;
                padding: 0.18rem 0.45rem;
            }
            .subcat-list {
                margin-left: 0.6rem;
                padding-left: 0.4rem;
                border-left-width: 1.5px;
            }
            .btn-ghost-toggle {
                padding: 0.35rem;
                font-size: 0.9rem;
            }
        }

        @media (max-width: 575px) {
            /* Small Mobile */
            .blog-side .p-3.p-lg-4 {
                padding: 0.75rem !important;
            }
            .blog-category-link {
                padding: 0.4rem 0.6rem;
                font-size: 0.85rem;
            }
            .badge-soft-brand {
                font-size: 0.65rem;
                padding: 0.15rem 0.4rem;
            }
            .subcat-list {
                margin-left: 0.5rem;
                padding-left: 0.3rem;
                border-left-width: 1px;
            }
            .btn-ghost-toggle {
                padding: 0.3rem;
                font-size: 0.85rem;
            }
        }
    </style>

    <script>
    document.addEventListener("DOMContentLoaded", function(){
        // Sử dụng event delegation để tối ưu hiệu suất
        document.querySelector(".blog-side").addEventListener("click", function(e) {
            const btn = e.target.closest(".toggle-subcats");
            if (!btn) return;

            const sublist = btn.closest(".blog-cat-item").querySelector(".subcat-list");
            const icon = btn.querySelector("i");
            const expanded = btn.getAttribute("aria-expanded") === "true";

            btn.setAttribute("aria-expanded", !expanded);
            icon.classList.toggle("bi-chevron-right", expanded);
            icon.classList.toggle("bi-chevron-down", !expanded);
            sublist.style.display = expanded ? "none" : "flex";
        });

        // Thêm hỗ trợ touch events cho mobile
        document.querySelectorAll(".toggle-subcats").forEach(btn => {
            btn.addEventListener("touchstart", function(e) {
                e.preventDefault(); // Ngăn scroll khi chạm
                this.click(); // Kích hoạt click event
            }, { passive: false });
        });
    });
    </script>
</div>
