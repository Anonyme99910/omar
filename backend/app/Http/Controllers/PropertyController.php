<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PropertyController extends Controller
{
    /**
     * Get all properties.
     */
    public function index(Request $request)
    {
        $query = Property::with('owner')->approved();

        // Filter by category
        if ($request->has('category')) {
            $query->category($request->category);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $properties = $query->paginate($request->get('per_page', 20));

        return response()->json($properties);
    }

    /**
     * Get property by ID.
     */
    public function show($id)
    {
        $property = Property::with('owner')->findOrFail($id);

        return response()->json($property);
    }

    /**
     * Create new property.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'price_unit' => 'required|in:tnd,tnd/m²,tnd/hectare',
            'location' => 'required|string|max:255',
            'size' => 'required|numeric|min:0',
            'size_unit' => 'required|in:m²,hectare',
            'phone_number' => 'required|string|max:20',
            'images' => 'required|array',
            'images.*' => 'string',
            'category' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first()
            ], 422);
        }

        $property = Property::create([
            'owner_id' => $request->user()->id,
            'title' => $request->title,
            'description' => $request->description,
            'price' => $request->price,
            'price_unit' => $request->price_unit,
            'location' => $request->location,
            'size' => $request->size,
            'size_unit' => $request->size_unit,
            'phone_number' => $request->phone_number,
            'images' => $request->images,
            'category' => $request->category,
            'status' => 'pending',
        ]);

        return response()->json($property, 201);
    }

    /**
     * Update property.
     */
    public function update(Request $request, $id)
    {
        $property = Property::findOrFail($id);

        // Check if user owns the property
        if ($property->owner_id !== $request->user()->id) {
            return response()->json([
                'error' => 'غير مصرح لك بتعديل هذا العقار'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'price' => 'sometimes|numeric|min:0',
            'price_unit' => 'sometimes|in:tnd,tnd/m²,tnd/hectare',
            'location' => 'sometimes|string|max:255',
            'size' => 'sometimes|numeric|min:0',
            'size_unit' => 'sometimes|in:m²,hectare',
            'phone_number' => 'sometimes|string|max:20',
            'images' => 'sometimes|array',
            'images.*' => 'string',
            'category' => 'sometimes|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first()
            ], 422);
        }

        $property->update($request->only([
            'title', 'description', 'price', 'price_unit', 
            'location', 'size', 'size_unit', 'phone_number', 
            'images', 'category'
        ]));

        return response()->json($property);
    }

    /**
     * Delete property.
     */
    public function destroy(Request $request, $id)
    {
        $property = Property::findOrFail($id);

        // Check if user owns the property
        if ($property->owner_id !== $request->user()->id) {
            return response()->json([
                'error' => 'غير مصرح لك بحذف هذا العقار'
            ], 403);
        }

        $property->delete();

        return response()->json([
            'message' => 'تم حذف العقار بنجاح'
        ]);
    }

    /**
     * Get user's properties.
     */
    public function userProperties(Request $request)
    {
        $properties = Property::where('owner_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 20));

        return response()->json($properties);
    }
}
