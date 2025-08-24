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
                                <span class="badge badge-soft-{{ $map[$t->status] ?? 'secondary' }}">{{ $status_text[$t->status] ?? $t->status }}</span>
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
                            <i class="bi bi-chat-dots" style="font-size: 3rem;"></i>
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
        /* =================== Banner =================== */
        .support-banner {
            height: 260px;
            background-image: linear-gradient(rgba(0,0,0,0.45), rgba(0,0,0,0.45)),
                url('https://images.unsplash.com/photo-1524758631624-e2822e304c36?q=80&w=1600&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
        }
        .support-banner .breadcrumb-item a {
            color: #f8f9fa;
            text-decoration: none;
        }
        .support-banner .breadcrumb-item a:hover {
            color: var(--brand);
        }
        .support-banner .breadcrumb-item.active {
            color: #adb5bd;
        }

        /* =================== Form and Button Styles =================== */
        .form-control:focus, .form-select:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 0.2rem var(--ring);
        }
        .btn-brand {
            background-color: var(--brand);
            border-color: var(--brand);
            color: #fff;
            padding: 0.5rem 1rem;
            transition: transform 0.15s ease, box-shadow 0.15s ease, background 0.15s ease;
        }
        .btn-brand:hover {
            background-color: var(--brand-600);
            border-color: var(--brand-600);
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
        }
        .btn-outline-brand {
            color: var(--brand);
            border-color: var(--brand);
            padding: 0.45rem 0.9rem;
            transition: background 0.15s ease, color 0.15s ease;
        }
        .btn-outline-brand:hover {
            background-color: var(--brand);
            border-color: var(--brand);
            color: #fff;
        }
        .text-brand {
            color: var(--brand);
        }

        /* =================== Badges =================== */
        .badge-soft-info {
            background: rgba(13, 110, 253, 0.1);
            color: #0d6efd;
        }
        .badge-soft-brand {
            background: rgba(162, 14, 56, 0.1);
            color: var(--brand);
        }
        .badge-soft-success {
            background: rgba(25, 135, 84, 0.1);
            color: #198754;
        }
        .badge-soft-muted {
            background: rgba(125, 114, 108, 0.1);
            color: var(--muted);
        }
        .badge-soft-secondary {
            background: rgba(108, 117, 125, 0.1);
            color: #6c757d;
        }

        /* =================== Card Styles =================== */
        .card-glass {
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.92), rgba(255, 255, 255, 0.98));
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            border: 1px solid rgba(15, 23, 42, 0.04);
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }
        .card-glass:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        }
        .rounded-4 {
            border-radius: 1rem !important;
        }

        /* =================== Links =================== */
        a {
            color: var(--brand);
            text-decoration: none;
        }
        a:hover {
            color: var(--brand-600);
        }

        /* =================== Responsive Design =================== */
        @media (max-width: 991px) {
            .support-banner {
                height: 220px;
            }
            .support-banner .display-5 {
                font-size: 2rem;
            }
            .row.g-4 {
                gap: 1rem;
            }
            .col-md-6 {
                flex: 0 0 50%;
                max-width: 50%;
            }
        }

        @media (max-width: 767px) {
            .support-banner {
                height: 180px;
            }
            .support-banner .display-5 {
                font-size: 1.8rem;
            }
            .container {
                padding-left: 15px;
                padding-right: 15px;
            }
            .btn-brand {
                padding: 0.4rem 0.8rem;
                font-size: 0.9rem;
            }
            .btn-outline-brand {
                padding: 0.35rem 0.7rem;
                font-size: 0.85rem;
            }
            .card-body {
                padding: 1rem;
            }
            .card-glass i {
                font-size: 2.5rem;
            }
        }

        @media (max-width: 575px) {
            .support-banner {
                height: 160px;
            }
            .support-banner .display-5 {
                font-size: 1.6rem;
            }
            .support-banner .breadcrumb {
                font-size: 0.85rem;
            }
            .col-md-6 {
                flex: 0 0 100%;
                max-width: 100%;
            }
            .btn-brand {
                padding: 0.35rem 0.7rem;
                font-size: 0.85rem;
            }
            .btn-outline-brand {
                padding: 0.3rem 0.6rem;
                font-size: 0.8rem;
            }
            .card-glass {
                padding: 0.75rem;
            }
            .card-glass i {
                font-size: 2rem;
            }
            .card-body {
                padding: 0.75rem;
            }
        }
    </style>
@endpush

@push('scripts-page')
    <script>
        if (typeof AOS !== 'undefined') {
            AOS.init({ once: true, duration: 600, offset: 80 });
        }
    </script>
@endpush