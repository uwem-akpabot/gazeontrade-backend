<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;

class OrderItems extends Model
{
    use HasFactory;
    protected $table = 'orderitems';
    protected $fillable = [
        'order_id',
        'product_id', 
        'qty',
        'price'
    ];

    public function product(){
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}