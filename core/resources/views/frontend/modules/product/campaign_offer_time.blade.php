
@if ($product->hasActiveCampaign())
    @php
        $activeCampaign = $product->getActiveCampaign();
    @endphp
    <div class="live_offer p-3 border bg-light">
        <div class="d-flex justify-content-between align-items-center">
            <div class="live-offer-img">
                <img src="{{ asset($activeCampaign->icon) }}" alt="{{ $activeCampaign->name }}">
            </div>
            <div class="offer-timer">
                <p class="text-center text-#000000 fw-bold py-0 mb-0">অফারটি শেষ হবে</p>
                <div class="deals__countdown--style3 d-flex" data-countdown="{{ $activeCampaign->end_date }}">
                    <!-- Countdown timer markup goes here -->
                </div>
            </div>
        </div>
    </div>
@endif
