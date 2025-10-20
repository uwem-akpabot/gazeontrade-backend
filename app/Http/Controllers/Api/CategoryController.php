<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;

class CategoryController extends Controller
{
    public function index(){
        $page = request()->get('page', 1);

        $categories = Category::select('id', 'name', 'slug', 'description', 'image')
            ->orderBy('id','desc')
            ->paginate(10);

        return response()->json([
            'status' => 200,
            'category' => $categories
        ]);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:191|unique:categories,name',
            'image' => 'required|image|mimes:jpeg,png,jpg,JPG|max:2048'
        ]);

        if ($validator->fails()){
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages(),
            ]);

        } else {
            $category = new Category;

            $category->name = $request->input('name');
            $category->description = $request->input('description');

            if ($request->hasFile('image')){
                $file = $request->file('image');
                $extension = $file->getClientOriginalExtension();
                $filename = time().'.'.$extension;
                $file->move('uploads/category/', $filename);
                $category->image = 'uploads/category/'.$filename;
            }
            $category->save();

            return response()->json([
                'status' => 200,
                'message' => 'Category added successfully'
            ]);
        }        
    }

    public function detail($slug){
        $category = Category::where('slug', $slug)->first();

        if ($category) {
            return response()->json([
                'status' => 200,
                'category' => $category
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No category found'
            ]);
        }
    }

    public function populateCategories(){
        $category = Category::get();
        return response()->json([
            'status' => 200, 
            'category' => $category
        ]);
    }

    public function edit($slug){
        $category = Category::where('slug', $slug)->first();

        if ($category) {
            return response()->json([
                'status' => 200,
                'category' => $category
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No category found'
            ]);
        }
    }

    public function update(Request $request, $slug){
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:191',
            'description' => 'required|max:191'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages(),
            ]);
        }
        $category = Category::where('slug', $slug)->first();

        if (!$category) {
            return response()->json([
                'status' => 404,
                'message' => 'No category slug found',
            ]);
        }

        $category->name = $request->input('name');
        $category->description = $request->input('description');

        if ($request->hasFile('image')) {
            $oldPath = public_path($category->image);
            if ($category->image && File::exists($oldPath)) {
                File::delete($oldPath);
            }

            $file = $request->file('image');
            $filename = time().'.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/category/'), $filename);
            $category->image = 'uploads/category/'.$filename;
        }
        $category->save();

        return response()->json([
            'status' => 200,
            'message' => 'Category updated successfully',
            'category' => $category,
        ]);
    } 

    public function destroy($slug){
        $category = Category::where('slug', $slug)->first();

        if ($category) {
            if ($category->image && File::exists(public_path($category->image))) {
                File::delete(public_path($category->image));
            }
            $category->delete();

            return response()->json([
                'status' => 200,
                'message' => 'Category deleted successfully'
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No category slug found'
            ]);
        }
    }
}
