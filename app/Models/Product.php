<?php

namespace App\Models;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;
    protected $table = 'products';
    protected $fillable = [
        'category_id', 
        'subcategory_id',
        'product', 
        'description', 
        'selling_price', 
        'original_price', 
        'quantity', 
        'brand', 
        'image',
        'image2',
        'image3',
        'image4',
        'image5',
        'image6',
        'image7',
        'image8',
        'featured_product', 
        'popular_product'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($product) {
            $product->slug = Str::slug($product->product);
        });

        static::updating(function ($product) {
            $product->slug = Str::slug($product->product);
        });
    }

    // protected $with = ['category'];
    public function category(){
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    protected $with = ['category', 'subcategory'];
    public function subcategory(){
        return $this->belongsTo(Subcategory::class, 'subcategory_id', 'id');
    }
}
