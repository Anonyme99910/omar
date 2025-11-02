<?php

namespace App\Http\Controllers;

use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PackageController extends Controller
{
    public function index()
    {
        try {
            $packages = Package::orderBy('created_at', 'desc')->get();
            
            return response()->json([
                'success' => true,
                'data' => $packages
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch packages: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json([
                'success' => false,
                'error' => 'فشل تحميل الباقات: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'sku' => 'nullable|string|max:100|unique:packages,sku',
            'description' => 'nullable|string',
            'price_قطاعي' => 'required|numeric|min:0',
            'price_جملة' => 'required|numeric|min:0',
            'price_صفحة' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'alert_quantity' => 'required|integer|min:0',
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'exists:products,id',
            'product_quantities' => 'nullable|array'
        ]);

        DB::beginTransaction();
        try {
            // Handle photo upload
            if ($request->hasFile('photo')) {
                $photo = $request->file('photo');
                $filename = time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();
                $photo->move(public_path('uploads/packages'), $filename);
                $validated['photos'] = json_encode(['/uploads/packages/' . $filename]);
            } else {
                $validated['photos'] = null;
            }

            $package = Package::create($validated);

            // Attach products to package
            if (!empty($validated['product_ids'])) {
                $productData = [];
                foreach ($validated['product_ids'] as $index => $productId) {
                    $quantity = $validated['product_quantities'][$index] ?? 1;
                    $productData[$productId] = ['quantity' => $quantity];
                }
                $package->products()->attach($productData);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم إضافة الباقة بنجاح',
                'data' => $package
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create package: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'فشل إضافة الباقة: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $package = Package::findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $package
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'الباقة غير موجودة'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'sku' => 'nullable|string|max:100|unique:packages,sku,' . $id,
            'description' => 'nullable|string',
            'price_قطاعي' => 'required|numeric|min:0',
            'price_جملة' => 'required|numeric|min:0',
            'price_صفحة' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'alert_quantity' => 'required|integer|min:0',
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'exists:products,id',
            'product_quantities' => 'nullable|array'
        ]);

        DB::beginTransaction();
        try {
            $package = Package::findOrFail($id);

            // Handle photo upload
            if ($request->hasFile('photo')) {
                $photo = $request->file('photo');
                $filename = time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();
                $photo->move(public_path('uploads/packages'), $filename);
                $validated['photos'] = json_encode(['/uploads/packages/' . $filename]);
            }

            $package->update($validated);

            // Update products
            if (isset($validated['product_ids'])) {
                $productData = [];
                foreach ($validated['product_ids'] as $index => $productId) {
                    $quantity = $validated['product_quantities'][$index] ?? 1;
                    $productData[$productId] = ['quantity' => $quantity];
                }
                $package->products()->sync($productData);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث الباقة بنجاح',
                'data' => $package
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update package: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'فشل تحديث الباقة'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $package = Package::findOrFail($id);
            $package->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف الباقة بنجاح'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete package: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'فشل حذف الباقة'
            ], 500);
        }
    }
}
