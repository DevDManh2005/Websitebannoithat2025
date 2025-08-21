@extends('admins.layouts.app')
@section('title', 'Sửa Voucher')

@push('styles')
<style>
  #voucher-edit .bar{
    border-radius:16px; padding:12px;
    background:linear-gradient(140deg, rgba(196,111,59,.08), rgba(78,107,82,.06) 70%), var(--card);
    border:1px solid rgba(32,25,21,.08); box-shadow:0 6px 22px rgba(18,38,63,.06);
  }
  #voucher-edit .card-soft{
    border-radius:16px; border:1px solid rgba(32,25,21,.08);
    box-shadow:0 6px 22px rgba(18,38,63,.06); background:var(--card);
  }
  #voucher-edit .card-soft .card-header{ background:transparent; border-bottom:1px dashed rgba(32,25,21,.12) }
</style>
@endpush

@section('content')
<div id="voucher-edit" class="container-fluid">
  <div class="bar mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div class="d-flex align-items-center gap-2">
      <h1 class="h5 fw-bold mb-0">Sửa Voucher: <span class="mono">{{ $voucher->code }}</span></h1>
      <span class="text-muted small">Cập nhật giá trị & điều kiện áp dụng</span>
    </div>
    <div>
      <a href="{{ route('admin.vouchers.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Danh sách
      </a>
    </div>
  </div>

  <div class="card card-soft">
    <div class="card-header fw-semibold">Thông tin Voucher</div>
    <div class="card-body">
      <form action="{{ route('admin.vouchers.update', $voucher) }}" method="POST" class="d-grid gap-2">
        @csrf @method('PUT')
        @include('admins.vouchers._form')
        <div class="d-flex gap-2">
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-check2-circle me-1"></i> Cập nhật
          </button>
          <a href="{{ route('admin.vouchers.index') }}" class="btn btn-outline-secondary">Hủy</a>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
