@extends('layouts.app')

@section('content')
  <div class="container mt-5">
    <h3 class="mb-4">
      @if(session('otp_type')=='verify_email') 
        XÁC MINH EMAIL 
      @else 
        KHÔI PHỤC MẬT KHẨU 
      @endif
    </h3>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
      <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <p>Chúng tôi đã gửi mã OTP tới: <strong>{{ session('otp_email') }}</strong></p>

    <form method="POST" action="{{ route('verify.otp') }}">
      @csrf
      <input type="hidden" name="email" value="{{ session('otp_email') }}">
      <div class="mb-3">
        <label for="code" class="form-label">Mã OTP</label>
        <input type="text" id="code" name="code" class="form-control" placeholder="Nhập mã OTP">
      </div>
      <button class="btn btn-primary">Xác nhận</button>
    </form>

    <hr>

    <form method="POST" action="{{ route('resend.otp') }}">
      @csrf
      <button class="btn btn-link">Gửi lại mã OTP</button>
    </form>
  </div>
@endsection
