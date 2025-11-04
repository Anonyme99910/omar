<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Get dashboard statistics
     */
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_properties' => Property::count(),
            'pending_properties' => Property::where('status', 'pending')->count(),
            'approved_properties' => Property::where('status', 'approved')->count(),
            'rejected_properties' => Property::where('status', 'rejected')->count(),
            'recent_users' => User::latest()->take(5)->get(),
            'recent_properties' => Property::with('owner')->latest()->take(10)->get(),
        ];

        return response()->json($stats);
    }

    /**
     * Get all users with pagination
     */
    public function users(Request $request)
    {
        $query = User::query();

        // Search
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('is_active') && $request->is_active !== '') {
            $query->where('is_active', $request->is_active);
        }

        try {
            $users = $query->withCount('properties')
                ->orderBy('created_at', 'desc')
                ->paginate($request->get('per_page', 20));

            return response()->json($users);
        } catch (\Exception $e) {
            \Log::error('Users query error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to load users',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle user active status
     */
    public function toggleUserStatus(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
        
        // Prevent admin from deactivating themselves
        if ($user->id === $request->user()->id) {
            return response()->json([
                'error' => 'لا يمكنك تعطيل حسابك الخاص'
            ], 400);
        }

        $user->is_active = !$user->is_active;
        $user->save();

        return response()->json([
            'message' => $user->is_active ? 'تم تفعيل المستخدم' : 'تم تعطيل المستخدم',
            'user' => $user
        ]);
    }

    /**
     * Get all properties with filters
     */
    public function properties(Request $request)
    {
        $query = Property::with('owner');

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->has('category')) {
            $query->where('category', $request->category);
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

        $properties = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 20));

        return response()->json($properties);
    }

    /**
     * Update property status (approve/reject)
     */
    public function updatePropertyStatus(Request $request, $propertyId)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $property = Property::findOrFail($propertyId);
        $property->status = $request->status;
        $property->save();

        return response()->json([
            'message' => 'تم تحديث حالة العقار بنجاح',
            'property' => $property->load('owner')
        ]);
    }

    /**
     * Delete property (admin only)
     */
    public function deleteProperty($propertyId)
    {
        $property = Property::findOrFail($propertyId);
        $property->delete();

        return response()->json([
            'message' => 'تم حذف العقار بنجاح'
        ]);
    }

    /**
     * Get statistics by category
     */
    public function statisticsByCategory()
    {
        $stats = Property::select('category', DB::raw('count(*) as count'))
            ->groupBy('category')
            ->get();

        return response()->json($stats);
    }

    /**
     * Get statistics by status
     */
    public function statisticsByStatus()
    {
        $stats = Property::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        return response()->json($stats);
    }
}
