@extends('admins.layouts.app')

@section('title', 'Chi tiết sản phẩm: ' . $product->name)

@section('content')
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Danh sách sản phẩm</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Chi tiết sản phẩm: {{ $product->name }}</h1>
        <div>
            <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-warning">
                <i class="bi bi-pencil-square me-1"></i> Sửa sản phẩm
            </a>
            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                Quay lại danh sách
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            {{-- THÔNG TIN CƠ BẢN --}}
            <div class="card shadow mb-4">
                <div class="card-header"><h5 class="card-title mb-0">Thông tin cơ bản</h5></div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-3">Tên sản phẩm</dt>
                        <dd class="col-sm-9">{{ $product->name }}</dd>

                        <dt class="col-sm-3">Slug</dt>
                        <dd class="col-sm-9">{{ $product->slug }}</dd>

                        <dt class="col-sm-3">Mô tả</dt>
                        <dd class="col-sm-9">{!! nl2br(e($product->description)) !!}</dd>
                    </dl>
                </div>
            </div>

            {{-- CÁC BIẾN THỂ --}}
            <div class="card shadow mb-4">
                <div class="card-header"><h5 class="card-title mb-0">Các biến thể</h5></div>
                <div class="card-body">
                    @forelse($product->variants as $variant)
                        <div class="mb-3">
                            <strong class="d-block">SKU: <span class="text-primary">{{ $variant->sku }}</span>
                                @if($variant->is_main_variant)
                                    <span class="badge bg-success ms-2">Biến thể chính</span>
                                @endif
                            </strong>
                            <ul class="list-unstyled mb-1 ps-3">
                                <li><strong>Thuộc tính:</strong> 
                                    @forelse((array)$variant->attributes as $key => $value)
                                        <span class="badge bg-secondary">{{ ucfirst($key) }}: {{ $value }}</span>
                                    @empty
                                        <span class="text-muted">Không có</span>
                                    @endforelse
                                </li>
                                <li><strong>Giá:</strong> {{ number_format($variant->price) }} ₫</li>
                                @if($variant->sale_price)
                                <li><strong>Giá khuyến mãi:</strong> <span class="text-danger">{{ number_format($variant->sale_price) }} ₫</span></li>
                                @endif
                                <li><strong>Cân nặng:</strong> {{ number_format($variant->weight) }} gram</li>
                            </ul>
                        </div>
                        @if(!$loop->last) <hr> @endif
                    @empty
                        <p class="text-muted">Sản phẩm này chưa có biến thể nào.</p>
                    @endforelse
                </div>
            </div>
            
            {{-- HÌNH ẢNH --}}
            <div class="card shadow mb-4">
                <div class="card-header"><h5 class="card-title mb-0">Hình ảnh</h5></div>
                <div class="card-body">
                    <div class="d-flex flex-wrap">
                        @forelse($product->images as $image)
                             <div class="me-2 mb-2 p-1 border rounded">
                                 <img src="{{ Str::startsWith($image->image_url, 'http') ? $image->image_url : asset('storage/' . $image->image_url) }}" 
                                      alt="Ảnh sản phẩm" class="img-fluid" style="width: 120px; height: 120px; object-fit: cover;">
                                 @if($image->is_primary)
                                     <div class="text-center"><span class="badge bg-primary mt-1">Ảnh chính</span></div>
                                 @endif
                             </div>
                        @empty
                            <p class="text-muted">Sản phẩm này chưa có hình ảnh.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            {{-- THÔNG TIN CHUNG --}}
            <div class="card shadow mb-4">
               <div class="card-header"><h5 class="card-title mb-0">Thông tin chung</h5></div>
               <div class="card-body">
                    <p><strong>Trạng thái:</strong> 
                        @if($product->is_active)
                            <span class="badge bg-success">Đang hoạt động</span>
                        @else
                            <span class="badge bg-danger">Đã ẩn</span>
                        @endif
                    </p>
                    <p><strong>Nổi bật:</strong> 
                         @if($product->is_featured)
                            <span class="badge bg-info">Có</span>
                        @else
                            <span class="badge bg-secondary">Không</span>
                        @endif
                    </p>
                    <hr>
                    <p><strong>Danh mục:</strong> 
                        @forelse($product->categories as $category)
                            <span class="badge bg-info">{{ $category->name }}</span>
                        @empty
                            N/A
                        @endforelse
                    </p>
                    <p><strong>Thương hiệu:</strong> {{ optional($product->brand)->name ?? 'N/A' }}</p>
                    <p><strong>Nhà cung cấp:</strong> {{ optional($product->supplier)->name ?? 'N/A' }}</p>
               </div>
            </div>
        </div>
    </div>
</div>
@endsection
