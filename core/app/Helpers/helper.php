<?php

use App\Models\backend\FiscalYear;
use App\Models\backend\JournalEntry;
use App\Models\backend\Offer;
use App\Models\backend\OfferDiscount;
use App\Models\backend\OfferDiscountProduct;
use App\Models\backend\OfferGift;
use App\Models\backend\OfferShipping;
use App\Models\backend\VoucherType;
use App\Models\Coupon;
use App\Models\Option;
use App\Models\Permission;
use App\Models\Product;
use App\Support\BranchScope;
use App\Support\WarehouseScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;


if (! function_exists('get_option')) {

    function get_option($name)
    {
        if (! empty($name)) {
            $option = Option::where("name", "=", $name)->first();
            if ($option) {
                return $option->value;
            } else {
                return '';
            }
        } else {
            return '';
        }
    }
}

if (! function_exists('get_client_ip')) {

    /**
     * get_client_ip
     *
     * @return string
     */
    function get_client_ip()
    {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP')) {
            $ipaddress = getenv('HTTP_CLIENT_IP');
        } else if (getenv('HTTP_X_FORWARDED_FOR')) {
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        } else if (getenv('HTTP_X_FORWARDED')) {
            $ipaddress = getenv('HTTP_X_FORWARDED');
        } else if (getenv('HTTP_FORWARDED_FOR')) {
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        } else if (getenv('HTTP_FORWARDED')) {
            $ipaddress = getenv('HTTP_FORWARDED');
        } else if (getenv('REMOTE_ADDR')) {
            $ipaddress = getenv('REMOTE_ADDR');
        } else {
            $ipaddress = 'UNKNOWN';
        }

        return $ipaddress;
    }
}

if (! function_exists('uploadFile')) {
    function uploadFile($file, $directory)
    {
        if ($file) {
            $uniqueId  = uniqid();
            $fileName  = $uniqueId . '.' . $file->getClientOriginalExtension();

            // Relative path for database
            $relPath = "uploads/$directory/" . date("Y/m/d/");

            // Absolute path for saving (outside core)
            $absPath = base_path('../' . $relPath);

            if (! file_exists($absPath)) {
                mkdir($absPath, 0755, true);
            }

            $file->move($absPath, $fileName);
            return $relPath . $fileName;
        }
        return null;
    }
}

if (! function_exists('image')) {
    function image($src, $cdn = false)
    {
        if (empty($src) || ! file_exists($src)) {
            return asset('theme/frontend/assets/img/default/book.png');
        }

        return asset($src);
    }
}

if (! function_exists('is_active')) {

    function is_active($mainString, $prefix)
    {
        // Extracting the prefix before the dot
        $prefixFromMainString = substr($mainString, 0, strpos($mainString, '.'));

        // Customizing the regular expression pattern
        $pattern = '/^' . preg_quote($prefix, '/') . '/';

        // Testing with preg_match
        return (bool) preg_match($pattern, $prefixFromMainString);
    }
}

if (! function_exists('can_view')) {
    function can_view($url)
    {

        $site_url = get_option('site_url') . "/";
        $url      = str_replace("www.", "", $url);
        //dd($site_url);
        $currentPath = str_replace($site_url, "", $url);
        $user_role   = Auth::user()->user_role;
        //super
        if ($user_role == 1) {
            return true;
        }

        ///$role = Role::whereId($user_role)->first();
        $permission = explode(",", Auth::user()->user_permission);

        foreach ($permission as $single) {

            $slug = Permission::whereId($single)->first();
            if ($slug) {

                if (in_array($currentPath, explode(",", $slug->slug))) {
                    return true;
                }
            }
        }

        return false;
    }
}

if (! function_exists('resizeImage')) {

    function resizeImage($filePath, $height, $width, $format)
    {
        list($originalWidth, $originalHeight) = getimagesize($filePath);

        if ($height == 0 || $width == 0) {
            $height = $originalHeight;
            $width  = $originalWidth;
        } else {
            // Calculate new dimensions
            $aspectRatio = $originalWidth / $originalHeight;
            if ($width / $height > $aspectRatio) {
                $width = $height * $aspectRatio;
            } else {
                $height = $width / $aspectRatio;
            }
        }

        // Create a new true color image with new dimensions
        $newImage = imagecreatetruecolor($width, $height);

        // Create image resource based on the format
        switch ($format) {
            case 'jpeg':
            case 'jpg':
                $imageResource = imagecreatefromjpeg($filePath);
                break;
            case 'png':
                $imageResource = imagecreatefrompng($filePath);
                break;
            case 'webp':
                $imageResource = imagecreatefromjpeg($filePath);
                break;
            default:
                throw new \Exception("Unsupported format: $format");
        }

        // Copy and resize old image into new image
        imagecopyresampled($newImage, $imageResource, 0, 0, 0, 0, $width, $height, $originalWidth, $originalHeight);

        // Save the new image
        switch ($format) {
            case 'jpeg':
            case 'jpg':
                imagejpeg($newImage, $filePath);
                break;
            case 'png':
                imagepng($newImage, $filePath);
                break;
            case 'webp':
                imagewebp($newImage, $filePath);
                break;
            default:
                throw new \Exception("Unsupported format: $format");
        }

        // Free up memory
        imagedestroy($newImage);
        imagedestroy($imageResource);
    }
}

/*if (!function_exists('uploadImage')) {
    function uploadImage($file, $directory, $watermarkPath = null, $quality = 60)
    {
        if ($file) {
            // Generate a unique filename using time and a random number
            $uniqueId = uniqid(); // Generates a unique ID based on the current time in microseconds
            $imageName = $uniqueId . '.webp'; // Fixed extension to webp
            $imagePath = "uploads/$directory/" . date("Y/m/d/");

            // 0755 permissions allow the owner full control over the directory,
            // while group members and others have read and execute permissions only.
            if (!file_exists($imagePath)) {
                mkdir($imagePath, 0755, true);
            }

            // Move the uploaded file to the designated directory
            $file->move($imagePath, $imageName);

            //dd($imagePath . $imageName);

            // Load the uploaded image
            $image = imagecreatefromstring(file_get_contents($imagePath . $imageName));
           // $image = imagecreatefromwebp($imagePath . $imageName);

            // If a watermark is provided, add it to the image
            if ($watermarkPath) {
                // Load the watermark image
                $watermark = imagecreatefrompng($watermarkPath);

                // Get dimensions of the watermark
                $watermarkWidth = imagesx($watermark);
                $watermarkHeight = imagesy($watermark);

                // Calculate position for the watermark (center of the image)
                $x = (imagesx($image) - $watermarkWidth) / 2;
                $y = (imagesy($image) - $watermarkHeight) / 2;

                // Merge the watermark onto the image
                imagecopy($image, $watermark, $x, $y, 0, 0, $watermarkWidth, $watermarkHeight);

                // Free memory for the watermark
                imagedestroy($watermark);
            }

            // Save the final image with the specified quality
            imagewebp($image, $imagePath . $imageName, $quality);

            // Free memory
            imagedestroy($image);

            return $imagePath . $imageName;
        }
        return null;
    }
}*/

if (! function_exists('uploadImage')) {
    function uploadImage($file, $directory, $watermarkPath = null, $quality = 60)
    {
        if ($file) {
            // Generate a unique filename using time and a random number
            $uniqueId  = uniqid();            // Generates a unique ID based on the current time in microseconds
            $imageName = $uniqueId . '.webp'; // Fixed extension to webp
            $imagePath = "uploads/$directory/" . date("Y/m/d/");

            // 0755 permissions allow the owner full control over the directory,
            // while group members and others have read and execute permissions only.
            if (! file_exists($imagePath)) {
                mkdir($imagePath, 0755, true);
            }

            // Move the uploaded file to the designated directory
            $file->move($imagePath, $imageName);

            // Load the uploaded image with ImageMagick
            $originalFilePath = $imagePath . $imageName;
            $outputPath       = $imagePath . $imageName; // Output path for the converted WebP image

            // Use ImageMagick to convert the image to WebP
            exec("convert $originalFilePath -quality $quality $outputPath");

            // If a watermark is provided, add it to the image
            if ($watermarkPath && file_exists($watermarkPath)) {
                // Load the watermark image
                $watermark       = new Imagick($watermarkPath);
                $watermarkWidth  = $watermark->getImageWidth();
                $watermarkHeight = $watermark->getImageHeight();

                // Create an Imagick object from the converted image
                $image = new Imagick($outputPath);

                // Calculate position for the watermark (center of the image)
                $x = ($image->getImageWidth() - $watermarkWidth) / 2;
                $y = ($image->getImageHeight() - $watermarkHeight) / 2;

                // Composite the watermark onto the image
                $image->compositeImage($watermark, Imagick::COMPOSITE_OVER, $x, $y);

                // Save the final image
                $image->writeImage($outputPath);

                // Clean up
                $image->destroy();
                $watermark->destroy();
            }

            return $outputPath; // Return the path of the saved WebP image
        }
        return null;
    }
}

/*if (!function_exists('uploadImages')) {
    function uploadImages($image, $format, $path, $height, $width)
    {
        if ($image) {
            // Create the directory path
            $directoryPath = $path . date("Y") . "/" . date("m") . "/" . date("d") . "/";
            if (!Storage::exists($directoryPath)) {
                Storage::makeDirectory($directoryPath);
            }

            // Generate the image name
            $imageName = uniqid() . time() . '.' . $format;
            $imagePath = $directoryPath . $imageName;

            // Move the uploaded image to the target directory
            $image->move($directoryPath, $imageName);
            // Resize the image
            resizeImage($imagePath, $height, $width, $format);

            return $imagePath;
        }

        return null;
    }
}*/

if (! function_exists('uploadImages')) {
    function uploadImages($image, $format, $path, $height = null, $width = null)
    {
        if ($image) {
            // Create the directory path
            $directoryPath = $path . date("Y") . "/" . date("m") . "/" . date("d") . "/";
            if (! Storage::exists($directoryPath)) {
                Storage::makeDirectory($directoryPath);
            }

            // Generate the image name
            $imageName = uniqid() . time() . '.' . $format;
            $imagePath = $directoryPath . $imageName;

            // Move the uploaded image to the target directory
            $image->move($directoryPath, $imageName);

            // Return the path to the uploaded image
            return $imagePath;
        }

        return null;
    }
}

if (! function_exists('UserID')) {

    function UserID()
    {
        return (Auth::check()) ? Auth::user()->id : 0;
    }
}

if (! function_exists('formatPrice')) {

    function formatPrice($price)
    {
        if (is_numeric($price)) {
            return '৳ ' . number_format($price, 2);
        } else {
            return '৳ 0.00'; // Return a default price if the input is invalid
        }
    }
}

if (! function_exists('generateOrderStatusBadge')) {
    function generateOrderStatusBadge($orderStatusId)
    {
        // Define status colors mapping
        $statusColors = [
            1 => ['label' => 'Pending', 'class' => 'bg-warning'],
            2 => ['label' => 'Confirmed', 'class' => 'bg-secondary'],
            3 => ['label' => 'Packing', 'class' => 'bg-info'],
            4 => ['label' => 'On the way', 'class' => ' badge rounded-pill bg-soft-info'],
            5 => ['label' => 'Cancelled', 'class' => 'bg-danger'],
            6 => ['label' => 'Return', 'class' => 'bg-dark text-white'],
            7 => ['label' => 'Completed', 'class' => 'badge-bgsuccess'],
            8 => ['label' => 'Hold', 'class' => 'badge rounded-pill bg-soft-danger'],
        ];

        // Check if the status ID exists in the colors mapping
        if (isset($statusColors[$orderStatusId])) {

            $status = $statusColors[$orderStatusId];

            return '<span class="badge rounded-pill   ' . $status['class'] . '">' . $status['label'] . '</span>';
        }
        return '';
    }
}

if (! function_exists('numberToWords')) {
    function numberToWords($number)
    {
        $hyphen      = '-';
        $conjunction = ' and ';
        $separator   = ', ';
        $negative    = 'negative ';
        $decimal     = ' point ';
        $dictionary  = [
            0                   => 'zero',
            1                   => 'one',
            2                   => 'two',
            3                   => 'three',
            4                   => 'four',
            5                   => 'five',
            6                   => 'six',
            7                   => 'seven',
            8                   => 'eight',
            9                   => 'nine',
            10                  => 'ten',
            11                  => 'eleven',
            12                  => 'twelve',
            13                  => 'thirteen',
            14                  => 'fourteen',
            15                  => 'fifteen',
            16                  => 'sixteen',
            17                  => 'seventeen',
            18                  => 'eighteen',
            19                  => 'nineteen',
            20                  => 'twenty',
            30                  => 'thirty',
            40                  => 'forty',
            50                  => 'fifty',
            60                  => 'sixty',
            70                  => 'seventy',
            80                  => 'eighty',
            90                  => 'ninety',
            100                 => 'hundred',
            1000                => 'thousand',
            1000000             => 'million',
            1000000000          => 'billion',
            1000000000000       => 'trillion',
            1000000000000000    => 'quadrillion',
            1000000000000000000 => 'quintillion',
        ];

        if (! is_numeric($number)) {
            return false;
        }

        if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
            // overflow
            trigger_error(
                'convert() only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
                E_USER_WARNING
            );
            return false;
        }

        if ($number < 0) {
            return $negative . numberToWords(abs($number));
        }

        $string = $fraction = null;

        if (strpos($number, '.') !== false) {
            list($number, $fraction) = explode('.', $number);
        }

        switch (true) {
            case $number < 21:
                $string = $dictionary[$number];
                break;
            case $number < 100:
                $tens   = ((int) ($number / 10)) * 10;
                $units  = $number % 10;
                $string = $dictionary[$tens];
                if ($units) {
                    $string .= $hyphen . $dictionary[$units];
                }
                break;
            case $number < 1000:
                $hundreds  = $number / 100;
                $remainder = $number % 100;
                $string    = $dictionary[$hundreds] . ' ' . $dictionary[100];
                if ($remainder) {
                    $string .= $conjunction . numberToWords($remainder);
                }
                break;
            default:
                $baseUnit     = pow(1000, floor(log($number, 1000)));
                $numBaseUnits = (int) ($number / $baseUnit);
                $remainder    = $number % $baseUnit;
                $string       = numberToWords($numBaseUnits) . ' ' . $dictionary[$baseUnit];
                if ($remainder) {
                    $string .= $remainder < 100 ? $conjunction : $separator;
                    $string .= numberToWords($remainder);
                }
                break;
        }

        if (null !== $fraction && is_numeric($fraction)) {
            $string .= $decimal;
            $words   = [];
            foreach (str_split((string) $fraction) as $number) {
                $words[] = $dictionary[$number];
            }
            $string .= implode(' ', $words);
        }

        return $string;
    }
}

// if (!function_exists('productSection')) {
//     function productSection($type)
//     {
//         if ($type == 'recent') {
//             // Retrieve the 10 most recent products
//             $latestProducts = Product::orderBy('created_at', 'desc')->take(10)->get();
//             return $latestProducts;
//         }

//         return collect(); // Return an empty collection if the type doesn't match
//     }
// }

if (! function_exists('sendMail')) {
    function sendMail($otp, $email, $name)
    {

        $data = [
            'otp'  => $otp,
            'name' => $name,
        ];

        $result = Mail::to($email)->send(new \App\Mail\OtpEmail($data));
        return $result;
    }
}

// //updated
// if (!function_exists('productSection')) {
//     function productSection($type)
//     {
//         if ($type == 'recent') {

//             //$latestProducts = \App\Models\Product::where('product_type', //'book')->orderBy('created_at', 'desc')->take(10)->get();

//             // Get the product with id = 1
//           $featuredProduct = \App\Models\Product::find([1, 2, 3]);

//             // Get the latest products excluding the one with id = 1
//             $latestProducts = \App\Models\Product::where('product_type', 'book')
//                 ->whereNotIn('id', [1, 2, 3])
//                 ->orderBy('created_at', 'desc')
//                 ->take(7)
//                 ->get()
//                 ->sortBy('stock_status');

//             // Combine the featured product with the latest products
//             $finalProducts = $latestProducts->prepend($featuredProduct);

//             return  $finalProducts;
//         }

//         return collect();
//     }
// }

if (! function_exists('productSection')) {
    function productSection($type)
    {
        if ($type == 'recent') {
            // Get the featured products with ids 1, 2, 3
            $featuredProducts = \App\Models\Product::whereIn('id', [1, 2, 3])->get();

            // Get the latest products excluding the featured ones
            $latestProducts = \App\Models\Product::where('product_type', 'book')
                ->whereNotIn('id', [1, 2, 3])
                //->where('stock_status','in_stock')
                ->orderBy('created_at', 'desc')
                ->take(7)
                ->get()                   // Fetch the results as a collection
                ->sortBy('stock_status'); // Sort the collection by stock_status

            // Combine the featured products with the latest products
            $finalProducts = $featuredProducts->merge($latestProducts);

            return $finalProducts;
        }

        return collect();
    }
}

// if (! function_exists('priceAfterDiscount')) {

//     function priceAfterDiscount($product)
//     {
//         $currentPrice = $product->current_price;
//         $mrpPrice = $product->mrp_price;
//         $discountType = $product->discount_type;
//         $discountAmount = $product->discount_amount;
//         $hasActiveCampaign = $product->hasActiveCampaign();
//         $activeCampaign = $hasActiveCampaign ? $product->getActiveCampaign() : null;

//         $existingDiscount = 0;

//         // Calculate existing discount based on product discount type
//         if ($discountType === 'percentage') {
//             $existingDiscount = ($discountAmount / 100) * $mrpPrice;
//         } elseif ($discountType === 'amount') {
//             $existingDiscount = $discountAmount;
//         }

//         // Calculate final discounted price including campaign discount if applicable
//         if ($hasActiveCampaign && $activeCampaign) {
//             if ($activeCampaign->discount_type === 'percent') {
//                 $campaignDiscountAmount = ($activeCampaign->discount / 100) * $currentPrice;
//             } else {
//                 $campaignDiscountAmount = $activeCampaign->discount;
//             }

//             $finalDiscountedPrice = $mrpPrice - ($existingDiscount + $campaignDiscountAmount);
//         } else {
//             $finalDiscountedPrice = $mrpPrice - $existingDiscount;
//         }

//         // Ensure final price doesn't go below zero
//         return max($finalDiscountedPrice, 0);
//     }
// }

if (! function_exists('priceAfterDiscount')) {
    function priceAfterDiscount($product)
    {
        if (! $product instanceof \App\Models\backend\Product) {
            throw new InvalidArgumentException('Expected an instance of Product model.');
        }

        $currentPrice   = $product->current_price;
        $mrpPrice       = $product->mrp_price;
        $discountType   = $product->discount_type;
        $discountAmount = $product->discount_amount;

        // Check if there is an active campaign
        $hasActiveCampaign = $product->hasActiveCampaign();
        $activeCampaign    = $hasActiveCampaign ? $product->getActiveCampaign() : null;

        $existingDiscount = 0;

        // Calculate existing discount based on product discount type
        if ($discountType == 'percentage') {
            $existingDiscount = ($discountAmount / 100) * $mrpPrice;
        } elseif ($discountType == 'amount') {
            $existingDiscount = $discountAmount;
        }

        // Calculate final discounted price including campaign discount if applicable
        if ($hasActiveCampaign && $activeCampaign) {

            if ($activeCampaign->discount_type->value == 'percent') {

                $discount = ($currentPrice / 100) * $activeCampaign->discount;

                $campaignDiscountAmount = $discount;
                //$campaignDiscountAmount = ($activeCampaign->discount / 100) * $currentPrice;
            } else {
                $campaignDiscountAmount = $activeCampaign->discount;
            }

            $finalDiscountedPrice = $mrpPrice - ($existingDiscount + $campaignDiscountAmount);
        } else {
            $finalDiscountedPrice = $mrpPrice - $existingDiscount;
        }

        // Ensure final price doesn't go below zero
        return max($finalDiscountedPrice, 0);
    }
}

if (! function_exists('calculateDiscount')) {

    function calculateDiscount($product)
    {
        $mrpPrice             = $product->mrp_price;
        $finalDiscountedPrice = priceAfterDiscount($product);

        $discountAmount     = $mrpPrice - $finalDiscountedPrice;
        $discountPercentage = ($discountAmount / $mrpPrice) * 100;

        return [
            'discountAmount'     => round($discountAmount),
            'discountPercentage' => round($discountPercentage),
        ];
    }
}

if (! function_exists('calculateCouponDiscount')) {

    function calculateCouponDiscount($cartSubtotal, $coupon, $cartItems)
    {
        $couponDiscount = 0;

        if ($coupon->c_type === 'cart_base') {

            // Check cart subtotal for cart_base coupon
            if ($cartSubtotal < $coupon->min_buy) {
                $difference = $coupon->min_buy - $cartSubtotal;
                return ['error' => 'Cart subtotal is less than the minimum buy value. You need an additional ৳' . $difference . ' to apply this coupon.', 'status' => 422];
            }

            // Calculate the discount
            if ($coupon->discount_type === 'amount') {
                $couponDiscount = $coupon->discount;
                if ($couponDiscount > $coupon->max_discount) {
                    $couponDiscount = $coupon->max_discount;
                }
            } elseif ($coupon->discount_type === 'percent') {
                $couponDiscount = ($cartSubtotal * ($coupon->discount / 100));
                if ($couponDiscount > $coupon->max_discount) {
                    $couponDiscount = $coupon->max_discount;
                }
            }
        } elseif ($coupon->c_type === 'product_base') {

            $couponProducts = $coupon->products;

            $matchingProducts         = [];
            $matchingProductsSubtotal = 0;

            foreach ($cartItems as $item) {

                if ($couponProducts->contains($item['id'])) {

                    $matchingProducts[] = $item['id'];

                    $matchingProductsSubtotal += $item['current_price'] * $item['quantity'];
                }
            }

            if (empty($matchingProducts)) {
                return ['error' => 'Add exact item(s) in cart to apply this coupon.', 'status' => 422];
            }

            // Calculate the discount for matching products
            if ($coupon->discount_type === 'amount') {
                $couponDiscount = $coupon->discount;
                if ($couponDiscount > $coupon->max_discount) {
                    $couponDiscount = $coupon->max_discount;
                }
                if ($couponDiscount > $matchingProductsSubtotal) {
                    $couponDiscount = $matchingProductsSubtotal;
                }
            } elseif ($coupon->discount_type === 'percent') {

                $couponDiscount = ($matchingProductsSubtotal * ($coupon->discount / 100));

                if ($couponDiscount > $coupon->max_discount) {
                    $couponDiscount = $coupon->max_discount;
                }
            }
        }

        return ['discount_amount' => number_format($couponDiscount, 2), 'status' => 200];
    }
}

function hasActiveCoupons()
{
    return Coupon::where('end_date', '>', now())->count() > 0;
}

if (! function_exists('calculateAverageRating')) {
    function calculateAverageRating($item)
    {

        $totalReviews = $item->reviews()->count();

        if ($totalReviews === 0) {
            return 0;
        }

        $sumRatings    = $item->reviews()->sum('rating');
        $averageRating = $sumRatings / $totalReviews;

        return round($averageRating, 1);
    }
}

function replaceQuotes($text)
{
    // Initialize a flag to alternate between opening and closing quotes
    $isOpeningQuote = true;

    // Use a callback function with preg_replace_callback
    $result = preg_replace_callback('/"/', function ($matches) use (&$isOpeningQuote) {
        if ($isOpeningQuote) {
            $isOpeningQuote = false;
            return '“'; // Opening quote
        } else {
            $isOpeningQuote = true;
            return '”'; // Closing quote
        }
    }, $text);

    return $result;
}

function convertToBengaliNumber($number)
{

    $englishNumbers = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    $bengaliNumbers = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];

    return str_replace($englishNumbers, $bengaliNumbers, $number);
}

if (! function_exists('isProductInWishlist')) {
    /**
     * Check if a product is in the authenticated user's wishlist.
     *
     * @param int $productId
     * @return bool
     */
    function isProductInWishlist($productId)
    {
        if (Auth::check()) {
            // Check if the product exists in the user's wishlist
            return Auth::user()->wishlist()->where('product_id', $productId)->exists();
        }
        return false;
    }
}

if (! function_exists('getCartWeightBasedShipping')) {
    /**
     * Calculate the total cart weight and corresponding shipping charge.

     * @return int Calculated shipping charge based on the weight.
     */
    function getCartWeightBasedShipping()
    {
        // Retrieve the cart from the session
        $baseCharge = get_option('shipping_charge') ?? 60;

        $cart = Session::get('cart', []); // Default to an empty array if 'cart' does not exist

        // Initialize total weight
        $totalWeight = 0;

        // Check if the cart contains items
        if (isset($cart['items']) && is_array($cart['items'])) {
            foreach ($cart['items'] as $item) {
                // Find the product by ID
                $product = Product::find($item['id']);

                if ($product) {
                    if ($product->isBundle == 1) {
                        // Calculate bundle weight
                        $bundleWeight = 0;

                        foreach ($product->bundleProducts as $bundleProduct) {
                            $bundleItem = Product::find($bundleProduct->bundle_product_id);
                            if ($bundleItem) {
                                $bundleWeight += ($bundleItem->weight ?? 0) * $bundleProduct->quantity;
                            }
                        }

                        $totalWeight += $bundleWeight * $item['quantity']; // Multiply by cart quantity
                    } else {
                        // Handle non-bundle product
                        $weight       = $product->weight ?? 0;       // Default to 0 if weight is null
                        $totalWeight += $weight * $item['quantity']; // Multiply by cart quantity
                    }
                }
            }
        }

        // // Add 10% of the total weight as packaging weight
        // $totalWeight += ($totalWeight * 0.1);

        // Convert total weight to kilograms and round up
        $weightInKg = ceil($totalWeight / 1000);

        // Calculate the total charge
        $extraChargePerKg = 20;
        if ($weightInKg <= 1) {
            return $baseCharge; // Base charge for up to 1 kg
        }

        // Charge for additional kilograms
        $additionalCharge = ($weightInKg - 1) * $extraChargePerKg;

        return $baseCharge + $additionalCharge;
    }

    // Get current branch id
    function current_branch_id(): ?int
    {
        return BranchScope::currentId();
    }
    // Get current warehouse id
    function current_warehouse_id(): ?int
    {
        return WarehouseScope::get();
    }

    // Get current fiscal year(according to is_active field in fiscal_years table)
    if (! function_exists('currentFiscalYear')) {

        function currentFiscalYear()
        {
            return FiscalYear::where('is_active', 1)->first();
        }
    }

    function requireFiscalYear()
    {
        $fy = currentFiscalYear();

        if (! $fy) {
            abort(503, 'Fiscal year is not configured.');
        }

        return $fy;
    }

    //voucher number genarate function
    if (! function_exists('generateVoucherNo')) {

        function generateVoucherNo(string $voucherCode): string
        {
            $fy = currentFiscalYear();

            if (! $fy) {
                throw new Exception('No active fiscal year found.');
            }

            $year = date('y', strtotime($fy->start_date)); // e.g. 24

            // Count existing vouchers for this type + fiscal year
            $lastNumber = JournalEntry::where('voucher_type_id', VoucherType::idByCode($voucherCode))
                ->where('fiscal_year_id', $fy->id)
                ->count();

            $next = $lastNumber + 1;

            return sprintf(
                '%s-%s-%06d',
                $voucherCode,
                $year,
                $next
            );
        }
    }

    if (! function_exists('stringShortner')) {
        function stringShortner($name, $length)
        {
            if (empty($name)) {
                return '';
            }
            if (mb_strlen($name) > $length) {
                return mb_substr($name, 0, $length) . '...';
            }

            return $name;
        }
    }

    if (! function_exists('generateInvoiceNo')) {

        /**
         * Generate invoice / voucher number
         *
         * @param string $prefix   e.g. EXP, PUR, SAL
         * @param int|null $branchId
         * @param string $table    default expenses
         * @param string $column   default invoice_no
         * @return string
         */
        function generateInvoiceNo(
            string $prefix,
            ?int $branchId = null,
            string $table = 'expenses',
            string $column = 'invoice_no'
        ): string {

            $branchId = $branchId ?? current_branch_id();
            $date     = Carbon::now()->format('Ymd');

            $base = "{$prefix}-{$date}-";

            // 🔐 lock table row to avoid race condition
            $last = DB::table($table)
                ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                ->whereDate('created_at', Carbon::today())
                ->where($column, 'like', "{$base}%")
                ->lockForUpdate()
                ->orderByDesc('id')
                ->value($column);

            $next = 1;

            if ($last) {
                $parts = explode('-', $last);
                $next  = (int) end($parts) + 1;
            }

            return $base . str_pad($next, 5, '0', STR_PAD_LEFT);
        }
    }

    if (! function_exists('isProductUnderAnyOffer')) {
        function isProductUnderAnyOffer($productId) 
        {
            $allOffer = Offer::where('is_active', 1)->where('start_date', '<=', now())->where('end_date', '>=', now())->get();
       

            foreach($allOffer as $offer) { 
                if($offer->offer_type == 'gift') {
                    $eligibleOffer = OfferGift::where('offer_id', $offer->id)->where('buy_product_id', $productId)->first();
                    if($eligibleOffer){
                        return $offer;
                    }
                } else if($offer->offer_type == 'bundle') {
                    $eligibleOffer = OfferGift::where('offer_id', $offer->id)->where('buy_product_id', $productId)->first();
                    if($eligibleOffer){
                        return $offer;
                    }
                } else if($offer->offer_type == 'shipping') {
                    $eligibleOffer = OfferShipping::where('offer_id', $offer->id)->where('product_id', $productId)->first();
                    if($eligibleOffer){
                        return $offer;
                    }
                } else if($offer->offer_type == 'discount') {
                   $offer_discount = OfferDiscount::where('offer_id', $offer->id)->first(); 
                    $eligibleOffer = OfferDiscountProduct::where('product_id', $productId)->where('offer_discount_id', $offer_discount->id)->first(); 

                    if($eligibleOffer){
                        return $offer;
                    }
                }
            }
            return null;
        }
    } 

    if (! function_exists('applyDiscount')) {
        function applyDiscount($discountRule, $product)
        {
            // $discountRule = OfferDiscount::where('offer_id', $offer->id)->first();
            
            if($discountRule->discount_type == 'percentage'){
                return $product->mrp * ($discountRule->discount_value / 100);
            } else { 
                return $discountRule->discount_value;   
            }
        }
    }

}
