@extends('admins.layouts.app')

@section('title', 'Danh sách sản phẩm')

@section('content')
    <div class="container-fluid">
        <!-- Breadcrumb -->
        <div class="row">
            <div class="col-md-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Sản phẩm</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Danh sách sản phẩm</h5>
                        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">Thêm mới</a>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success" role="alert">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger" role="alert">
                                {{ session('error') }}
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tên sản phẩm</th>
                                    <th>Danh mục</th>
                                    <th>Biến thể chính</th>
                                    <th>Giá (Chính)</th>
                                    <th>Hiển thị</th>
                                    <th>Nổi bật</th>
                                    <th class="text-end">Hành động</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse ($products as $product)
                                    <tr>
                                        @php
                                            $mainVariant = $product->variants->firstWhere('is_main_variant', true);
                                        @endphp

                                        <td>{{ $product->id }}</td>
                                        <td>
                                            <a href="{{ route('admin.products.edit', $product->id) }}">{{ $product->name }}</a>
                                        </td>
                                        <td>{{ $product->category->name ?? 'N/A' }}</td>
                                        
                                        {{-- === LOGIC SỬA LỖI HIỂN THỊ NẰM Ở ĐÂY === --}}
                                        <td>
                                            @if($mainVariant)
                                                {{ $mainVariant->sku }}
                                                @if(!empty($mainVariant->attributes))
                                                    @php
                                                        $attributeParts = [];
                                                        foreach($mainVariant->attributes as $key => $value) {
                                                            $attributeParts[] = ucfirst($key) . ': ' . $value;
                                                        }
                                                    @endphp
                                                    <span class="text-muted">({{ implode(', ', $attributeParts) }})</span>
                                                @endif
                                            @else
                                                <span class="text-muted">Chưa có biến thể chính</span>
                                            @endif
                                        </td>
                                        
                                        <td>
                                            @if($mainVariant)
                                                @if($mainVariant->sale_price)
                                                    <span class="text-danger">{{ number_format($mainVariant->sale_price) }} VNĐ</span>
                                                    <small class="text-muted text-decoration-line-through">{{ number_format($mainVariant->price) }} VNĐ</small>
                                                @else
                                                    <span>{{ number_format($mainVariant->price) }} VNĐ</span>
                                                @endif
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        
                                        <td>
                                            @if ($product->is_active)
                                                <span class="badge bg-success">Hiển thị</span>
                                            @else
                                                <span class="badge bg-secondary">Ẩn</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($product->is_featured)
                                                <span class="badge bg-primary">Nổi bật</span>
                                            @else
                                                <span class="badge bg-light text-dark">Không</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('admin.products.show', $product->id) }}" class="btn btn-sm btn-info">Xem</a>
                                            <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-warning">Sửa</a>
                                            <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này không?')">Xóa</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">Không có sản phẩm nào.</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $products->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection