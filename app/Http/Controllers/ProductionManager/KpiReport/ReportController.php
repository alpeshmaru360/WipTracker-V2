<?php

namespace App\Http\Controllers\ProductionManager\KpiReport;

use App\Http\Controllers\Controller; 

class ReportController  extends Controller
{
    public function index()
    {
        return view('layouts.kpireports');
    }
}