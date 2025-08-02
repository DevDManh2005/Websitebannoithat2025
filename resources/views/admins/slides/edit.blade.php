@extends('admins.layouts.app')

@section('title', 'Sửa Slide')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Sửa Slide</h1>
    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('admin.slides.update', $slide) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @include('admins.slides._form')
                <button type="submit" class="btn btn-primary">Cập nhật</button>
                <a href="{{ route('admin.slides.index') }}" class="btn btn-secondary">Hủy</a>
            </form>
        </div>
    </div>
</div>
@endsection
