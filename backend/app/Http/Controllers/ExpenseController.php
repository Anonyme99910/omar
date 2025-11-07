<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Expense::with('expenseType');

            // Filter by date range
            if ($request->has('start_date')) {
                $query->where('expense_date', '>=', $request->start_date);
            }
            if ($request->has('end_date')) {
                $query->where('expense_date', '<=', $request->end_date);
            }

            // Filter by type
            if ($request->has('expense_type_id')) {
                $query->where('expense_type_id', $request->expense_type_id);
            }

            // Filter by recurrence
            if ($request->has('recurrence_type')) {
                $query->where('recurrence_type', $request->recurrence_type);
            }

            $expenses = $query->orderBy('expense_date', 'desc')->get();

            return response()->json([
                'success' => true,
                'data' => $expenses
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'فشل تحميل المصروفات'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'expense_type_id' => 'required|exists:expense_types,id',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'recurrence_type' => 'required|in:once,monthly,yearly',
            'remarks' => 'nullable|string',
            'is_fixed' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $expense = Expense::create($request->all());
            $expense->load('expenseType');
            
            return response()->json([
                'success' => true,
                'message' => 'تم إضافة المصروف بنجاح',
                'data' => $expense
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'فشل إضافة المصروف'
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'expense_type_id' => 'required|exists:expense_types,id',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'recurrence_type' => 'required|in:once,monthly,yearly',
            'remarks' => 'nullable|string',
            'is_fixed' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $expense = Expense::findOrFail($id);
            $expense->update($request->all());
            $expense->load('expenseType');
            
            return response()->json([
                'success' => true,
                'message' => 'تم تحديث المصروف بنجاح',
                'data' => $expense
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'فشل تحديث المصروف'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $expense = Expense::findOrFail($id);
            $expense->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'تم حذف المصروف بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'فشل حذف المصروف'
            ], 500);
        }
    }

    public function statistics(Request $request)
    {
        try {
            $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
            $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));

            // Total expenses
            $totalExpenses = Expense::whereBetween('expense_date', [$startDate, $endDate])
                ->sum('amount');

            // Monthly expenses
            $monthlyExpenses = Expense::whereBetween('expense_date', [$startDate, $endDate])
                ->where('recurrence_type', 'monthly')
                ->sum('amount');

            // Yearly expenses
            $yearlyExpenses = Expense::whereBetween('expense_date', [$startDate, $endDate])
                ->where('recurrence_type', 'yearly')
                ->sum('amount');

            // Fixed expenses
            $fixedExpenses = Expense::whereBetween('expense_date', [$startDate, $endDate])
                ->where('is_fixed', true)
                ->sum('amount');

            // Expenses by type
            $expensesByType = Expense::select('expense_type_id', DB::raw('SUM(amount) as total'))
                ->with('expenseType:id,name_ar')
                ->whereBetween('expense_date', [$startDate, $endDate])
                ->groupBy('expense_type_id')
                ->get();

            // Monthly trend (last 6 months)
            $monthlyTrend = Expense::select(
                    DB::raw('DATE_FORMAT(expense_date, "%Y-%m") as month'),
                    DB::raw('SUM(amount) as total')
                )
                ->where('expense_date', '>=', now()->subMonths(6)->startOfMonth())
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'total_expenses' => $totalExpenses,
                    'monthly_expenses' => $monthlyExpenses,
                    'yearly_expenses' => $yearlyExpenses,
                    'fixed_expenses' => $fixedExpenses,
                    'expenses_by_type' => $expensesByType,
                    'monthly_trend' => $monthlyTrend,
                    'period' => [
                        'start_date' => $startDate,
                        'end_date' => $endDate
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'فشل تحميل الإحصائيات'
            ], 500);
        }
    }
}
