<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Customer;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function dashboard()
    {
        // Get current date in configured timezone
        $today = now()->startOfDay();
        $tomorrow = now()->addDay()->startOfDay();
        $thisMonth = now()->startOfMonth();
        $nextMonth = now()->addMonth()->startOfMonth();

        // Today's statistics (exclude void sales)
        // Use >= today AND < tomorrow to ensure we only get today's sales
        $todaySales = Sale::where('created_at', '>=', $today)
            ->where('created_at', '<', $tomorrow)
            ->where('status', '!=', 'void')
            ->sum('total');

        $todayOrders = Sale::where('created_at', '>=', $today)
            ->where('created_at', '<', $tomorrow)
            ->where('status', '!=', 'void')
            ->count();

        // This month's statistics (exclude void sales)
        // Use >= start of month AND < start of next month
        $monthSales = Sale::where('created_at', '>=', $thisMonth)
            ->where('created_at', '<', $nextMonth)
            ->where('status', '!=', 'void')
            ->sum('total');

        $monthOrders = Sale::where('created_at', '>=', $thisMonth)
            ->where('created_at', '<', $nextMonth)
            ->where('status', '!=', 'void')
            ->count();

        // Inventory statistics
        $totalProducts = Product::count();
        $lowStockProducts = Product::whereColumn('quantity', '<=', 'alert_quantity')->count();
        $outOfStockProducts = Product::where('quantity', 0)->count();

        // Total inventory value (using retail price as base)
        $inventoryValue = Product::selectRaw('SUM(quantity * price_قطاعي) as total')
            ->first()
            ->total ?? 0;

        // Top selling products this month
        $topProducts = SaleItem::select('product_id', DB::raw('SUM(quantity) as total_sold'))
            ->whereHas('sale', function($q) use ($thisMonth, $nextMonth) {
                $q->where('created_at', '>=', $thisMonth)
                  ->where('created_at', '<', $nextMonth)
                  ->where('status', '!=', 'void');
            })
            ->groupBy('product_id')
            ->orderBy('total_sold', 'desc')
            ->limit(5)
            ->with('product')
            ->get();

        // Top customers
        $topCustomers = Customer::orderBy('total_purchases', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'today' => [
                'sales' => $todaySales,
                'orders' => $todayOrders
            ],
            'month' => [
                'sales' => $monthSales,
                'orders' => $monthOrders
            ],
            'inventory' => [
                'total_products' => $totalProducts,
                'low_stock' => $lowStockProducts,
                'out_of_stock' => $outOfStockProducts,
                'total_value' => $inventoryValue
            ],
            'top_products' => $topProducts,
            'top_customers' => $topCustomers
        ]);
    }

    public function salesReport(Request $request)
    {
        $startDate = $request->start_date ?? now()->startOfMonth();
        $endDate = $request->end_date ?? now();

        $sales = Sale::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', '!=', 'void')
            ->with(['customer', 'items.product'])
            ->get();

        $totalSales = $sales->sum('total');
        $totalOrders = $sales->count();
        $averageOrderValue = $totalOrders > 0 ? $totalSales / $totalOrders : 0;

        // Calculate profit
        $totalProfit = 0;
        foreach ($sales as $sale) {
            foreach ($sale->items as $item) {
                $profit = ($item->unit_price - $item->product->cost_price) * $item->quantity;
                $totalProfit += $profit;
            }
        }

        // Sales by payment method
        $salesByPayment = Sale::whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('status', ['paid', 'partially_paid'])
            ->select('payment_method', DB::raw('SUM(total) as total'))
            ->groupBy('payment_method')
            ->get();

        // Daily sales
        $dailySales = Sale::whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('status', ['paid', 'partially_paid'])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'summary' => [
                'total_sales' => $totalSales,
                'total_orders' => $totalOrders,
                'average_order_value' => $averageOrderValue,
                'total_profit' => $totalProfit,
                'profit_margin' => $totalSales > 0 ? ($totalProfit / $totalSales) * 100 : 0
            ],
            'sales_by_payment' => $salesByPayment,
            'daily_sales' => $dailySales,
            'sales' => $sales
        ]);
    }

    public function productReport(Request $request)
    {
        $startDate = $request->start_date ?? now()->startOfMonth();
        $endDate = $request->end_date ?? now();

        // Best selling products
        $bestSelling = SaleItem::select('product_id', 
            DB::raw('SUM(quantity) as total_sold'),
            DB::raw('SUM(total_price) as total_revenue'))
            ->whereHas('sale', function($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate])
                  ->whereIn('status', ['paid', 'partially_paid']);
            })
            ->groupBy('product_id')
            ->orderBy('total_sold', 'desc')
            ->with('product.category', 'product.brand')
            ->get();

        // Sales by category
        $salesByCategory = SaleItem::select('products.category_id',
            DB::raw('SUM(sale_items.quantity) as total_sold'),
            DB::raw('SUM(sale_items.total_price) as total_revenue'))
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->whereHas('sale', function($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate])
                  ->whereIn('status', ['paid', 'partially_paid']);
            })
            ->groupBy('products.category_id')
            ->with('product.category')
            ->get();

        // Sales by brand
        $salesByBrand = SaleItem::select('products.brand_id',
            DB::raw('SUM(sale_items.quantity) as total_sold'),
            DB::raw('SUM(sale_items.total_price) as total_revenue'))
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->whereHas('sale', function($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate])
                  ->whereIn('status', ['paid', 'partially_paid']);
            })
            ->groupBy('products.brand_id')
            ->with('product.brand')
            ->get();

        return response()->json([
            'best_selling' => $bestSelling,
            'sales_by_category' => $salesByCategory,
            'sales_by_brand' => $salesByBrand
        ]);
    }

    public function inventoryReport()
    {
        $products = Product::with(['category', 'brand'])
            ->select('*', DB::raw('stock_quantity * cost_price as stock_value'))
            ->get();

        $totalValue = $products->sum('stock_value');
        $totalProducts = $products->count();
        $lowStock = $products->filter(function($p) {
            return $p->stock_quantity <= $p->min_stock_level;
        })->count();

        return response()->json([
            'summary' => [
                'total_products' => $totalProducts,
                'total_value' => $totalValue,
                'low_stock_count' => $lowStock
            ],
            'products' => $products
        ]);
    }

    public function profitReport(Request $request)
    {
        $startDate = $request->start_date ?? now()->startOfMonth();
        $endDate = $request->end_date ?? now();

        $sales = Sale::whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('status', ['paid', 'partially_paid'])
            ->with('items.product')
            ->get();

        $totalRevenue = 0;
        $totalCost = 0;
        $totalProfit = 0;

        foreach ($sales as $sale) {
            foreach ($sale->items as $item) {
                $revenue = $item->total_price;
                $cost = $item->product->cost_price * $item->quantity;
                $profit = $revenue - $cost;

                $totalRevenue += $revenue;
                $totalCost += $cost;
                $totalProfit += $profit;
            }
        }

        $profitMargin = $totalRevenue > 0 ? ($totalProfit / $totalRevenue) * 100 : 0;

        return response()->json([
            'total_revenue' => $totalRevenue,
            'total_cost' => $totalCost,
            'total_profit' => $totalProfit,
            'profit_margin' => $profitMargin
        ]);
    }
}
