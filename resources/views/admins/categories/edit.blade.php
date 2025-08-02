@extends('admins.layouts.app')

@section('title', 'Sửa danh mục: ' . $category->name)

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Sửa danh mục: {{ $category->name }}</h1>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @include('admins.categories._form')
                <button type="submit" class="btn btn-primary">Cập nhật</button>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Hủy</a>
            </form>
        </div>
    </div>
</div>
@endsection