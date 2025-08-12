@extends('layouts.app')

@section('title', 'Đăng ký')

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


<div class="container py-5">
  <div class="row g-4 align-items-center">
    {{-- FORM --}}
    <div class="col-lg-5 mx-auto" data-aos="fade-right">
      <div class="mb-4">
        <h1 class="fw-bold mb-1">Tạo tài khoản</h1>
        <p class="text-muted mb-0">Tham gia để nhận ưu đãi và theo dõi đơn hàng dễ dàng.</p>
      </div>

      <form method="POST" action="{{ route('register') }}" class="needs-validation" novalidate>
        @csrf

        {{-- BƯỚC 1: THÊM ID VÀO CÁC INPUT ĐỂ JS DỄ DÀNG XỬ LÝ --}}

        {{-- Name --}}
        <div class="mb-3">
          <label class="form-label" for="name">Họ tên</label>
          <div class="input-group input-group-lg rounded-4 shadow-sm-soft">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-person"></i></span>
            <input type="text" id="name" name="name" value="{{ old('name') }}"
                   class="form-control border-start-0" placeholder="Nguyễn Văn A" required
                   pattern="^[^0-9]+$" title="Họ tên không được chứa ký tự số.">
          </div>
          @error('name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </div>

        {{-- Email --}}
        <div class="mb-3">
          <label class="form-label" for="email">Email</label>
          <div class="input-group input-group-lg rounded-4 shadow-sm-soft">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-envelope"></i></span>
            <input type="email" id="email" name="email" value="{{ old('email') }}"
                   class="form-control border-start-0" placeholder="ban@gmail.com" required
                   pattern=".*@gmail\.com" title="Email phải có đuôi @gmail.com">
          </div>
          @error('email') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </div>

        {{-- Password --}}
        <div class="mb-3">
          <div class="d-flex justify-content-between align-items-center">
            <label class="form-label mb-1" for="pwd">Mật khẩu</label>
          </div>
          <div class="input-group input-group-lg rounded-4 shadow-sm-soft">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-lock"></i></span>
            <input id="pwd" type="password" name="password" class="form-control border-start-0"
                   placeholder="Tối thiểu 8 ký tự" required minlength="8">
            <button type="button" id="togglePwd" class="input-group-text bg-white border-start-0 eye-btn">
              <i class="bi bi-eye"></i>
            </button>
          </div>
          @error('password') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
          <div class="form-text">Gợi ý: dùng chữ hoa, số và ký tự đặc biệt.</div>
        </div>

        {{-- Confirm Password --}}
        <div class="mb-3">
          <label class="form-label" for="pwd2">Xác nhận mật khẩu</label>
          <div class="input-group input-group-lg rounded-4 shadow-sm-soft">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-shield-lock"></i></span>
            <input id="pwd2" type="password" name="password_confirmation" class="form-control border-start-0"
                   placeholder="Nhập lại mật khẩu" required>
            <button type="button" id="togglePwd2" class="input-group-text bg-white border-start-0 eye-btn">
              <i class="bi bi-eye"></i>
            </button>
          </div>
        </div>

        {{-- Terms --}}
        <div class="form-check mb-3">
          <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
          <label class="form-check-label" for="terms">
            Tôi đồng ý với <a href="#" class="link-underline link-underline-opacity-0">Điều khoản</a> & <a href="#" class="link-underline link-underline-opacity-0">Chính sách</a>.
          </label>
          <div class="invalid-feedback">Vui lòng chấp nhận điều khoản.</div>
        </div>

        {{-- Submit --}}
        <button type="submit" class="btn btn-lg btn-gradient w-100 rounded-pill d-flex align-items-center justify-content-center gap-2">
          <i class="bi bi-person-plus"></i> Đăng ký
        </button>

        <div class="text-center mt-3">
          <span class="text-muted">Đã có tài khoản?</span>
          <a href="{{ route('login.form') }}" class="fw-semibold">Đăng nhập</a>
        </div>

        <div class="d-flex align-items-center gap-2 text-muted small mt-3">
          <i class="bi bi-shield-lock"></i>
          <span>Thông tin được mã hóa qua TLS.</span>
        </div>
      </form>
    </div>

    {{-- IMAGE / BRAND SIDE --}}
    <div class="col-lg-7" data-aos="fade-left">
      <div class="reg-hero position-relative rounded-5 overflow-hidden">
        <img src="https://images.pexels.com/photos/3637728/pexels-photo-3637728.jpeg"
             alt="Interior" class="w-100 h-100 object-fit-cover" />
        <div class="reg-hero-overlay"></div>
        <div class="reg-hero-badge">
          <div class="h5 mb-0">Nội thất đẹp</div>
          <div class="small opacity-75">Trải nghiệm mua sắm hiện đại</div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('styles')
{{-- CSS GIỮ NGUYÊN --}}
<style>
  .shadow-sm-soft{ box-shadow: 0 6px 18px rgba(16,24,40,.06); }
  .btn-gradient{
    background: linear-gradient(135deg,#ff4d6d,#c1126b);
    color:#fff; border:none;
  }
  .btn-gradient:hover{ filter: brightness(0.98); color:#fff; }
  .eye-btn{ cursor:pointer; }
  .reg-hero{ min-height: 520px; }
  .reg-hero-overlay{
    position:absolute; inset:0;
    background: radial-gradient(120% 60% at 100% 0, rgba(255,255,255,.1), rgba(0,0,0,.35));
  }
  .reg-hero-badge{
    position:absolute; left:24px; bottom:24px;
    background: rgba(255,255,255,.85);
    backdrop-filter: blur(6px);
    padding:14px 18px; border-radius:16px;
    box-shadow: 0 10px 30px rgba(16,24,40,.15);
  }
  .form-control:focus{ box-shadow: 0 0 0 .2rem rgba(193,18,107,.15); }
</style>
@endpush

@push('scripts-page')
<script>
  // AOS init
  try{ AOS && AOS.init?.({ duration: 650, once: true, offset: 60 }); }catch(e){}

  // Toggle password visibility
  const toggle = (btnId, inputId) => {
    const btn = document.getElementById(btnId);
    const input = document.getElementById(inputId);
    if(!btn || !input) return;
    btn.addEventListener('click', () => {
      const isPwd = input.type === 'password';
      input.type = isPwd ? 'text' : 'password';
      btn.innerHTML = isPwd ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>';
      input.focus();
    });
  };
  toggle('togglePwd','pwd');
  toggle('togglePwd2','pwd2');

  /********** BẮT ĐẦU PHẦN SỬA ĐỔI LOGIC VALIDATION **********/

  const toastEl = document.getElementById('liveToast');
  const toastTitle = document.getElementById('toast-title');
  const toastBody = document.getElementById('toast-body');
  const toast = toastEl ? new bootstrap.Toast(toastEl) : null;

  function showToast(title, body, type = 'danger') {
    if (!toast || !toastTitle || !toastBody) return;
    toastTitle.textContent = title;
    toastBody.innerHTML = body;
    toastEl.classList.remove('text-bg-success', 'text-bg-danger');
    toastEl.classList.add(type === 'success' ? 'text-bg-success' : 'text-bg-danger');
    toast.show();
  }

  const form = document.querySelector('form.needs-validation');
  const passwordInput = document.getElementById('pwd');
  const confirmPasswordInput = document.getElementById('pwd2');

  // BƯỚC 2a: TẠO HÀM KIỂM TRA MẬT KHẨU TRÙNG KHỚP
  function validatePasswordConfirmation() {
    if (passwordInput.value !== confirmPasswordInput.value) {
      confirmPasswordInput.setCustomValidity('Mật khẩu xác nhận không trùng khớp.');
    } else {
      confirmPasswordInput.setCustomValidity(''); // Rất quan trọng: phải xóa lỗi khi đã khớp
    }
  }

  // GỌI HÀM KIỂM TRA MỖI KHI NGƯỜI DÙNG NHẬP (real-time)
  if(passwordInput && confirmPasswordInput) {
    passwordInput.addEventListener('input', validatePasswordConfirmation);
    confirmPasswordInput.addEventListener('input', validatePasswordConfirmation);
  }

  // BƯỚC 2b: CẬP NHẬT TOÀN BỘ LOGIC KHI SUBMIT FORM
  if (form) {
    form.addEventListener('submit', function(event) {
      // Chạy kiểm tra mật khẩu lần cuối trước khi submit
      validatePasswordConfirmation();
      
      if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
        
        let errorMessages = [];
        
        // Duyệt qua từng trường để lấy lỗi tùy chỉnh
        form.querySelectorAll('input[required]').forEach(el => {
          if (!el.validity.valid) {
            let fieldName = document.querySelector(`label[for="${el.id}"]`)?.textContent || el.name;
            let message = '';
            
            // Xây dựng thông báo lỗi tùy chỉnh cho từng trường hợp
            if (el.validity.valueMissing) {
              message = `Vui lòng nhập ${fieldName.toLowerCase()}.`;
              if (el.type === 'checkbox') message = 'Vui lòng chấp nhận điều khoản.';
            } else if (el.validity.patternMismatch) {
              message = el.title; // Lấy từ thuộc tính title của input
            } else if (el.validity.tooShort) {
              message = `${fieldName} phải có ít nhất ${el.minLength} ký tự.`;
            } else if (el.validity.customError) {
              message = el.validationMessage; // Lấy lỗi "mật khẩu không khớp"
            } else {
              message = `Trường ${fieldName} không hợp lệ.`;
            }
            errorMessages.push(`<li>${message}</li>`);
          }
        });
        
        // Loại bỏ các thông báo trùng lặp và hiển thị toast
        const uniqueErrors = [...new Set(errorMessages)];
        showToast('Vui lòng sửa các lỗi sau', `<ul>${uniqueErrors.join('')}</ul>`, 'danger');
      }
      form.classList.add('was-validated');
    }, false);
  }

  // Script kiểm tra lỗi từ server (giữ nguyên)
  document.addEventListener('DOMContentLoaded', function() {
    @if (session('success'))
      showToast('Thành công', `{!! session('success') !!}`, 'success');
    @endif
    @if ($errors->any())
      let serverErrorHtml = '<ul>';
      @foreach ($errors->all() as $error)
        serverErrorHtml += '<li>{!! $error !!}</li>';
      @endforeach
      serverErrorHtml += '</ul>';
      showToast('Có lỗi xảy ra', serverErrorHtml, 'danger');
    @endif
  });
</script>
@endpush