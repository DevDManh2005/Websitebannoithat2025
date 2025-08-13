@extends('admins.layouts.app')

@section('title','Bài viết')

@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4">Bài viết</h1>
    <a href="{{ route('admin.blogs.create') }}" class="btn btn-primary">
      <i class="bi bi-plus-circle"></i> Thêm bài viết
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
            <th>Tiêu đề</th>
            <th>Danh mục</th>
            <th>Trạng thái</th>
            <th>Xuất bản</th>
            <th class="text-end"></th>
          </tr>
        </thead>
        <tbody>
        @forelse($blogs as $p)
          <tr>
            <td class="fw-medium">{{ $p->title }}</td>
            <td>{{ $p->category?->name }}</td>
            <td>
              @if($p->is_published)
                <span class="badge bg-success">Published</span>
              @else
                <span class="badge bg-secondary">Draft</span>
              @endif
            </td>
            <td>{{ $p->published_at ? $p->published_at->format('d/m/Y H:i') : '' }}</td>
            <td class="text-end">
              <a class="btn btn-sm btn-warning" href="{{ route('admin.blogs.edit',$p) }}">
                <i class="bi bi-pencil-square"></i>
              </a>
              <form class="d-inline" action="{{ route('admin.blogs.destroy',$p) }}" method="POST"
                    onsubmit="return confirm('Xóa bài viết?');">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="text-center text-muted py-4">Chưa có bài viết nào.</td>
          </tr>
        @endforelse
        </tbody>
      </table>
    </div>

    @if(method_exists($blogs,'links'))
      <div class="card-footer">
        {{ $blogs->links() }}
      </div>
    @endif
  </div>
</div>
@endsection
