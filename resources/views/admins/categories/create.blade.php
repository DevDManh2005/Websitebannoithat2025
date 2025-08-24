@extends(auth()->user()->role->name === 'staff' ? 'staffs.layouts.app' : 'admins.layouts.app')

@section('title', 'Tạo danh mục mới')

@push('styles')
<style>
    /* Đồng bộ với style từ app.blade.php */
    .category-create .card {
        background: linear-gradient(180deg, rgba(234,223,206,.25), transparent), var(--card);
        border: 0;
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        animation: fadeIn 0.5s ease;
    }

    .category-create .card-header {
        border-bottom: 1px dashed rgba(32,25,21,.1);
        background: transparent;
        font-weight: 600;
        color: var(--text);
    }

    .category-create .alert-danger {
        background: #fde7e7; /* Đồng bộ với bg-danger-soft */
        color: #992f2f; /* Đồng bộ với badge bg-danger-soft */
        border-radius: 12px;
        border: 1px solid rgba(153,47,47,.25);
        animation: fadeIn 0.5s ease;
    }

    .category-create .btn-primary,
    .category-create .btn-outline-primary,
    .category-create .btn-secondary {
        border-radius: 10px;
        transition: background-color 0.2s ease, border-color 0.2s ease, transform 0.2s ease;
    }

    .category-create .btn-primary {
        background: var(--brand);
        border-color: var(--brand);
    }

    .category-create .btn-primary:hover {
        background: var(--brand-600);
        border-color: var(--brand-600);
        transform: translateY(-1px);
    }

    .category-create .btn-outline-primary {
        color: var(--brand);
        border-color: var(--brand);
    }

    .category-create .btn-outline-primary:hover {
        background: var(--brand);
        color: #fff;
        transform: translateY(-1px);
    }

    .category-create .btn-secondary {
        background: var(--muted);
        border-color: var(--muted);
    }

    .category-create .btn-secondary:hover {
        background: var(--wood-800);
        border-color: var(--wood-800);
        transform: translateY(-1px);
    }

    .category-create .btn.ripple {
        position: relative;
        overflow: hidden;
    }

    .category-create .btn.ripple:after {
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

    .category-create .btn.ripple:active:after {
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
        .category-create .h5 {
            font-size: 1.1rem;
        }

        .category-create .card {
            border-radius: 12px;
        }

        .category-create .btn {
            font-size: 0.9rem;
            padding: 0.5rem 1rem;
        }

        .category-create .alert-danger {
            font-size: 0.9rem;
            padding: 0.75rem;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid category-create">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h5 mb-0 fw-bold">Tạo danh mục mới</h1>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-primary ripple">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
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
            <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @include('admins.categories._form')
                <div class="mt-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary ripple"><i class="bi bi-save"></i> Lưu</button>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary ripple">Hủy</a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Hiệu ứng ripple cho các nút
    document.querySelectorAll('.category-create .btn.ripple').forEach(el => {
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