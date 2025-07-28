@extends('admins.layouts.app')

@section('title', 'Chi tiết nhà cung cấp')

@section('content')
<h1 class="mb-4">Chi tiết nhà cung cấp #{{ $supplier->id }}</h1>
<dl class="row">
  <dt class="col-sm-3">Tên</dt>
  <dd class="col-sm-9">{{ $supplier->name }}</dd>

  <dt class="col-sm-3">Người liên hệ</dt>
  <dd class="col-sm-9">{{ $supplier->contact_name ?? '—' }}</dd>

  <dt class="col-sm-3">Điện thoại</dt>
  <dd class="col-sm-9">{{ $supplier->phone        ?? '—' }}</dd>

  <dt class="col-sm-3">Email</dt>
  <dd class="col-sm-9">{{ $supplier->email        ?? '—' }}</dd>

  <dt class="col-sm-3">Địa chỉ</dt>
  <dd class="col-sm-9">{{ $supplier->address      ?? '—' }}</dd>
</dl>
<a href="{{ route('admin.suppliers.index') }}" class="btn btn-secondary">Quay lại</a>
@endsection
