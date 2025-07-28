@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h2>Quên mật khẩu</h2>

    @if (session('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    <form method="POST" action="{{ route('forgot') }}">
        @csrf

        <div class="mb-3">
            <label class="form-label">Email đã đăng ký</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-warning">Gửi mã OTP</button>
    </form>
</div>
@endsection
