<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    public function index(){
        $page = request()->get('page', 1);

        $products = Product::select('id', 'category_id', 'subcategory_id', 'product', 'slug', 
                'description', 'selling_price', 'original_price', 'quantity', 'brand', 
                'image', 'featured_product', 'popular_product')
            ->with('category:id,name')
            ->orderBy('id','desc')
            ->paginate(10);

        return response()->json([
            'status' => 200,
            'product' => $products
        ]);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|max:191',
            'subcategory_id' => 'required|max:191',
            'product' => 'required|max:191',
            'description' => 'required|max:191|unique:products,description',
            'selling_price' => 'required|max:20',
            'image' => 'required|image|mimes:jpeg,png,jpg,JPG|max:2048'
        ]);

        if ($validator->fails()){
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages(),
            ]);

        } else {
            $product = new Product;

            $product->category_id = $request->input('category_id');
            $product->subcategory_id = $request->input('subcategory_id');
            $product->product = $request->input('product');
            $product->description = $request->input('description');
            $product->selling_price = $request->input('selling_price');
            $product->original_price = $request->input('original_price');
            $product->quantity = $request->input('quantity');
            $product->brand = $request->input('brand');
            $product->delivery_time = $request->input('delivery_time');

            if ($request->hasFile('image')){
                $file = $request->file('image');
                $extension = $file->getClientOriginalExtension();
                $filename = time().'.'.$extension;
                $file->move('uploads/product/', $filename);
                $product->image = 'uploads/product/'.$filename;
            }

            for ($i = 2; $i <= 8; $i++) {
                $field = 'image'.$i;
                if ($request->hasFile($field)) {
                    $file = $request->file($field);
                    $filename = time().'_'.$field.'.'.$file->getClientOriginalExtension();
                    $file->move('uploads/product/', $filename);
                    $product->$field = 'uploads/product/'.$filename;
                }
            }

            $product->featured_product = $request->input('featured_product') == true ? '1':'0';
            $product->popular_product = $request->input('popular_product') == true ? '1':'0';
            $product->save();

            return response()->json([
                'status' => 200,
                'message' => 'Product added successfully'
            ]);
        }        
    }

    public function detail($slug){
        $product = Product::where('slug', $slug)->first();

        if ($product) {
            return response()->json([
                'status' => 200,
                'product' => $product
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No product found'
            ]);
        }
    }

    public function edit($slug){
        $product = Product::where('slug', $slug)->first();

        if ($product) {
            return response()->json([
                'status' => 200,
                'product' => $product
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No product found'
            ]);
        }
    }

    public function update(Request $request, $slug){
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|max:191',
            'product' => 'required|max:191',
            'selling_price' => 'required|max:20'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages(),
            ]);
        }
        $product = Product::where('slug', $slug)->first();

        if (!$product) {
            return response()->json([
                'status' => 404,
                'message' => 'No product slug found',
            ]);
        }

        $product->category_id = $request->input('category_id');
        $product->product = $request->input('product');
        $product->description = $request->input('description');
        $product->selling_price = $request->input('selling_price');
        $product->original_price = $request->input('original_price');
        $product->quantity = $request->input('quantity');
        $product->brand = $request->input('brand');
        $product->delivery_time = $request->input('delivery_time');

        if ($request->hasFile('image')) {
            $oldPath = public_path($product->image);
            if ($product->image && File::exists($oldPath)) {
                File::delete($oldPath);
            }

            $file = $request->file('image');
            $filename = time().'.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/product/'), $filename);
            $product->image = 'uploads/product/'.$filename;
        }

        for ($i = 2; $i <= 8; $i++) {
            $field = 'image'.$i;
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $filename = time().'_'.$field.'.'.$file->getClientOriginalExtension();
                $file->move('uploads/product/', $filename);
                $product->$field = 'uploads/product/'.$filename;
            }
        }

        $product->featured_product = $request->input('featured_product');
        $product->popular_product = $request->input('popular_product');
        $product->save();

        return response()->json([
            'status' => 200,
            'message' => 'Product updated successfully',
            'product' => $product,
        ]);
    }           

    public function destroy($slug){
        $product = Product::where('slug', $slug)->first();

        if ($product) {
            if ($product->image && File::exists(public_path($product->image))) {
                File::delete(public_path($product->image));
            }
            $product->delete();

            return response()->json([
                'status' => 200,
                'message' => 'Product deleted successfully'
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No product slug found'
            ]);
        }
    }
}
