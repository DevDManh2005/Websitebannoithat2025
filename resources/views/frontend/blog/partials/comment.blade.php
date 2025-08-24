@php
    /** @var \App\Models\BlogComment $comment */
@endphp

<div class="comment-card card border-0 rounded-4" id="comment-{{ $comment->id }}" data-aos="fade-up">
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
                    <div class="d-flex align-items-center gap-2">
                        <h6 class="mb-0 fw-bold text-dark">{{ $comment->user->name }}</h6>
                        @if($comment->user->isStaff())
                            <span class="badge badge-soft-brand rounded-pill">Quản trị viên</span>
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

@push('styles')
<style>
/* =================== Comment Card =================== */
.comment-card {
    background: var(--card);
    border-left: 3px solid rgba(var(--brand-rgb), 0.3);
    transition: transform 0.25s ease, box-shadow 0.25s ease;
}
.comment-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow);
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
    background: rgba(255, 255, 255, 0.95);
    border-left: 3px solid rgba(var(--brand-rgb), 0.2);
}

/* =================== Avatar =================== */
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
    border: 2px solid var(--card);
}

/* =================== Buttons and Badges =================== */
.badge-soft-brand {
    background: rgba(var(--brand-rgb), 0.1);
    color: var(--brand);
    font-size: 0.75rem;
}
.reply-btn {
    border-radius: 20px;
    padding: 0.35rem 1rem;
    font-size: 0.85rem;
    border-color: var(--brand);
    color: var(--brand);
    transition: all 0.2s ease;
}
.reply-btn:hover {
    background-color: var(--brand);
    color: #fff;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

/* =================== Responsive Design =================== */
@media (max-width: 991px) {
    .comment-card .card-body {
        padding: 1.5rem;
    }
    .comment-replies {
        padding-left: 1.25rem;
        padding-right: 1.25rem;
    }
    .avatar-wrapper img {
        width: 48px !important;
        height: 48px !important;
    }
}

@media (max-width: 767px) {
    .comment-card .card-body {
        padding: 1.25rem;
    }
    .comment-replies {
        padding-left: 1rem;
        padding-right: 1rem;
    }
    .avatar-wrapper img {
        width: 40px !important;
        height: 40px !important;
    }
    .avatar-wrapper::after {
        width: 10px;
        height: 10px;
        border-width: 1.5px;
    }
    .reply-btn {
        padding: 0.3rem 0.8rem;
        font-size: 0.8rem;
    }
    .comment-content {
        font-size: 0.9rem;
    }
    .badge-soft-brand {
        font-size: 0.7rem;
    }
}

@media (max-width: 575px) {
    .comment-card .card-body {
        padding: 1rem;
    }
    .comment-replies {
        padding-left: 0.75rem;
        padding-right: 0.75rem;
    }
    .d-flex.gap-3 {
        gap: 0.75rem !important;
    }
    .avatar-wrapper img {
        width: 36px !important;
        height: 36px !important;
    }
    .avatar-wrapper::after {
        width: 8px;
        height: 8px;
        border-width: 1px;
    }
    .reply-btn {
        padding: 0.25rem 0.6rem;
        font-size: 0.75rem;
    }
    .comment-content {
        font-size: 0.85rem;
    }
    .badge-soft-brand {
        font-size: 0.65rem;
    }
    .text-muted.small {
        font-size: 0.7rem;
    }
}
</style>
@endpush