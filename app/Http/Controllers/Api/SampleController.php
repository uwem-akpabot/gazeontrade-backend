<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sample;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;

class SampleController extends Controller
{
    public function index(){
        $page = request()->get('page', 1);

        $samples = Sample::select('id','slug','name','description','image','category_id')
            ->with('category:id,name')
            ->orderBy('id','desc')
            ->paginate(10);

        return response()->json([
            'status' => 200,
            'sample' => $samples
        ]);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|max:191',
            'name' => 'required|max:191|unique:samples,name',
            'image' => 'required|image|mimes:jpeg,png,jpg,JPG|max:2048'
        ]);

        if ($validator->fails()){
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages(),
            ]);

        } else {
            $sample = new Sample;

            $sample->category_id = $request->input('category_id');
            $sample->name = $request->input('name');
            // $sample->slug = $request->input('slug');
            $sample->description = $request->input('description');

            if ($request->hasFile('image')){
                $file = $request->file('image');
                $extension = $file->getClientOriginalExtension();
                $filename = time().'.'.$extension;
                $file->move('uploads/sample/', $filename);
                $sample->image = 'uploads/sample/'.$filename;
            }

            $sample->save();

            return response()->json([
                'status' => 200,
                'message' => 'Sample added successfully'
            ]);
        }        
    }

    public function edit($slug){
        $sample = Sample::where('slug', $slug)->first();

        if ($sample) {
            return response()->json([
                'status' => 200,
                'sample' => $sample
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No sample found'
            ]);
        }
    }

    public function update(Request $request, $slug){
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:191',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages(),
            ]);
        }

        $sample = Sample::where('slug', $slug)->first();

        if (!$sample) {
            return response()->json([
                'status' => 404,
                'message' => 'No sample slug found',
            ]);
        }

        $sample->category_id = (int) $request->input('category_id');
        $sample->name = $request->input('name');
        $sample->description = $request->input('description');

        if ($request->hasFile('image')) {
            $oldPath = public_path($sample->image);
            if ($sample->image && File::exists($oldPath)) {
                File::delete($oldPath);
            }

            $file = $request->file('image');
            $filename = time().'.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/sample/'), $filename);
            $sample->image = 'uploads/sample/'.$filename;
        }
        $sample->save();

        return response()->json([
            'status' => 200,
            'message' => 'Sample updated successfully',
            'sample' => $sample,
        ]);
    }

    public function destroy($slug){
        $sample = Sample::where('slug', $slug)->first();

        if ($sample) {
            if ($sample->image && File::exists(public_path($sample->image))) {
                File::delete(public_path($sample->image));
            }

            $sample->delete();

            return response()->json([
                'status' => 200,
                'message' => 'Sample deleted successfully'
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No sample slug found'
            ]);
        }
    }
}