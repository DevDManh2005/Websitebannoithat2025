@extends('layouts.app')

@section('title', 'Tạo yêu cầu hỗ trợ')

@section('content')
    {{-- Banner --}}
    <div class="support-banner d-flex align-items-center justify-content-center text-white mb-5">
        <div class="container text-center" data-aos="fade-in">
            <h1 class="display-4">Tạo Yêu Cầu Hỗ Trợ</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-center bg-transparent p-0 m-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('support.index') }}">Hỗ trợ</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Tạo yêu cầu</li>
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
        @if ($errors->any())
            <div class="alert alert-danger" data-aos="fade-up">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row justify-content-center">
            <div class="col-lg-10" data-aos="fade-up">
                <div class="card card-glass rounded-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 text-brand">Nhập thông tin yêu cầu</h5>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST" action="{{ route('support.store') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                                <input type="text" name="subject" class="form-control form-control-modern @error('subject') is-invalid @enderror"
                                        value="{{ old('subject') }}" placeholder="Ví dụ: Vấn đề đơn hàng #1234">
                                @error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nội dung <span class="text-danger">*</span></label>
                                <textarea name="message" rows="6" class="form-control form-control-modern @error('message') is-invalid @enderror"
                                            placeholder="Mô tả chi tiết vấn đề của bạn...">{{ old('message') }}</textarea>
                                @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Đính kèm (tuỳ chọn)</label>
                                <input type="file" name="attachment" class="form-control form-control-modern @error('attachment') is-invalid @enderror">
                                @error('attachment')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <div class="form-text">Hỗ trợ: jpg, jpeg, png, webp, pdf, doc, docx, xls, xlsx (≤ 4MB).</div>
                            </div>

                            <div class="d-flex gap-2">
                                <button class="btn btn-brand"><i class="bi bi-send me-1"></i> Gửi yêu cầu</button>
                                <a href="{{ route('support.index') }}" class="btn btn-outline-secondary">Về danh sách</a>
                            </div>
                            <div class="text-muted small mt-2">Cần ít nhất nội dung. Tệp đính kèm là không bắt buộc.</div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* =================== Banner =================== */
        .support-banner {
            height: 250px;
            background-image: linear-gradient(rgba(0, 0, 0, 0.45), rgba(0, 0, 0, 0.45)),
                url('https://images.unsplash.com/photo-1524758631624-e2822e304c36?q=80&w=1600&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
        }
        .support-banner .breadcrumb-item a {
            color: var(--sand);
            text-decoration: none;
        }
        .support-banner .breadcrumb-item a:hover {
            color: var(--brand);
        }
        .support-banner .breadcrumb-item.active {
            color: var(--muted);
        }

        /* =================== Form and Button Styles =================== */
        .form-control-modern, .form-select.form-control-modern {
            border-radius: 0.8rem;
            border: 1px solid #e9ecef;
            background: #fff;
            font-size: 1rem;
        }
        .form-control-modern:focus, .form-select.form-control-modern:focus {
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
        .btn-outline-secondary {
            color: #6c757d;
            border-color: #6c757d;
            padding: 0.45rem 0.9rem;
            transition: background 0.15s ease, color 0.15s ease;
        }
        .btn-outline-secondary:hover {
            background-color: #6c757d;
            border-color: #6c757d;
            color: #fff;
        }
        .text-brand {
            color: var(--brand);
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
            .support-banner .display-4 {
                font-size: 2rem;
            }
            .col-lg-10 {
                flex: 0 0 100%;
                max-width: 100%;
            }
            .card-body {
                padding: 1.5rem;
            }
        }

        @media (max-width: 767px) {
            .support-banner {
                height: 180px;
            }
            .support-banner .display-4 {
                font-size: 1.8rem;
            }
            .container {
                padding-left: 15px;
                padding-right: 15px;
            }
            .card-body {
                padding: 1rem;
            }
            .btn-brand {
                padding: 0.4rem 0.8rem;
                font-size: 0.9rem;
            }
            .btn-outline-secondary {
                padding: 0.35rem 0.7rem;
                font-size: 0.85rem;
            }
            .form-control-modern, .form-select.form-control-modern {
                font-size: 0.9rem;
            }
        }

        @media (max-width: 575px) {
            .support-banner {
                height: 160px;
            }
            .support-banner .display-4 {
                font-size: 1.6rem;
            }
            .support-banner .breadcrumb {
                font-size: 0.85rem;
            }
            .card-glass {
                padding: 0.75rem;
            }
            .card-body {
                padding: 0.75rem;
            }
            .btn-brand {
                padding: 0.35rem 0.7rem;
                font-size: 0.85rem;
            }
            .btn-outline-secondary {
                padding: 0.3rem 0.6rem;
                font-size: 0.8rem;
            }
            .form-control-modern, .form-select.form-control-modern {
                font-size: 0.85rem;
            }
        }
    </style>
@endpush

@push('scripts-page')
    <script>
        if (typeof AOS !== 'undefined') {
            AOS.init({
                once: true,
                duration: 600,
                offset: 80
            });
        }
    </script>
@endpush