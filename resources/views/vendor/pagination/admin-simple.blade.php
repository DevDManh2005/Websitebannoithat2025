@if ($paginator->hasPages())
<nav role="navigation" aria-label="Pagination Navigation">
  <ul class="pagination mb-0">

    <li class="page-item {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
      @if ($paginator->onFirstPage())
        <span class="page-link" aria-hidden="true">&laquo;&nbsp;Prev</span>
      @else
        <a class="page-link" href="{{ $paginator->previousPageUrl() }}" aria-label="@lang('pagination.previous')">
          <span aria-hidden="true">&laquo;</span><span class="ms-1">Prev</span>
        </a>
      @endif
    </li>

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
