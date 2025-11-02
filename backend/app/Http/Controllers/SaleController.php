<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\Customer;
use App\Models\InventoryMovement;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Elibyy\TCPDF\Facades\TCPDF;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $query = Sale::with(['customer', 'items.product', 'payments']);

        // Filter by status with scopes
        if ($request->has('status')) {
            if ($request->status === 'unpaid') {
                $query->unpaid();
            } elseif ($request->status === 'partially_paid') {
                $query->partiallyPaid();
            } elseif ($request->status === 'paid') {
                $query->paid();
            } elseif ($request->status === 'void') {
                $query->void();
            } else {
                $query->where('status', $request->status);
            }
        }

        // Filter by date range (use issue_date if available, fallback to created_at)
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereRaw('DATE(COALESCE(issue_date, created_at)) BETWEEN ? AND ?', [
                $request->start_date, 
                $request->end_date
            ]);
        }

        if ($request->has('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        $sales = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // Get status counts for tabs
        $counts = [
            'all' => Sale::count(),
            'unpaid' => Sale::unpaid()->count(),
            'partially_paid' => Sale::partiallyPaid()->count(),
            'paid' => Sale::paid()->count(),
            'void' => Sale::void()->count()
        ];
        
        return response()->json([
            'sales' => $sales,
            'counts' => $counts
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'nullable|exists:customers,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'payment_method' => 'required|in:cash,card,transfer',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            // Calculate totals
            $subtotal = 0;
            foreach ($request->items as $item) {
                $subtotal += $item['quantity'] * $item['unit_price'];
            }

            $tax = $request->tax ?? 0;
            $discount = $request->discount ?? 0;
            $total = $subtotal + $tax - $discount;

            // Determine payment status
            $paidAmount = $request->paid_amount ?? $total;
            $balanceDue = $total - $paidAmount;
            
            // Set status based on payment
            if ($paidAmount <= 0) {
                $status = 'issued'; // Unpaid
            } elseif ($paidAmount >= $total) {
                $status = 'paid'; // Fully paid
            } else {
                $status = 'partially_paid'; // Partially paid
            }

            // Create sale
            $sale = Sale::create([
                'customer_id' => $request->customer_id,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'discount' => $discount,
                'shipping' => 0,
                'total' => $total,
                'paid_sum' => $paidAmount,
                'balance_due' => $balanceDue,
                'payment_method' => $request->payment_method,
                'status' => $status,
                'issue_date' => now()->toDateString(),
                'notes' => $request->notes,
                'created_by' => auth()->id()
            ]);

            // Create sale items and update inventory
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);

                // Check stock
                if ($product->quantity < $item['quantity']) {
                    throw new \Exception("Insufficient stock for product: {$product->name}");
                }

                // Create sale item
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['quantity'] * $item['unit_price']
                ]);

                // Update product stock
                $previousStock = $product->quantity;
                $product->quantity -= $item['quantity'];
                $product->save();

                // Create inventory movement
                InventoryMovement::create([
                    'product_id' => $product->id,
                    'type' => 'sale',
                    'quantity' => -$item['quantity'],
                    'previous_stock' => $previousStock,
                    'new_stock' => $product->quantity,
                    'related_type' => 'sale',
                    'related_id' => $sale->id,
                    'reference' => $sale->invoice_number,
                    'notes' => 'Sale transaction',
                    'moved_at' => now(),
                    'created_by' => auth()->id()
                ]);
            }

            // Create payment record if amount was paid
            if ($paidAmount > 0) {
                Payment::create([
                    'sale_id' => $sale->id,
                    'method' => $request->payment_method,
                    'amount' => $paidAmount,
                    'paid_at' => now(),
                    'notes' => 'Initial payment from POS',
                    'created_by' => auth()->id()
                ]);
            }

            // Update customer statistics
            if ($request->customer_id) {
                $customer = Customer::find($request->customer_id);
                $customer->total_purchases += $total;
                $customer->total_orders += 1;
                $customer->save();
            }

            DB::commit();
            return response()->json($sale->load(['customer', 'items.product']), 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $sale = Sale::with(['customer', 'items.product'])->findOrFail($id);
        return response()->json($sale);
    }

    public function cancel($id)
    {
        $sale = Sale::findOrFail($id);

        if ($sale->status === 'cancelled') {
            return response()->json(['error' => 'Sale already cancelled'], 400);
        }

        DB::beginTransaction();
        try {
            // Restore inventory
            foreach ($sale->items as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $previousStock = $product->quantity;
                    $product->quantity += $item->quantity;
                    $product->save();

                    InventoryMovement::create([
                        'product_id' => $product->id,
                        'type' => 'return',
                        'quantity' => $item->quantity,
                        'previous_stock' => $previousStock,
                        'new_stock' => $product->quantity,
                        'reference' => $sale->invoice_number . ' (Cancelled)',
                        'notes' => 'Sale cancellation',
                        'moved_at' => now()
                    ]);
                }
            }

            // Update customer statistics
            if ($sale->customer_id) {
                $customer = Customer::find($sale->customer_id);
                $customer->total_purchases -= $sale->total;
                $customer->total_orders -= 1;
                $customer->save();
            }

            $sale->status = 'cancelled';
            $sale->save();

            DB::commit();
            return response()->json($sale->load(['customer', 'items.product']));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to cancel sale'], 500);
        }
    }

    public function todaySales()
    {
        $sales = Sale::with(['customer', 'items.product'])
            ->whereDate('created_at', today())
            ->where('status', 'completed')
            ->get();

        $total = $sales->sum('total');
        $count = $sales->count();

        return response()->json([
            'sales' => $sales,
            'total' => $total,
            'count' => $count
        ]);
    }

    public function downloadPdf($id)
    {
        try {
            $sale = Sale::with(['customer', 'items.product'])->findOrFail($id);
            
            // Generate PDF
            $pdfContent = $this->generatePdfContent($sale);
            
            // Save to storage
            $fileName = 'invoice-' . $sale->invoice_number . '.pdf';
            $path = 'invoices/' . $fileName;
            \Storage::disk('public')->put($path, $pdfContent);
            
            // Update sale with PDF path
            $sale->update(['pdf_path' => $path]);
            
            // Return PDF for download
            return response($pdfContent)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
        } catch (\Exception $e) {
            \Log::error('PDF Generation Error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'error' => 'Failed to generate PDF',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    private function generatePdfContent($sale)
    {
        // Calculate dynamic sizing based on number of items
        $itemCount = $sale->items->count();
        
        // Determine font size and spacing based on item count
        if ($itemCount <= 5) {
            $fontSize = 11;
            $spacing = 'normal';
        } elseif ($itemCount <= 10) {
            $fontSize = 10;
            $spacing = 'compact';
        } elseif ($itemCount <= 15) {
            $fontSize = 9;
            $spacing = 'compact';
        } elseif ($itemCount <= 20) {
            $fontSize = 8;
            $spacing = 'tight';
        } else {
            $fontSize = 7;
            $spacing = 'tight';
        }
        
        $html = view('invoice-pdf-tcpdf', [
            'sale' => $sale,
            'fontSize' => $fontSize,
            'spacing' => $spacing,
            'itemCount' => $itemCount
        ])->render();
        
        $pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8');
        
        // Set document information
        $pdf->SetCreator('Perfume Store');
        $pdf->SetAuthor('Perfume Store');
        $pdf->SetTitle('Invoice ' . $sale->invoice_number);
        
        // Tighter margins for more content
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(false);
        
        // Set font
        $pdf->SetFont('aealarabiya', '', $fontSize);
        
        // Add a page
        $pdf->AddPage();
        
        // Set RTL
        $pdf->setRTL(true);
        
        // Write HTML
        $pdf->writeHTML($html, true, false, true, false, '');
        
        // Return PDF content
        return $pdf->Output('', 'S');
    }
    
    public function getWhatsAppMessage($id)
    {
        $sale = Sale::with(['customer', 'items.product'])->findOrFail($id);
        
        // Generate PDF if not exists
        if (!$sale->pdf_path || !\Storage::disk('public')->exists($sale->pdf_path)) {
            $pdfContent = $this->generatePdfContent($sale);
            $fileName = 'invoice-' . $sale->invoice_number . '.pdf';
            $path = 'invoices/' . $fileName;
            \Storage::disk('public')->put($path, $pdfContent);
            $sale->update(['pdf_path' => $path]);
        }
        
        // Create short, clean public URL for PDF
        $pdfUrl = url('api/invoice/' . $sale->invoice_number);

        // Generate minimal WhatsApp message including the URL (no emojis, very simple)
        $message = $this->generateWhatsAppMessage($sale, $pdfUrl);
        
        return response()->json([
            'message' => $message,
            'phone' => $sale->customer->phone ?? null
        ]);
    }
    
    public function publicPdfDownload($invoiceNumber)
    {
        // Find sale by invoice number
        $sale = Sale::where('invoice_number', $invoiceNumber)
            ->with(['customer', 'items.product'])
            ->firstOrFail();
        
        // Generate PDF if not exists
        if (!$sale->pdf_path || !\Storage::disk('public')->exists($sale->pdf_path)) {
            $pdfContent = $this->generatePdfContent($sale);
            $fileName = 'invoice-' . $sale->invoice_number . '.pdf';
            $path = 'invoices/' . $fileName;
            \Storage::disk('public')->put($path, $pdfContent);
            $sale->update(['pdf_path' => $path]);
        }
        
        // Return PDF file
        $filePath = storage_path('app/public/' . $sale->pdf_path);
        
        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($sale->pdf_path) . '"'
        ]);
    }
    
    public function void($id)
    {
        $sale = Sale::with('items.product')->findOrFail($id);
        
        if ($sale->status === 'void') {
            return response()->json(['error' => 'Invoice already voided'], 422);
        }
        
        DB::transaction(function () use ($sale) {
            // Update status
            $sale->status = 'void';
            $sale->save();
            
            // Release reserved stock
            foreach ($sale->items as $item) {
                $product = $item->product;
                // Reserved quantity feature removed
                if (true) {
                    
                    InventoryMovement::create([
                        'product_id' => $product->id,
                        'type' => 'release',
                        'quantity' => $item->quantity,
                        'previous_stock' => $product->quantity,
                        'new_stock' => $product->quantity,
                        'related_type' => 'sale',
                        'related_id' => $sale->id,
                        'notes' => 'Released due to invoice void',
                        'moved_at' => now(),
                        'created_by' => auth()->id()
                    ]);
                }
            }
        });
        
        return response()->json([
            'message' => 'Invoice voided successfully',
            'sale' => $sale->fresh()
        ]);
    }
    
    private function generateWhatsAppMessage($sale, string $pdfUrl)
    {
        // Minimal, robust Arabic text. No emojis, no special markdown.
        // Keep ASCII-only punctuation to avoid any client encoding quirks.
        $lines = [];
        $lines[] = 'شكراً لشرائك من متجر العطور';
        $lines[] = 'رقم الفاتورة: ' . $sale->invoice_number;
        $lines[] = '';
        $lines[] = 'رابط تحميل الفاتورة (PDF):';
        $lines[] = $pdfUrl;
        return implode("\n", $lines);
    }
}
