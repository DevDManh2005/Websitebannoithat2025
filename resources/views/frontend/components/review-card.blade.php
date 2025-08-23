@props(['name', 'content', 'avatar'])

<div class="card p-3 mb-3 shadow-sm border-0 review-card">
    <div class="d-flex align-items-center mb-2">
        <img src="{{ $avatar }}" alt="{{ $name }}" class="rounded-circle me-3" width="50" height="50">
        <h6 class="mb-0 fw-bold">{{ $name }}</h6>
    </div>
    <p class="text-muted mb-0 fst-italic">
        “{{ $content }}”
    </p>
</div>
</div>