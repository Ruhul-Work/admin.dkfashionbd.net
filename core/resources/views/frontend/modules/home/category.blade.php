<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="position__relative">
                <div class="category-slider swiper">
                    <div class="swiper-wrapper">
                        @if($categories->isEmpty())

                                <div class="card card-body text-center text-danger">No category available</div>

                        @else
                            @foreach($categories as $category)

                                <div class="swiper-slide py-3">
                                    
                                    
                                    <a class="category-link" href="{{ route('category.single',['slug'=>$category->slug ?? $category->id]) }}">
                                           <div class="card category-info3">
                                            <div class="card-body text-center">
                                               <p>  {{  \Illuminate\Support\Str::limit($category->name, 18, ' ..') }} </p>
                                            </div>
                                           </div>
                                        </a>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
