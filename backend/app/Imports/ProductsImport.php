<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class ProductsImport implements 
    ToModel, 
    WithHeadingRow, 
    WithValidation, 
    SkipsOnError,
    SkipsOnFailure,
    WithBatchInserts,
    WithChunkReading
{
    protected $errors = [];
    protected $imported = 0;
    protected $skipped = 0;

    /**
     * Transform each row into a Product model
     */
    public function model(array $row)
    {
        try {
            // Auto-calculate prices if not provided
            $price_جملة = $row['price_جملة'] ?? 0;
            $price_قطاعي = $row['price_قطاعي'] ?? 0;
            $price_صفحة = $row['price_صفحة'] ?? 0;
            
            // If جملة is provided but others aren't, calculate them
            if ($price_جملة > 0) {
                if ($price_قطاعي == 0) {
                    $price_قطاعي = round($price_جملة * 1.15, 2);
                }
                if ($price_صفحة == 0) {
                    $price_صفحة = round($price_جملة * 1.25, 2);
                }
            }
            
            // Create or update product
            $product = Product::updateOrCreate(
                ['name_ar' => trim($row['name_ar'])], // Match by Arabic name
                [
                    'name' => $row['name'] ?? $row['name_ar'],
                    'name_ar' => trim($row['name_ar']),
                    'sku' => $row['sku'] ?? null,
                    'production_cost' => $row['production_cost'] ?? 0,
                    'price_جملة' => $price_جملة,
                    'price_قطاعي' => $price_قطاعي,
                    'price_صفحة' => $price_صفحة,
                    'volume_ml' => $row['volume_ml'] ?? '',
                    'quantity' => $row['quantity'] ?? 0,
                    'alert_quantity' => $row['alert_quantity'] ?? 10,
                    'is_active' => isset($row['is_active']) ? ($row['is_active'] == 1 || $row['is_active'] == 'active') : true,
                ]
            );

            $this->imported++;
            return $product;

        } catch (\Exception $e) {
            $this->skipped++;
            $this->errors[] = [
                'row' => $row,
                'error' => $e->getMessage()
            ];
            Log::error('Product import error: ' . $e->getMessage(), ['row' => $row]);
            return null;
        }
    }

    /**
     * Validation rules for each row
     */
    public function rules(): array
    {
        return [
            'name_ar' => 'required|string|max:255',
            'price_جملة' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
        ];
    }

    /**
     * Custom validation messages
     */
    public function customValidationMessages()
    {
        return [
            'name_ar.required' => 'اسم المنتج بالعربية مطلوب',
            'price_جملة.required' => 'سعر الجملة مطلوب',
            'price_جملة.numeric' => 'سعر الجملة يجب أن يكون رقم',
            'quantity.required' => 'الكمية مطلوبة',
            'quantity.integer' => 'الكمية يجب أن تكون رقم صحيح',
        ];
    }

    /**
     * Handle errors
     */
    public function onError(\Throwable $e)
    {
        $this->errors[] = $e->getMessage();
    }

    /**
     * Handle validation failures
     */
    public function onFailure(\Maatwebsite\Excel\Validators\Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $this->errors[] = [
                'row' => $failure->row(),
                'attribute' => $failure->attribute(),
                'errors' => $failure->errors(),
                'values' => $failure->values()
            ];
        }
    }

    /**
     * Batch insert size
     */
    public function batchSize(): int
    {
        return 100;
    }

    /**
     * Chunk size for reading
     */
    public function chunkSize(): int
    {
        return 100;
    }

    /**
     * Get import statistics
     */
    public function getStats()
    {
        return [
            'imported' => $this->imported,
            'skipped' => $this->skipped,
            'errors' => $this->errors
        ];
    }
}
