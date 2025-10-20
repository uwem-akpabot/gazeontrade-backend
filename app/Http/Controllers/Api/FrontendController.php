<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FrontendController extends Controller
{
    // homepage left menu category listing
    public function listCategories(){
        $categories = Category::select('id', 'name', 'slug')
            ->orderBy('name','asc')
            ->take(10)
            ->get();

        return response()->json([
            'status' => 200,
            'category' => $categories
        ]);
    }

    // collections page category listings
    public function categories(){
        $categories = Category::select('id', 'name', 'slug', 'description', 'image')
            ->orderBy('name','asc')
            ->take(50)
            ->get();

        return response()->json([
            'status' => 200,
            'category' => $categories
        ]);
    }

    // homepage featured products
    public function featuredProducts(){
        $products = Product::with('category:id,slug')
            ->select('id', 'category_id', 'product', 'slug', 'selling_price', 'image')
            ->where('featured_product', 1)
            ->orderBy('id','desc')
            ->take(16)
            ->get();

        return response()->json([
            'status' => 200,
            'product' => $products
        ]);
    }

    // homepage popular products
    public function popularProducts(){
        $products = Product::select('id', 'product', 'slug', 'selling_price', 'image')
            ->where('popular_product', 1)
            ->orderBy('id','desc')
            ->take(20)
            ->get();

        return response()->json([
            'status' => 200,
            'product' => $products
        ]);
    }

    public function categoriesProducts($slug){
        $category = Category::where('slug', $slug)->first();

        if($category){
            $product = Product::where('category_id', $category->id)->get();

            if ($product){
                return response()->json([
                    'status' => 200,
                    'product_data' => [
                        'product' => $product,
                        'category' => $category
                    ]
                ]);
            } else {
                return response()->json([
                    'status' => 400,
                    'message' => 'No product available'
                ]);
            }
            
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No such product found'
            ]);
        }
    }

    public function productDetail($category_slug, $product_slug){
        $category = Category::where('slug', $category_slug)->first();

        if($category){
            $product = Product::where('category_id', $category->id)
                ->where('slug', $product_slug)
                ->first();

            if ($product){
                return response()->json([
                    'status' => 200,
                    'product' => $product
                ]);
            } else {
                return response()->json([
                    'status' => 400,
                    'message' => 'No product available'
                ]);
            }
            
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No such product found'
            ]);
        }
    }
}
