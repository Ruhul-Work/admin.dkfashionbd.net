@forelse($publishers as $publisher)

    <div class="col-md-4 col-sm-6 col-12  mb-10">
        <a class="publication-link" href="{{route('publisher.single',['slug'=>$publisher->slug ?:$publisher->id])}}">
            <div class="card publication-border shadow">
                <div class="d-flex justify-content-start align-items-center">
                    @if($publisher->icon)
                    <img class="publication-img" src="{{ image($publisher->icon) }}" alt="{{ $publisher->name }}">
                    @else
                    <img class="publication-img"  src="{{ asset('theme/frontend/assets/img/icon/publication.jpg') }}" alt="{{ $publisher->name }}">
                    @endif
                    <p><i class="ri-book-open-fill"></i>
                        {{ $publisher->name }}</p>
                </div>
            </div>
        </a>
    </div>
@empty
    <p>No publishers found.</p>
@endforelse


@include('frontend.modules.pagination.pagination_design', ['items' => $publishers])



