@extends('layouts.app')
@section('title','Quên mật khẩu')

@section('content')
{{-- Thêm HTML của Toast --}}
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1100">
  <div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="toast-header">
      <i class="bi bi-bell-fill me-2"></i>
      <strong class="me-auto" id="toast-title">Thông báo</strong>
      <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
    <div class="toast-body" id="toast-body">
      Nội dung thông báo ở đây.
    </div>
  </div>
</div>

<div class="container py-5">
  <div class="row g-4 align-items-center">
    <div class="col-lg-5 mx-auto" data-aos="fade-right">
      <div class="mb-4">
        <h1 class="fw-bold mb-1">Quên mật khẩu</h1>
        <p class="text-muted mb-0">Nhập email đã đăng ký, chúng tôi sẽ gửi mã OTP để đặt lại.</p>
      </div>

      {{-- Các khối Alert tĩnh đã được xóa --}}

      <form method="POST" action="{{ route('forgot') }}" class="needs-validation" novalidate>
        @csrf
        <div class="mb-3">
          <label class="form-label" for="emailInput">Email đã đăng ký</label>
          <div class="input-group input-group-lg rounded-4 shadow-sm-soft">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-envelope"></i></span>
            {{-- Bổ sung: Thêm ID, pattern và title --}}
            <input type="email"
                   id="emailInput"
                   name="email"
                   class="form-control border-start-0"
                   placeholder="ban@gmail.com"
                   required
                   pattern=".*@gmail\.com"
                   title="Email phải có đuôi @gmail.com">
          </div>
          {{-- Giữ lại @error để hiển thị lỗi inline từ server nếu có --}}
           @error('email')
            <div class="invalid-feedback d-block">{{ $message }}</div>
          @enderror
        </div>

        <button type="submit" class="btn btn-lg btn-gradient w-100 rounded-pill d-flex align-items-center justify-content-center gap-2">
          <i class="bi bi-send"></i> Gửi mã OTP
        </button>

        <div class="text-center mt-3">
          <a href="{{ route('login.form') }}" class="fw-semibold">Quay lại đăng nhập</a>
        </div>
      </form>
    </div>

    <div class="col-lg-7" data-aos="fade-left">
      <div class="reg-hero position-relative rounded-5 overflow-hidden">
        <img src="https://images.pexels.com/photos/271805/pexels-photo-271805.jpeg"
             class="w-100 h-100 object-fit-cover" alt="interior">
        <div class="reg-hero-overlay"></div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
  .shadow-sm-soft{ box-shadow:0 6px 18px rgba(16,24,40,.06); }
  .btn-gradient{ background:linear-gradient(135deg,#ff4d6d,#c1126b); color:#fff; border:none; }
  .btn-gradient:hover{ filter:brightness(.98); color:#fff; }
  .reg-hero{ min-height: 480px; }
  .reg-hero-overlay{ position:absolute; inset:0; background:radial-gradient(120% 60% at 100% 0, rgba(255,255,255,.1), rgba(0,0,0,.35)); }
  .form-control:focus{ box-shadow:0 0 0 .2rem rgba(193,18,107,.15); }
</style>
@endpush

@push('scripts-page')
<script>
  try{AOS&&AOS.init?.({duration:650,once:true,offset:60})}catch(e){};

  /********** BẮT ĐẦU PHẦN BỔ SUNG **********/

  const toastEl = document.getElementById('liveToast');
  const toast = toastEl ? new bootstrap.Toast(toastEl, { delay: 5000 }) : null;

  function showToast(title, body, type = 'danger') {
    if (!toast) return;
    const toastTitle = document.getElementById('toast-title');
    const toastBody = document.getElementById('toast-body');
    toastTitle.textContent = title;
    toastBody.innerHTML = body;
    toastEl.classList.remove('text-bg-success', 'text-bg-danger');
    toastEl.classList.add(type === 'success' ? 'text-bg-success' : 'text-bg-danger');
    toast.show();
  }

  // Bổ sung script kiểm tra lỗi client-side khi submit
  const form = document.querySelector('form.needs-validation');
  if (form) {
      form.addEventListener('submit', function(event) {
          if (!form.checkValidity()) {
              event.preventDefault();
              event.stopPropagation();
              
              const emailInput = document.getElementById('emailInput');
              let message = 'Vui lòng kiểm tra lại thông tin.';

              if (emailInput && !emailInput.validity.valid) {
                 message = emailInput.validity.valueMissing 
                      ? `Vui lòng nhập email.`
                      : emailInput.title || `Email không hợp lệ.`;
              }
              
              showToast('Dữ liệu không hợp lệ', message);
          }
          form.classList.add('was-validated');
      }, false);
  }

  // Script hiển thị lỗi/thành công từ server (sau khi tải lại trang)
  document.addEventListener('DOMContentLoaded', function() {
    // Sửa lại để bắt session 'success' thay vì 'message' cho nhất quán
    @if (session('success'))
      showToast('Thành công', `{!! session('success') !!}`, 'success');
    @endif
    
    @if ($errors->any())
      // Dùng $errors->first() vì form này thường chỉ có 1 lỗi chính
      showToast('Có lỗi xảy ra', `{!! $errors->first() !!}`, 'danger');
    @endif
  });
</script>
@endpush