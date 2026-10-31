<?php
namespace App\Models\backend;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table   = 'products';
    
    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $hidden = [
        // 'cost_price',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = ['cost_price' => 'float'];

    public function product_type()
    {
        return $this->belongsTo(ProductType::class);
    }
    public function category_type()
    {
        return $this->belongsTo(CategoryType::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class, 'subcategory_id');
    }
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
    public function size()
    {
        return $this->belongsTo(Size::class);
    }
    public function color()
    {
        return $this->belongsTo(Color::class);
    }
    public function paper_quality()
    {
        return $this->belongsTo(PaperQuality::class, 'paper_id');
    }

    public function specifications()
    {
        return $this->hasOne(ProductSpecification::class);
    }

    public function children()
    {
        return $this->hasMany(Product::class, 'parent_id', 'id');
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function stockLedgers()
    {
        return $this->hasMany(StockLedger::class);
    }
    
    public function coupon()
    {
        return $this->belongsToMany(Coupon::class);
    }

    public function offerDiscountProducts()
    {
        return $this->belongsTo(OfferDiscountProduct::class); 
    }

}
