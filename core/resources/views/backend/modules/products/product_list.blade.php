 
 @foreach ($products as $product)
     <div class="product-card  bg-white rounded-3 m-3 d-flex p-3 " data-product_id="{{ $product->id }}"
         style="height: 150px; width:30%; cursor: pointer;">
         <img class="img-fluid rounded col-lg-6 product-img" src="{{ image($product->image) }}" alt="img">
         <div class="px-3">
             <p class="py-1 lh-sm text-lg">{{ $product->name }} <br><span
                     class="text-xs lh-1 py-1 fw-semibold my-1">{{ $product->category->name }}</span></p>
             <hr class="my-1 lh-1">
             <h1 class="text-sm lh-1 fw-semibold p-1">${{ $product->price }}</h1>

         </div>
     </div>
 @endforeach
 <div id="pagination" class=" m-3">
     {{ $products->links('pagination::bootstrap-5') }}
 </div>
 <script>
     $(document).ready(function() {
         //purchase cart functionality
         $('.product-card').on('click', function() {
             let productId = $(this).data('product_id');
             if (productExistsInCart(productId)) {
                 alert('Product already exists in purchase cart!');
             } else {
                 addToPurchaseCart(productId);
             }
             loadPurchaseItems();
         });

         function productExistsInCart(productId) {
             let cart = JSON.parse(localStorage.getItem('purchaseCart')) || [];
             let exists = false;
             // console.log(cart);  
             cart.forEach(element => {

                 if (element.parent_id == productId || element.id == productId) {
                     exists = true;
                     console.log(element.id);
                 }
             });
             return exists;
         }

         function addToPurchaseCart(productId) {
             let url = "{{ route('product.childProductList', ':parentId') }}".replace(':parentId', productId);
             let childProducts = [];
             $.ajax({
                 url: url,
                 type: 'GET',
                 success: function(res) {
                     let products = res.products;
                     let cart = [];
                     if (localStorage.getItem('purchaseCart')) {
                         cart = JSON.parse(localStorage.getItem('purchaseCart'));
                     }
                     childProducts = products.map(function(product) {
                         product['quantity'] = 1;
                         return product; // default quantity

                     });
                     localStorage.setItem('purchaseCart', JSON.stringify(cart.concat(
                     childProducts)));
                     alert('Product(s) added to purchase cart successfully!');
                         loadPurchaseItems();
                 },
                 error: function(xhr) {
                     console.error(xhr.responseText);
                 }
             });
         } 

     });
 </script>
