<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\InventoryMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()
            ->select(['id', 'name', 'name_ar', 'sku', 
                      'selling_price', 'price_جملة', 'price_قطاعي', 'price_صفحة',
                      'volume_ml', 'quantity', 'alert_quantity', 'photos', 'is_active']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('name_ar', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($request->has('low_stock') && $request->low_stock) {
            $query->whereColumn('quantity', '<=', 'alert_quantity');
        }

        $perPage = $request->get('per_page', 50);
        $products = $query->orderBy('name_ar')->paginate($perPage);
        
        return response()->json($products);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name_ar' => 'required|string|max:255',
            'price_جملة' => 'required|numeric|min:0',
            'price_قطاعي' => 'required|numeric|min:0',
            'price_صفحة' => 'required|numeric|min:0',
            'volume_ml' => 'nullable|string|max:50',
            'quantity' => 'required|integer|min:0',
            'alert_quantity' => 'required|integer|min:1',
            'photo' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $data = $request->except('photo');
            
            // Handle photo upload
            if ($request->hasFile('photo')) {
                $photo = $request->file('photo');
                $filename = time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();
                $photo->move(public_path('uploads/products'), $filename);
                $data['photos'] = json_encode(['/uploads/products/' . $filename]);
            }
            
            $product = Product::create($data);

            // Create initial inventory movement
            if ($product->quantity > 0) {
                InventoryMovement::create([
                    'product_id' => $product->id,
                    'type' => 'purchase',
                    'quantity' => $product->quantity,
                    'previous_stock' => 0,
                    'new_stock' => $product->quantity,
                    'reference' => 'Initial Stock',
                    'notes' => 'Initial product creation',
                    'moved_at' => now()
                ]);
            }

            DB::commit();
            return response()->json($product, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Product creation failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to create product',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $product = Product::with(['inventoryMovements'])->findOrFail($id);
        return response()->json($product);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name_ar' => 'string|max:255',
            'price_جملة' => 'numeric|min:0',
            'price_قطاعي' => 'numeric|min:0',
            'price_صفحة' => 'numeric|min:0',
            'volume_ml' => 'nullable|string|max:50',
            'quantity' => 'integer|min:0',
            'alert_quantity' => 'integer|min:1',
            'photo' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->except('photo');
        
        // Handle photo upload
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $filename = time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();
            $photo->move(public_path('uploads/products'), $filename);
            $data['photos'] = json_encode(['/uploads/products/' . $filename]);
        }

        $product->update($data);
        return response()->json($product);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return response()->json(['message' => 'Product deleted successfully']);
    }

    public function searchBySku($sku)
    {
        $product = Product::where('sku', $sku)->first();

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json($product);
    }

    public function lowStock()
    {
        $products = Product::with(['category', 'brand'])
            ->whereRaw('stock_quantity <= min_stock_level')
            ->get();

        return response()->json($products);
    }

    public function adjustStock(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1',
            'type' => 'required|in:in,out,adjustment',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $product = Product::findOrFail($id);
        $previousStock = $product->stock_quantity;

        DB::beginTransaction();
        try {
            // Map old type to new type
            $movementType = 'manual_adjust';
            
            if ($request->type === 'in') {
                $product->stock_quantity += $request->quantity;
                $movementType = 'purchase';
            } elseif ($request->type === 'out') {
                if ($product->stock_quantity < $request->quantity) {
                    return response()->json([
                        'error' => 'Insufficient stock',
                        'available' => $product->stock_quantity,
                        'requested' => $request->quantity
                    ], 400);
                }
                $product->stock_quantity -= $request->quantity;
                $movementType = 'manual_adjust';
            } else {
                $product->stock_quantity = $request->quantity;
                $movementType = 'manual_adjust';
            }

            $product->save();

            InventoryMovement::create([
                'product_id' => $product->id,
                'type' => $movementType,
                'quantity' => $request->quantity,
                'previous_stock' => $previousStock,
                'new_stock' => $product->stock_quantity,
                'reference' => $request->reference ?? 'Manual Adjustment',
                'notes' => $request->notes,
                'moved_at' => now()
            ]);

            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Stock adjusted successfully',
                'product' => $product->load(['category', 'brand']),
                'previous_stock' => $previousStock,
                'new_stock' => $product->stock_quantity
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Stock adjustment failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to adjust stock',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
