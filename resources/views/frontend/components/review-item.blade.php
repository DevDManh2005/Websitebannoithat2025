@props(['review'])

<div class="review-item-card" data-aos="fade-up">
    <div class="review-item-header">
        <img src="{{ $review->user->avatar_url }}?v={{ optional($review->user->profile)->updated_at?->timestamp }}"
            alt="{{ $review->user->name }}" class="reviewer-avatar">
        <div class="reviewer-meta">
            <div class="d-flex align-items-center">
                <strong class="reviewer-name">{{ $review->user->name }}</strong>
                {{-- This is a placeholder for a real "verified purchase" logic --}}
                @if(true)
                    <span class="badge-soft-success ms-2">Đã mua hàng</span>
                @endif
            </div>
            <div class="d-flex align-items-center">
                @include('frontend.components.star-rating', ['rating' => $review->rating])
                <small class="review-date ms-2">{{ $review->created_at->diffForHumans() }}</small>
            </div>
        </div>
    </div>

    <p class="review-content">{{ $review->review }}</p>

    @if($review->image)
        <div class="review-image-attachment">
            <a href="{{ asset('storage/' . $review->image) }}" class="review-image-lightbox">
                <img src="{{ asset('storage/' . $review->image) }}" alt="Hình ảnh đánh giá" class="img-thumbnail">
            </a>
        </div>
    @endif
</div>

@once
    @push('styles')
        <style>
            .review-item-card {
                background: linear-gradient(165deg, rgba(255, 255, 255, .6), rgba(255, 255, 255, .8));
                border: 1px solid rgba(0, 0, 0, .05);
                border-radius: var(--radius, 12px);
                padding: 1.25rem;
                margin-bottom: 1.5rem;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
            }

            .review-item-header {
                display: flex;
                align-items: flex-start;
                margin-bottom: 0.75rem;
            }

            .reviewer-avatar {
                width: 45px;
                height: 45px;
                border-radius: 50%;
                object-fit: cover;
                margin-right: 0.75rem;
                border: 2px solid rgba(var(--brand-rgb, 162, 14, 56), 0.3);
                flex-shrink: 0;
            }

            .reviewer-name {
                font-weight: 600;
                font-size: 1rem;
                color: var(--text);
            }

            .review-date {
                color: var(--muted);
                font-size: 0.8rem;
            }

            /* Assuming star-rating component uses this class */
            .star-rating {
                color: var(--brand);
                font-size: 0.9rem;
            }

            .badge-soft-success {
                background-color: rgba(25, 135, 84, 0.1);
                color: #198754;
                font-size: 0.7rem;
                font-weight: 600;
                padding: .2em .4em;
            }

            .review-content {
                color: var(--text);
                line-height: 1.6;
                padding-left: calc(45px + 0.75rem);
                /* Align with text above avatar */
            }

            .review-image-attachment {
                padding-left: calc(45px + 0.75rem);
                margin-top: 0.75rem;
            }

            .review-image-attachment .img-thumbnail {
                border-radius: 8px;
                border-color: rgba(0, 0, 0, 0.1);
                max-width: 150px;
                cursor: zoom-in;
                transition: transform .2s ease;
            }

            .review-image-attachment .img-thumbnail:hover {
                transform: scale(1.03);
            }

            /* Image Lightbox Styles */
            .review-image-lightbox-overlay {
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, 0.85);
                backdrop-filter: blur(5px);
                z-index: 9999;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 2rem;
                opacity: 0;
                animation: lightboxFadeIn .3s forwards;
            }

            .review-image-lightbox-overlay img {
                display: block;
                max-width: 90%;
                max-height: 90%;
                object-fit: contain;
            }

            .lightbox-close-btn {
                position: absolute;
                top: 1.5rem;
                right: 1.5rem;
                background: transparent;
                border: none;
                color: white;
                font-size: 2rem;
                line-height: 1;
                cursor: pointer;
            }

            @keyframes lightboxFadeIn {
                to {
                    opacity: 1;
                }
            }
        </style>
    @endpush

    @push('scripts-page')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                document.body.addEventListener('click', function (e) {
                    const lightboxTrigger = e.target.closest('.review-image-lightbox');
                    if (lightboxTrigger) {
                        e.preventDefault();
                        const imageUrl = lightboxTrigger.getAttribute('href');

                        const overlay = document.createElement('div');
                        overlay.className = 'review-image-lightbox-overlay';

                        const closeBtn = document.createElement('button');
                        closeBtn.className = 'lightbox-close-btn';
                        closeBtn.innerHTML = '&times;';
                        closeBtn.setAttribute('aria-label', 'Close image viewer');

                        const img = document.createElement('img');
                        img.src = imageUrl;

                        overlay.appendChild(img);
                        overlay.appendChild(closeBtn);
                        document.body.appendChild(overlay);

                        const closeLightbox = () => {
                            overlay.style.animation = 'none'; // Prevent re-triggering fade-in
                            overlay.style.opacity = '0';
                            overlay.style.transition = 'opacity .3s';
                            setTimeout(() => overlay.remove(), 300);
                        };

                        closeBtn.addEventListener('click', closeLightbox);
                        overlay.addEventListener('click', (event) => {
                            if (event.target === overlay) { // Only close if clicking on the backdrop
                                closeLightbox();
                            }
                        });
                    }
                });
            });
        </script>
    @endpush
@endonce