@extends(auth()->user()->role->name === 'staff' ? 'staffs.layouts.app' : 'admins.layouts.app')

@section('title', 'Sửa danh mục: ' . $category->name)

@push('styles')
<style>
    /* Đồng bộ với style từ app.blade.php và create.blade.php */
    .category-edit .card {
        background: linear-gradient(180deg, rgba(234,223,206,.25), transparent), var(--card);
        border: 0;
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        animation: fadeIn 0.5s ease;
    }

    .category-edit .card-header {
        border-bottom: 1px dashed rgba(32,25,21,.1);
        background: transparent;
        font-weight: 600;
        color: var(--text);
    }

    .category-edit .alert-danger {
        background: #fde7e7; /* Đồng bộ với bg-danger-soft */
        color: #992f2f; /* Đồng bộ với badge bg-danger-soft */
        border-radius: 12px;
        border: 1px solid rgba(153,47,47,.25);
        animation: fadeIn 0.5s ease;
    }

    .category-edit .btn-primary,
    .category-edit .btn-outline-primary,
    .category-edit .btn-secondary {
        border-radius: 10px;
        transition: background-color 0.2s ease, border-color 0.2s ease, transform 0.2s ease;
    }

    .category-edit .btn-primary {
        background: var(--brand);
        border-color: var(--brand);
    }

    .category-edit .btn-primary:hover {
        background: var(--brand-600);
        border-color: var(--brand-600);
        transform: translateY(-1px);
    }

    .category-edit .btn-outline-primary {
        color: var(--brand);
        border-color: var(--brand);
    }

    .category-edit .btn-outline-primary:hover {
        background: var(--brand);
        color: #fff;
        transform: translateY(-1px);
    }

    .category-edit .btn-secondary {
        background: var(--muted);
        border-color: var(--muted);
    }

    .category-edit .btn-secondary:hover {
        background: var(--wood-800);
        border-color: var(--wood-800);
        transform: translateY(-1px);
    }

    .category-edit .btn.ripple {
        position: relative;
        overflow: hidden;
    }

    .category-edit .btn.ripple:after {
        content: '';
        position: absolute;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(255,255,255,.35);
        transform: translate(-50%,-50%);
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.6s, width 0.6s, height 0.6s;
    }

    .category-edit .btn.ripple:active:after {
        width: 100px;
        height: 100px;
        opacity: 0.6;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Responsive */
    @media (max-width: 767.98px) {
        .category-edit .h5 {
            font-size: 1.1rem;
        }

        .category-edit .card {
            border-radius: 12px;
        }

        .category-edit .btn {
            font-size: 0.9rem;
            padding: 0.5rem 1rem;
        }

        .category-edit .alert-danger {
            font-size: 0.9rem;
            padding: 0.75rem;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid category-edit">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h5 mb-0 fw-bold">Sửa danh mục: {{ $category->name }}</h1>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-primary ripple">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            <ul class="mb-0">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-header"><strong>Thông tin danh mục</strong></div>
        <div class="card-body">
            <form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @include('admins.categories._form')
                <div class="mt-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary ripple"><i class="bi bi-save"></i> Cập nhật</button>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary ripple">Hủy</a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Hiệu ứng ripple cho các nút
    document.querySelectorAll('.category-edit .btn.ripple').forEach(el => {
        el.addEventListener('click', function(e) {
            const btn = e.currentTarget;
            const rect = btn.getBoundingClientRect();
            const circle = document.createElement('span');
            const d = Math.max(rect.width, rect.height);
            Object.assign(circle.style, {
                width: d + 'px',
                height: d + 'px',
                position: 'absolute',
                left: (e.clientX - rect.left) + 'px',
                top: (e.clientY - rect.top) + 'px',
                transform: 'translate(-50%,-50%)',
                background: 'rgba(255,255,255,.35)',
                borderRadius: '50%',
                pointerEvents: 'none',
                opacity: '0.6',
                transition: 'opacity .6s, width .6s, height .6s'
            });
            btn.appendChild(circle);
            requestAnimationFrame(() => {
                circle.style.width = circle.style.height = (d * 1.8) + 'px';
                circle.style.opacity = '0';
            });
            setTimeout(() => circle.remove(), 600);
        });
    });
</script>
@endpush
@endsection