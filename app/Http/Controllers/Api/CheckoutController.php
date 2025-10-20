<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Order;
use Illuminate\Support\Facades\Validator;

class CheckoutController extends Controller
{
    public function placeorder(Request $request){
        if (auth('sanctum')->check()){

            $validator = Validator::make($request->all(), [
                'fname' => 'required|max:191',
                'sname' => 'required|max:191', 
                'country' => 'required|max:191',
                'street' => 'required|max:191', 
                'city' => 'required|max:191', 
                'thestate' => 'required|max:191', 
                'phone' => 'required|max:191',
                'email' => 'required|max:191'
            ]);

            if ($validator->fails()){
                return response()->json([
                    'status' => 422,
                    'errors' => $validator->messages(),
                ]);            
            } else {
                $user_id = auth('sanctum')->user()->id;
                $order = new Order;

                $order->user_id = $user_id;
                $order->fname = $request->input('fname');
                $order->sname = $request->input('sname');
                $order->country = $request->input('country');
                $order->street = $request->input('street');
                $order->city = $request->input('city');
                $order->thestate = $request->input('thestate');
                $order->phone = $request->input('phone');
                $order->email = $request->input('email');
                $order->other = $request->input('other');
                $order->save();

                $cart = Cart::where('user_id', $user_id)->get();
                
                $orderitems = [];
                foreach($cart as $item){
                    $orderitems[] = [
                        'product_id' => $item->product_id,
                        'qty' => $item->product_qty,
                        'price' => $item->product->selling_price,
                    ];

                    // decrement Quantity when product is purchased
                    $item->product->update([
                        'qty' => $item->product->qty - $item->product_qty
                    ]);
                }

                $order->orderitems()->createMany($orderitems);
                // empty the cart
                Cart::destroy($cart);
                
                return response()->json([
                    'status' => 200,
                    'message' => 'Order placed successfully'
                ]);
            }

        } else {
            return response()->json([
                'status' => 401,
                'message' => 'Login to continue'
            ]);
        }
    }
}
