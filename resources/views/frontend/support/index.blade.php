@extends('layouts.app')

@section('title', 'Hỗ trợ của tôi')

@section('content')
    {{-- Banner --}}
    <div class="support-banner d-flex align-items-center justify-content-center text-white mb-5">
        <div class="container text-center" data-aos="fade-in">
            <h1 class="display-5">Hỗ Trợ Của Tôi</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-center bg-transparent p-0 m-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Hỗ trợ</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="container my-5">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert" data-aos="fade-up">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-4" data-aos="fade-up">
            <h5 class="mb-0 fw-bold text-brand">Danh sách yêu cầu</h5>
            <a href="{{ route('support.create') }}" class="btn btn-brand">
                <i class="bi bi-plus-circle me-1"></i> Tạo yêu cầu mới
            </a>
        </div>

        <div class="row g-4">
            @forelse($tickets as $t)
                @php
                    $map = [
                        'open' => 'info',
                        'in_progress' => 'brand',
                        'resolved' => 'success',
                        'closed' => 'muted',
                    ];
                    $status_text = [
                        'open' => 'Đang chờ',
                        'in_progress' => 'Đang xử lý',
                        'resolved' => 'Đã giải quyết',
                        'closed' => 'Đã đóng'
                    ];
                @endphp
                <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="{{ $loop->index*50 }}">
                    <div class="card card-glass h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                {{-- Sửa lỗi: Sử dụng màu tùy chỉnh --}}
                                <span class="badge badge-soft-brand bg-{{ $map[$t->status] ?? 'secondary' }}">{{ $status_text[$t->status] ?? $t->status }}</span>
                                <small class="text-muted">{{ $t->updated_at->diffForHumans() }}</small>
                            </div>
                            <h6 class="fw-semibold mb-2 text-truncate text-brand" title="{{ $t->subject }}">
                                {{ $t->subject }}
                            </h6>
                            <div class="text-muted small mb-3">
                                {{ \Illuminate\Support\Str::limit($t->message, 120) }}
                            </div>
                            <div class="mt-auto">
                                <a href="{{ route('support.show', $t) }}" class="btn btn-outline-brand w-100">
                                    Xem chi tiết
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12" data-aos="fade-up">
                    <div class="card card-glass text-center text-muted py-5 rounded-4">
                        <div class="card-body">
                            <i class="bi bi-chat-dots" style="font-size: 3rem; color: #ccc;"></i>
                            <p class="mt-3">Chưa có yêu cầu hỗ trợ nào.</p>
                            <a href="{{ route('support.create') }}" class="btn btn-brand rounded-pill mt-3">Tạo yêu cầu đầu tiên</a>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>

        <div class="mt-4" data-aos="fade-up">
            {{ $tickets->onEachSide(1)->links() }}
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .support-banner{
            height: 260px;
            background-image: linear-gradient(rgba(0,0,0,.45), rgba(0,0,0,.45)),
                url('https://images.unsplash.com/photo-1524758631624-e2822e304c36?q=80&w=1600&auto=format&fit=crop');
            background-size: cover; background-position: center;
        }
        .support-banner .breadcrumb-item a{ color:#f8f9fa; }
        .support-banner .breadcrumb-item.active{ color:#adb5bd; }

        .form-control:focus, .form-select:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 0.25rem rgba(var(--brand-rgb, 162,14,56),.25);
        }
        .btn-brand{ background-color:var(--brand); border-color:var(--brand); }
        .btn-brand:hover{ background-color:var(--brand-600); border-color:var(--brand-600); }
        .btn-outline-brand{ color:var(--brand); border-color:var(--brand); }
        .btn-outline-brand:hover{ background-color:var(--brand); border-color:var(--brand); color:#fff; }
        .text-brand{ color:var(--brand); }
        .badge.bg-primary{ background-color:#0d6efd !important; }
        .badge.bg-warning{ background-color:#ffc107 !important; }
        .badge.bg-success{ background-color:#198754 !important; }

        .card-glass {
            background: linear-gradient(180deg, rgba(255, 255, 255, .82), rgba(255, 255, 255, .95));
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(32, 25, 21, .08);
            border: 1px solid rgba(15, 23, 42, .04);
        }
        .rounded-4{ border-radius:1rem !important; }
        a { color:var(--brand); }
        a:hover { color:var(--brand-600); }
    </style>
@endpush

@push('scripts-page')
    <script>
        if (typeof AOS !== 'undefined') {
            AOS.init({ once:true, duration:600, offset:80 });
        }
    </script>
@endpush
