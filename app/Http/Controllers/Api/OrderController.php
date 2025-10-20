<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(){
        $orders = Order::with('orderitems.product')->get(); // <-- eager load order items and products
    
        // $orders = Order::all();
        return response()->json([
            'status' => 200, 
            'orders' => $orders
        ]);
    }

    public function orderDetail($id){
        // $order = Order::find($id);
        $order = Order::with(['orderitems.product'])->find($id); // include related products

        if ($order) {
            return response()->json([
                'status' => 200,
                'order' => $order
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Order not found'
            ], 404);
        }
    }
}