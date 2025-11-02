<?php

namespace App\Http\Controllers;

use App\Models\DamagedProduct;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DamagedProductController extends Controller
{
    public function index()
    {
        $damagedProducts = DamagedProduct::with(['product', 'creator'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'sku' => $item->product->sku ?? '-',
                    'product_name' => $item->product->name_ar,
                    'category_name' => '-',
                    'damaged_quantity' => $item->quantity,
                    'reorder_level' => $item->product->alert_quantity,
                    'available_stock' => $item->product->quantity,
                    'status' => $item->product->quantity > $item->product->alert_quantity ? 'متوفر' : 'منخفض',
                    'cost_price' => $item->cost_price,
                    'total_loss' => $item->total_loss,
                    'damage_type' => $item->damage_type,
                    'notes' => $item->notes,
                    'created_at' => $item->created_at->toISOString(),
                    'created_by' => $item->creator->name ?? 'System'
                ];
            });

        return response()->json($damagedProducts);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'damage_type' => 'required|in:expired,broken,defective,water_damage,other',
            'notes' => 'nullable|string|max:500'
        ]);

        $product = Product::findOrFail($validated['product_id']);

        // Check stock availability
        if ($product->quantity < $validated['quantity']) {
            return response()->json([
                'error' => 'الكمية المطلوبة أكبر من المخزون المتاح',
                'available' => $product->quantity
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Calculate total loss (using wholesale price as cost)
            $costPrice = $product->price_جملة ?? 0;
            $totalLoss = $costPrice * $validated['quantity'];

            // Create damaged product record
            $damagedProduct = DamagedProduct::create([
                'product_id' => $validated['product_id'],
                'quantity' => $validated['quantity'],
                'cost_price' => $costPrice,
                'total_loss' => $totalLoss,
                'damage_type' => $validated['damage_type'],
                'notes' => $validated['notes'] ?? '',
                'created_by' => auth()->id()
            ]);

            // Deduct from inventory
            $previousStock = $product->quantity;
            $product->quantity -= $validated['quantity'];
            $product->save();

            // Create inventory movement
            \App\Models\InventoryMovement::create([
                'product_id' => $product->id,
                'type' => 'manual_adjust',
                'quantity' => -$validated['quantity'],
                'previous_stock' => $previousStock,
                'new_stock' => $product->quantity,
                'reference' => 'Damaged Product #' . $damagedProduct->id,
                'notes' => "تلف: {$validated['damage_type']} - {$validated['notes']}",
                'moved_at' => now(),
                'created_by' => auth()->id()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم تسجيل المنتج التالف بنجاح',
                'damaged_product' => $damagedProduct->load('product'),
                'new_stock' => $product->quantity
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to create damaged product: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'فشل تسجيل المنتج التالف',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function stats()
    {
        $stats = DB::table('damaged_products')
            ->select(
                DB::raw('COUNT(*) as total_damaged'),
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('SUM(total_loss) as total_loss')
            )
            ->first();

        return response()->json([
            'total_damaged' => $stats->total_damaged ?? 0,
            'total_quantity' => $stats->total_quantity ?? 0,
            'total_loss' => $stats->total_loss ?? 0
        ]);
    }

    public function destroy($id)
    {
        $damagedProduct = DamagedProduct::with('product')->findOrFail($id);
        
        DB::beginTransaction();
        try {
            // Restore quantity back to inventory
            $product = $damagedProduct->product;
            $previousStock = $product->quantity;
            $product->quantity += $damagedProduct->quantity;
            $product->save();
            
            // Create inventory movement for restoration
            \App\Models\InventoryMovement::create([
                'product_id' => $product->id,
                'type' => 'manual_adjust',
                'quantity' => $damagedProduct->quantity,
                'previous_stock' => $previousStock,
                'new_stock' => $product->quantity,
                'reference' => 'Damaged Product Restored #' . $damagedProduct->id,
                'notes' => 'استعادة من المنتجات التالفة - تم الحذف',
                'moved_at' => now(),
                'created_by' => auth()->id()
            ]);
            
            // Delete the damaged product record
            $damagedProduct->delete();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'تم حذف السجل واستعادة الكمية للمخزون',
                'restored_quantity' => $damagedProduct->quantity,
                'new_stock' => $product->quantity
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to restore damaged product: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'فشل حذف السجل',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
