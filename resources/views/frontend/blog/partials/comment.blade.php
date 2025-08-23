@php
    /** @var \App\Models\BlogComment $comment */
@endphp

<div class="comment-card card border-0 shadow-sm rounded-3 mb-4" id="comment-{{ $comment->id }}" data-aos="fade-up">
    <div class="card-body p-4">
        <div class="d-flex gap-3">
            <div class="flex-shrink-0">
                <div class="avatar-wrapper">
                    <img src="{{ $comment->user->profile->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($comment->user->name) . '&background=' . str_replace('#', '', $settings['brand_color'] ?? 'A20E38') . '&color=fff' }}"
                         alt="{{ $comment->user->name }}"
                         class="rounded-circle"
                         width="52"
                         height="52">
                </div>
            </div>
            <div class="flex-grow-1">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="d-flex align-items-center">
                        <h6 class="mb-0 fw-bold text-dark">{{ $comment->user->name }}</h6>
                        @if($comment->user->isStaff())
                            <span class="badge bg-brand ms-2">Quản trị viên</span>
                        @endif
                    </div>
                    <span class="text-muted small">
                        <i class="bi bi-clock me-1"></i>{{ $comment->created_at->diffForHumans() }}
                    </span>
                </div>
                <p class="mb-3 comment-content">{{ $comment->comment }}</p>
                
                @auth
                    <button class="btn btn-sm btn-outline-brand reply-btn" 
                            data-comment-id="{{ $comment->id }}"
                            data-username="{{ $comment->user->name }}">
                        <i class="bi bi-reply me-1"></i>Trả lời
                    </button>
                @endauth
            </div>
        </div>
    </div>

    {{-- Replies --}}
    @if($comment->children->count())
        <div class="comment-replies ps-4 pe-4 pb-3">
            @foreach($comment->children as $reply)
                @include('frontend.blog.partials.comment', ['comment' => $reply])
            @endforeach
        </div>
    @endif
</div>

<style>
.comment-card {
    background: var(--card);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    border-left: 3px solid rgba(var(--brand-rgb), 0.3) !important;
}

.comment-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.08) !important;
}

.avatar-wrapper {
    position: relative;
}

.avatar-wrapper::after {
    content: '';
    position: absolute;
    bottom: 0;
    right: 0;
    width: 12px;
    height: 12px;
    background-color: var(--brand);
    border-radius: 50%;
    border: 2px solid #fff;
}

.comment-content {
    line-height: 1.6;
    color: var(--text);
    font-size: 0.95rem;
    padding: 0.5rem 0;
}

.comment-replies {
    background: rgba(var(--brand-rgb), 0.03);
    border-bottom-left-radius: 0.5rem;
    border-bottom-right-radius: 0.5rem;
}

.comment-replies .comment-card {
    background: rgba(255, 255, 255, 0.7);
    border-left: 3px solid rgba(var(--brand-rgb), 0.2) !important;
}

.reply-btn {
    border-radius: 20px;
    padding: 0.35rem 1rem;
    font-size: 0.85rem;
    transition: all 0.2s ease;
}

.reply-btn:hover {
    background-color: var(--brand);
    color: white;
    transform: translateY(-1px);
}

/* Responsive adjustments */
@media (max-width: 576px) {
    .comment-card .card-body {
        padding: 1.25rem;
    }
    
    .comment-replies {
        padding-left: 1rem;
        padding-right: 1rem;
    }
    
    .d-flex.gap-3 {
        gap: 1rem !important;
    }
}
</style>