<?php

namespace App\Http\Controllers\SalesManager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductType;
use App\Models\Project;

class SalesManagerController extends Controller
{
    public function dashboard()
    {

        $page_title = "";
        $product_type = ProductType::get();

        return view('expected_orders.index', compact('page_title', 'product_type'));
    }
}
