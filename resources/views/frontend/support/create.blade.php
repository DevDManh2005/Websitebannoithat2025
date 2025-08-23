@extends('layouts.app')

@section('title', 'Tạo yêu cầu hỗ trợ')

@section('content')
    {{-- Banner --}}
    <div class="profile-banner d-flex align-items-center justify-content-center text-white mb-5">
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
        .profile-banner {
            height: 250px;
            background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('https://anphonghouse.com/wp-content/uploads/2018/06/hinh-nen-noi-that-dep-full-hd-so-43-0.jpg');
            background-size: cover;
            background-position: center;
        }
        .profile-banner .breadcrumb-item a { color: var(--sand); }
        .profile-banner .breadcrumb-item.active { color: var(--muted); }

        .support-banner{
            height: 260px;
            background-image: linear-gradient(rgba(0,0,0,.45), rgba(0,0,0,.45)),
                url('https://images.unsplash.com/photo-1524758631624-e2822e304c36?q=80&w=1600&auto=format&fit=crop');
            background-size: cover; background-position: center;
        }
        .support-banner .breadcrumb-item a{ color:#f8f9fa; }
        .support-banner .breadcrumb-item.active{ color:#adb5bd; }

        .form-control-modern, .form-select.form-control-modern{
            border-radius: .8rem;
            border: 1px solid #e9ecef;
            background: #fff;
        }
        .form-control-modern:focus, .form-select.form-control-modern:focus{
            border-color:var(--brand);
            box-shadow: 0 0 0 .2rem rgba(var(--brand-rgb),.15);
        }
        .card-glass {
            background: linear-gradient(180deg, rgba(255, 255, 255, .82), rgba(255, 255, 255, .95));
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(32, 25, 21, .08);
            border: 1px solid rgba(15, 23, 42, .04);
        }
        .btn-brand{
            background-color:var(--brand);
            border-color:var(--brand);
            color:var(--card);
        }
        .btn-brand:hover{
            background-color:var(--brand-600);
            border-color:var(--brand-600);
        }
        .text-brand{ color: var(--brand); }
    </style>
@endpush

@push('scripts-page')
    <script>
        if (typeof AOS !== 'undefined') {
            AOS.init({
                once:true,
                duration:600,
                offset:80
            });
        }
    </script>
@endpush
