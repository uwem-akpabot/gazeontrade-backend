<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;

class SubcategoryController extends Controller
{
    public function index(){
        $page = request()->get('page', 1);

        $subcategories = Subcategory::select('id', 'category_id', 'name', 'slug', 'description', 
                'image', 'popular_style')
            ->with('category:id,name')
            ->orderBy('id','desc')
            ->paginate(20);

        return response()->json([
            'status' => 200,
            'subcategory' => $subcategories
        ]);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|max:191',
            'name' => 'required|max:191',
            'description' => 'required|max:191|unique:subcategories,description',
            'image' => 'required|image|mimes:jpeg,png,jpg,JPG|max:2048'
        ]);

        if ($validator->fails()){
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages(),
            ]);

        } else {
            $subcategory = new Subcategory;

            $subcategory->category_id = $request->input('category_id');
            $subcategory->name = $request->input('name');
            $subcategory->description = $request->input('description');

            if ($request->hasFile('image')){
                $file = $request->file('image');
                $extension = $file->getClientOriginalExtension();
                $filename = time().'.'.$extension;
                $file->move('uploads/subcategory/', $filename);
                $subcategory->image = 'uploads/subcategory/'.$filename;
            }
            $subcategory->popular_style = $request->input('popular_style') == true ? '1':'0';
            $subcategory->save();

            return response()->json([
                'status' => 200,
                'message' => 'Subcategory added successfully'
            ]);
        }        
    }

    public function detail($slug){
        $subcategory = Subcategory::where('slug', $slug)->first();

        if ($subcategory) {
            return response()->json([
                'status' => 200,
                'subcategory' => $subcategory
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No subcategory found'
            ]);
        }
    }

    public function edit($slug){
        $subcategory = Subcategory::where('slug', $slug)->first();

        if ($subcategory) {
            return response()->json([
                'status' => 200,
                'subcategory' => $subcategory
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No subcategory found'
            ]);
        }
    }

    public function update(Request $request, $slug){
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|max:191',
            'name' => 'required|max:191',
            'description' => 'required|max:191'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages(),
            ]);
        }
        $subcategory = Subcategory::where('slug', $slug)->first();

        if (!$subcategory) {
            return response()->json([
                'status' => 404,
                'message' => 'No subcategory slug found',
            ]);
        }

        $subcategory->category_id = $request->input('category_id');
        $subcategory->name = $request->input('name');
        $subcategory->description = $request->input('description');

        if ($request->hasFile('image')) {
            $oldPath = public_path($subcategory->image);
            if ($subcategory->image && File::exists($oldPath)) {
                File::delete($oldPath);
            }

            $file = $request->file('image');
            $filename = time().'.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/subcategory/'), $filename);
            $subcategory->image = 'uploads/subcategory/'.$filename;
        }
        $subcategory->popular_style = $request->input('popular_style');
        $subcategory->save();

        return response()->json([
            'status' => 200,
            'message' => 'Subcategory updated successfully',
            'subcategory' => $subcategory,
        ]);
    }           

    public function destroy($slug){
        $subcategory = Subcategory::where('slug', $slug)->first();

        if ($subcategory) {
            if ($subcategory->image && File::exists(public_path($subcategory->image))) {
                File::delete(public_path($subcategory->image));
            }
            $subcategory->delete();

            return response()->json([
                'status' => 200,
                'message' => 'Subcategory deleted successfully'
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No subcategory slug found'
            ]);
        }
    }

    public function getByCategory($category_id){
        $subcategories = Subcategory::where('category_id', $category_id)->get();

        return response()->json([
            'status' => 200,
            'subcategory' => $subcategories
        ]);
    }

}