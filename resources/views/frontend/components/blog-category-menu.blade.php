@php
    /** @var \Illuminate\Support\Collection $blogCategories */
    // Yêu cầu: $blogCategories có các field: id, name, slug, parent_id (null nếu là danh mục gốc)

    $activeSlug = (string) request('danh-muc');

    // Lập bảng tra cứu nhanh
    $byId      = $blogCategories->keyBy('id');
    $byParent  = $blogCategories->groupBy(function ($c) { return $c->parent_id ?: 0; });

    // Tính các ID cần expand sẵn (đường dẫn từ danh mục đang xem ngược lên gốc)
    $expandedIds = [];
    if ($activeSlug !== '') {
        $cur = $blogCategories->firstWhere('slug', $activeSlug);
        while ($cur) {
            $expandedIds[$cur->id] = true;
            $cur = $cur->parent_id ? $byId->get($cur->parent_id) : null;
        }
    }

    // Đệ quy render cây
    $renderTree = function ($parentId = 0, $level = 0) use (&$renderTree, $byParent, $expandedIds, $activeSlug) {
        $children = $byParent->get($parentId, collect());
        if ($children->isEmpty()) return;

        echo '<ul class="blog-tree level-'.$level.'" role="tree">';
        foreach ($children as $cat) {
            $hasChildren = !empty($byParent->get($cat->id)) && $byParent->get($cat->id)->isNotEmpty();
            $isExpanded  = $hasChildren && isset($expandedIds[$cat->id]);
            $isActive    = $activeSlug === $cat->slug;

            echo '<li class="tree-item'.($hasChildren?' has-children':'').($isExpanded?' expanded':'').'" role="treeitem" aria-expanded="'.($isExpanded?'true':'false').'">';

            if ($hasChildren) {
                echo '<button class="toggle-branch" type="button" aria-label="Mở/đóng nhóm">
                        <i class="bi bi-chevron-right"></i>
                      </button>';
            } else {
                echo '<span class="leaf-dot" aria-hidden="true"></span>';
            }

            echo '<a class="blog-category-link '.($isActive?'active':'').'"
                     href="'.e(route('blog.index', ['danh-muc' => $cat->slug])).'"
                     title="'.e($cat->name).'">
                     <i class="bi '.($hasChildren?'bi-folder2':'bi-tag').'"></i>
                     <span>'.e($cat->name).'</span>
                  </a>';

            if ($hasChildren) {
                echo '<div class="children" style="display:'.($isExpanded?'block':'none').'">';
                $renderTree($cat->id, $level+1);
                echo '</div>';
            }

            echo '</li>';
        }
        echo '</ul>';
    };
@endphp

{{-- ===== MODAL ===== --}}
<div class="blog-modal-overlay" id="blog-modal-overlay" aria-hidden="true">
    <div class="blog-modal-content" role="dialog" aria-modal="true">
        <div class="blog-modal-header">
            <h5 class="blog-modal-title">Chuyên mục tin tức</h5>
            <button type="button" class="btn-close-modal" id="blog-modal-close" aria-label="Đóng">&times;</button>
        </div>

        <div class="blog-modal-body">
            {{-- Link: tất cả bài viết --}}
            <div class="mb-2">
                <a href="{{ route('blog.index') }}"
                   class="blog-category-link root-all {{ request()->routeIs('blog.index') && !request('danh-muc') ? 'active' : '' }}">
                    <i class="bi bi-files"></i>
                    <span>Tất cả bài viết</span>
                </a>
            </div>

            {{-- CÂY DANH MỤC (chỉ render cấp 1; nhánh con lazy hiển thị) --}}
            <nav class="blog-category-tree" aria-label="Cây chuyên mục">
                {!! $renderTree(0, 0) !!}
            </nav>
        </div>
    </div>
</div>

@once
<style>
/* ===== Modal giữ nguyên ===== */
.blog-modal-overlay{position:fixed;inset:0;z-index:1070;background:rgba(30,25,22,.5);backdrop-filter:blur(8px) saturate(1.2);
display:flex;align-items:center;justify-content:center;opacity:0;visibility:hidden;transition:opacity .3s ease,visibility .3s}
.blog-modal-overlay.active{opacity:1;visibility:visible}
.blog-modal-content{background:var(--card,#fff);border-radius:var(--radius,12px);box-shadow:0 20px 50px rgba(0,0,0,.2);
width:min(520px,92vw);max-height:80vh;display:flex;flex-direction:column;transform:scale(.95);transition:transform .3s cubic-bezier(.2,.9,.3,1)}
.blog-modal-overlay.active .blog-modal-content{transform:scale(1)}
.blog-modal-header{display:flex;justify-content:space-between;align-items:center;padding:1rem 1.25rem;border-bottom:1px solid rgba(0,0,0,.07)}
.blog-modal-title{font-weight:700;margin:0}
.btn-close-modal{background:none;border:0;font-size:2rem;line-height:1;color:var(--muted);opacity:.7;transition:transform .2s,color .2s}
.btn-close-modal:hover{color:var(--brand);transform:rotate(90deg)}
.blog-modal-body{padding:1rem;overflow-y:auto}

/* ===== Tree styles ===== */
.blog-category-link{display:flex;align-items:center;gap:.65rem;padding:.55rem .75rem;border-radius:8px;color:var(--text);
font-weight:500;text-decoration:none;transition:background .15s ease,color .15s ease}
.blog-category-link:hover{background:rgba(var(--brand-rgb,162,14,56),.07);color:var(--brand)}
.blog-category-link.active{background:rgba(var(--brand-rgb,162,14,56),.1);color:var(--brand);font-weight:600}
.blog-category-link i{font-size:1.05rem}
.blog-category-link.root-all{font-weight:600}

.blog-category-tree{--indent: 16px}
.blog-tree{list-style:none;margin:0;padding:0}
.blog-tree.level-0{padding-left:0}
.blog-tree.level-1{padding-left:calc(var(--indent) + 4px)}
.blog-tree.level-2{padding-left:calc(var(--indent)*2 + 4px)}
.blog-tree.level-3{padding-left:calc(var(--indent)*3 + 4px)}

.tree-item{position:relative;display:flex;align-items:flex-start;gap:.35rem}
.toggle-branch{margin:.2rem 0 0 .1rem;flex:0 0 auto;width:26px;height:26px;border:0;border-radius:8px;background:transparent;
display:inline-flex;align-items:center;justify-content:center;cursor:pointer;transition:background .15s}
.toggle-branch:hover{background:rgba(0,0,0,.05)}
.toggle-branch .bi{transition:transform .2s ease}
.tree-item.expanded > .toggle-branch .bi{transform:rotate(90deg)}
.leaf-dot{width:6px;height:6px;border-radius:50%;background:#cbd5e1;margin:.7rem .35rem 0 .35rem;display:inline-block}

.children{margin-left: 26px} /* thụt nhẹ sau nút toggle */
</style>
@endonce

@once
@push('scripts-page')
<script>
// Mở modal (giữ nguyên)
(function() {
    const openBtn  = document.getElementById('open-blog-modal');
    const closeBtn = document.getElementById('blog-modal-close');
    const overlay  = document.getElementById('blog-modal-overlay');

    if (openBtn && closeBtn && overlay) {
        const openModal = (e) => { e.preventDefault(); overlay.classList.add('active'); overlay.setAttribute('aria-hidden','false'); };
        const closeModal = () => { overlay.classList.remove('active'); overlay.setAttribute('aria-hidden','true'); };
        openBtn.addEventListener('click', openModal);
        closeBtn.addEventListener('click', closeModal);
        overlay.addEventListener('click', (e)=>{ if(e.target===overlay) closeModal(); });
        document.addEventListener('keydown', (e)=>{ if(e.key==='Escape' && overlay.classList.contains('active')) closeModal(); });
    }
})();

// Toggle tree (event delegation để không cần rebind)
(function(){
    const body = document.querySelector('#blog-modal-overlay .blog-modal-body');
    if(!body) return;
    body.addEventListener('click', function(e){
        const btn = e.target.closest('.toggle-branch');
        if(!btn) return;
        const item = btn.closest('.tree-item');
        if(!item) return;
        const box = item.querySelector(':scope > .children');
        const expanded = item.classList.toggle('expanded');
        item.setAttribute('aria-expanded', expanded ? 'true' : 'false');
        if(box) box.style.display = expanded ? 'block' : 'none';
    });
})();
</script>
@endpush
@endonce
