<?php

namespace App\Http\Controllers\Setting;  

use App\Models\StockHistory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StockHistoryController extends Controller
{
    public function index(){

        $StockHistorys = \DB::table('stock_history')
				    ->join('projects', 'stock_history.project_id', '=', 'projects.id')
				    ->join('products_of_projects', 'stock_history.product_id', '=', 'products_of_projects.id')
				    ->select(
				        'stock_history.*',
				        'projects.project_name',
				        'products_of_projects.cart_model_name',
				        'products_of_projects.description',
				    )
    			->get();

        return view('settings.stock_history', compact('StockHistorys'));
    }
	
}
