@extends('admins.layouts.app')

@section('title','Danh mục bài viết')

@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4">Danh mục</h1>
    <a href="{{ route('admin.blog-categories.create') }}" class="btn btn-primary">
      <i class="bi bi-plus-circle"></i> Thêm danh mục
    </a>
  </div>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <div class="card shadow-sm">
    <div class="table-responsive">
      <table class="table align-middle mb-0">
        <thead>
          <tr>
            <th style="width:35%">Tên</th>
            <th style="width:20%">Slug</th>
            <th style="width:25%">Danh mục cha</th>
            <th style="width:10%">Trạng thái</th>
            <th class="text-end" style="width:10%"></th>
          </tr>
        </thead>
        <tbody>
          @forelse($categories as $cat)
            <tr>
              <td class="fw-medium">{{ $cat->name }}</td>
              <td><code>{{ $cat->slug }}</code></td>
              <td>{{ $cat->parent?->name }}</td>
              <td>
                @if($cat->is_active)
                  <span class="badge bg-success">ON</span>
                @else
                  <span class="badge bg-secondary">OFF</span>
                @endif
              </td>
              <td class="text-end">
                <a href="{{ route('admin.blog-categories.edit', $cat) }}" class="btn btn-sm btn-warning">
                  <i class="bi bi-pencil-square"></i>
                </a>
                <form action="{{ route('admin.blog-categories.destroy', $cat) }}"
                      method="POST" class="d-inline"
                      onsubmit="return confirm('Xóa danh mục này?');">
                  @csrf @method('DELETE')
                  <button class="btn btn-sm btn-danger">
                    <i class="bi bi-trash"></i>
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="text-center text-muted">Chưa có danh mục nào.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if(method_exists($categories,'links'))
      <div class="card-footer">
        {{ $categories->links() }}
      </div>
    @endif
  </div>
</div>
@endsection
