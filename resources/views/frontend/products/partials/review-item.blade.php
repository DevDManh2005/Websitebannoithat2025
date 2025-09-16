// filepath: [review-item.blade.php](http://_vscodecontentref_/1)
@php
    $depth = $depth ?? 0;
    $maxDepth = 6;
    $isOwner = auth()->check() && auth()->id() === $review->user_id;
@endphp

<div class="card mb-3">
    <div class="card-body">
        <div class="d-flex align-items-start">
            <img src="{{ $review->user->avatar_url ?? asset('images/default-avatar.png') }}" class="rounded-circle me-3"
                width="48" height="48" alt="avatar">
            <div class="flex-grow-1">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <strong>{{ $review->user->name ?? 'Người dùng' }}</strong>
                        <small class="text-muted ms-2">{{ $review->created_at->diffForHumans() }}</small>
                    </div>
                    <div class="small text-muted">
                        @auth
                            @if ($isOwner)
                                <a class="link-secondary" data-bs-toggle="collapse"
                                    href="#edit-review-{{ $review->id }}">Sửa</a>
                                <form class="d-inline ms-2" method="POST" action="{{ route('reviews.destroy', $review) }}"
                                    onsubmit="return confirm('Xoá đánh giá này?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-link link-danger p-0 align-baseline">Xoá</button>
                                </form>
                            @endif

                            @if ($canReply)
                                <a class="link-secondary ms-2" data-bs-toggle="collapse"
                                    href="#reply-review-{{ $review->id }}">Trả lời</a>
                            @endif
                        @endauth
                    </div>
                </div>

                <div class="mt-2">
                    {{-- Loại bỏ prefix [reply:#id] khi hiển thị --}}
                    {!! nl2br(e(preg_replace('/^\[reply:#\d+\]/', '', $review->review))) !!}
                </div>

                {{-- Edit form --}}
                @if ($isOwner)
                    <div class="collapse mt-2" id="edit-review-{{ $review->id }}">
                        <form method="POST" action="{{ route('reviews.update', $review) }}">
                            @csrf @method('PATCH')
                            <div class="mb-2">
                                <textarea name="review" class="form-control" rows="3">{{ preg_replace('/^\[reply:#\d+\]/', '', $review->review) }}</textarea>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-outline-brand">Lưu</button>
                                <a class="btn btn-sm btn-light" data-bs-toggle="collapse"
                                    href="#edit-review-{{ $review->id }}">Hủy</a>
                            </div>
                        </form>
                    </div>
                @endif

                {{-- Reply form --}}
                @auth
                    @if ($canReply)
                        <div class="collapse mt-2" id="reply-review-{{ $review->id }}">
                            <form method="POST" action="{{ route('reviews.reply', $review) }}">
                                @csrf
                                <input type="hidden" name="parent_id" value="{{ $review->id }}">
                                <div class="mb-2">
                                    <textarea name="review" class="form-control" rows="2" placeholder="Viết trả lời..."></textarea>
                                </div>
                                <button class="btn btn-sm btn-variant">Gửi trả lời</button>
                            </form>
                        </div>
                    @endif
                @endauth

                {{-- Replies đệ quy --}}
                @if (!empty($repliesMap[$review->id] ?? []) && $depth < $maxDepth)
                    <div class="mt-3 ps-4 border-start">
                        @foreach ($repliesMap[$review->id] as $rep)
                            @include('frontend.products.partials.review-item', [
                                'review' => $rep,
                                'repliesMap' => $repliesMap,
                                'canReply' => $canReply,
                                'currentUserId' => $currentUserId,
                                'depth' => $depth + 1,
                            ])
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
