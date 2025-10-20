<?php

namespace App\Models;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Subcategory extends Model
{
    use HasFactory;
    protected $table = 'subcategories';
    protected $fillable = [
        'category_id', 
        'name', 
        'description', 
        'image',
        'popular_style'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($subcategory) {
            $subcategory->slug = Str::slug($subcategory->name);
        });

        static::updating(function ($subcategory) {
            $subcategory->slug = Str::slug($subcategory->name);
        });
    }

    protected $with = ['category'];
    public function category(){
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function products(){
        return $this->hasMany(Product::class);
    }
}