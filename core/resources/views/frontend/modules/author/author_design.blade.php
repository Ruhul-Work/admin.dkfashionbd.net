@forelse($authors as $author)
   

         <div class="col-sm-6 col-md-4 col-lg-3 mb-4">
            
            
            
                    @if($author->icon)
                   
                   
                         <a class="writer-link" href="{{route('author.single', ['slug' => $author->slug ??$author->id])}}">
                        <img src="{{ image($author->icon) }}" class="author-img" alt="{{ $author->name }}">
                          @else
                           <img class="writer-img" src="{{ asset('theme/frontend/assets/img/icon/author.jpg') }}" alt="{{ $author->name }}">
                          @endif
                        <h6 class="author-name">{{ $author->name }}</h6>
                         </a>
                    </div>
                  

          
    
@empty
    <p>No authors found.</p>
@endforelse

@include('frontend.modules.pagination.pagination_design', ['items' => $authors])




