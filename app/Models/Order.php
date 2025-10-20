<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $table = 'orders';
    protected $fillable = [
        'fname',
        'sname', 
        'country',
        'street', 
        'city', 
        'thestate', 
        'phone',
        'email', 
        'other'
    ];

    public function orderitems(){
        return $this->hasMany(OrderItems::class, 'order_id', 'id');
    }
}
