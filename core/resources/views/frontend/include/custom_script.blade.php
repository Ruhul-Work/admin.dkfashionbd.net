
<script>
// Disable right-click
document.addEventListener('contextmenu', (event) => event.preventDefault());

// Disable common key shortcuts
document.addEventListener('keydown', (event) => {
    // F12 or Ctrl+Shift+I or Ctrl+Shift+J or Ctrl+U
    if (event.key === 'F12' || 
        (event.ctrlKey && (event.key === 'u' || event.key === 'U')) || 
        (event.ctrlKey && event.shiftKey && 
         (event.key === 'i' || event.key === 'I' || 
          event.key === 'j' || event.key === 'J'))) {
        event.preventDefault();
    }
});


    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function showToast(message, type = 'success', duration = 3000) {
        const toastContainer = $('#toastContainer');

        // Determine the appropriate Bootstrap class for the toast based on the type
        let toastClass;
        switch(type) {
            case 'warning':
                toastClass = 'bg-warning';
                break;

            case 'error':
                toastClass = 'bg-danger';
                break;

            case 'danger':
                toastClass = 'bg-danger';
                break;
            case 'success':
            default:
                toastClass = 'bg-success';
                break;
        }

        const toast = $('<div>').addClass(`toast align-items-center text-white fs-5 ${toastClass} border-0`)
            .attr('role', 'alert')
            .attr('aria-live', 'assertive')
            .attr('aria-atomic', 'true');

        const toastBody = $('<div>').addClass('d-flex');

        const toastIcon = $('<div>').addClass('toast-body').html(message);

        toastBody.append(toastIcon);
        toast.append(toastBody);

        toastContainer.append(toast);

        toast.toast({ delay: duration });

        toast.toast('show'); // Show the toast

        // Remove toast after it hides
        toast.on('hidden.bs.toast', function () {
            $(this).remove();
        });
    }



    function showLoader(loaderId) {
        $('#' + loaderId).find('.loader').show();
    }

    function hideLoader(loaderId) {
        $('#' + loaderId).find('.loader').hide();
    }



    // ==================lazy loading================

    // Create an IntersectionObserver
    let observer = new IntersectionObserver((entries, observer) => {
        lazyLoadImage(entries, observer);
    }, {
        root: null, // viewport
        rootMargin: '0px',
        threshold: 0.1 // trigger when 10% of the image is visible
    });

    function lazyLoadImage(entries, observer) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                var img = entry.target;
                var dataSrc = img.getAttribute('data-src');

                // Set src attribute to the value of data-src and mark it as loaded
                img.setAttribute('src', dataSrc);
                img.classList.remove('lazy-load');
                observer.unobserve(img); // Stop observing this image
            }
        });
    }

    // Function to observe all lazy-load images
    function observeLazyLoadImages() {
        let lazyLoadImages = document.querySelectorAll('.lazy-load');
        lazyLoadImages.forEach(img => {
            observer.observe(img);
        });
    }

    observeLazyLoadImages();


    // ==================lazy loading end ================


    // Function to filter search results on all prodcut
    function searchFilter(query, listSelector, itemSelector) {
        var lowerCaseQuery = query.toLowerCase().trim();

        $(listSelector + ' li').each(function() {
            var itemName = $(this).find(itemSelector).text().toLowerCase();

            if (itemName.includes(lowerCaseQuery)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }

    // Debounce function to delay execution of a function
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    $(document).ready(function() {

        //============== search form auto placeholder===============
        let typingInterval; // To store the interval ID

        // Function to start typing effect
        function startTypingEffect(input, texts) {
            let textIndex = 0;
            let charIndex = 0;
            let currentText = '';
            let isDeleting = false;

            function type() {
                if (charIndex < texts[textIndex].length && !isDeleting) {
                    currentText += texts[textIndex].charAt(charIndex);
                    charIndex++;
                } else if (charIndex > 0 && isDeleting) {
                    currentText = currentText.slice(0, -1);
                    charIndex--;
                } else if (charIndex === 0 && isDeleting) {
                    isDeleting = false;
                    textIndex = (textIndex + 1) % texts.length;
                } else if (charIndex === texts[textIndex].length) {
                    isDeleting = true;
                }

                input.attr('placeholder', currentText);

                const typeSpeed = isDeleting ? 50 : 150;
                const nextTypeSpeed = isDeleting && charIndex === 0 ? 500 : typeSpeed;
                typingInterval = setTimeout(type, nextTypeSpeed);
            }

            // Start typing effect initially
            type();
        }

        // Function to stop typing effect on manual searching form
        function stopTypingEffect() {
            clearTimeout(typingInterval);
        }

        const texts = [
            "বই ও লেখকের নাম দিয়ে অনুসন্ধান করুন",
            "বিষয়ের নাম দিয়ে অনুসন্ধান করুন",
            "প্রকাশকের নাম দিয়ে অনুসন্ধান করুন"
        ];

        // Initialize typing effect for standalone input
        const inputStandalone = $('#header__search--form-1 #auto-type-1');

        startTypingEffect(inputStandalone, texts);

        // Function to initialize typing effect for modal input
        function initializeModalTypingEffect() {
            // Find the modal input element
            const inputModal = $('#search-modal').find('#auto-type-2');
            startTypingEffect(inputModal, texts);
        }

        // Event listener for elements that open the modal
        $('[data-open="search-modal"]').on('click', function () {
            stopTypingEffect(); // Stop typing effect on standalone input
            initializeModalTypingEffect();
        });

        // Optionally, restart the standalone typing effect when modal is hidden
        $('#search-modal').on('hidden.bs.modal', function () {
            startTypingEffect(inputStandalone, texts);
        });

        // Function to perform product search
        function performProductSearch(searchTerm, productType, resultsContainer) {
            if (searchTerm.length === 0) {
                resultsContainer.empty();
                return;
            }

            $.ajax({
                url: "{{ route('search') }}",
                type: "GET",
                dataType: 'json',
                data: {
                    search: searchTerm,
                    product_type: productType
                },
                success: function(response) {
                    resultsContainer.empty(); // Clear previous results

                    if (response.html.length > 0) {
                        resultsContainer.html(response.html);
                        resultsContainer.show();
                    } else {
                        resultsContainer.html('<p>কোনো পণ্য পাওয়া যায়নি</p>').show();
                    }
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });
        }

        // Debounced version of performProductSearch function (delayed by 500ms)
        const debouncedProductSearch = debounce(function(searchTerm, productType, resultsContainer) {
            performProductSearch(searchTerm, productType, resultsContainer);
        }, 500);

        // Input event handler for search input (manual searching)
        $('#header__search--form-1 #auto-type-1').on('input', function() {
            debouncedProductSearch($(this).val().trim(), $('#header__search--form-1 #product_type-1').val(), $('#header__search--form-1 #search-results-1'));
        });

        // Submit event handler for search form (manual, optional)
        $('#header__search--form-1').submit(function(e) {
            e.preventDefault();
            debouncedProductSearch($('#header__search--form-1 #auto-type-1').val().trim(), $('#header__search--form-1 #product_type-1').val(), $('#header__search--form-1 #search-results-1'));
        });

        // ======================modal searching====================

        // Input event handler for search input in modal (assuming modal id is 'search-modal')
        $('#search-modal').on('input', '#auto-type-2', function() {
            debouncedProductSearch($(this).val().trim(), $('#search-modal #product_type-2').val(), $('#search-modal #search-results-2'));
        });

        // Submit event handler for search form in modal (assuming modal id is 'search-modal')
        $('#search-modal').on('submit', '.header__search--form', function(e) {
            e.preventDefault(); // Prevent form submission
            debouncedProductSearch($('#search-modal #auto-type-2').val().trim(), $('#search-modal #product_type-2').val(), $('#search-modal #search-results-2'));
        });

        // Hide search results when clicking outside the search input or results container within the modal
        $(document).on('click', function(event) {
            if (!$(event.target).closest('#auto-type-1, #search-results-1, #auto-type-2, #search-results-2').length) {
                $('#search-results-1, #search-results-2').html('').hide();
            }
        });

    });




</script>

<script>

    $('#subscribeForm').on('submit', function(e) {
        e.preventDefault();

        var formData = $(this).serialize();

        $.ajax({
            url: $(this).attr('action'),
            method: $(this).attr('method'),
            data: formData,
            success: function(response) {

                showToast(response.message, 'success');

                $('#subscribeForm')[0].reset();
            },
            error: function(xhr) {

                var response = xhr.responseJSON;

                if (response && response.error) {
                    showToast(response.error, 'danger');
                } else {
                    showToast('একটি ত্রুটি ঘটেছে। অনুগ্রহ করে আবার চেষ্টা করুন।', 'danger');
                }
            }
        });
    });


    document.addEventListener("DOMContentLoaded", function () {
        const accountBtn = document.querySelector('.header__account--btn');
        const dropdown = document.querySelector('.header__account--dropdown');

        accountBtn.addEventListener('click', function (event) {
            event.preventDefault(); // Prevent the default link behavior
            dropdown.style.display = (dropdown.style.display === 'block') ? 'none' : 'block';
        });

        window.addEventListener('click', function (event) {
            if (!accountBtn.contains(event.target) && !dropdown.contains(event.target)) {
                dropdown.style.display = 'none';
            }
        });
    });

</script>
