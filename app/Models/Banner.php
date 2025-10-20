<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Banner extends Model
{
    use HasFactory;
    protected $table = 'banners';
    protected $fillable = [
        'image', 
        'caption'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($banner) {
            $banner->slug = Str::slug($banner->banner);
        });

        static::updating(function ($banner) {
            $banner->slug = Str::slug($banner->banner);
        });
    }
}