<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = User::select(['id', 'name', 'email', 'role', 'permissions', 'is_active', 'created_at'])
            ->where('role', '!=', 'admin');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $employees = $query->orderBy('created_at', 'desc')->get();
        
        return response()->json($employees);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|in:cashier,manager,inventory',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $employee = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'is_active' => $request->is_active ?? true
        ]);

        return response()->json($employee, 201);
    }

    public function show($id)
    {
        $employee = User::findOrFail($id);
        return response()->json($employee);
    }

    public function update(Request $request, $id)
    {
        $employee = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'email' => 'email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:6',
            'role' => 'in:cashier,manager,inventory',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->only(['name', 'email', 'role', 'is_active']);
        
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $employee->update($data);
        
        return response()->json($employee);
    }

    public function destroy($id)
    {
        $employee = User::findOrFail($id);
        
        if ($employee->role === 'admin') {
            return response()->json(['error' => 'Cannot delete admin user'], 403);
        }
        
        $employee->delete();
        
        return response()->json(['message' => 'Employee deleted successfully']);
    }

    public function updatePermissions(Request $request, $id)
    {
        $employee = User::findOrFail($id);
        
        if ($employee->role === 'admin') {
            return response()->json(['error' => 'Cannot modify admin permissions'], 403);
        }

        $validator = Validator::make($request->all(), [
            'permissions' => 'array',
            'permissions.*' => 'string|in:dashboard,clients,employees,roles,pos,invoices,sales-analysis,expenses,stock,inventory'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'message' => 'Validation failed',
                'received' => $request->all()
            ], 422);
        }

        $employee->update([
            'permissions' => $request->permissions
        ]);
        
        return response()->json([
            'message' => 'Permissions updated successfully',
            'employee' => $employee
        ]);
    }
}
