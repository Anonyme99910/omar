<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    /**
     * Upload image.
     */
    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
            
            // Store in public/uploads directory
            $path = $image->storeAs('uploads', $filename, 'public');
            
            $url = Storage::url($path);

            return response()->json([
                'url' => $url,
                'path' => $path,
            ]);
        }

        return response()->json([
            'error' => 'لم يتم رفع أي صورة'
        ], 400);
    }

    /**
     * Upload multiple images.
     */
    public function uploadMultiple(Request $request)
    {
        $request->validate([
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $urls = [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs('uploads', $filename, 'public');
                $urls[] = Storage::url($path);
            }
        }

        return response()->json([
            'urls' => $urls
        ]);
    }

    /**
     * Delete image.
     */
    public function delete(Request $request)
    {
        $request->validate([
            'path' => 'required|string',
        ]);

        $path = str_replace('/storage/', '', $request->path);
        
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
            
            return response()->json([
                'message' => 'تم حذف الصورة بنجاح'
            ]);
        }

        return response()->json([
            'error' => 'الصورة غير موجودة'
        ], 404);
    }
}
