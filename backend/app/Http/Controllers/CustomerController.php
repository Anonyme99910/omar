<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::select(['id', 'name', 'phone', 'address', 'segment', 'total_purchases', 'total_orders', 'created_at']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $perPage = $request->get('per_page', 50);
        $customers = $query->orderBy('total_purchases', 'desc')->paginate($perPage);
        return response()->json($customers);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'segment' => 'nullable|in:جملة,قطاعي,صفحة'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $customer = Customer::create($request->only(['name','phone','address','segment']));
        return response()->json($customer, 201);
    }

    public function show($id)
    {
        $customer = Customer::with(['sales' => function($query) {
            $query->select(['id', 'customer_id', 'invoice_number', 'total', 'payment_method', 'status', 'created_at'])
                  ->orderBy('created_at', 'desc')
                  ->limit(50);
        }])->findOrFail($id);
        return response()->json($customer);
    }

    public function history($id)
    {
        $customer = Customer::findOrFail($id);
        $sales = $customer->sales()
            ->with(['items.product:id,name,name_ar'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return response()->json([
            'customer' => $customer,
            'sales' => $sales
        ]);
    }

    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'phone' => 'string|max:20',
            'address' => 'nullable|string',
            'segment' => 'nullable|in:جملة,قطاعي,صفحة'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Only update fields that are present in request
        $data = $request->only(['name','phone','address','segment']);
        
        // Remove segment if it's null or empty to prevent setting NULL
        if (empty($data['segment'])) {
            unset($data['segment']);
        }
        
        $customer->update($data);
        return response()->json($customer);
    }

    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();
        return response()->json(['message' => 'Customer deleted successfully']);
    }
}
