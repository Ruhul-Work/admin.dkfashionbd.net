<?php
namespace App\Jobs;

use App\Models\backend\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncProductToSecondApp implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries   = 5;
    public $backoff = [10, 30, 60, 120, 300];

    public function __construct(public int $productId)
    {
    }

    public function handle(): void
    {
        $parent = Product::with([
            'category_type',
            'category',
            'subcategory',
            'product_type',
            'brand',
            'unit',
            'color',
            'size',
            'paper_quality',
            'specifications',
            'children.category_type',
            'children.category',
            'children.subcategory',
            'children.product_type',
            'children.brand',
            'children.unit',
            'children.color',
            'children.size',
            'children.paper_quality',
            'children.specifications',
        ])->find($this->productId);

        if (! $parent) {
            Log::warning('Sync skipped: parent product not found', ['id' => $this->productId]);
            return;
        }

        $app2ParentId = $this->syncSingleProduct($parent, null);

        foreach ($parent->children as $child) {
            $this->syncSingleProduct($child, $app2ParentId);
        }

        Log::info('Product (with variants) fully synced to app2', [
            'app1_parent_id' => $parent->id,
            'app2_parent_id' => $app2ParentId,
            'children_count' => $parent->children->count(),
        ]);
    }

    private function syncSingleProduct(Product $product, ?int $app2ParentId): int
    {
        $http = Http::withToken(env('APP2_API_TOKEN'))
            ->acceptJson()
            ->timeout(30);

        foreach ($this->buildMediaAttachments($product) as $fieldName => $attachments) {
            foreach ($attachments as $attachment) {
                $http = $http->attach($fieldName, $attachment['contents'], $attachment['name']);
            }
        }

        $response = $http->post(env('APP2_API_URL'), $this->buildPayload($product, $app2ParentId));

        Log::info('app2 raw response', [
            'product_id' => $product->id,
            'status'     => $response->status(),
            'body'       => $response->body(),
        ]);

        if (! $response->successful()) {
            throw new \Exception(
                "app2 sync failed for product id {$product->id} (sku: {$product->sku}): "
                . $response->status() . ' - ' . $response->body()
            );
        }

        $app2ProductId = $response->json('data.id');

        if (! $app2ProductId) {
            throw new \Exception("app2 response e product id paoa jayni. Response: " . $response->body());
        }

        Log::info('Single product synced to app2', [
            'app1_id' => $product->id,
            'app2_id' => $app2ProductId,
            'sku'     => $product->sku,
        ]);

        return $app2ProductId;
    }

    private function buildPayload(Product $product, ?int $app2ParentId): array
    {
        return [
            'name'               => $product->name,
            'sku'                => $product->sku,
            'slug'               => $product->slug,
            'barcode'            => $product->barcode,
            'parent_id'          => $app2ParentId,

            'category_type_name' => $product->category_type?->name,
            'category_name'      => $product->category?->name,
            'subcategory_name'   => $product->subcategory?->name,
            'product_type_name'  => $product->product_type?->name,
            'brand_name'         => $product->brand?->name,
            'unit_name'          => $product->unit?->name,
            'color_name'         => $product->color?->name,
            'size_name'          => $product->size?->name,
            'paper_name'         => $product->paper_quality?->name,

            'is_active'          => $product->is_active,
            'has_variant'        => $product->has_variants,
            'material'           => $product->material,
            'weight_gsm'         => $product->specifications?->weight_gsm ?? $product->weight ?? null,
            'fabric_weave'       => $product->specifications?->fabric_weave,
            'fabric_type'        => $product->specifications?->fabric_type,
            'origin_country'     => $product->specifications?->origin_country,
            'care_instructions'  => $product->specifications?->care_instructions,
            'durability_rating'  => $product->specifications?->durability_rating,
            'transparency_text'  => $product->specifications?->transparency_text,
            'description'        => $product->description,
            'short_description'  => $product->short_description,
            'meta_title'         => $product->meta_title,
            'meta_description'   => $product->meta_description,
            'meta_keywords'      => $product->meta_keywords,
        ];
    }

    private function buildMediaAttachments(Product $product): array
    {
        $attachments = [];

        $imagePaths = $this->normalizePaths($product->images);
        if (! empty($imagePaths)) {
            foreach ($imagePaths as $path) {
                $file = $this->createAttachment($path);
                if ($file) {
                    $attachments['images[]'][] = $file;
                }
            }

            $firstImage = $this->createAttachment($imagePaths[0]);
            if ($firstImage) {
                $attachments['image'][] = $firstImage;
            }
        } elseif ($singleImage = $this->createAttachment($product->image)) {
            $attachments['image'][] = $singleImage;
        }

        if ($thumbnail = $this->createAttachment($product->thumbnail_image)) {
            $attachments['thumbnail_image'][] = $thumbnail;
        }

        if ($sizeChart = $this->createAttachment($product->size_chart_image)) {
            $attachments['size_chart_image'][] = $sizeChart;
        }

        if ($metaImage = $this->createAttachment($product->meta_image)) {
            $attachments['meta_image'][] = $metaImage;
        }

        if ($product->category && $product->category->icon) {
            $categoryIcon = $this->createAttachment($product->category->icon);
            if ($categoryIcon) {
                $attachments['category_image'][] = $categoryIcon;
            }
        }

        return $attachments;
    }

    private function createAttachment(?string $path): ?array
    {
        if (empty($path)) {
            return null;
        }

        $fullPath = dirname(base_path()) . DIRECTORY_SEPARATOR . $path;
        $fullPath = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $fullPath);

        if (! file_exists($fullPath)) {
            Log::warning("File not found for sync: $fullPath");
            return null;
        }

        return [
            'contents' => file_get_contents($fullPath),
            'name'     => basename($fullPath),
        ];
    }

    private function normalizePaths(mixed $value): array
    {
        if (empty($value)) {
            return [];
        }

        if (is_array($value)) {
            return array_values(array_filter($value));
        }

        $decoded = json_decode((string) $value, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return array_values(array_filter($decoded));
        }

        return [(string) $value];
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Product sync to app2 permanently failed after all retries', [
            'product_id' => $this->productId,
            'error'      => $exception->getMessage(),
        ]);
    }
}
