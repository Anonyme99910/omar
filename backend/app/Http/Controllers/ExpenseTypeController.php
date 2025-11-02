<?php

namespace App\Http\Controllers;

use App\Models\ExpenseType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExpenseTypeController extends Controller
{
    public function index()
    {
        try {
            $types = ExpenseType::orderBy('name_ar')->get();
            return response()->json([
                'success' => true,
                'data' => $types
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'فشل تحميل أنواع المصروفات'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name_ar' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $type = ExpenseType::create($request->all());
            return response()->json([
                'success' => true,
                'message' => 'تم إضافة نوع المصروف بنجاح',
                'data' => $type
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'فشل إضافة نوع المصروف'
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name_ar' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $type = ExpenseType::findOrFail($id);
            $type->update($request->all());
            return response()->json([
                'success' => true,
                'message' => 'تم تحديث نوع المصروف بنجاح',
                'data' => $type
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'فشل تحديث نوع المصروف'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $type = ExpenseType::findOrFail($id);
            $type->delete();
            return response()->json([
                'success' => true,
                'message' => 'تم حذف نوع المصروف بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'فشل حذف نوع المصروف'
            ], 500);
        }
    }
}
