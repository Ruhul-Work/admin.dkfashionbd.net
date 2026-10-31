
@forelse($campaigns as $campaign)
    <div class=" col-lg-4 col-md-6 mb-4">
        <div class="campaign__item">
            <div class="campaign__item--icon" onclick="window.location.href='{{ route('campaign.single', ['slug' => $campaign->slug ?: $campaign->id]) }}'">


                <img    data-src="{{ asset($campaign->icon) }}" src="{{ asset('theme/frontend/assets/img/default/campaign.png') }}" alt="{{ $campaign->name }}" class="img-fluid lazy-load">

            </div>
            <div class="campaign__item--content">
                <h3 class="campaign__item--title">{{ $campaign->name }}</h3>
                <p class="campaign__item--end-time">Ends: {{ \Carbon\Carbon::parse($campaign->end_date)->format('F j, Y') }}</p>

                <a href="{{ route('campaign.single', ['slug' => $campaign->slug ?: $campaign->id]) }}" class="catch-now-btn">Catch Now</a>
            </div>
        </div>
    </div>


    <style>
        .campaign__item {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            padding: 15px;
            margin-bottom: 10px;
            overflow: hidden;
            text-align: center;
        }
        .campaign__item--icon {
            margin-bottom: 20px;
        }

        .campaign__item--title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .campaign__item--end-time {
            color: #666;
            font-size: 14px;
        }
        .catch-now-btn {
            background-color: var(--bintel-danger-color);

            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            text-transform: uppercase;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .catch-now-btn:hover {
            background-color: var(--bintel-success-color);
            color: #fff !important;
        }


    </style>
@empty
    <p>No campaigns found.</p>
@endforelse
@include('frontend.modules.pagination.pagination_design', ['items' => $campaigns])
