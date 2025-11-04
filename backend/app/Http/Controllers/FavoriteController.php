<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Property;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    /**
     * Get user's favorites.
     */
    public function index(Request $request)
    {
        $favorites = Favorite::with('property.owner')
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 20));

        return response()->json($favorites);
    }

    /**
     * Add property to favorites.
     */
    public function store(Request $request, $propertyId)
    {
        // Check if property exists
        $property = Property::findOrFail($propertyId);

        // Check if already favorited
        $existing = Favorite::where('user_id', $request->user()->id)
            ->where('property_id', $propertyId)
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'العقار موجود بالفعل في المفضلة'
            ], 200);
        }

        $favorite = Favorite::create([
            'user_id' => $request->user()->id,
            'property_id' => $propertyId,
        ]);

        return response()->json($favorite, 201);
    }

    /**
     * Remove property from favorites.
     */
    public function destroy(Request $request, $propertyId)
    {
        $favorite = Favorite::where('user_id', $request->user()->id)
            ->where('property_id', $propertyId)
            ->first();

        if (!$favorite) {
            return response()->json([
                'error' => 'العقار غير موجود في المفضلة'
            ], 404);
        }

        $favorite->delete();

        return response()->json([
            'message' => 'تم إزالة العقار من المفضلة'
        ]);
    }

    /**
     * Check if property is favorited.
     */
    public function check(Request $request, $propertyId)
    {
        $isFavorited = Favorite::where('user_id', $request->user()->id)
            ->where('property_id', $propertyId)
            ->exists();

        return response()->json([
            'is_favorited' => $isFavorited
        ]);
    }
}
