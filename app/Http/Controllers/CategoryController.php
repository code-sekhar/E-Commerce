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
                'status' => 'required',
            ],[
                'name.required' => 'Name is required',
                'description.required' => 'Description is required',
                'image.image' => 'Image must be an image',
                'image.mimes' => 'Image must be a file of type: jpeg, png, jpg, gif, svg',
                'image.max' => 'Image size must not exceed 2MB',
                'status.required' => 'Status is required',
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
                'status' => $request->status,
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
}
