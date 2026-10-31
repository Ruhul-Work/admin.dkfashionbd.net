@forelse($categories as $category)


    <div class="col-md-4 col-sm-6 col-12 mb-20">
        <a class="subject-link" href="{{route('category.single',['slug' => $category->slug ?: $category->id])}}">
            <div class="card p-4 subject-border shadow-sm">
                <p><i class="ri-book-2-fill text-red-english-moja"></i> {{$category->name}}</p>
            </div>
        </a>
    </div>
@empty
    <p>No categories found.</p>
@endforelse

@include('frontend.modules.pagination.pagination_design', ['items' => $categories])
