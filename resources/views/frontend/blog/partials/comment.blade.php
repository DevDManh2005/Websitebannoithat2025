@php
  $name    = $c->user->name ?? 'Ẩn danh';
  $initial = mb_strtoupper(mb_substr($name, 0, 1, 'UTF-8'));
  $avatar  = optional($c->user->profile ?? null)->avatar; // có thể null
@endphp

<div class="comment mb-3 p-3 bg-white border rounded-3">
  <div class="d-flex">
    <div class="me-3 flex-shrink-0">
      @if($avatar)
        <img src="{{ asset('storage/'.$avatar) }}"
             alt="{{ $name }}"
             class="rounded-circle"
             style="width:40px;height:40px;object-fit:cover;">
      @else
        <div class="rounded-circle d-inline-flex align-items-center justify-content-center
                    bg-danger-subtle text-danger fw-bold"
             style="width:40px;height:40px;">
          {{ $initial }}
        </div>
      @endif
    </div>

    <div class="flex-grow-1">
      <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
        <strong>{{ $name }}</strong>
        <small class="text-muted">{{ optional($c->created_at)->diffForHumans() }}</small>
      </div>

      <div class="mb-2">{{ $c->comment }}</div>

      @auth
        <button type="button"
                class="btn btn-link btn-sm p-0 comment-actions"
                data-reply="{{ $c->id }}">
          <i class="bi bi-reply me-1"></i>Trả lời
        </button>
      @endauth

      @if($c->children && $c->children->count())
        <div class="children mt-2">
          @foreach($c->children as $child)
            @include('frontend.blog.partials.comment', ['c' => $child])
          @endforeach
        </div>
      @endif
    </div>
  </div>
</div>
