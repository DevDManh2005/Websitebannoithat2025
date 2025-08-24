{{-- resources/views/admins/brands/show.blade.php --}}
@extends('admins::layouts.app')


@section('title','Chi tiết Thương hiệu')

@section('content')
@php
    use Illuminate\Support\Str;
    $logo = $brand->logo ?? null;
    $logoUrl = $logo
        ? (Str::startsWith($logo, ['http://','https://','//']) ? $logo : asset('storage/'.$logo))
        : null;
@endphp

<style>
  .card-soft{ border-radius:16px; border:1px solid rgba(32,25,21,.08) }
  .card-soft .card-header{ background:transparent; border-bottom:1px dashed rgba(32,25,21,.12) }
  .badge.bg-success-soft{ background:#e5f7ed; color:#1e6b3a }
  .badge.bg-secondary-soft{ background:#ececec; color:#545b62 }
</style>

<div class="container-fluid">

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h5 mb-0 fw-bold">Chi tiết Thương hiệu</h1>
    <div class="d-flex gap-2">
      <a href="{{ route('admin.brands.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Quay lại
      </a>
      <a href="{{ route('admin.brands.edit', $brand) }}" class="btn btn-primary">
        <i class="bi bi-pencil-square"></i> Sửa
      </a>
    </div>
  </div>

  <div class="row g-3">
    <div class="col-lg-8">
      <div class="card card-soft">
        <div class="card-header"><strong>Thông tin</strong></div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table align-middle mb-0">
              <tbody>
                <tr>
                  <th style="width:220px">ID</th>
                  <td>#{{ $brand->id }}</td>
                </tr>
                <tr>
                  <th>Tên</th>
                  <td class="fw-semibold">{{ $brand->name }}</td>
                </tr>
                <tr>
                  <th>Slug</th>
                  <td><code>{{ $brand->slug ?? '—' }}</code></td>
                </tr>
                <tr>
                  <th>Trạng thái</th>
                  <td>
                    @if($brand->is_active)
                      <span class="badge bg-success-soft">Hoạt động</span>
                    @else
                      <span class="badge bg-secondary">Không hoạt động</span>
                    @endif
                  </td>
                </tr>
                <tr>
                  <th>Ngày tạo</th>
                  <td>{{ $brand->created_at?->format('d/m/Y H:i') ?? '—' }}</td>
                </tr>
                <tr>
                  <th>Ngày cập nhật</th>
                  <td>{{ $brand->updated_at?->format('d/m/Y H:i') ?? '—' }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card card-soft">
        <div class="card-header"><strong>Logo</strong></div>
        <div class="card-body text-center">
          @if($logoUrl)
            <img src="{{ $logoUrl }}" alt="{{ $brand->name }}" class="img-fluid rounded" style="max-height:180px">
          @else
            <div class="text-muted">Chưa có logo</div>
          @endif
        </div>
      </div>
    </div>
  </div>

</div>
@endsection
