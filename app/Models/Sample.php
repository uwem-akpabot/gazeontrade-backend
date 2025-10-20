<?php

namespace App\Models;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Sample extends Model
{
    use HasFactory;
    protected $table = 'samples';
    protected $fillable = [
        'category_id', 
        'slug',
        'name', 
        'description',
        'image'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($sample) {
            $sample->slug = Str::slug($sample->name);
        });

        static::updating(function ($sample) {
            $sample->slug = Str::slug($sample->name);
        });
    }

    protected $with = ['category'];
    public function category(){
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }
}
