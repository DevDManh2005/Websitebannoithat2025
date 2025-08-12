@extends('layouts.app')
@section('title','Xác minh OTP')

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
      <div class="mb-3">
        <h1 class="fw-bold mb-1">
          @if(session('otp_type')=='verify_email') Xác minh email @else Khôi phục mật khẩu @endif
        </h1>
        <p class="text-muted mb-0">Chúng tôi đã gửi mã OTP tới: <strong>{{ session('otp_email') }}</strong></p>
      </div>

      {{-- Các khối alert tĩnh đã được xóa --}}

      <form method="POST" action="{{ route('verify.otp') }}" id="otpForm" class="needs-validation" novalidate>
        @csrf
        <input type="hidden" name="email" value="{{ session('otp_email') }}">
        {{-- Thêm validation cho trường ẩn OTP --}}
        <input type="hidden" name="code" id="otpHidden" required pattern="\d{6}" title="Vui lòng nhập đủ 6 số OTP.">

        <div class="mb-3">
          <label class="form-label" for="otpGroup">Nhập mã OTP</label>
          <div class="otp-group" id="otpGroup" data-length="6">
            @for($i=0;$i<6;$i++)
              <input class="otp-box" type="text" inputmode="numeric" maxlength="1" aria-label="OTP digit {{$i+1}}"/>
            @endfor
          </div>
          <div class="form-text">Mã có hiệu lực trong 10 phút.</div>
           @error('code') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </div>

        <button type="submit" class="btn btn-lg btn-gradient w-100 rounded-pill d-flex align-items-center justify-content-center gap-2">
          <i class="bi bi-shield-check"></i> Xác nhận
        </button>
      </form>

      <div class="d-flex justify-content-between align-items-center mt-3">
        <form method="POST" action="{{ route('resend.otp') }}">
          @csrf
          {{-- Thêm ID và disabled cho nút gửi lại --}}
          <button type="submit" id="resendBtn" class="btn btn-link p-0" disabled>Gửi lại mã OTP</button>
        </form>
        <a href="{{ route('login.form') }}" class="text-muted">Về đăng nhập</a>
      </div>
    </div>

    <div class="col-lg-7" data-aos="fade-left">
      <div class="reg-hero position-relative rounded-5 overflow-hidden">
        <img src="https://images.pexels.com/photos/1643383/pexels-photo-1643383.jpeg"
             class="w-100 h-100 object-fit-cover" alt="interior">
        <div class="reg-hero-overlay"></div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('styles')
{{-- CSS GIỮ NGUYÊN --}}
<style>
.btn-gradient{ background:linear-gradient(135deg,#ff4d6d,#c1126b); color:#fff; border:none; } .btn-gradient:hover{ filter:brightness(.98); color:#fff; } .reg-hero{ min-height: 480px; } .reg-hero-overlay{ position:absolute; inset:0; background:radial-gradient(120% 60% at 100% 0, rgba(255,255,255,.1), rgba(0,0,0,.35)); } .otp-group{ display:flex; gap:10px; } .otp-box{width:48px; height:56px; text-align:center; font-size:22px; border-radius:12px;border:1px solid #dee2e6; outline:none; transition:.2s; box-shadow:0 6px 18px rgba(16,24,40,.06); } .otp-box:focus{ border-color:#c1126b; box-shadow:0 0 0 .2rem rgba(193,18,107,.15); }
</style>
@endpush

@push('scripts-page')
<script>
  try{AOS&&AOS.init?.({duration:650,once:true,offset:60})}catch(e){}
  // OTP script (giữ nguyên)
  const group=document.getElementById('otpGroup'),hidden=document.getElementById('otpHidden');if(group&&hidden){const boxes=[...group.querySelectorAll('.otp-box')],sync=()=>hidden.value=boxes.map(b=>b.value).join('');boxes.forEach((box,idx)=>{box.addEventListener('input',e=>{box.value=box.value.replace(/[^0-9]/g,'').slice(0,1);if(box.value&&idx<boxes.length-1)boxes[idx+1].focus();sync();});box.addEventListener('keydown',e=>{if(e.key==='Backspace'&&!box.value&&idx>0){boxes[idx-1].focus();}if(e.key==='ArrowLeft'&&idx>0){boxes[idx-1].focus();}if(e.key==='ArrowRight'&&idx<boxes.length-1){boxes[idx+1].focus();}});box.addEventListener('paste',e=>{e.preventDefault();const data=(e.clipboardData||window.clipboardData).getData('text').replace(/\D/g,'').slice(0,boxes.length);data.split('').forEach((ch,i)=>{if(boxes[i])boxes[i].value=ch;});boxes[Math.min(data.length,boxes.length)-1]?.focus();sync();});});}

  /********** BẮT ĐẦU PHẦN BỔ SUNG VALIDATION, TOAST & COUNTDOWN **********/
  
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
  const otpForm = document.getElementById('otpForm');
  if (otpForm) {
      otpForm.addEventListener('submit', function(event) {
          if (!otpForm.checkValidity()) {
              event.preventDefault();
              event.stopPropagation();
              showToast('Dữ liệu không hợp lệ', 'Vui lòng nhập đủ 6 số OTP.');
          }
          otpForm.classList.add('was-validated');
      }, false);
  }

  // Cải tiến UX: Thêm bộ đếm ngược cho nút "Gửi lại mã"
  const resendBtn = document.getElementById('resendBtn');
  if (resendBtn) {
    let countdown = 60; // 60 giây
    const originalText = resendBtn.textContent;
    
    const timer = setInterval(() => {
      countdown--;
      resendBtn.textContent = `Gửi lại sau (${countdown}s)`;
      if (countdown <= 0) {
        clearInterval(timer);
        resendBtn.textContent = originalText;
        resendBtn.disabled = false;
      }
    }, 1000);
  }

  // Script hiển thị lỗi từ server (sau khi tải lại trang)
  document.addEventListener('DOMContentLoaded', function() {
    @if (session('success'))
      showToast('Thành công', `{!! session('success') !!}`, 'success');
    @endif
    
    @if ($errors->any())
      showToast('Có lỗi xảy ra', '<ul>@foreach($errors->all() as $error)<li>{!! $error !!}</li>@endforeach</ul>', 'danger');
    @endif
  });
</script>
@endpush