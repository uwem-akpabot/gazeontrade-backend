<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItems;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;

class DashboardController extends Controller
{
    public function stats()
    {
        $products = Product::count();
        $categories = Category::count();
        $orders = Order::count();
        $orderitems = OrderItems::sum('price'); // example field

        return response()->json([
            'status' => 200,
            'products' => $products,
            'categories' => $categories,
            'orders' => $orders,
            'orderitems' => $orderitems
        ]);
    }

    public function banner(){
        $banner = Banner::all();
        return response()->json([
            'status' => 200, 
            'banner' => $banner
        ]);
    }

    public function editBanner($id){
        $banner = Banner::find($id);

        if($banner){
            return response()->json([
                'status' => 200,
                'banner' => $banner
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No banner found'
            ]);
        }
    }

    public function updateBanner(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'caption' => 'required|max:191',
        ]);

        if ($validator->fails()){
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages(),
            ]);

        } else {
            $banner = Banner::find($id);

            if ($banner){
                $banner->caption = $request->input('caption');
                
                if ($request->hasFile('image')){
                    $path = $banner->image;

                    if (File::exists($path)){
                        File::delete($path);
                    }

                    $file = $request->file('image');
                    $extension = $file->getClientOriginalExtension();
                    $filename = time().'.'.$extension;
                    $file->move('uploads/banner/', $filename);
                    $banner->image = 'uploads/banner/'.$filename;
                }
                $banner->update();

                return response()->json([
                    'status' => 200,
                    'message' => 'Banner updated successfully'
                ]);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'No banner id found'
                ]);
            }            
        }        
    } 
}