@extends('layouts.app')

@section('title', 'Tạo yêu cầu hỗ trợ')

@section('content')
  {{-- Banner --}}
  <div class="support-banner d-flex align-items-center justify-content-center text-white mb-5">
    <div class="container text-center" data-aos="fade-in">
      <h1 class="display-5">Tạo Yêu Cầu Hỗ Trợ</h1>
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
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-white py-3">
            <h5 class="mb-0">Nhập thông tin yêu cầu</h5>
          </div>
          <div class="card-body p-4">
            <form method="POST" action="{{ route('support.store') }}" enctype="multipart/form-data">
              @csrf
              <div class="mb-3">
                <label class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                <input type="text" name="subject" class="form-control @error('subject') is-invalid @enderror"
                       value="{{ old('subject') }}" placeholder="Ví dụ: Vấn đề đơn hàng #1234">
                @error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>

              <div class="mb-3">
                <label class="form-label">Nội dung <span class="text-danger">*</span></label>
                <textarea name="message" rows="6" class="form-control @error('message') is-invalid @enderror"
                          placeholder="Mô tả chi tiết vấn đề của bạn...">{{ old('message') }}</textarea>
                @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>

              <div class="mb-3">
                <label class="form-label">Đính kèm (tuỳ chọn)</label>
                <input type="file" name="attachment" class="form-control @error('attachment') is-invalid @enderror">
                @error('attachment')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="form-text">Hỗ trợ: jpg, jpeg, png, webp, pdf, doc, docx, xls, xlsx (≤ 4MB).</div>
              </div>

              <div class="d-flex gap-2">
                <button class="btn btn-primary"><i class="bi bi-send me-1"></i> Gửi yêu cầu</button>
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
  {{-- AOS + theme --}}
  <link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css">
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
      border-color: #A20E38;
      box-shadow: 0 0 0 0.25rem rgba(162,14,56,.25);
    }
    .btn-primary{ background-color:#A20E38; border-color:#A20E38; }
    .btn-primary:hover{ background-color:#8b0c30; border-color:#8b0c30; }
    a { color:#A20E38; }
    a:hover { color:#8b0c30; }
  </style>
@endpush

@push('scripts')
  <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
  <script>AOS.init({ once:true, duration:600, offset:80 });</script>
@endpush
