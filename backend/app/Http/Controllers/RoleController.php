<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = [
            ['id' => 1, 'name' => 'admin', 'name_ar' => 'مدير', 'permissions' => ['all']],
            ['id' => 2, 'name' => 'cashier', 'name_ar' => 'كاشير', 'permissions' => ['sales', 'customers']],
            ['id' => 3, 'name' => 'manager', 'name_ar' => 'مدير فرع', 'permissions' => ['sales', 'customers', 'reports']],
            ['id' => 4, 'name' => 'inventory', 'name_ar' => 'مخزن', 'permissions' => ['products', 'inventory']],
        ];
        
        return response()->json($roles);
    }
}
