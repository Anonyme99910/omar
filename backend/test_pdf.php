<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing PDF Generation for Sale ID 41...\n\n";

try {
    $sale = App\Models\Sale::with(['customer', 'items.product'])->find(41);
    
    if (!$sale) {
        echo "❌ Sale ID 41 not found!\n";
        
        // Show available sales
        $sales = App\Models\Sale::orderBy('id', 'desc')->take(5)->get(['id', 'invoice_number']);
        echo "\nAvailable sales:\n";
        foreach ($sales as $s) {
            echo "  - ID: {$s->id}, Invoice: {$s->invoice_number}\n";
        }
        exit;
    }
    
    echo "✅ Sale found: {$sale->invoice_number}\n";
    echo "   Customer: " . ($sale->customer ? $sale->customer->name : 'None') . "\n";
    echo "   Items: " . $sale->items->count() . "\n";
    echo "   Total: {$sale->total}\n\n";
    
    // Test view rendering
    echo "Testing view rendering...\n";
    $html = view('invoice-pdf-tcpdf', [
        'sale' => $sale,
        'fontSize' => 11,
        'spacing' => 'normal',
        'itemCount' => $sale->items->count()
    ])->render();
    
    echo "✅ View rendered successfully (" . strlen($html) . " bytes)\n\n";
    
    // Test TCPDF
    echo "Testing TCPDF...\n";
    $pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8');
    $pdf->SetCreator('Test');
    $pdf->SetMargins(10, 10, 10);
    $pdf->SetAutoPageBreak(false);
    $pdf->SetFont('aealarabiya', '', 11);
    $pdf->AddPage();
    $pdf->setRTL(true);
    $pdf->writeHTML($html, true, false, true, false, '');
    $content = $pdf->Output('', 'S');
    
    echo "✅ PDF generated successfully (" . strlen($content) . " bytes)\n";
    echo "\n✅ All tests passed! PDF generation works.\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
}
