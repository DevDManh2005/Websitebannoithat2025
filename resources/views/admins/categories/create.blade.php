{{-- resources/views/admins/categories/create.blade.php --}}
@extends('admins::layouts.app')


@section('title', 'Tạo danh mục mới')

@section('content')
<style>
  .card-soft{ border-radius:16px; border:1px solid rgba(32,25,21,.08) }
  .card-soft .card-header{ background:transparent; border-bottom:1px dashed rgba(32,25,21,.12) }
</style>

<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h5 mb-0 fw-bold">Tạo danh mục mới</h1>
    <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left"></i> Quay lại
    </a>
  </div>

  @if ($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
    </div>
  @endif

  <div class="card card-soft">
    <div class="card-header"><strong>Thông tin danh mục</strong></div>
    <div class="card-body">
      <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @include('admins.categories._form')
        <div class="mt-3 d-flex gap-2">
          <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Lưu</button>
          <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Hủy</a>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
