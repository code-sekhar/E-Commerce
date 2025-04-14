<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Category;
use Exception;


class CategoryController extends Controller
{
    public function store(Request $request) {
        try{
            $authUse_id = auth()->user()->id;

            $request->validate([
                'name' => 'required|unique:categories',
                'description' => 'required',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ],[
                'name.required' => 'Name is required',
                'description.required' => 'Description is required',
                'image.image' => 'Image must be an image',
                'image.mimes' => 'Image must be a file of type: jpeg, png, jpg, gif, svg',
                'image.max' => 'Image size must not exceed 2MB',
            ]);
            $imagePath = null;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imagePath = $image->store('categories', 'public');
            }

            $slug = Str::slug($request->name);

            $category = Category::create([
                'name' => $request->name,
                'slug' => $slug,
                'description' => $request->description,
                'image' => $imagePath,
                'user_id' => $authUse_id
            ]);

            return response()->json([
                'message' => 'Category created successfully',
                'category' => $category
            ], 201);
        }catch(Exception $e){
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
    //get all categories
    public function index() {
        try{
            $authUse_id = auth()->user()->id;

            $categories = Category::where('user_id', $authUse_id)->get();
            if($categories->isEmpty()){
                return response()->json([
                    'message' => 'No categories found'
                ], 404);
            }
            return response()->json([
                'message' => 'Categories retrieved successfully',
                'categories' => $categories,
            ],201);
        }catch(Exception $e){
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
    //update category
    public function update(Request $request, $id) {
        try{
            $authUse_id = auth()->user()->id;

            $category = Category::where('id', $id)->where('user_id', $authUse_id)->first();
            if (!$category) {
                return response()->json([
                    'message' => 'Category not found'
                ], 404);
            }
            $request->validate([
                'name' => 'required',
                'description' => 'required',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ],[
                'name.required' => 'Name is required',
                'description.required' => 'Description is required',
                'image.image' => 'Image must be an image',
                'image.mimes' => 'Image must be a file of type: jpeg, png, jpg, gif, svg',
                'image.max' => 'Image size must not exceed 2MB',
            ]);
            $imagePath = $category->image;
            if ($request->hasFile('image')) {
                //old image
                if($category->image && file_exists(public_path('uploads/categories/'.$category->image))){
                    unlink(public_path('uploads/categories/'.$category->image));
                }
                $image = $request->file('image');
                $imagePath = $image->store('categories', 'public');

            }
            $slug = Str::slug($request->name);
            $category->update([
                'name' => $request->name,
                'slug' => $slug,
                'description' => $request->description,
                'image' => $imagePath,
            ]);
            return response()->json([
                'message' => 'Category updated successfully',
                'category' => $category
            ], 200);
        }catch(Exception $e){
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
    //delete category
    public function destroy($id) {
        try{
            $authUse_id = auth()->user()->id;

            $category = Category::where('id', $id)->where('user_id', $authUse_id)->first();
            if (!$category) {
                return response()->json([
                    'message' => 'Category not found'
                ], 404);
            }
            if($category->image && file_exists(public_path('uploads/categories/'.$category->image))){
                unlink(public_path('uploads/categories/'.$category->image));
            }
            $category->delete();
            return response()->json([
                'message' => 'Category deleted successfully'
            ], 200);
        }catch(Exception $e){
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
