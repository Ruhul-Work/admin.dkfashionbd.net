<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
             Schema::create('products', function (Blueprint $t) {
            $t->id();

            // Parent/Child (single-table variants)
            $t->foreignId('parent_id')
              ->nullable()
              ->constrained('products')
              ->cascadeOnUpdate()
              ->cascadeOnDelete(); // parent delete => children delete

            $t->boolean('has_variants')->default(false); // parent: 1, child/single: 0
            $t->boolean('is_sellable')->default(false);  // child/single: 1, parent: 0

            // Identity
            $t->string('name');
            $t->string('slug');
            $t->string('sku')->unique();            // globally unique
            $t->string('barcode')->nullable()->unique();

            // Core refs
            $t->foreignId('unit_id')->constrained('units')->cascadeOnUpdate()->restrictOnDelete();
            $t->foreignId('brand_id')->nullable()->constrained('brands')->cascadeOnUpdate()->nullOnDelete();
            $t->foreignId('product_type_id')->nullable()->constrained('product_types')->cascadeOnUpdate()->nullOnDelete();

            // Category refs
            $t->foreignId('category_type_id')->nullable()->constrained('category_types')->cascadeOnUpdate()->nullOnDelete();
            $t->foreignId('category_id')->nullable()->constrained('categories')->cascadeOnUpdate()->nullOnDelete();
            $t->foreignId('subcategory_id')->nullable()->constrained('categories')->cascadeOnUpdate()->nullOnDelete();

            // Tax
            $t->foreignId('tax_id')->nullable()->constrained('taxes')->cascadeOnUpdate()->nullOnDelete();
            $t->boolean('tax_included')->default(false);

            // Variant attributes (nullable: apparel/book/etc.)
            $t->foreignId('color_id')->nullable()->constrained('colors')->cascadeOnUpdate()->nullOnDelete();
            $t->foreignId('size_id')->nullable()->constrained('sizes')->cascadeOnUpdate()->nullOnDelete();
            $t->foreignId('paper_id')->nullable()->constrained('papers')->cascadeOnUpdate()->nullOnDelete(); // format_id

            // Pricing
            $t->decimal('price', 12, 2)->default(0);       // selling/base price
            $t->decimal('cost_price', 12, 2)->nullable();  // avg/last cost
            $t->decimal('mrp', 12, 2)->nullable();

            // Discount
            $t->enum('discount_type', ['percent','flat'])->nullable();
            $t->decimal('discount_value', 12, 2)->nullable();
            $t->timestamp('discount_starts_at')->nullable();
            $t->timestamp('discount_ends_at')->nullable();

            // Inventory flags
            $t->boolean('track_stock')->default(true);
            $t->decimal('reorder_level', 12, 3)->nullable();

            // Media
            $t->string('image')->nullable();
            $t->string('thumbnail_image')->nullable();
            $t->string('size_chart_image')->nullable();

            // Descriptive
            $t->string('material')->nullable();
            $t->string('meta_title')->nullable();
            $t->text('meta_description')->nullable();
            $t->text('meta_keywords')->nullable();
            $t->string('meta_image')->nullable();
            $t->string('short_description')->nullable();
            $t->longText('description')->nullable();

            // Other flags
            $t->boolean('is_active')->default(true);

            // Physical dimensions
            $t->decimal('weight', 12, 3)->nullable();
            $t->decimal('width', 12, 3)->nullable();
            $t->decimal('height', 12, 3)->nullable();
            $t->decimal('length', 12, 3)->nullable();

            $t->softDeletes();
            $t->timestamps();

            // Useful indexes
            $t->index(['parent_id', 'is_sellable'], 'prod_parent_sellable_idx');
            $t->index(['category_id', 'subcategory_id']);
            $t->index(['brand_id', 'is_active']);

            // Optional: prevent exact duplicate attribute combo under same parent
            // NOTE: MySQL UNIQUE + NULL ⇒ multiple NULLs allowed.
            $t->unique(['parent_id','color_id','size_id','paper_id'], 'prod_parent_attr_combo_uniq');

            // Slug fast lookup (optional unique if you want)
            $t->index('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
