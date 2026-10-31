

<script>
    $(document).ready(function() {
        // Function to handle adding a product to the cart
        $(document).on('click', '.add-to-cart', function() {
            var productId = $(this).data('product-id');
            var quantity = $(this).data('quantity');
            var isBuyNow = $(this).hasClass('buy-now');

            addToCart(productId, quantity, isBuyNow);
        });


        $(document).on('click', '.update-cart-btn', function() {
            var productId = $(this).data('product-id');
            var input = $(this).siblings('label').find('.quantity__number');
            var quantity = parseInt(input.val());

            // Get the max quantity from the data attribute or from get_option()
            var maxQuantity = parseInt(input.data('max-quantity'));

            if ($(this).hasClass('increase')) {
                quantity++;
            } else if ($(this).hasClass('decrease')) {
                quantity = quantity > 1 ? quantity - 1 : 1;
            }

            // Validate against max quantity
            if (quantity > maxQuantity) {
                showToast('আপনি সর্বাধিক ' + maxQuantity + ' পরিমাণের চেয়ে বেশি অর্ডার করতে পারবেন না.','danger');
                quantity = maxQuantity;
            }

            input.val(quantity);
            updateCart(productId, quantity);
        });



        $(document).on('click', '.remove-from-cart', function() {

            var productId = $(this).data('product-id');

            removeFromCart(productId);
        });



        function addToCart(productId, quantity, isBuyNow = false) {

            $.ajax({
                url: "{{route('cart.add')}}",
                method: 'POST',
                data: {
                    product_id: productId,
                    quantity: quantity
                },
                success: function(response) {
                    
                    showToast(response.message, 'success');
                    updateSubtotal();
                    updateCartCount();

                    if (isBuyNow) {
                        // Redirect to checkout page if "Buy Now" button was clicked
                        window.location.href = "{{ route('cart.show') }}";
                    } else {
                        // Reload the current page
                        if (window.location.href === '{{ route('cart.show') }}') {
                            setTimeout(() => {
                                location.reload();
                            }, 1000);
                        }
                    }
                },


                error: function(xhr) {
                    var errorMessage = xhr.responseJSON.message;
                    showToast(errorMessage, 'danger');
                }

            });
        }

        function updateCart(productId, quantity) {
            $.ajax({
                url: "{{ route('cart.update') }}",
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    product_id: productId,
                    quantity: quantity
                },
                success: function(response) {

                    updateCartRow(response.item);
                    updateSubtotal();
                    updateCartCount();
                    showToast(response.message, 'success');

                },
                error: function(xhr) {
                    var errorMessage = xhr.responseJSON.message;
                    showToast(errorMessage, 'danger');
                }
            });
        }

        function updateCartRow(item) {
            var cartRow = $('button[data-product-id="' + item.id + '"]').closest('tr');

            var total = item.current_price * item.quantity;

            cartRow.find('#total_' + item.id).text('৳' + total.toFixed(2));
        }


        function updateSubtotal() {
            $.ajax({
                url: "{{ route('cart.total') }}",
                method: 'GET',
                success: function(response) {

                    $('#subtotal').text('৳' + response.total.toFixed(2));


                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        }


        // AJAX function to remove a product from the cart
        function removeFromCart(productId) {
            $.ajax({
                url: "{{ route('cart.remove') }}",
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    product_id: productId
                },
                success: function(response) {
                    // Remove the item row from the cart table
                    $('button[data-product-id="' + productId + '"]').closest('tr').remove();

                    updateSubtotal();
                    updateCartCount();
                    showToast(response.message, 'success');
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });}


        function updateCartCount() {
            $.ajax({
                url: "{{ route('cart.count') }}",
                method: 'GET',
                success: function(response) {
                    $('.cart-item-count').text(response.count);
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });

        }

        @if(session()->has('cart'))
        updateCartCount();
        @endif


        $('#clearCartBtn').click(function() {
            if (confirm('আপনি কি নিশ্চিত যে আপনি আপনার কার্ট খালি করতে চান? এই পদক্ষেপটি পূর্বাবস্থায় ফেরানো যাবে না।')) {
                $.ajax({
                    url: '{{ route('cart.clear') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {

                        showToast(response.message, 'success');

                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                        alert('Failed to clear cart. Please try again.');
                    }
                });
            }
        });






    });


    $(document).ready(function() {

        @if(Auth::check() && Auth::user()->wishlist()->exists())
        updateWishlistCount();
        @endif


        $(document).on('click', '.add-to-wishlist', function() {

            var productId = $(this).data('product-id');

            var $button = $(this);

            if ($button.hasClass('active')) {

                removeFromWishlist(productId, $button);

            } else {

                addToWishlist(productId, $button);

            }
        });





        function addToWishlist(productId, button) {
            $.ajax({
                url: "{{ route('wishlist.add') }}",
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    product_id: productId
                },

                success: function(response) {
                    updateWishlistCount();
                    showToast(response.message, 'success');

                    button.addClass('active');
                    if (window.location.href === '{{ route('wishlist.index') }}') {
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    }

                },
                error: function(xhr, status, error) {
                    if (xhr.status === 401) {
                        showToast('Please log in first to add products to your wishlist.', 'warning');
                        {{--window.location.href = "{{ route('login') }}";--}}
                    } else {
                        // Extract the error message from the response if available
                        const response = JSON.parse(xhr.responseText);
                        const message = response.message || 'An error occurred. Please try again.';
                        showToast(message, 'danger');
                        console.error(error);
                    }
                }
            });
        }



        $(document).on('click', '.remove-from-wishlist', function() {
            var productId = $(this).data('product-id');
            var $button = $(this);

            removeFromWishlist(productId, $button); // Call the remove function
        });

        function removeFromWishlist(productId, $button) {
            $.ajax({
                url: "{{ route('wishlist.remove') }}", // Your remove route
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}', // CSRF token for security
                    product_id: productId
                },
                success: function(response) {
                    updateWishlistCount(); // Update the wishlist count
                     $button.removeClass('active');
                    showToast(response.message, 'danger'); // Show success message
                     

                    // Optionally, remove the item from the DOM
                    $button.closest('tr').remove(); // Removes the row from the table
                    
                    
                     if (window.location.href === '{{ route('wishlist.index') }}') {
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    }
                },
                error: function(xhr, status, error) {
                    console.error(error); // Log the error
                    showToast('Failed to remove from wishlist. Please try again.', 'error'); // Show error message
                }
            });
        }



        function updateWishlistCount() {
            $.ajax({
                url: '{{ route('wishlist.count') }}',
                method: 'GET',
                success: function(response) {
                    $('.wishlist-count').text(response.count);
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        }


    });

</script>

