@extends('admins.layouts.app')

@section('title', 'Chi tiết danh mục: ' . $category->name)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Chi tiết danh mục: {{ $category->name }}</h1>
        <div>
            <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-warning">Sửa</a>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Quay lại</a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header">Thông tin</div>
                <div class="card-body">
                    @if($category->image)
                        <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" class="img-fluid rounded mb-3">
                    @endif
                    <dl>
                        <dt>Tên</dt>
                        <dd>{{ $category->name }}</dd>
                        <dt>Slug</dt>
                        <dd>{{ $category->slug }}</dd>
                        <dt>Danh mục cha</dt>
                        <dd>{{ $category->parent->name ?? '—' }}</dd>
                        <dt>Trạng thái</dt>
                        <dd>@if($category->is_active) <span class="badge bg-success">Hiện</span> @else <span class="badge bg-secondary">Ẩn</span> @endif</dd>
                        <dt>Vị trí</dt>
                        <dd>{{ $category->position }}</dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header">Sản phẩm thuộc danh mục này</div>
                <div class="card-body">
                    @if($category->products->isNotEmpty())
                        <ul>
                        @foreach($category->products as $product)
                            <li><a href="{{ route('admin.products.show', $product) }}">{{ $product->name }}</a></li>
                        @endforeach
                        </ul>
                    @else
                        <p class="text-muted">Chưa có sản phẩm nào thuộc danh mục này.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection