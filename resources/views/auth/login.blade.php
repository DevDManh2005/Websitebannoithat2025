@extends('layouts.app')

@section('title','Đăng nhập')

@section('content')
{{-- HTML CỦA TOAST (GIỮ NGUYÊN) --}}
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

<div class="auth-wrap">
  <div class="container-xl">
    <div class="row g-4 align-items-center">

      {{-- Col: Form --}}
      <div class="col-lg-5" data-aos="fade-up" data-aos-delay="50">
        <div class="auth-card shadow-lg">
          <div class="mb-4">
            <div class="d-flex align-items-center gap-2">
              <span class="bullet"></span>
              <h2 class="fw-bold mb-0">Chào mừng trở lại</h2>
            </div>
            <p class="text-muted mb-0 mt-2">
              Đăng nhập để tiếp tục mua sắm nội thất đẹp đẽ của bạn.
            </p>
          </div>

          <form method="POST" action="{{ route('login') }}" class="mt-2 needs-validation" novalidate>
            @csrf

            {{-- Email --}}
            <div class="mb-3">
              <label class="form-label small text-muted" for="emailInput">Email</label>
              <div class="input-group input-group-lg fancy-input">
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                {{-- BỔ SUNG: Thêm ID, pattern và title --}}
                <input type="email"
                       id="emailInput"
                       name="email"
                       class="form-control @error('email') is-invalid @enderror"
                       placeholder="admin@gmail.com"
                       value="{{ old('email') }}"
                       autocomplete="username"
                       required
                       pattern=".*@gmail\.com"
                       title="Email phải có đuôi @gmail.com">
              </div>
              @error('email')
                <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>

            {{-- Password --}}
            <div class="mb-2">
              <div class="d-flex justify-content-between align-items-center">
                <label class="form-label small text-muted mb-2" for="passwordInput">Mật khẩu</label>
                <a class="small text-decoration-none"
                   href="{{ route('forgot.form') }}">Quên mật khẩu?</a>
              </div>

              <div class="input-group input-group-lg fancy-input">
                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                <input type="password"
                       name="password"
                       id="passwordInput"
                       class="form-control @error('password') is-invalid @enderror"
                       placeholder="••••••••"
                       autocomplete="current-password"
                       required>
                <button class="input-group-text bg-transparent border-start-0 eye-btn"
                        type="button" id="togglePassword" aria-label="Hiện/ẩn mật khẩu">
                  <i class="bi bi-eye"></i>
                </button>
              </div>
              @error('password')
                <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>

            {{-- Remember --}}
            <div class="form-check mb-3">
              <input class="form-check-input" type="checkbox" name="remember" id="remember">
              <label class="form-check-label" for="remember">Ghi nhớ lần sau</label>
            </div>

            {{-- Submit --}}
            <button type="submit" class="btn btn-lg btn-primary-gradient w-100 d-flex align-items-center justify-content-center gap-2">
              <i class="bi bi-box-arrow-in-right"></i> Đăng nhập
            </button>

            <p class="text-center mt-3 mb-0">
              Chưa có tài khoản?
              <a href="{{ route('register.form') }}" class="fw-semibold">Đăng ký</a>
            </p>

            <div class="text-muted small mt-3 d-flex align-items-center gap-2">
              <i class="bi bi-shield-lock"></i>
              Được bảo vệ bằng chuẩn TLS & mã hoá mật khẩu.
            </div>
          </form>
        </div>
      </div>

      {{-- Col: Hero image --}}
      <div class="col-lg-7 d-none d-lg-block" data-aos="zoom-in" data-aos-delay="120">
        <div class="hero-visual shadow-lg">
          <img src="https://images.unsplash.com/photo-1524758631624-e2822e304c36?q=80&w=1600&auto=format&fit=crop"
               class="w-100 h-100 object-fit-cover" alt="Nội thất hiện đại">
          <div class="glass-badge">
            <i class="bi bi-stars me-2"></i> Sắm nội thất theo phong cách của bạn
          </div>
        </div>
      </div>

    </div>
  </div>
  <div class="bg-blob blob-1"></div>
  <div class="bg-blob blob-2"></div>
</div>
@endsection

@push('styles')
{{-- CSS GIỮ NGUYÊN --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css"/>
<style>
.auth-wrap{position:relative;min-height:calc(100dvh - 120px);display:flex;align-items:center;padding-block:48px;background:radial-gradient(1200px 600px at -10% -10%, #f8e8ff 0%, transparent 60%),radial-gradient(900px 500px at 110% 120%, #e6f7ff 0%, transparent 60%),linear-gradient(180deg,#ffffff, #f8fafc);}.bg-blob{ position:absolute; filter:blur(50px); opacity:.35; z-index:0; }.blob-1{ width:360px; height:360px; left:-60px; top:40px; background:#ffd6e7; border-radius:50%; }.blob-2{ width:420px; height:420px; right:-80px; bottom:-20px; background:#d6f4ff; border-radius:50%; }.auth-card{position:relative;z-index:1;backdrop-filter: blur(8px);background: rgba(255,255,255,.78);border: 1px solid rgba(15,23,42,.06);border-radius: 18px;padding: 28px 26px;}.bullet{width:12px; height:12px; border-radius:50%;background:linear-gradient(135deg,#ff54a6,#d1006f);box-shadow:0 0 0 4px rgba(255,84,166,.2);}.fancy-input .input-group-text{ background:transparent; border-right:0; }.fancy-input .form-control{ border-left:0; }.fancy-input .form-control, .fancy-input .input-group-text{border-color: #e7eaf0;transition: all .2s ease;}.fancy-input:focus-within .form-control,.fancy-input:focus-within .input-group-text{border-color:#b83280;box-shadow:0 0 0 4px rgba(184,50,128,.08);}.eye-btn{ cursor:pointer; }.btn-primary-gradient{--start:#ff2a6b;--end:#b83280;border:0;background:linear-gradient(90deg,var(--start),var(--end));color:#fff;border-radius: 999px;}.btn-primary-gradient:hover{filter:brightness(.95);transform: translateY(-1px);}.hero-visual{position:relative;height:560px;border-radius:22px;overflow:hidden;background:#fff;}.hero-visual:before{content:"";position:absolute; inset:0;background:radial-gradient(800px 240px at 40% -10%, rgba(255,255,255,.7), transparent 55%);pointer-events:none;}.glass-badge{position:absolute; left:18px; bottom:18px;backdrop-filter: blur(8px);color:#1f2937;background:rgba(255,255,255,.7);border:1px solid rgba(15,23,42,.08);border-radius:999px;padding:10px 14px;font-weight:600;display:inline-flex; align-items:center;}@media (max-width: 991.98px){.auth-wrap{ padding-block:32px; }.hero-visual{ height:240px; margin-top:12px; }.auth-card{ padding:22px 18px; }}
</style>
@endpush

@push('scripts-page')
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>
  AOS.init({ duration: 700, once: true, offset: 60 });

  // Toggle password
  (function(){
    const btn = document.getElementById('togglePassword');
    const input = document.getElementById('passwordInput');
    if(!btn || !input) return;
    btn.addEventListener('click', () => {
      const isPwd = input.type === 'password';
      input.type = isPwd ? 'text' : 'password';
      btn.innerHTML = isPwd ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>';
      input.focus();
    });
  })();
  
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
              
              let errorMessages = [];
              form.querySelectorAll(':invalid').forEach(el => {
                  let fieldName = document.querySelector(`label[for="${el.id}"]`)?.textContent || el.name;
                  let message = el.validity.valueMissing 
                      ? `Vui lòng nhập ${fieldName.toLowerCase()}.`
                      : el.title || `Trường ${fieldName} không hợp lệ.`;
                  errorMessages.push(`<li>${message}</li>`);
              });

              showToast('Vui lòng kiểm tra lại', `<ul>${[...new Set(errorMessages)].join('')}</ul>`);
          }
          form.classList.add('was-validated');
      }, false);
  }

  // Script hiển thị lỗi từ server (sau khi tải lại trang)
  document.addEventListener('DOMContentLoaded', function() {
    @if (session('success'))
      showToast('Thành công', `{!! session('success') !!}`, 'success');
    @endif
    
    @if (session('error'))
      showToast('Có lỗi xảy ra', `{!! session('error') !!}`, 'danger');
    @endif

    @if ($errors->any())
      let serverErrorHtml = '<ul>';
      @foreach ($errors->all() as $error)
        serverErrorHtml += '<li>{!! $error !!}</li>';
      @endforeach
      serverErrorHtml += '</ul>';
      showToast('Dữ liệu không hợp lệ', serverErrorHtml, 'danger');
    @endif
  });
</script>
@endpush