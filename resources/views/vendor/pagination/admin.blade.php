@if ($paginator->hasPages())
<nav role="navigation" aria-label="Pagination Navigation">
  <ul class="pagination mb-0">

    {{-- Previous --}}
    <li class="page-item {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
      @if ($paginator->onFirstPage())
        <span class="page-link" aria-hidden="true">&laquo;&nbsp;Prev</span>
      @else
        <a class="page-link" href="{{ $paginator->previousPageUrl() }}" aria-label="@lang('pagination.previous')">
          <span aria-hidden="true">&laquo;</span><span class="ms-1">Prev</span>
        </a>
      @endif
    </li>

    {{-- Page numbers --}}
    @foreach ($elements as $element)
      @if (is_string($element))
        <li class="page-item disabled"><span class="page-link">{{ $element }}</span></li>
      @endif

      @if (is_array($element))
        @foreach ($element as $page => $url)
          @if ($page == $paginator->currentPage())
            <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></li>
          @else
            <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
          @endif
        @endforeach
      @endif
    @endforeach

    {{-- Next --}}
    <li class="page-item {{ $paginator->hasMorePages() ? '' : 'disabled' }}">
      @if ($paginator->hasMorePages())
        <a class="page-link" href="{{ $paginator->nextPageUrl() }}" aria-label="@lang('pagination.next')">
          <span class="me-1">Next</span><span aria-hidden="true">&raquo;</span>
        </a>
      @else
        <span class="page-link" aria-hidden="true">Next&nbsp;&raquo;</span>
      @endif
    </li>

  </ul>
</nav>
@endif
