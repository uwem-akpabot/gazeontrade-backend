<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    public function addtocart(Request $request){
        if (auth('sanctum')->check()){

            $user_id = auth('sanctum')->user()->id;
            $product_id = $request->product_id;
            $product_qty = $request->product_qty;

            // check if product exists
            $productCheck = Product::where('id', $product_id)->first();
            
            if ($productCheck){
                if (Cart::where('product_id', $product_id)
                    ->where('user_id', $user_id)
                    ->exists()){

                     return response()->json([
                        'status' => 409, // already inserted
                        'message' => $productCheck->name.' already added to cart'
                    ]);
                } 
                else {
                    $cartitem = new Cart;
                    $cartitem->user_id = $user_id;
                    $cartitem->product_id = $product_id;
                    $cartitem->product_qty = $product_qty;
                    $cartitem->save();

                    return response()->json([
                        'status' => 201,
                        'message' => 'Added to Cart'
                    ]);
                }

            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'Product not found'
                ]);
            }

        } else {
            return response()->json([
                'status' => 401,
                'message' => 'You have to Login before you can add to cart'
            ]);
        }
    }

    public function viewcart(){
        if (auth('sanctum')->check()){
            $user_id = auth('sanctum')->user()->id;            
            $cartitems = Cart::where('user_id', $user_id)->get();
            
            return response()->json([
                'status' => 200,
                'cart' => $cartitems
            ]);
        } 
        else {
            return response()->json([
                'status' => 401,
                'message' => 'You have to Login to view cart data'
            ]);
        }
    }

    public function updatequantity($cart_id, $scope){
        if (auth('sanctum')->check()){
            $user_id = auth('sanctum')->user()->id;            
            $cartitem = Cart::where('id', $cart_id)->where('user_id', $user_id)->first();
            
            if ($scope == 'inc'){ 
                if ($cartitem->product_qty < 10) {              
                    $cartitem->product_qty += 1;
                }
            } else if ($scope == 'dec'){
                if ($cartitem->product_qty > 1){
                    $cartitem->product_qty -= 1;
                }
            }

            $cartitem->update();
            
            return response()->json([
                'status' => 200,
                'message' => 'Quantity updated'
            ]);
        } 
        else {
            return response()->json([
                'status' => 401,
                'message' => 'Login to continue'
            ]);
        }
    }

    public function deleteCartItem($cart_id){
        if (auth('sanctum')->check()){
            $user_id = auth('sanctum')->user()->id;            
            $cartitem = Cart::where('id', $cart_id)->where('user_id', $user_id)->first();
            
            if ($cartitem){
                $cartitem->delete();

                return response()->json([
                    'status' => 200,
                    'message' => 'Cart item removed successfully'
                ]);

            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'Cart item not found'
                ]);
            }
        }
        else {
            return response()->json([
                'status' => 401,
                'message' => 'Login to continue'
            ]);
        }
    }
}
