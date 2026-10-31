@if($blogs->count() > 0)
    <ul class="blog-list-group list-group" >
        @foreach($blogs as $blog)
            <li class="list-group-item">
                <a href="{{ route('blogs.single', ['slug_or_id' => $blog->slug ?: $blog->id]) }}" class="text-dark d-block">
                    <h5 class="mb-1">
                        {{ \Illuminate\Support\Str::words($blog->title, 5, '...') }}
                    </h5>
                </a>
            </li>
        @endforeach
    </ul>
@else
    <p class="text-muted">আপনার অনুসন্ধানের জন্য কোনো ব্লগ পাওয়া যায়নি।</p>
@endif


<style>


</style>

