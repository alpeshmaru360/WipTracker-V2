<?php

// In any controller
use App\Services\DashboardService;

class DashboardController extends Controller
{
    public function __invoke(DashboardService $service)
    {
        $data = $service->getDashboardData(auth()->user()->role);     

        return view('production_superwisor.dashboard', $data);
    }
}
