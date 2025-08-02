@extends('admins.layouts.app')

@section('title', 'Tạo Slide mới')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Tạo Slide mới</h1>
    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('admin.slides.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @include('admins.slides._form')
                <button type="submit" class="btn btn-primary">Tạo mới</button>
                <a href="{{ route('admin.slides.index') }}" class="btn btn-secondary">Hủy</a>
            </form>
        </div>
    </div>
</div>
@endsection
