@if (RealEstateHelper::isEnabledReview())
    @php
        Theme::asset()->add('star-rating', 'vendor/core/plugins/real-estate/libraries/star-rating/star-rating.min.css');
        Theme::asset()->container('footer')->add('star-rating', 'vendor/core/plugins/real-estate/libraries/star-rating/star-rating.min.js');
        Theme::asset()->container('footer')->add('front-review', 'vendor/core/plugins/real-estate/js/front-review.js', ['jquery', 'star-rating'], version: '1.0.0');
    @endphp

    <div @class(['single-vacation-rental-reviews', $class ?? null])>
        <div class="reviews-header">
            <div class="h7 title fw-6">{{ __('Guest Reviews') }}</div>
            @if ($model->reviews_count > 0)
                <div class="reviews-summary">
                    <div class="rating-display">
                        <div class="rating-stars">
                            @include('plugins/real-estate::themes.partials.review-star', [
                                'avgStar' => $model->reviews_avg_star,
                                'count' => $model->reviews_count,
                            ])
                        </div>
                        <div class="rating-text">
                            <span class="rating-score">{{ number_format($model->reviews_avg_star, 1) }}</span>
                            <span class="rating-count">({{ $model->reviews_count }} {{ $model->reviews_count == 1 ? __('review') : __('reviews') }})</span>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        @if ($model->reviews_count > 0)
            <div class="reviews-list">
                @foreach ($model->reviews()->with('author')->latest()->limit(5)->get() as $review)
                    <div class="review-item">
                        <div class="review-header">
                            <div class="reviewer-info">
                                <div class="reviewer-avatar">
                                    @if ($review->author && $review->author->avatar)
                                        {{ RvMedia::image($review->author->avatar, $review->author->name, 'thumb') }}
                                    @else
                                        <div class="avatar-placeholder">
                                            <x-core::icon name="ti ti-user" />
                                        </div>
                                    @endif
                                </div>
                                <div class="reviewer-details">
                                    <div class="reviewer-name">{{ $review->author ? $review->author->name : __('Anonymous') }}</div>
                                    <div class="review-date">{{ $review->created_at->format('M d, Y') }}</div>
                                </div>
                            </div>
                            <div class="review-rating">
                                @include('plugins/real-estate::themes.partials.review-star', [
                                    'avgStar' => $review->star,
                                    'count' => null,
                                ])
                            </div>
                        </div>
                        
                        @if ($review->content)
                            <div class="review-content">
                                <p>{{ $review->content }}</p>
                            </div>
                        @endif
                    </div>
                @endforeach
                
                @if ($model->reviews_count > 5)
                    <div class="reviews-load-more">
                        <button type="button" class="btn btn-outline-primary" id="load-more-reviews" 
                                data-url="{{ route('public.ajax.reviews', ['model' => get_class($model), 'id' => $model->id]) }}">
                            {{ __('Load More Reviews') }}
                        </button>
                    </div>
                @endif
            </div>
        @else
            <div class="no-reviews">
                <div class="no-reviews-icon">
                    <x-core::icon name="ti ti-message-circle" />
                </div>
                <div class="no-reviews-text">
                    <h6>{{ __('No reviews yet') }}</h6>
                    <p>{{ __('Be the first to leave a review for this vacation rental.') }}</p>
                </div>
            </div>
        @endif

        @auth('account')
            <div class="write-review-section">
                <div class="h7 title fw-6">{{ __('Write a Review') }}</div>
                <form class="review-form" action="{{ route('public.reviews.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="reviewable_type" value="{{ get_class($model) }}">
                    <input type="hidden" name="reviewable_id" value="{{ $model->id }}">
                    
                    <div class="rating-input">
                        <label class="form-label">{{ __('Your Rating') }}</label>
                        <div class="star-rating-input">
                            <input type="hidden" name="star" id="rating-input" value="5">
                            <div class="stars" id="star-rating">
                                @for ($i = 1; $i <= 5; $i++)
                                    <span class="star" data-rating="{{ $i }}">
                                        <x-core::icon name="ti ti-star" />
                                    </span>
                                @endfor
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="review-content" class="form-label">{{ __('Your Review') }}</label>
                        <textarea class="form-control" id="review-content" name="content" rows="4" 
                                  placeholder="{{ __('Share your experience with this vacation rental...') }}"></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">{{ __('Submit Review') }}</button>
                </form>
            </div>
        @else
            <div class="login-to-review">
                <p>{{ __('Please') }} <a href="{{ route('public.account.login') }}">{{ __('login') }}</a> {{ __('to write a review.') }}</p>
            </div>
        @endauth
    </div>
@endif

<style>
.single-vacation-rental-reviews {
    margin-bottom: 30px;
    padding: 24px;
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 8px;
}

.reviews-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 16px;
    border-bottom: 1px solid #e9ecef;
}

.reviews-summary {
    text-align: right;
}

.rating-display {
    display: flex;
    align-items: center;
    gap: 12px;
}

.rating-text {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
}

.rating-score {
    font-size: 18px;
    font-weight: 600;
    color: #212529;
}

.rating-count {
    font-size: 14px;
    color: #6c757d;
}

.reviews-list {
    margin-bottom: 30px;
}

.review-item {
    padding: 20px 0;
    border-bottom: 1px solid #f1f3f4;
}

.review-item:last-child {
    border-bottom: none;
}

.review-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 12px;
}

.reviewer-info {
    display: flex;
    gap: 12px;
}

.reviewer-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    overflow: hidden;
    flex-shrink: 0;
}

.reviewer-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.avatar-placeholder {
    width: 100%;
    height: 100%;
    background: #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
    font-size: 18px;
}

.reviewer-name {
    font-weight: 500;
    color: #212529;
    margin-bottom: 2px;
}

.review-date {
    font-size: 14px;
    color: #6c757d;
}

.review-content {
    margin-left: 52px;
    color: #495057;
    line-height: 1.6;
}

.no-reviews {
    text-align: center;
    padding: 40px 20px;
    color: #6c757d;
}

.no-reviews-icon {
    font-size: 48px;
    margin-bottom: 16px;
    opacity: 0.5;
}

.no-reviews-text h6 {
    margin-bottom: 8px;
    color: #495057;
}

.write-review-section {
    border-top: 1px solid #e9ecef;
    padding-top: 24px;
    margin-top: 24px;
}

.rating-input {
    margin-bottom: 20px;
}

.star-rating-input {
    margin-top: 8px;
}

.stars {
    display: flex;
    gap: 4px;
}

.star {
    cursor: pointer;
    font-size: 20px;
    color: #ddd;
    transition: color 0.2s ease;
}

.star:hover,
.star.active {
    color: #ffc107;
}

.login-to-review {
    text-align: center;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 6px;
    margin-top: 20px;
}

.login-to-review a {
    color: #007bff;
    text-decoration: none;
}

.login-to-review a:hover {
    text-decoration: underline;
}

@media (max-width: 768px) {
    .single-vacation-rental-reviews {
        padding: 16px;
    }
    
    .reviews-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
    }
    
    .reviews-summary {
        text-align: left;
    }
    
    .rating-display {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }
    
    .rating-text {
        align-items: flex-start;
    }
    
    .review-header {
        flex-direction: column;
        gap: 8px;
    }
    
    .review-content {
        margin-left: 0;
        margin-top: 12px;
    }
}
</style>
