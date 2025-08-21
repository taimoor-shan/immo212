@if ($vacationRental->description)
    <div @class(['widget-box', $class ?? null])>
        <div class="h7 title fw-6">{{ __('About This Vacation Rental') }}</div>
     <div class="description-content" id="desc">
    <div class="desc-text">
        {!! BaseHelper::clean($vacationRental->content) !!}
    </div>
    <button id="toggleBtn" class="readMore mt-2">Show more</button>
</div>

        @if ($vacationRental->house_rules)
            <div class="house-rules mt-4">
                <div class="h7 title fw-6 mb-3">{{ __('House Rules') }}</div>
                <div class="house-rules-content">
                    <div class="">
                        {!! nl2br(e($vacationRental->house_rules)) !!}
                    </div>
                </div>
            </div>
        @endif
    </div>
@endif

<style>

.description-content {
    margin-top: 16px;
    line-height: 1.7;
}

.house-rules {
    border-top: 1px solid #e9ecef;
    padding-top: 20px;
}

.house-rules-content {
    background: #f3f5fa;
    padding: 16px;
    border-left: 1px solid #dc3545;
}

.cancellation-policy {
    border-top: 1px solid #e9ecef;
    padding-top: 20px;
}

.policy-content {
    background: #f0f9ff;
    padding: 16px;
    border-radius: 6px;
    border-left: 4px solid #007bff;
}

.policy-type {
    font-weight: 600;
    color: #007bff;
    text-transform: capitalize;
}

@media (max-width: 768px) {
    .single-vacation-rental-description {
        padding: 16px;
        margin-bottom: 20px;
    }
}

.desc-text {
  max-height: 170px; /* around 3 lines */
  overflow: hidden;
  transition: all 0.3s ease;
}
.desc-text.expanded {
  max-height: none;
}
.desc-text  strong{
    font-size: 18px;
    font-weight: 500;
        display: inline-block;
}
.desc-text  p{
    margin-bottom: 0.5rem;
}
.desc-text  ul{
    margin-bottom: 0.5rem;
    list-style-type: disc!important;
}
.desc-text  li{
    margin-bottom: 0.25rem;
}

</style>

<script>
document.addEventListener("DOMContentLoaded", function () {
  const text = document.querySelector("#desc .desc-text");
  const btn = document.getElementById("toggleBtn");

  btn.addEventListener("click", function () {
    text.classList.toggle("expanded");
    btn.textContent = text.classList.contains("expanded") ? "Show less" : "Show more";
  });
});
</script>
