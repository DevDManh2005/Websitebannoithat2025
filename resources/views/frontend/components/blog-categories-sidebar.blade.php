@php
    use App\Models\BlogCategory;

    $categories = $categories
        ?? BlogCategory::with('children.children.children') // load nhiều cấp
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

            // Kiểm tra xem nhánh có chứa slug đang active không
            $containsActive = $hasChildren && $c->children->contains(function($child) use ($activeSlug) {
                return $child->slug === $activeSlug 
                    || ($child->children && $child->children->contains(fn($gc) => $gc->slug === $activeSlug));
            });

            $expanded = $isActive || $containsActive;
            $padding = 1.25 + $level * 1;

            echo '<div class="blog-cat-item">';

            // --- Row chính ---
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

            // --- Sub list (ẩn mặc định) ---
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
        .blog-cat-row { display:flex; align-items:center; }
        .blog-category-link {
            display:flex; align-items:center; flex-grow:1;
            padding:0.55rem 0.75rem; border-radius:8px;
            color:var(--text); text-decoration:none;
        }
        .blog-category-link.active,
        .blog-category-link:hover { background:rgba(162,14,56,.1); color:var(--brand); }
        .subcat-list { 
            display:none; flex-direction:column; 
            border-left:2px dashed rgba(162,14,56,.15); 
            margin-left:.8rem; padding-left:.5rem;
        }
        .btn-ghost-toggle{border:none;background:transparent;cursor:pointer;}
    </style>

    <script>
    document.addEventListener("DOMContentLoaded", function(){
        document.querySelectorAll(".toggle-subcats").forEach(btn=>{
            btn.addEventListener("click", function(e){
                const sublist = this.closest(".blog-cat-item").querySelector(".subcat-list");
                const icon = this.querySelector("i");
                const expanded = this.getAttribute("aria-expanded")==="true";

                this.setAttribute("aria-expanded", !expanded);
                icon.classList.toggle("bi-chevron-right", expanded);
                icon.classList.toggle("bi-chevron-down", !expanded);
                sublist.style.display = expanded ? "none" : "flex";
            });
        });
    });
    </script>
</div>
