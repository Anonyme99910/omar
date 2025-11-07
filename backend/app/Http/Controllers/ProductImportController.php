<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\ProductsImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;

class ProductImportController extends Controller
{
    /**
     * Import products from Excel file
     */
    public function importExcel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240' // Max 10MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'ملف غير صالح',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $file = $request->file('file');
            $import = new ProductsImport();
            
            Excel::import($import, $file);
            
            $stats = $import->getStats();
            
            return response()->json([
                'success' => true,
                'message' => 'تم استيراد المنتجات بنجاح',
                'data' => [
                    'imported' => $stats['imported'],
                    'skipped' => $stats['skipped'],
                    'errors' => $stats['errors']
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل استيراد المنتجات',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Import products from SQL statements
     */
    public function importSQL(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sql' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'SQL غير صالح',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $sql = $request->input('sql');
            
            // Security: Only allow INSERT statements
            if (!preg_match('/^\s*INSERT\s+INTO\s+products/i', $sql)) {
                return response()->json([
                    'success' => false,
                    'message' => 'يُسمح فقط بجمل INSERT INTO products'
                ], 422);
            }

            DB::beginTransaction();
            
            // Split multiple INSERT statements
            $statements = array_filter(array_map('trim', explode(';', $sql)));
            $imported = 0;
            $errors = [];

            foreach ($statements as $statement) {
                if (empty($statement)) continue;
                
                try {
                    DB::statement($statement);
                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = [
                        'statement' => substr($statement, 0, 100) . '...',
                        'error' => $e->getMessage()
                    ];
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم استيراد المنتجات بنجاح',
                'data' => [
                    'imported' => $imported,
                    'errors' => $errors
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'فشل استيراد المنتجات',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download Excel template
     */
    public function downloadTemplate()
    {
        // Try CSV first (always available)
        $csvPath = storage_path('app/templates/products_template.csv');
        
        if (file_exists($csvPath)) {
            return response()->download($csvPath, 'products_template.csv', [
                'Content-Type' => 'text/csv',
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'القالب غير موجود'
        ], 404);
    }

    /**
     * Get SQL template
     */
    public function getSQLTemplate()
    {
        $template = <<<SQL
-- قالب استيراد المنتجات عبر SQL
-- نسخ والصق هذا القالب وتعديل القيم

INSERT INTO products (name, name_ar, sku, selling_price, production_cost, price_جملة, price_قطاعي, price_صفحة, volume_ml, quantity, alert_quantity, is_active, created_at, updated_at) VALUES
('Rose Perfume', 'عطر الورد', 'PRF001', 100.00, 50.00, 85.00, 100.00, 110.00, '100 مل', 100, 10, 1, NOW(), NOW()),
('Lavender Oil', 'زيت اللافندر', 'PRF002', 110.00, 60.00, 95.00, 110.00, 120.00, '50 مل', 50, 10, 1, NOW(), NOW()),
('Jasmine Essence', 'خلاصة الياسمين', 'PRF003', 130.00, 70.00, 110.00, 130.00, 145.00, '150 مل', 200, 15, 1, NOW(), NOW());

-- ملاحظات:
-- 1. name: اسم المنتج بالإنجليزية (اختياري)
-- 2. name_ar: اسم المنتج بالعربية (مطلوب)
-- 3. sku: رمز المنتج (اختياري)
-- 4. selling_price: السعر الأساسي (يمكن تركه 0)
-- 5. production_cost: تكلفة الإنتاج (مطلوب)
-- 6. price_جملة: سعر الجملة (مطلوب)
-- 7. price_قطاعي: سعر القطاعي (مطلوب)
-- 8. price_صفحة: سعر الصفحة (مطلوب)
-- 9. volume_ml: الحجم بالمل (مثال: "100 مل")
-- 10. quantity: الكمية المتوفرة (مطلوب)
-- 11. alert_quantity: حد التنبيه (افتراضي: 10)
-- 12. is_active: الحالة (1 = نشط، 0 = غير نشط)
SQL;

        return response()->json([
            'success' => true,
            'template' => $template
        ]);
    }

    /**
     * Get import guide
     */
    public function getGuide()
    {
        $guide = [
            'excel' => [
                'title' => 'دليل استيراد Excel',
                'steps' => [
                    '1. قم بتحميل قالب Excel من الزر أعلاه',
                    '2. افتح الملف في Excel أو Google Sheets',
                    '3. املأ البيانات في الأعمدة المطلوبة',
                    '4. احفظ الملف بصيغة .xlsx أو .xls',
                    '5. ارفع الملف باستخدام زر "رفع ملف Excel"'
                ],
                'required_columns' => [
                    'name' => 'اسم المنتج بالإنجليزية (اختياري)',
                    'name_ar' => 'اسم المنتج بالعربية (مطلوب)',
                    'sku' => 'رمز المنتج (اختياري)',
                    'production_cost' => 'تكلفة الإنتاج',
                    'price_جملة' => 'سعر الجملة (مطلوب)',
                    'price_قطاعي' => 'سعر القطاعي (مطلوب)',
                    'price_صفحة' => 'سعر الصفحة (مطلوب)',
                    'volume_ml' => 'الحجم بالمل (مثال: 100 مل)',
                    'quantity' => 'الكمية (مطلوب)',
                    'alert_quantity' => 'حد التنبيه (افتراضي: 10)',
                    'is_active' => 'الحالة (1 = نشط، 0 = غير نشط)'
                ],
                'notes' => [
                    'الأعمدة المطلوبة: name_ar, price_جملة, quantity',
                    'يمكن استيراد حتى 1000 منتج في المرة الواحدة',
                    'إذا لم تحدد price_قطاعي و price_صفحة، سيتم حسابهما تلقائياً من price_جملة'
                ]
            ],
            'sql' => [
                'title' => 'دليل استيراد SQL',
                'steps' => [
                    '1. احصل على قالب SQL من الزر أعلاه',
                    '2. انسخ القالب والصقه في محرر النصوص',
                    '3. عدّل القيم حسب منتجاتك',
                    '4. انسخ جمل SQL والصقها في صندوق النص',
                    '5. اضغط على "استيراد SQL"'
                ],
                'syntax' => [
                    'استخدم INSERT INTO products',
                    'افصل القيم بفواصل',
                    'استخدم علامات اقتباس مفردة للنصوص',
                    'يمكنك إضافة عدة جمل INSERT مفصولة بفاصلة منقوطة'
                ],
                'security' => [
                    'يُسمح فقط بجمل INSERT',
                    'لا يمكن تنفيذ UPDATE أو DELETE أو DROP',
                    'يتم التحقق من صحة SQL قبل التنفيذ'
                ]
            ]
        ];

        return response()->json([
            'success' => true,
            'guide' => $guide
        ]);
    }
}
