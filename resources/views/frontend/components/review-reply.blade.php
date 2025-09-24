<div class="d-flex gap-2 mb-2 pt-2">
    <div class="flex-shrink-0">
        <img src="https://i.pravatar.cc/64?u={{ $reply->user_id }}" class="rounded-circle border" width="32" height="32" alt="avatar">
    </div>
    <div class="flex-grow-1">
        <div class="d-flex align-items-center">
            <strong>{{ $reply->user->name ?? '—' }}</strong>
            <span class="badge bg-light text-muted ms-2">Phản hồi</span>
            <small class="text-muted ms-2">{{ $reply->created_at?->format('d/m/Y H:i') }}</small>
        </div>
        <div class="mb-2">{!! nl2br(e($reply->review_content)) !!}</div>

        @if (!empty($reply->images))
            <div class="review-images d-flex flex-wrap gap-2 mt-2">
                @foreach($reply->images as $imageUrl)
                    <a href="{{ $imageUrl }}" data-fancybox="reply-{{ $reply->id }}">
                        <img src="{{ $imageUrl }}" alt="Review image"
                             style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                    </a>
                @endforeach
            </div>
        @endif

        @auth
            @php
                $isOwner = (int) auth()->id() === (int) $reply->user_id;
            @endphp
            <div class="small mt-1 d-flex flex-wrap gap-2">
                @if ($isOwner)
                    <a class="link-secondary" data-bs-toggle="collapse" href="#edit-reply-{{ $reply->id }}">Sửa</a>
                    <form class="d-inline" method="POST" action="{{ route('reviews.destroy', $reply) }}" onsubmit="return confirm('Xoá phản hồi này?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-link link-danger p-0 align-baseline">Xoá</button>
                    </form>
                @endif
                @if ($canReplyGlobal)
                     <a class="link-secondary" data-bs-toggle="collapse" href="#reply-to-reply-{{ $reply->id }}">Trả lời</a>
                @endif
            </div>

            {{-- Form sửa reply --}}
            @if($isOwner)
            <div class="collapse mt-1" id="edit-reply-{{ $reply->id }}">
                <form method="POST" action="{{ route('reviews.update', $reply) }}">
                    @csrf @method('PATCH')
                    <textarea name="content" rows="2" class="form-control">{{ $reply->review_content }}</textarea>
                    <button class="btn btn-sm btn-brand rounded-pill mt-1">Lưu</button>
                </form>
            </div>
            @endif

            {{-- Form trả lời reply --}}
            @if($canReplyGlobal)
            <div class="collapse mt-1" id="reply-to-reply-{{ $reply->id }}">
                {{-- Lưu ý: action trỏ đến $reply, không phải $review gốc --}}
                <form method="POST" action="{{ route('reviews.reply', $reply) }}">
                    @csrf
                    <input type="hidden" name="parent_reply_id" value="{{ $reply->id }}">
                    <textarea name="content" rows="2" class="form-control" placeholder="Trả lời {{ $reply->user->name ?? '' }}..."></textarea>
                    <button class="btn btn-sm btn-outline-brand rounded-pill mt-1">Gửi</button>
                </form>
            </div>
            @endif
        @endauth
    </div>
</div>

{{-- Gọi đệ quy để render các cấp reply con --}}
@php
    render_replies($reply->id, $repliesByParent, $canReplyGlobal);
@endphp