@php
  use Illuminate\Support\Str;

  /** @var \App\Models\Category $node */
  $hasChildren = $node->relationLoaded('children')
      ? $node->children->isNotEmpty()
      : (optional($node->children)->count() > 0);

  $collapseId = 'cat-node-'.$node->id;
  $imgUrl = $node->image
      ? (Str::startsWith($node->image, ['http://','https://','//']) ? $node->image : asset('storage/'.$node->image))
      : null;
@endphp

<tr class="level-{{ $level }}">
  <td class="text-muted sticky-col-left">#{{ $node->id }}</td>

  <td>
    <img src="{{ $imgUrl ?: 'https://via.placeholder.com/56x56?text=DM' }}"
         class="cat-thumb"
         alt="{{ $node->name }}"
         loading="lazy"
         onerror="this.onerror=null;this.src='https://via.placeholder.com/56x56?text=DM';">
  </td>

  <td class="text-truncate tree-cell">
    <div class="d-flex align-items-center gap-2 node-wrap">
      @if($hasChildren)
        <button
          class="caret-toggle btn-icon border-0"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#{{ $collapseId }}"
          aria-expanded="false"
          aria-controls="{{ $collapseId }}"
          title="Xem danh mục con">
          <i class="bi bi-caret-right-fill"></i>
        </button>
      @else
        <span class="text-muted"><i class="bi bi-dot"></i></span>
      @endif

      @if($level>0)
        <span class="text-muted">└─</span>
      @endif

      <a class="fw-semibold text-decoration-none"
         href="{{ route('admin.categories.edit', $node) }}"
         title="{{ $node->name }}">
        <span class="cat-name">{{ $node->name }}</span>
      </a>
    </div>
  </td>

  <td class="text-truncate">{{ $node->parent?->name ?? '—' }}</td>

  <td>
    <span class="badge {{ $node->is_active ? 'bg-success-soft' : 'bg-secondary-soft' }}">
      {{ $node->is_active ? 'Hiện' : 'Ẩn' }}
    </span>
  </td>

  <td>{{ $node->position }}</td>

  <td class="text-end sticky-col-right">
    <div class="d-none d-md-inline-flex gap-1">
      <a href="{{ route('admin.categories.show', $node) }}" class="btn btn-sm btn-outline-secondary ripple" data-bs-toggle="tooltip" title="Xem"><i class="bi bi-eye"></i></a>
      <a href="{{ route('admin.categories.edit', $node) }}" class="btn btn-sm btn-outline-primary ripple" data-bs-toggle="tooltip" title="Sửa"><i class="bi bi-pencil-square"></i></a>
      <form action="{{ route('admin.categories.destroy', $node) }}" method="POST" class="d-inline js-del-form" data-confirm="Xóa “{{ $node->name }}”?">
        @csrf @method('DELETE')
        <button class="btn btn-sm btn-danger ripple" type="submit" data-bs-toggle="tooltip" title="Xóa"><i class="bi bi-trash"></i></button>
      </form>
    </div>

    <div class="dropdown d-inline d-md-none">
      <button class="btn btn-sm btn-outline-secondary dropdown-toggle ripple" data-bs-toggle="dropdown">Hành động</button>
      <ul class="dropdown-menu dropdown-menu-end">
        <li><a class="dropdown-item" href="{{ route('admin.categories.show', $node) }}"><i class="bi bi-eye me-2"></i>Xem</a></li>
        <li><a class="dropdown-item" href="{{ route('admin.categories.edit', $node) }}"><i class="bi bi-pencil-square me-2"></i>Sửa</a></li>
        <li><hr class="dropdown-divider"></li>
        <li>
          <form action="{{ route('admin.categories.destroy', $node) }}" method="POST" class="js-del-form" data-confirm="Xóa “{{ $node->name }}”?">
            @csrf @method('DELETE')
            <button class="dropdown-item text-danger" type="submit"><i class="bi bi-trash me-2"></i>Xóa</button>
          </form>
        </li>
      </ul>
    </div>
  </td>

  <td></td>
</tr>

@if($hasChildren)
  <tr>
    <td colspan="8" class="p-0">
      <div class="collapse" id="{{ $collapseId }}">
        <div class="child-inner">
          <div class="table-responsive">
            <table class="table table-sm subtable align-middle mb-0">
              <tbody>
                @foreach($node->children as $child)
                  @include('admins.categories._node', ['node' => $child, 'level' => $level + 1])
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </td>
  </tr>
@endif
