@extends('layouts.app')
@section('title', 'Đặt lại mật khẩu')

@section('content')
  {{-- HTML của Toast --}}
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
      <h1 class="fw-bold mb-1">Đặt lại mật khẩu</h1>
      <p class="text-muted mb-0">Nhập email, mã OTP đã nhận và mật khẩu mới.</p>
      </div>

      {{-- Alert tĩnh đã được xóa --}}

      <form method="POST" action="{{ route('reset.password') }}" class="needs-validation" novalidate>
      @csrf

      {{-- Email --}}
      <div class="mb-3">
        <label class="form-label" for="emailInput">Email</label>
        <div class="input-group input-group-lg rounded-4 shadow-sm-soft">
        <span class="input-group-text bg-white border-end-0"><i class="bi bi-envelope"></i></span>
        <input type="email" id="emailInput" name="email" class="form-control border-start-0"
          placeholder="ban@gmail.com" required pattern=".*@gmail\.com" title="Email phải có đuôi @gmail.com">
        </div>
        @error('email') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
      </div>

      {{-- OTP --}}
      <div class="mb-3">
        <label class="form-label" for="otpGroup">Mã OTP</label>
        <div class="otp-group" id="otpGroup" data-length="6">
        @for($i = 0; $i < 6; $i++)
      <input class="otp-box" type="text" inputmode="numeric" maxlength="1" aria-label="OTP digit {{$i + 1}}" />
      @endfor
        </div>
        {{-- Thêm validation cho trường ẩn OTP --}}
        <input type="hidden" name="code" id="otpHidden" required pattern="\d{6}" title="Vui lòng nhập đủ 6 số OTP.">
        <div class="form-text">Mã hiệu lực 10 phút.</div>
        @error('code') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
      </div>

      {{-- New password --}}
      <div class="mb-3">
        <label class="form-label" for="pwd">Mật khẩu mới</label>
        <div class="input-group input-group-lg rounded-4 shadow-sm-soft">
        <span class="input-group-text bg-white border-end-0"><i class="bi bi-lock"></i></span>
        {{-- Sửa placeholder và thêm minlength --}}
        <input id="pwd" type="password" name="password" class="form-control border-start-0"
          placeholder="Tối thiểu 9 ký tự" required minlength="9">
        <button type="button" id="togglePwd" class="input-group-text bg-white border-start-0 eye-btn"><i
          class="bi bi-eye"></i></button>
        </div>
        @error('password') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
      </div>

      {{-- Confirm --}}
      <div class="mb-3">
        <label class="form-label" for="pwd2">Xác nhận mật khẩu mới</label>
        <div class="input-group input-group-lg rounded-4 shadow-sm-soft">
        <span class="input-group-text bg-white border-end-0"><i class="bi bi-shield-lock"></i></span>
        <input id="pwd2" type="password" name="password_confirmation" class="form-control border-start-0"
          placeholder="Nhập lại mật khẩu" required>
        <button type="button" id="togglePwd2" class="input-group-text bg-white border-start-0 eye-btn"><i
          class="bi bi-eye"></i></button>
        </div>
      </div>

      <button type="submit"
        class="btn btn-lg btn-gradient w-100 rounded-pill d-flex align-items-center justify-content-center gap-2">
        <i class="bi bi-check2-circle"></i> Đổi mật khẩu
      </button>
      </form>
    </div>

    <div class="col-lg-7" data-aos="fade-left">
      <div class="reg-hero position-relative rounded-5 overflow-hidden">
      <img src="https://images.pexels.com/photos/271743/pexels-photo-271743.jpeg" class="w-100 h-100 object-fit-cover"
        alt="interior">
      <div class="reg-hero-overlay"></div>
      </div>
    </div>
    </div>
  </div>
@endsection

@push('styles')
  {{-- CSS GIỮ NGUYÊN --}}
  <style>
    .shadow-sm-soft {
    box-shadow: 0 6px 18px rgba(16, 24, 40, .06);
    }

    .btn-gradient {
    background: linear-gradient(135deg, #ff4d6d, #c1126b);
    color: #fff;
    border: none;
    }

    .btn-gradient:hover {
    filter: brightness(.98);
    color: #fff;
    }

    .eye-btn {
    cursor: pointer;
    }

    .reg-hero {
    min-height: 480px;
    }

    .reg-hero-overlay {
    position: absolute;
    inset: 0;
    background: radial-gradient(120% 60% at 100% 0, rgba(255, 255, 255, .1), rgba(0, 0, 0, .35));
    }

    .form-control:focus {
    box-shadow: 0 0 0 .2rem rgba(193, 18, 107, .15);
    }

    .otp-group {
    display: flex;
    gap: 10px;
    }

    .otp-box {
    width: 48px;
    height: 56px;
    text-align: center;
    font-size: 22px;
    border-radius: 12px;
    border: 1px solid #dee2e6;
    outline: none;
    transition: .2s;
    box-shadow: 0 6px 18px rgba(16, 24, 40, .06);
    }

    .otp-box:focus {
    border-color: #c1126b;
    box-shadow: 0 0 0 .2rem rgba(193, 18, 107, .15);
    }
  </style>
@endpush

@push('scripts-page')
  <script>
    try { AOS && AOS.init?.({ duration: 650, once: true, offset: 60 }) } catch (e) { }
    const t = (b, i) => { const btn = document.getElementById(b), inp = document.getElementById(i); if (!btn || !inp) return; btn.addEventListener('click', () => { const p = inp.type === 'password'; inp.type = p ? 'text' : 'password'; btn.innerHTML = p ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>'; inp.focus(); }); }; t('togglePwd', 'pwd'); t('togglePwd2', 'pwd2');
    const group = document.getElementById('otpGroup'), hidden = document.getElementById('otpHidden'); if (group && hidden) { const boxes = [...group.querySelectorAll('.otp-box')], sync = () => hidden.value = boxes.map(b => b.value).join(''); boxes.forEach((box, idx) => { box.addEventListener('input', e => { box.value = box.value.replace(/[^0-9]/g, '').slice(0, 1); if (box.value && idx < boxes.length - 1) boxes[idx + 1].focus(); sync(); }); box.addEventListener('keydown', e => { if (e.key === 'Backspace' && !box.value && idx > 0) { boxes[idx - 1].focus(); } if (e.key === 'ArrowLeft' && idx > 0) { boxes[idx - 1].focus(); } if (e.key === 'ArrowRight' && idx < boxes.length - 1) { boxes[idx + 1].focus(); } }); box.addEventListener('paste', e => { e.preventDefault(); const data = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '').slice(0, boxes.length); data.split('').forEach((ch, i) => { if (boxes[i]) boxes[i].value = ch; }); boxes[Math.min(data.length, boxes.length) - 1]?.focus(); sync(); }); }); }

    /********** BẮT ĐẦU PHẦN BỔ SUNG VALIDATION & TOAST **********/

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

    const form = document.querySelector('form.needs-validation');
    const passwordInput = document.getElementById('pwd');
    const confirmPasswordInput = document.getElementById('pwd2');

    function validatePasswordConfirmation() {
    if (passwordInput.value !== confirmPasswordInput.value) {
      confirmPasswordInput.setCustomValidity('Mật khẩu xác nhận không trùng khớp.');
    } else {
      confirmPasswordInput.setCustomValidity('');
    }
    }

    if (passwordInput && confirmPasswordInput) {
    passwordInput.addEventListener('input', validatePasswordConfirmation);
    confirmPasswordInput.addEventListener('input', validatePasswordConfirmation);
    }

    if (form) {
    form.addEventListener('submit', function (event) {
      validatePasswordConfirmation();

      if (!form.checkValidity()) {
      event.preventDefault();
      event.stopPropagation();

      let errorMessages = [];

      // Dùng querySelectorAll trên một danh sách các ID
      const fields = ['emailInput', 'otpHidden', 'pwd', 'pwd2'];
      fields.forEach(id => {
        const el = document.getElementById(id);
        if (el && !el.validity.valid) {
        let fieldName = document.querySelector(`label[for="${el.id}"]`)?.textContent || el.name;
        let message = '';

        if (el.validity.valueMissing) {
          message = `Vui lòng nhập ${fieldName.toLowerCase()}.`;
        } else if (el.validity.patternMismatch) {
          message = el.title; // Lấy từ thuộc tính title
        } else if (el.validity.tooShort) {
          message = `${fieldName} phải có ít nhất ${el.minLength} ký tự.`;
        } else if (el.validity.customError) {
          message = el.validationMessage; // Lỗi mật khẩu không khớp
        } else {
          message = `Trường ${fieldName} không hợp lệ.`;
        }
        errorMessages.push(`<li>${message}</li>`);
        }
      });

      const uniqueErrors = [...new Set(errorMessages)];
      showToast('Vui lòng sửa các lỗi sau', `<ul>${uniqueErrors.join('')}</ul>`);
      }
      form.classList.add('was-validated');
    }, false);
    }

    // Script hiển thị lỗi từ server
    document.addEventListener('DOMContentLoaded', function () {
    @if (session('success'))
    showToast('Thành công', `{!! session('success') !!}`, 'success');
    @endif

    @if ($errors->any())
    showToast('Có lỗi xảy ra', '<ul>@foreach($errors->all() as $error)<li>{!! $error !!}</li>@endforeach</ul>', 'danger');
    @endif
    });
  </script>
@endpush