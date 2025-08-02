@extends('admins.layouts.app')

@section('title', 'Tạo danh mục mới')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Tạo danh mục mới</h1>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @include('admins.categories._form')
                <button type="submit" class="btn btn-primary">Lưu</button>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Hủy</a>
            </form>
        </div>
    </div>
</div>
@endsection