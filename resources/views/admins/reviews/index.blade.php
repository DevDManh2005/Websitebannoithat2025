@extends(in_array(auth()->user()->role->name ?? '', ['staff','nhanvien']) ? 'staffs.layouts.app' : 'admins.layouts.app')

@section('title', 'Quản lý Đánh giá')

@section('content')
@php
    use Illuminate\Support\Str;

    // Chuẩn hoá tập dữ liệu hiện có (trong trang) để suy luận trạng thái
    $list    = method_exists($reviews, 'items') ? $reviews->items() : $reviews;
    $byId    = [];
    foreach ($list as $r) { $byId[$r->id] = $r; }

    // Map: parentId => đã có phản hồi từ admin/staff?
    $hasStaffReply = [];
    foreach ($list as $r) {
        if (Str::startsWith($r->review, '[reply:#') && preg_match('/^\[reply:#(\d+)\]/u', $r->review, $m)) {
            $pid = (int) $m[1];
            $isStaffAuthor = in_array(optional(optional($r->user)->role)->name, ['admin','nhanvien','staff']);
            if ($isStaffAuthor) { $hasStaffReply[$pid] = true; }
        }
    }

    $totalReviews = method_exists($reviews,'total') ? $reviews->total() : $reviews->count();
@endphp

<style>
    .card-soft { border-radius: 16px; border: 1px solid rgba(32,25,21,.08); }
    .card-soft .card-header { background: transparent; border-bottom: 1px dashed rgba(32,25,21,.12); }
    .filter-bar {
        border-radius: 16px; padding: 12px;
        background: linear-gradient(140deg, rgba(196,111,59,.08), rgba(78,107,82,.06) 70%), #fff;
        border: 1px solid rgba(32,25,21,.08);
    }
    .table td, .table th { vertical-align: middle; }
    .badge.bg-success-soft { background: #e5f7ed; color: #1e6b3a; }
    .badge.bg-warning-soft { background: #fff4d8; color: #7a5600; }
    .badge.bg-secondary-soft { background: #f0f0f0; color: #555; }
    .badge.bg-info-soft { background:#e8f3ff; color:#0b63c5; }
    .review-snippet {
        display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical;
        overflow: hidden; max-width: 100%;
    }
    .star { color: #f5b300; }
    .product-link { font-weight: 600; text-decoration: none; }
    .thumb-mini { width: 44px; height: 44px; object-fit: cover; border-radius: 8px; border: 1px solid rgba(32,25,21,.08); background: #faf6f0; cursor: zoom-in; }
    .tr-reply { background: rgba(0,0,0,.015); }
    .text-clip { max-width: 520px; }

    /* Responsive */
    @media (max-width: 1199px) {.text-clip {max-width: 420px;}}
    @media (max-width: 991px)  {.filter-bar { padding: 10px; } .thumb-mini { width:40px; height:40px; } .text-clip{max-width: 360px;}}
    @media (max-width: 767px)  {
        .filter-bar .d-flex.align-items-center { flex-direction: column; align-items: flex-start; gap: 1rem; }
        .filter-bar form .col-12 { margin-bottom: 0.5rem; }
        .thumb-mini { width: 36px; height: 36px; }
        .table th, .table td { font-size: 0.85rem; padding: 0.55rem; }
        .review-snippet { -webkit-line-clamp: 2; } .text-clip{max-width: 260px;}
    }
    @media (max-width: 575px)  {
        .filter-bar { padding: 8px; }
        .thumb-mini { width: 32px; height: 32px; }
        .table th, .table td { font-size: 0.8rem; padding: 0.45rem; }
        .h1, .h5 { font-size: 1.1rem; }
        .btn { font-size: 0.8rem; padding: 0.3rem 0.6rem; }
        .text-clip{max-width: 200px;}
    }
</style>

<div class="container-fluid">
    {{-- Thanh tiêu đề + lọc --}}
    <div class="filter-bar mb-3">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <h1 class="h5 mb-0 fw-bold">Đánh giá sản phẩm</h1>
                <span class="text-muted small">({{ number_format($totalReviews) }} mục)</span>
            </div>
            {{-- Bộ lọc phụ trợ trên giao diện --}}
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="only-unreplied">
                <label class="form-check-label" for="only-unreplied">Chỉ hiển thị <strong>chưa phản hồi</strong></label>
            </div>
        </div>

        {{-- Lọc nhanh --}}
        <form class="row g-2 mt-2" method="get" action="{{ route('admin.reviews.index') }}">
            <div class="col-12 col-md-7">
                <div class="input-group">
                    <span class="input-group-text bg-transparent"><i class="bi bi-search"></i></span>
                    <input type="text" name="q" class="form-control" value="{{ request('q') }}" placeholder="Tìm theo sản phẩm / người đánh giá…">
                </div>
            </div>
            <div class="col-6 col-md-3">
                <select name="rating" class="form-select">
                    <option value="">-- Xếp hạng --</option>
                    @for($i=5;$i>=1;$i--)
                        <option value="{{ $i }}" {{ (string)request('rating')===(string)$i ? 'selected' : '' }}>{{ $i }} sao</option>
                    @endfor
                </select>
            </div>
            <div class="col-6 col-md-2 d-grid">
                <button class="btn btn-outline-secondary"><i class="bi bi-funnel me-1"></i>Lọc</button>
            </div>
        </form>
    </div>

    <div class="card card-soft shadow">
        <div class="card-header d-flex justify-content-between align-items-center">
            <strong>Danh sách đánh giá</strong>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success"><i class="bi bi-check-circle me-1"></i>{{ session('success') }}</div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="reviews-table">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 22%; min-width: 180px;">Sản phẩm</th>
                            <th style="width: 18%; min-width: 160px;">Người đánh giá</th>
                            <th class="text-center" style="width: 14%; min-width: 120px;">Loại / Xếp hạng</th>
                            <th style="width: 31%; min-width: 240px;">Nội dung</th>
                            <th class="text-end" style="width: 15%; min-width: 160px;">Trạng thái / Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($reviews as $review)
                            @php
                                $isReply = Str::startsWith($review->review, '[reply:#');
                                $content = $isReply ? preg_replace('/^\[reply:#\d+\]\s*/u', '', $review->review) : $review->review;

                                // Root: đã có phản hồi từ staff/admin chưa?
                                $rootReplied = !$isReply && !empty($hasStaffReply[$review->id]);

                                // Nếu là reply → có đang phản hồi tới review của admin/staff không?
                                $notifyStaff = false;
                                $replyParentId = null;
                                if ($isReply && preg_match('/^\[reply:#(\d+)\]/u', $review->review, $m)) {
                                    $replyParentId = (int)$m[1];
                                    $parent = $byId[$replyParentId] ?? null;
                                    if ($parent) {
                                        $parentIsStaff = in_array(optional(optional($parent->user)->role)->name, ['admin','nhanvien','staff']);
                                        if ($parentIsStaff) $notifyStaff = true;
                                    }
                                }
                            @endphp

                            <tr class="{{ $isReply ? 'tr-reply' : '' }}"
                                data-root="{{ $isReply ? '0' : '1' }}"
                                data-replied="{{ $rootReplied ? '1' : '0' }}">
                                {{-- Sản phẩm --}}
                                <td>
                                    <a class="product-link" href="{{ route('admin.products.show', $review->product_id) }}" title="Xem sản phẩm">
                                        {{ optional($review->product)->name ?? ('SP #'.$review->product_id) }}
                                    </a>
                                    @if($isReply && $replyParentId)
                                        <div class="small text-muted">↳ trả lời #{{ $replyParentId }}</div>
                                    @endif
                                </td>

                                {{-- Người đánh giá --}}
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="https://i.pravatar.cc/32?u={{ $review->user_id }}" class="rounded-circle border" width="28" height="28" alt="avt">
                                        <div>
                                            <div>{{ optional($review->user)->name ?? '—' }}</div>
                                            <small class="text-muted">{{ optional($review->user)->email ?? '' }}</small>
                                        </div>
                                    </div>
                                </td>

                                {{-- Loại / Xếp hạng --}}
                                <td class="text-center">
                                    @if(!$isReply)
                                        <div class="star mb-1">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="bi {{ $i <= $review->rating ? 'bi-star-fill' : 'bi-star' }}"></i>
                                            @endfor
                                        </div>
                                        <small class="text-muted">{{ $review->rating }}/5</small>
                                    @else
                                        <span class="badge bg-secondary-soft">Phản hồi</span>
                                        @if($notifyStaff)
                                            <div class="mt-1">
                                                <span class="badge bg-info-soft"><i class="bi bi-bell-fill me-1"></i>Phản hồi tới BQT</span>
                                            </div>
                                        @endif
                                    @endif
                                </td>

                                {{-- Nội dung --}}
                                <td class="text-clip">
                                    <div class="review-snippet mb-1">{{ $content }}</div>
                                    @if($review->image)
                                        <img src="{{ asset('storage/' . $review->image) }}" class="thumb-mini review-image"
                                             data-full="{{ asset('storage/' . $review->image) }}" alt="Ảnh đánh giá" loading="lazy">
                                    @endif
                                    <div class="small text-muted mt-1">
                                        {{ $review->created_at?->format('d/m/Y H:i') }}
                                    </div>
                                </td>

                                {{-- Trạng thái / Hành động --}}
                                <td class="text-end">
                                    @if(!$isReply)
                                        <div class="mb-2">
                                            @if($rootReplied)
                                                <span class="badge bg-success-soft align-middle" id="badge-replied-{{ $review->id }}">
                                                    <i class="bi bi-check2-circle me-1"></i>Đã phản hồi
                                                </span>
                                            @else
                                                <span class="badge bg-warning-soft align-middle" id="badge-replied-{{ $review->id }}">
                                                    <i class="bi bi-hourglass-split me-1"></i>Cần phản hồi
                                                </span>
                                            @endif
                                        </div>

                                        {{-- Nút Phản hồi (duy nhất) --}}
                                        <button
                                            class="btn btn-sm btn-outline-primary"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#reply-{{ $review->id }}">
                                            <i class="bi bi-reply me-1"></i>Phản hồi
                                        </button>

                                        {{-- Form reply (collapse) --}}
                                        <div class="collapse mt-2 text-start" id="reply-{{ $review->id }}">
                                            <form method="POST"
                                                  action="{{ route('reviews.reply', $review) }}"
                                                  class="js-reply-form"
                                                  data-parent="{{ $review->id }}">
                                                @csrf
                                                <div class="input-group">
                                                    <textarea name="content" class="form-control" rows="2" placeholder="Nhập phản hồi..."></textarea>
                                                    <button class="btn btn-primary">
                                                        <span class="js-send-text"><i class="bi bi-send"></i></span>
                                                        <span class="js-sending d-none"><span class="spinner-border spinner-border-sm me-1"></span>Đang gửi</span>
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    @else
                                        {{-- Hàng reply: không có nút nào --}}
                                        <div class="text-muted small">—</div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Chưa có đánh giá nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Phân trang --}}
            <div class="mt-3">
                {{ method_exists($reviews,'withQueryString') ? $reviews->withQueryString()->links() : $reviews->links() }}
            </div>
        </div>
    </div>
</div>

{{-- Modal xem ảnh lớn --}}
<div class="modal fade" id="imgModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <img src="" id="imgModalSrc" class="w-100" alt="Ảnh đánh giá" style="border-radius: .5rem;">
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    // Xem ảnh lớn
    const thumbs = document.querySelectorAll('.review-image');
    const modalEl = document.getElementById('imgModal');
    const modalImg = document.getElementById('imgModalSrc');
    thumbs.forEach(t => {
        t.addEventListener('click', () => {
            const full = t.getAttribute('data-full');
            if(full){ modalImg.src = full; new bootstrap.Modal(modalEl).show(); }
        });
    });

    // Lọc "Chỉ chưa phản hồi" trên giao diện
    const onlyUnreplied = document.getElementById('only-unreplied');
    const table = document.getElementById('reviews-table');
    onlyUnreplied?.addEventListener('change', () => {
        table.querySelectorAll('tbody tr').forEach(tr => {
            const isRoot = tr.getAttribute('data-root') === '1';
            const replied = tr.getAttribute('data-replied') === '1';
            tr.style.display = (onlyUnreplied.checked && isRoot && replied) ? 'none' : '';
        });
    });

    // Submit phản hồi (AJAX) → chuyển trạng thái "Đã phản hồi" ngay
    document.querySelectorAll('.js-reply-form').forEach(f => {
        f.addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = f.querySelector('button');
            const txt = f.querySelector('.js-send-text');
            const spn = f.querySelector('.js-sending');
            const parentId = f.dataset.parent;

            btn.disabled = true; txt.classList.add('d-none'); spn.classList.remove('d-none');

            try {
                const res = await fetch(f.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: new FormData(f)
                });
                // Dù backend trả redirect/html, coi như thành công nếu status 2xx/3xx
                if (res.ok || (res.status >= 300 && res.status < 400)) {
                    // Cập nhật badge và thuộc tính
                    const badge = document.getElementById('badge-replied-' + parentId);
                    if (badge) {
                        badge.className = 'badge bg-success-soft align-middle';
                        badge.innerHTML = '<i class="bi bi-check2-circle me-1"></i>Đã phản hồi';
                    }
                    const row = f.closest('tr');
                    if (row) row.setAttribute('data-replied','1');

                    // Thu gọn form
                    const collapseEl = f.closest('.collapse');
                    if (collapseEl) bootstrap.Collapse.getOrCreateInstance(collapseEl).hide();

                    // Toast nhỏ
                    if (window.Swal) {
                        Swal.fire({ icon:'success', title:'Đã gửi phản hồi', showConfirmButton:false, timer:1400, toast:true, position:'top-end' });
                    }
                } else {
                    if (window.Swal) Swal.fire({ icon:'error', title:'Không gửi được phản hồi' });
                }
            } catch (e2) {
                if (window.Swal) Swal.fire({ icon:'error', title:'Lỗi kết nối' });
            } finally {
                btn.disabled = false; txt.classList.remove('d-none'); spn.classList.add('d-none');
            }
        });
    });
});
</script>
@endpush
@endsection
