

<div class="pagination__area bg__gray--color d-lg-flex justify-content-between align-items-center">
    <div>
        <p class="product__showing--count">
            {{ $items->firstItem() }}–{{ $items->lastItem() }}  দেখাচ্ছে  {{ $items->total() }} ফলাফল এর মধ্যে
        </p>
    </div>

    <nav class="pagination justify-content-center">
        <ul class="pagination__wrapper d-flex align-items-center justify-content-center">
            @if($items->onFirstPage())
                <li class="pagination__list disabled">
                    <span class="pagination__item link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22.51" height="20.443" viewBox="0 0 512 512">
                            <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="48" d="M244 400L100 256l144-144M120 256h292"/>
                        </svg>
                    </span>
                </li>
            @else
                <li class="pagination__list">
                    <a href="{{ $items->previousPageUrl() }}" class="pagination__item link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22.51" height="20.443" viewBox="0 0 512 512">
                            <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="48" d="M244 400L100 256l144-144M120 256h292"/>
                        </svg>
                        <span class="visually-hidden">Previous</span>
                    </a>
                </li>
            @endif

            @foreach($items->getUrlRange(1, $items->lastPage()) as $page => $url)
                @if($page == $items->currentPage())
                    <li class="pagination__list">
                        <span class="pagination__item pagination__item--current">{{ $page }}</span>
                    </li>
                @elseif ($page == 1 || $page == $items->lastPage() || ($page >= $items->currentPage() - 1 && $page <= $items->currentPage() + 1))
                    <li class="pagination__list">
                        <a href="{{ $url }}" class="pagination__item link">{{ $page }}</a>
                    </li>
                @elseif (($page == $items->currentPage() - 2 && $items->currentPage() > 3) || ($page == $items->currentPage() + 2 && $items->currentPage() < $items->lastPage() - 2))
                    <li class="pagination__list disabled">
                        <span class="pagination__item link">...</span>
                    </li>
                @endif
            @endforeach

            @if($items->hasMorePages())
                <li class="pagination__list">
                    <a href="{{ $items->nextPageUrl() }}" class="pagination__item link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22.51" height="20.443" viewBox="0 0 512 512">
                            <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="48" d="M268 112l144 144-144 144M392 256H100"/>
                        </svg>
                        <span class="visually-hidden">Next</span>
                    </a>
                </li>
            @else
                <li class="pagination__list disabled">
                    <span class="pagination__item link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22.51" height="20.443" viewBox="0 0 512 512">
                            <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="48" d="M268 112l144 144-144 144M392 256H100"/>
                        </svg>
                    </span>
                </li>
            @endif
        </ul>
    </nav>
</div>





{{--        <div class="pagination__area bg__gray--color d-lg-flex justify-content-between align-items-center">--}}
{{--        <div>--}}
{{--            <p class="product__showing--count">--}}
{{--               {{ $items->firstItem() }}–{{ $items->lastItem() }} এর মধ্যে {{ $items->total() }}  ফলাফল দেখাচ্ছে--}}
{{--            </p>--}}
{{--        </div>--}}
{{--            <nav class="pagination justify-content-center">--}}
{{--                <ul class="pagination__wrapper d-flex align-items-center justify-content-center">--}}
{{--                    @if($items->currentPage() > 1)--}}
{{--                        <li class="pagination__list">--}}
{{--                            <a href="{{ $items->url($items->currentPage() - 1) }}" class="pagination__item--arrow link">--}}
{{--                                <svg xmlns="http://www.w3.org/2000/svg" width="22.51" height="20.443" viewBox="0 0 512 512">--}}
{{--                                    <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="48" d="M244 400L100 256l144-144M120 256h292"/>--}}
{{--                                </svg>--}}
{{--                                <span class="visually-hidden">pagination arrow</span>--}}
{{--                                Prev--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                    @endif--}}

{{--                    @foreach(range(max($items->currentPage() - 1, 1), min($items->currentPage() + 1, $items->lastPage())) as $page)--}}
{{--                        <li class="pagination__list">--}}
{{--                            <a href="{{ $items->url($page) }}" class="pagination__item link @if($page == $items->currentPage()) pagination__item--current @endif">{{ $page }}</a>--}}
{{--                        </li>--}}
{{--                    @endforeach--}}

{{--                    @if($items->currentPage() < $items->lastPage())--}}
{{--                        <li class="pagination__list">--}}
{{--                            <a href="{{ $items->url($items->currentPage() + 1) }}" class="pagination__item--arrow link">--}}
{{--                                Next--}}
{{--                                <svg xmlns="http://www.w3.org/2000/svg" width="22.51" height="20.443" viewBox="0 0 512 512">--}}
{{--                                    <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="48" d="M268 112l144 144-144 144M392 256H100"/>--}}
{{--                                </svg>--}}
{{--                                <span class="visually-hidden">pagination arrow</span>--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                    @endif--}}
{{--                </ul>--}}
{{--            </nav>--}}

{{--        </div>--}}

