<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::select(['id', 'name', 'name_ar', 'is_active'])
            ->where('is_active', true)
            ->withCount('products')
            ->orderBy('name')
            ->get();
        return response()->json($brands);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'description' => 'nullable|string',
            'logo' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $brand = Brand::create($request->all());
        return response()->json($brand, 201);
    }

    public function show($id)
    {
        $brand = Brand::with('products')->findOrFail($id);
        return response()->json($brand);
    }

    public function update(Request $request, $id)
    {
        $brand = Brand::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'name_ar' => 'string|max:255',
            'description' => 'nullable|string',
            'logo' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $brand->update($request->all());
        return response()->json($brand);
    }

    public function destroy($id)
    {
        $brand = Brand::findOrFail($id);
        $brand->delete();
        return response()->json(['message' => 'Brand deleted successfully']);
    }
}
