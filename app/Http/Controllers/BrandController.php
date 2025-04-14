<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function store(Request $request) {
        try{
            $authUse_id = auth()->user()->id;

            $request->validate([
                'name' => 'required|unique:brands',
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
                $imagePath = $image->store('brands', 'public');
            }

            $slug = Str::slug($request->name);

            $brand = Brand::create([
                'name' => $request->name,
                'slug' => $slug,
                'description' => $request->description,
                'image' => $imagePath,
                'user_id' => $authUse_id
            ]);

            return response()->json([
                'message' => 'Brand created successfully',
                'brand' => $brand,
            ],201);
        }catch(Exception $e){
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
    //Get all Brands
    public function index() {
        try{
            $brands = Brand::all();
            if($brands->isEmpty()){
                return response()->json([
                    'message' => 'No Brands found'
                ], 404);
            }
            return response()->json([
                'message' => 'Brands retrieved successfully',
                'brands' => $brands,
            ],201);
        }catch(Exception $e){
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
    //Update Brand
    public function update(Request $request, $id) {
        try{
            $authUse_id = auth()->user()->id;

            $brand = Brand::where('id', $id)->where('user_id', $authUse_id)->first();
            if (!$brand) {
                return response()->json([
                    'message' => 'Brand not found'
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
            $imagePath = $brand->image;
            if ($request->hasFile('image')) {
                //old image
                if($brand->image && file_exists(public_path('uploads/brands/'.$brand->image))){
                    unlink(public_path('uploads/brands/'.$brand->image));
                }
                $image = $request->file('image');
                $imagePath = $image->store('brands', 'public');
            }

            $slug = Str::slug($request->name);

            $brand->update([
                'name' => $request->name,
                'slug' => $slug,
                'description' => $request->description,
                'image' => $imagePath,
                'user_id' => $authUse_id
            ]);

            return response()->json([
                'message' => 'Brand updated successfully',
                'brand' => $brand,
            ],201);
        }catch(Exception $e){
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function destroy($id) {
        try{
            $authUse_id = auth()->user()->id;

            $brand = Brand::where('id', $id)->where('user_id', $authUse_id)->first();
            if (!$brand) {
                return response()->json([
                    'message' => 'Brand not found'
                ], 404);
            }
            if($brand->image && file_exists(public_path('uploads/brands/'.$brand->image))){
                unlink(public_path('uploads/brands/'.$brand->image));
            }
            $brand->delete();
            return response()->json([
                'message' => 'Brand deleted successfully'
            ], 201);
        }catch(Exception $e){
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
