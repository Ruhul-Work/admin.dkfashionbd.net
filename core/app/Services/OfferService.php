<?php

namespace App\Services;

use App\Models\backend\Offer;
use App\Models\backend\OfferGift;
use App\Models\backend\OfferShipping;
use App\Models\backend\OfferDiscount;
use App\Models\backend\OfferDiscountProduct;
use App\Models\backend\OfferDiscountCategory;
use App\Models\backend\OfferShippingArea;
use App\Models\backend\OfferShippingProduct;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OfferService
{
    public function getAllOffers()
    {
        return Offer::latest()->get();
    }

    public function createOffer(array $data)
    {
        return DB::transaction(function () use ($data) {
            $offerData = [
                'name'                  => $data['name'],
                'slug'                  => $data['slug'] ?? Str::slug($data['name']),
                'description'           => $data['description'] ?? null,
                'banner_image'          => $data['banner_image'] ?? null,
                'offer_type'            => $data['offer_type'],
                'start_date'            => $data['start_date'],
                'end_date'              => $data['end_date'],
                'announcement_start_at' => $data['announcement_start_at'] ?? null,
                'announcement_end_at'   => $data['announcement_end_at'] ?? null,
                'is_active'             => $data['is_active'] ?? 1,
            ];

            $offer = Offer::create($offerData);

            $this->createOfferDetails($offer, $data);

            return $offer;
        });
    }

    protected function createOfferDetails(Offer $offer, array $data)
    {
        switch ($offer->offer_type) {
            case 'gift':
                OfferGift::create([
                    'offer_id'            => $offer->id,
                    'buy_product_id'      => $data['buy_product_id'],
                    'buy_quantity'        => $data['buy_quantity'] ?? 1,
                    'gift_type'           => $data['gift_type'],
                    'free_product_id'     => $data['free_product_id'] ?? null,
                    'free_quantity'       => $data['free_quantity'] ?? 1,
                    'min_purchase_amount' => $data['min_purchase_amount'] ?? 0.00,
                ]);
                break;

            case 'bundle':
                // Buy One Get Multiple — multiple rows in offer_gifts, one per free item
                $bundleItems = $data['bundle_items'] ?? [];
                foreach ($bundleItems as $item) {
                    if (!empty($item['free_product_id'])) {
                        OfferGift::create([
                            'offer_id'            => $offer->id,
                            'buy_product_id'      => $data['bundle_buy_product_id'],
                            'buy_quantity'        => $data['bundle_buy_quantity'] ?? 1,
                            'gift_type'           => 'different_product',
                            'free_product_id'     => $item['free_product_id'],
                            'free_quantity'       => $item['free_quantity'] ?? 1,
                            'min_purchase_amount' => 0.00,
                        ]);
                    }
                }
                break;

            case 'shipping':
                $shipping = OfferShipping::create([
                    'offer_id'              => $offer->id,
                    'free_shipping_on'      => $data['free_shipping_on'],
                    'shipping_applies_to'   => $data['shipping_applies_to'] ?? 'all_products',
                    'min_order_amount'      => $data['min_order_amount'] ?? 0.00,
                    'applicable_area'       => $data['applicable_area'] ?? 'all',
                    'max_shipping_discount' => $data['max_shipping_discount'] ?? null,
                ]);

                if ($data['shipping_applies_to'] == 'specific_products' && isset($data['shipping_product_ids'])) {
                    foreach ($data['shipping_product_ids'] as $product_id) {
                        OfferShippingProduct::create([
                            'offer_shipping_id' => $shipping->id,
                            'product_id'        => $product_id,
                        ]);
                    }
                }

                if (($data['applicable_area'] ?? 'all') == 'custom' && isset($data['area_ids'])) {
                    foreach ($data['area_ids'] as $area_id) {
                        OfferShippingArea::create([
                            'offer_shipping_id' => $shipping->id,
                            'area_id'           => $area_id,
                        ]);
                    }
                }
                break;

            case 'discount':
                $discount = OfferDiscount::create([
                    'offer_id'           => $offer->id,
                    'discount_type'      => $data['discount_type'] ?? 'percentage',
                    'discount_value'     => $data['discount_value'],
                    'max_discount_amount'=> $data['max_discount_amount'] ?? null,
                    'min_order_amount'   => $data['min_order_amount'] ?? 0.00,
                    'applies_to'         => $data['applies_to'] ?? 'all_products',
                    'max_usage_total'    => $data['max_usage_total'] ?? null,
                    'max_usage_per_user' => $data['max_usage_per_user'] ?? 1,
                ]);

                if ($data['applies_to'] == 'specific_products' && isset($data['product_ids'])) {
                    foreach ($data['product_ids'] as $product_id) {
                        OfferDiscountProduct::create([
                            'offer_discount_id' => $discount->id,
                            'product_id'        => $product_id,
                        ]);
                    }
                } elseif ($data['applies_to'] == 'specific_categories' && isset($data['category_ids'])) {
                    foreach ($data['category_ids'] as $category_id) {
                        OfferDiscountCategory::create([
                            'offer_discount_id' => $discount->id,
                            'category_id'       => $category_id,
                        ]);
                    }
                }
                break;
        }
    }

    public function updateOffer(Offer $offer, array $data)
    {
        return DB::transaction(function () use ($offer, $data) {
            $offer->update([
                'name'                  => $data['name'],
                'slug'                  => $data['slug'] ?? Str::slug($data['name']),
                'description'           => $data['description'] ?? null,
                'banner_image'          => $data['banner_image'] ?? $offer->banner_image,
                'start_date'            => $data['start_date'],
                'end_date'              => $data['end_date'],
                'announcement_start_at' => $data['announcement_start_at'] ?? null,
                'announcement_end_at'   => $data['announcement_end_at'] ?? null,
                'is_active'             => $data['is_active'] ?? $offer->is_active,
            ]);

            $this->updateOfferDetails($offer, $data);

            return $offer;
        });
    }

    protected function updateOfferDetails(Offer $offer, array $data)
    {
        switch ($offer->offer_type) {
            case 'gift':
                $offer->gift()->update([
                    'buy_product_id'      => $data['buy_product_id'],
                    'buy_quantity'        => $data['buy_quantity'] ?? 1,
                    'gift_type'           => $data['gift_type'],
                    'free_product_id'     => $data['free_product_id'] ?? null,
                    'free_quantity'       => $data['free_quantity'] ?? 1,
                    'min_purchase_amount' => $data['min_purchase_amount'] ?? 0.00,
                ]);
                break;

            case 'bundle':
                // Delete all existing bundle rows and recreate
                $offer->giftItems()->delete();
                $bundleItems = $data['bundle_items'] ?? [];
                foreach ($bundleItems as $item) {
                    if (!empty($item['free_product_id'])) {
                        OfferGift::create([
                            'offer_id'            => $offer->id,
                            'buy_product_id'      => $data['bundle_buy_product_id'],
                            'buy_quantity'        => $data['bundle_buy_quantity'] ?? 1,
                            'gift_type'           => 'different_product',
                            'free_product_id'     => $item['free_product_id'],
                            'free_quantity'       => $item['free_quantity'] ?? 1,
                            'min_purchase_amount' => 0.00,
                        ]);
                    }
                }
                break;

            case 'shipping':
                $shipping = $offer->shipping;
                $shipping->update([
                    'free_shipping_on'      => $data['free_shipping_on'],
                    'shipping_applies_to'   => $data['shipping_applies_to'] ?? 'all_products',
                    'min_order_amount'      => $data['min_order_amount'] ?? 0.00,
                    'applicable_area'       => $data['applicable_area'] ?? 'all',
                    'max_shipping_discount' => $data['max_shipping_discount'] ?? null,
                ]);

                // Update products
                $shipping->products()->delete();
                if ($data['shipping_applies_to'] == 'specific_products' && isset($data['shipping_product_ids'])) {
                    foreach ($data['shipping_product_ids'] as $product_id) {
                        OfferShippingProduct::create([
                            'offer_shipping_id' => $shipping->id,
                            'product_id'        => $product_id,
                        ]);
                    }
                }

                // Update custom areas
                $shipping->customAreas()->delete();
                if (($data['applicable_area'] ?? 'all') == 'custom' && isset($data['area_ids'])) {
                    foreach ($data['area_ids'] as $area_id) {
                        OfferShippingArea::create([
                            'offer_shipping_id' => $shipping->id,
                            'area_id'           => $area_id,
                        ]);
                    }
                }
                break;

            case 'discount':
                $discount = $offer->discount;
                $discount->update([
                    'discount_type'      => $data['discount_type'] ?? 'percentage',
                    'discount_value'     => $data['discount_value'],
                    'max_discount_amount'=> $data['max_discount_amount'] ?? null,
                    'min_order_amount'   => $data['min_order_amount'] ?? 0.00,
                    'applies_to'         => $data['applies_to'] ?? 'all_products',
                    'max_usage_total'    => $data['max_usage_total'] ?? null,
                    'max_usage_per_user' => $data['max_usage_per_user'] ?? 1,
                ]);

                // Update products/categories
                $discount->products()->delete();
                $discount->categories()->delete();

                if ($data['applies_to'] == 'specific_products' && isset($data['product_ids'])) {
                    foreach ($data['product_ids'] as $product_id) {
                        OfferDiscountProduct::create([
                            'offer_discount_id' => $discount->id,
                            'product_id'        => $product_id,
                        ]);
                    }
                } elseif ($data['applies_to'] == 'specific_categories' && isset($data['category_ids'])) {
                    foreach ($data['category_ids'] as $category_id) {
                        OfferDiscountCategory::create([
                            'offer_discount_id' => $discount->id,
                            'category_id'       => $category_id,
                        ]);
                    }
                }
                break;
        }
    }

    public function deleteOffer(Offer $offer)
    {
        return $offer->delete();
    }
}
