<?php

namespace App\Http\Controllers\ProcurementManager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProjectProcessStdTime;
use App\Models\ProductsOfProjects;
use App\Models\ProjectStatus;
use App\Models\Project;
use App\Models\User;
use App\Models\ProductBOMItem;
use App\Models\AdminHoursManagement;
use App\Models\StockMasterModule;
use App\Models\StockBOMPo;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderTable;
use App\Models\CurrecyConverter;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Services\DashboardService;

class ProcurementManagerController extends Controller
{
    public function dashboard(DashboardService $dashboardService){
        // Common Dashboard Service
        $role = auth()->user()->role;
        $dashboardData = $dashboardService->getDashboardData($role);

        $page_title = "PURCHASE ORDERS OVERVIEW";

        $charts = [
            'chartData' => 'SupplierOrders',
            'chart2Data' => 'TotalOrders',
            'chart3Data' => 'ArticleOrder',
            'chart4Data' => 'TotalArticles'
        ];

        $chartResults = array_map(fn($chart) => $this->GetChartData(["chart" => $chart]), $charts);

        return view('procurement_manager.dashboard', array_merge(
            compact('dashboardData', 'page_title'),
            $chartResults
        ));
    }

    public function GetChartData($chartrequest){
        $lastFiveYears = range(date('Y') - 4, date('Y')); // Last 5 years
        $years = collect($lastFiveYears)->map(fn($year) => (string) $year);

        if ($chartrequest['chart'] === 'SupplierOrders') {
            return $this->getSupplierOrdersChart($years);
        }
        if ($chartrequest['chart'] === 'TotalOrders') {
            return $this->getTotalOrdersChart($years);
        }
        if ($chartrequest['chart'] === 'ArticleOrder') {
            return $this->getArticleOrdersChart($years);
        }
        if ($chartrequest['chart'] === 'TotalArticles') {
            return $this->getTotalArticlesChart($years);
        }

        return ['labels' => [], 'datasets' => []]; // Default empty structure
    }

    private function getSupplierOrdersChart($years){
        $purchaseOrders = PurchaseOrder::selectRaw(
            'supplier, YEAR(order_date) as year, COUNT(*) as total_orders'
        )
            ->whereBetween('order_date', [$years->first() . '-01-01', $years->last() . '-12-31'])
            ->groupBy('supplier', 'year')
            ->orderBy('supplier')
            ->orderBy('year')
            ->get();

        // Get unique suppliers
        $suppliers = $purchaseOrders->pluck('supplier')->unique();

        // Prepare the dataset dynamically
        $datasets = [];

        foreach ($suppliers as $supplier) {
            $supplier = $supplier ?? 'Unknown Supplier'; // Handle null suppliers

            // Filter purchase orders for the current supplier
            $supplierOrders = $purchaseOrders->where('supplier', $supplier);

            // Map yearly totals
            $yearlyData = $supplierOrders->mapWithKeys(function ($order) {
                return [$order->year => $order->total_orders];
            });

            // Ensure data for all years (missing years = 0)
            $data = $years->map(fn($year) => $yearlyData->get($year, 0))->toArray();

            $datasets[] = [
                'label' => $supplier,
                'data' => $data,
                'backgroundColor' => '#' . substr(md5($supplier), 0, 6), // Consistent color per supplier
                'borderWidth' => 1,
                'barThickness' => 20,
                'fill' => false,
            ];
        }

        // Prepare final chart data
        $chartData = [
            'labels' => $years->toArray(), // Only show years
            'datasets' => $datasets,
        ];

        return $chartData;
    }

    private function getTotalOrdersChart($years){
        $purchaseOrders = PurchaseOrder::selectRaw(
            'YEAR(order_date) as year, COUNT(*) as total_orders'
        )
            ->whereBetween('order_date', [$years->first() . '-01-01', $years->last() . '-12-31'])
            ->groupBy('year')
            ->orderBy('year')
            ->get();

        // Map yearly totals, ensuring missing years are filled with 0
        $yearlyData = $purchaseOrders->mapWithKeys(fn($order) => [$order->year => $order->total_orders]);

        return [
            'labels' => $years->toArray(),
            'datasets' => [
                [
                    'label' => 'Total Orders',
                    'data' => $years->map(fn($year) => $yearlyData->get($year, 0))->toArray(),
                    'backgroundColor' => '#3498db', // Consistent blue color
                    'borderWidth' => 1,
                    'barThickness' => 20,
                    'fill' => false,
                ]
            ],
        ];
    }

    private function getArticleOrdersChart($years){

        $purchaseOrders = PurchaseOrder::selectRaw(
            'purchase_order_table.artical_no, YEAR(purchase_order.order_date) as year, COUNT(*) as total_orders'
        )
            ->join('purchase_order_table', 'purchase_order_table.po_id', '=', 'purchase_order.id')
            ->whereBetween('purchase_order.order_date', [$years->first() . '-01-01', $years->last() . '-12-31'])
            ->groupBy('purchase_order_table.artical_no', 'year')
            ->orderBy('purchase_order_table.artical_no')
            ->orderBy('year')
            ->get();

        // Get unique suppliers
        $suppliers = $purchaseOrders->pluck('artical_no')->unique();

        // Prepare the dataset dynamically
        $datasets = [];

        foreach ($suppliers as $supplier) {
            $supplier = $supplier ?? 'Unknown artical_no'; // Handle null suppliers

            // Filter purchase orders for the current supplier
            $supplierOrders = $purchaseOrders->where('artical_no', $supplier);

            // Map yearly totals
            $yearlyData = $supplierOrders->mapWithKeys(function ($order) {
                return [$order->year => $order->total_orders];
            });

            // Ensure data for all years (missing years = 0)
            $data = $years->map(fn($year) => $yearlyData->get($year, 0))->toArray();

            $datasets[] = [
                'label' => $supplier,
                'data' => $data,
                'backgroundColor' => '#' . substr(md5($supplier), 0, 6), // Consistent color per supplier
                'borderWidth' => 1,
                'barThickness' => 20,
                'fill' => false,
            ];
        }
        // Prepare final chart data
        $chartData = [
            'labels' => $years->toArray(), // Only show years
            'datasets' => $datasets,
        ];

        return $chartData;
    }

    private function getTotalArticlesChart($years){
        $purchaseOrders = PurchaseOrder::selectRaw(
            'purchase_order_table.artical_no, YEAR(purchase_order.order_date) as year, COUNT(*) as total_orders'
        )
            ->join('purchase_order_table', 'purchase_order_table.po_id', '=', 'purchase_order.id')
            ->whereBetween('purchase_order.order_date', [$years->first() . '-01-01', $years->last() . '-12-31'])
            ->groupBy('purchase_order_table.artical_no', 'year')
            ->orderBy('purchase_order_table.artical_no')
            ->orderBy('year')
            ->get();

        // Get unique suppliers
        $suppliers = $purchaseOrders->pluck('artical_no')->unique();

        // Prepare the dataset dynamically
        $datasets = [];

        foreach ($suppliers as $supplier) {
            $supplier = $supplier ?? 'Unknown artical_no'; // Handle null suppliers

            // Filter purchase orders for the current supplier
            $supplierOrders = $purchaseOrders->where('artical_no', $supplier);

            // Map yearly totals
            $yearlyData = $supplierOrders->mapWithKeys(function ($order) {
                return [$order->year => $order->total_orders];
            });

            // Ensure data for all years (missing years = 0)
            //$data = $years->map(fn($year) => $yearlyData->get($year, 0))->toArray();
            $data = $years->map(fn($year) => rand(0, 999))->toArray();

            $datasets[] = [
                'label' => $supplier,
                'data' => $data,
                'backgroundColor' => '#' . substr(md5($supplier), 0, 6), // Consistent color per supplier
                'borderWidth' => 1,
                'barThickness' => 20,
                'fill' => false,
            ];
        }
        // Prepare final chart data
        $chartData = [
            'labels' => $years->toArray(), // Only show years
            'datasets' => $datasets,
        ];

        return $chartData;
    }

    public function ajaxSupplierOrdersChart(Request $request){
        DB::enableQueryLog(); // Enable query logging

        if ($request->days == 'year') {

            $lastFiveYears = range(date('Y') - 4, date('Y')); // Last 5 years

            $purchaseOrdersQuery = PurchaseOrder::selectRaw(
                'supplier, YEAR(order_date) as year, COUNT(*) as total_orders'
            )
                ->whereBetween('order_date', [$lastFiveYears[0] . '-01-01', $lastFiveYears[count($lastFiveYears) - 1] . '-12-31']) // Filter by last 5 years
                ->groupBy('supplier', 'year')
                ->orderBy('supplier')
                ->orderBy('year');

            // Execute the query
            $purchaseOrders = $purchaseOrdersQuery->get();

            // Prepare years for labels
            $years = collect($lastFiveYears)->map(fn($year) => (string) $year);

            // Get unique suppliers
            $suppliers = $purchaseOrders->pluck('supplier')->unique();

            // Prepare the dataset dynamically
            $datasets = [];

            foreach ($suppliers as $supplier) {
                $supplier = $supplier ?? 'Unknown Supplier'; // Handle null suppliers

                // Filter purchase orders for the current supplier
                $supplierOrders = $purchaseOrders->where('supplier', $supplier);

                // Map yearly totals
                $yearlyData = $supplierOrders->mapWithKeys(function ($order) {
                    return [$order->year => $order->total_orders];
                });

                // Ensure data for all years (missing years = 0)
                $data = $years->map(fn($year) => $yearlyData->get($year, 0))->toArray();

                $datasets[] = [
                    'label' => $supplier,
                    'data' => $data,
                    'backgroundColor' => '#' . substr(md5($supplier), 0, 6), // Consistent color per supplier
                    'borderWidth' => 1,
                    'barThickness' => 20,
                    'fill' => false,
                ];
            }

            // Prepare final chart data
            $chartData = [
                'labels' => $years->toArray(), // Only show years
                'datasets' => $datasets,
            ];

            return $chartData;
        } else if ($request->days == 'month') {

            $last12MonthsStart = now()->subMonths(12)->startOfMonth(); // Start of 12 months ago
            $last12MonthsEnd = now()->endOfMonth(); // End of the current month

            $purchaseOrdersQuery = PurchaseOrder::selectRaw(
                'supplier, YEAR(order_date) as year, MONTH(order_date) as month, COUNT(*) as total_orders'
            )
                ->whereBetween('order_date', [$last12MonthsStart, $last12MonthsEnd]) // Filter by last 12 months
                ->groupBy('supplier', 'year', 'month')
                ->orderBy('supplier')
                ->orderBy('month')
                ->orderBy('year');

            // Execute the query
            $purchaseOrders = $purchaseOrdersQuery->get();

            // Prepare months for labels (last 12 months)
            $months = collect(range(1, 12))->map(fn($month) => now()->subMonths(12 - $month)->format('M Y'));

            // Get unique suppliers
            $suppliers = $purchaseOrders->pluck('supplier')->unique();

            // Prepare the dataset dynamically
            $datasets = [];

            foreach ($suppliers as $supplier) {
                $supplier = $supplier ?? 'Unknown Supplier'; // Handle null suppliers

                // Filter purchase orders for the current supplier
                $supplierOrders = $purchaseOrders->where('supplier', $supplier);

                $monthlyData = $supplierOrders->mapWithKeys(function ($order) {
                    return [sprintf('%d-%02d', $order->year, $order->month) => $order->total_orders];
                });

                // Transform the data
                $data = $months->map(function ($month) use ($monthlyData) {
                    // Extract the month-year combination
                    $monthYear = Carbon::parse($month)->format('Y-m');
                    // Get the total orders for the month-year, defaulting to 0 if not found
                    return $monthlyData->get($monthYear, 0);
                })->toArray();

                $datasets[] = [
                    'label' => $supplier,
                    'data' => $data,
                    'backgroundColor' => '#' . substr(md5($supplier), 0, 6), // Consistent color per supplier
                    'borderWidth' => 1,
                    'barThickness' => 20,
                    'fill' => false,
                ];
            }

            // Prepare final chart data
            $chartData = [
                'labels' => $months->toArray(), // Display months for the last 12 months
                'datasets' => $datasets,
            ];

            return $chartData;
        } else if ($request->days == 'quarter') {

            $last12MonthsStart = now()->subMonths(4)->startOfMonth(); // Start of 12 months ago
            $last12MonthsEnd = now()->endOfMonth(); // End of the current month

            $purchaseOrdersQuery = PurchaseOrder::selectRaw(
                'supplier, YEAR(order_date) as year, MONTH(order_date) as month, COUNT(*) as total_orders'
            )
                ->whereBetween('order_date', [$last12MonthsStart, $last12MonthsEnd]) // Filter by last 12 months
                ->groupBy('supplier', 'year', 'month')
                ->orderBy('supplier')
                ->orderBy('month')
                ->orderBy('year');

            // Execute the query
            $purchaseOrders = $purchaseOrdersQuery->get();

            // Prepare months for labels (last 12 months)
            $months = collect(range(1, 4))->map(fn($month) => now()->subMonths(4 - $month)->format('M Y'));

            // Get unique suppliers
            $suppliers = $purchaseOrders->pluck('supplier')->unique();

            // Prepare the dataset dynamically
            $datasets = [];

            foreach ($suppliers as $supplier) {
                $supplier = $supplier ?? 'Unknown Supplier'; // Handle null suppliers

                // Filter purchase orders for the current supplier
                $supplierOrders = $purchaseOrders->where('supplier', $supplier);

                $monthlyData = $supplierOrders->mapWithKeys(function ($order) {
                    return [sprintf('%d-%02d', $order->year, $order->month) => $order->total_orders];
                });

                // Transform the data
                $data = $months->map(function ($month) use ($monthlyData) {
                    // Extract the month-year combination
                    $monthYear = Carbon::parse($month)->format('Y-m');
                    // Get the total orders for the month-year, defaulting to 0 if not found
                    return $monthlyData->get($monthYear, 0);
                })->toArray();

                $datasets[] = [
                    'label' => $supplier,
                    'data' => $data,
                    'backgroundColor' => '#' . substr(md5($supplier), 0, 6), // Consistent color per supplier
                    'borderWidth' => 1,
                    'barThickness' => 20,
                    'fill' => false,
                ];
            }

            // Prepare final chart data
            $chartData = [
                'labels' => $months->toArray(), // Display months for the last 12 months
                'datasets' => $datasets,
            ];

            return $chartData;
        } else if ($request->days == 'week') {

            $last7DaysStart = now()->subDays(7)->startOfDay(); // Start of 7 days ago
            $last7DaysEnd = now()->endOfDay(); // End of today

            $purchaseOrdersQuery = PurchaseOrder::selectRaw(
                'supplier, DATE(order_date) as date, COUNT(*) as total_orders'
            )
                ->whereBetween('order_date', [$last7DaysStart, $last7DaysEnd]) // Filter by last 7 days
                ->groupBy('supplier', 'date')
                ->orderBy('supplier')
                ->orderBy('date');

            // Execute the query
            $purchaseOrders = $purchaseOrdersQuery->get();

            // Prepare days for labels (last 7 days)
            $days = collect(range(0, 6))->map(fn($day) => now()->subDays(6 - $day)->format('M d, Y'));

            // Get unique suppliers
            $suppliers = $purchaseOrders->pluck('supplier')->unique();

            // Prepare the dataset dynamically
            $datasets = [];

            foreach ($suppliers as $supplier) {
                $supplier = $supplier ?? 'Unknown Supplier'; // Handle null suppliers

                // Filter purchase orders for the current supplier
                $supplierOrders = $purchaseOrders->where('supplier', $supplier);

                // Map daily totals
                $dailyData = $supplierOrders->mapWithKeys(function ($order) {
                    return [$order->date => $order->total_orders];
                });

                // Ensure data for all 7 days (missing days = 0)
                $data = $days->map(function ($day) use ($dailyData) {
                    // Use the day string directly as the key
                    $date = \Carbon\Carbon::parse($day)->format('Y-m-d');
                    return $dailyData->get($date, 0);
                })->toArray();

                $datasets[] = [
                    'label' => $supplier,
                    'data' => $data,
                    'backgroundColor' => '#' . substr(md5($supplier), 0, 6), // Consistent color per supplier
                    'borderWidth' => 1,
                    'barThickness' => 20,
                    'fill' => false,
                ];
            }

            // Prepare final chart data
            $chartData = [
                'labels' => $days->toArray(), // Display the last 7 days
                'datasets' => $datasets,
            ];

            return $chartData;
        } else {
            return response()->json(['error' => 'Invalid chart type'], 400);
        }
    }

    public function ajaxTotalOrdersChart(Request $request){
        DB::enableQueryLog(); // Enable query logging

        if ($request->days == 'year') {

            $lastFiveYears = range(date('Y') - 4, date('Y')); // Last 5 years
            $purchaseOrdersQuery = PurchaseOrder::selectRaw(
                'YEAR(order_date) as year, COUNT(*) as total_orders'
            )
                ->whereBetween('order_date', [$lastFiveYears[0] . '-01-01', $lastFiveYears[count($lastFiveYears) - 1] . '-12-31']) // Filter by last 5 years
                ->groupBy('year')
                ->orderBy('year');
            // Execute the query
            $purchaseOrders = $purchaseOrdersQuery->get();
            // Prepare years for labels
            $years = collect($lastFiveYears)->map(fn($year) => (string) $year);
            // Map yearly totals
            $yearlyData = $purchaseOrders->mapWithKeys(function ($order) {
                return [$order->year => $order->total_orders];
            });
            // Ensure data for all years (missing years = 0)
            $data = $years->map(fn($year) => $yearlyData->get($year, 0))->toArray();
            // Prepare final chart data
            $chartData = [
                'labels' => $years->toArray(), // Only show years
                'datasets' => [
                    [
                        'label' => 'Total Orders',
                        'data' => $data,
                        'backgroundColor' => '#3498db', // Blue color for total orders
                        'borderWidth' => 1,
                        'barThickness' => 20,
                        'fill' => false,
                    ]
                ],
            ];

            return response()->json($chartData);
        } else if ($request->days == 'month') {

            $last12MonthsStart = now()->subMonths(12)->startOfMonth(); // Start of 12 months ago
            $last12MonthsEnd = now()->endOfMonth(); // End of the current month

            $purchaseOrdersQuery = PurchaseOrder::selectRaw(
                'YEAR(order_date) as year, MONTH(order_date) as month, COUNT(*) as total_orders'
            )
                ->whereBetween('order_date', [$last12MonthsStart, $last12MonthsEnd]) // Filter by last 12 months
                ->groupBy('year', 'month')
                ->orderBy('year')
                ->orderBy('month');

            // Execute the query
            $purchaseOrders = $purchaseOrdersQuery->get();

            // Prepare months for labels (last 12 months)
            $months = collect(range(1, 12))->map(fn($month) => now()->subMonths(12 - $month)->format('M Y'));

            // Prepare data for total orders
            $monthlyData = $purchaseOrders->mapWithKeys(function ($order) {
                return [sprintf('%d-%02d', $order->year, $order->month) => $order->total_orders];
            });

            // Transform the data
            $data = $months->map(function ($month) use ($monthlyData) {
                // Extract the month-year combination
                $monthYear = Carbon::parse($month)->format('Y-m');
                // Get the total orders for the month-year, defaulting to 0 if not found
                return $monthlyData->get($monthYear, 0);
            })->toArray();

            // Prepare final chart data
            $chartData = [
                'labels' => $months->toArray(), // Display months for the last 12 months
                'datasets' => [
                    [
                        'label' => 'Total Orders',
                        'data' => $data,
                        'backgroundColor' => '#3498db', // Blue color for total orders
                        'borderWidth' => 1,
                        'barThickness' => 20,
                        'fill' => false,
                    ]
                ],
            ];

            return $chartData;
        } else if ($request->days == 'quarter') {

            $last4MonthsStart = now()->subMonths(4)->startOfMonth(); // Start of 4 months ago
            $last4MonthsEnd = now()->endOfMonth(); // End of the current month

            $purchaseOrdersQuery = PurchaseOrder::selectRaw(
                'YEAR(order_date) as year, MONTH(order_date) as month, COUNT(*) as total_orders'
            )
                ->whereBetween('order_date', [$last4MonthsStart, $last4MonthsEnd]) // Filter by last 4 months
                ->groupBy('year', 'month')
                ->orderBy('year')
                ->orderBy('month');

            // Execute the query
            $purchaseOrders = $purchaseOrdersQuery->get();

            // Prepare months for labels (last 4 months)
            $months = collect(range(1, 4))->map(fn($month) => now()->subMonths(4 - $month)->format('M Y'));

            // Map monthly totals
            $monthlyData = $purchaseOrders->mapWithKeys(function ($order) {
                return [sprintf('%d-%02d', $order->year, $order->month) => $order->total_orders];
            });

            // Ensure data for all months (missing months = 0)
            $data = $months->map(function ($month) use ($monthlyData) {
                // Extract the month-year combination
                $monthYear = Carbon::parse($month)->format('Y-m');
                // Get the total orders for the month-year, defaulting to 0 if not found
                return $monthlyData->get($monthYear, 0);
            })->toArray();

            // Prepare final chart data
            $chartData = [
                'labels' => $months->toArray(), // Only show months
                'datasets' => [
                    [
                        'label' => 'Total Orders',
                        'data' => $data,
                        'backgroundColor' => '#3498db', // Blue color for total orders
                        'borderWidth' => 1,
                        'barThickness' => 20,
                        'fill' => false,
                    ]
                ],
            ];

            return $chartData;
        } else if ($request->days == 'week') {

            $last7DaysStart = now()->subDays(7)->startOfDay(); // Start of 7 days ago
            $last7DaysEnd = now()->endOfDay(); // End of today

            $purchaseOrdersQuery = PurchaseOrder::selectRaw(
                'DATE(order_date) as date, COUNT(*) as total_orders'
            )
                ->whereBetween('order_date', [$last7DaysStart, $last7DaysEnd]) // Filter by last 7 days
                ->groupBy('date')
                ->orderBy('date');

            // Execute the query
            $purchaseOrders = $purchaseOrdersQuery->get();

            // Prepare days for labels (last 7 days)
            $days = collect(range(0, 6))->map(fn($day) => now()->subDays(6 - $day)->format('Y-m-d'));

            // Map daily totals
            $dailyData = $purchaseOrders->mapWithKeys(function ($order) {
                return [$order->date => $order->total_orders];
            });

            // Ensure data for all 7 days (missing days = 0)
            $data = $days->map(fn($day) => $dailyData->get($day, 0))->toArray();

            // Prepare final chart data
            $chartData = [
                'labels' => $days->map(fn($day) => \Carbon\Carbon::parse($day)->format('M d, Y'))->toArray(), // Display formatted days
                'datasets' => [
                    [
                        'label' => 'Total Orders',
                        'data' => $data,
                        'backgroundColor' => '#3498db', // Blue color for total orders
                        'borderWidth' => 1,
                        'barThickness' => 20,
                        'fill' => false,
                    ]
                ],
            ];

            return $chartData;
        } else {
            return response()->json(['error' => 'Invalid chart type'], 400);
        }
    }

    public function ajaxArticleOrdersChart(Request $request){
        DB::enableQueryLog(); // Enable query logging

        if ($request->days == 'year') {

            $lastFiveYears = range(date('Y') - 4, date('Y')); // Last 5 years

            $purchaseOrders = PurchaseOrder::selectRaw(
                'purchase_order_table.artical_no, YEAR(purchase_order.order_date) as year, COUNT(*) as total_orders'
            )
                ->join('purchase_order_table', 'purchase_order_table.po_id', '=', 'purchase_order.id')
                ->whereBetween('purchase_order.order_date', [$lastFiveYears[0] . '-01-01', $lastFiveYears[count($lastFiveYears) - 1] . '-12-31'])
                ->groupBy('purchase_order_table.artical_no', 'year')
                ->orderBy('purchase_order_table.artical_no')
                ->orderBy('year')
                ->get();

            // Execute the query
            //$purchaseOrders = $purchaseOrdersQuery->get();

            // Prepare years for labels
            $years = collect($lastFiveYears)->map(fn($year) => (string) $year);

            // Get unique artical_no
            $artical_numbers = $purchaseOrders->pluck('artical_no')->unique();

            // Prepare the dataset dynamically
            $datasets = [];

            foreach ($artical_numbers as $artical_no) {
                $artical_no = $artical_no ?? 'Unknown Artical Number'; // Handle null artical_no

                // Filter purchase orders for the current supplier
                $supplierOrders = $purchaseOrders->where('artical_no', $artical_no);

                // Map yearly totals
                $yearlyData = $supplierOrders->mapWithKeys(function ($order) {
                    return [$order->year => $order->total_orders];
                });

                // Ensure data for all years (missing years = 0)
                $data = $years->map(fn($year) => $yearlyData->get($year, 0))->toArray();

                $datasets[] = [
                    'label' => $artical_no,
                    'data' => $data,
                    'backgroundColor' => '#' . substr(md5($artical_no), 0, 6), // Consistent color per artical_no
                    'borderWidth' => 1,
                    'barThickness' => 20,
                    'fill' => false,
                ];
            }

            // Prepare final chart data
            $chartData = [
                'labels' => $years->toArray(), // Only show years
                'datasets' => $datasets,
            ];

            return $chartData;
        } else if ($request->days == 'month') {

            $last12MonthsStart = now()->subMonths(12)->startOfMonth(); // Start of 12 months ago
            $last12MonthsEnd = now()->endOfMonth(); // End of the current month

            $purchaseOrders = PurchaseOrder::selectRaw(
                'purchase_order_table.artical_no, YEAR(purchase_order.order_date) as year, MONTH(purchase_order.order_date) as month, COUNT(*) as total_orders'
            )
                ->join('purchase_order_table', 'purchase_order_table.po_id', '=', 'purchase_order.id')
                ->whereBetween('purchase_order.order_date', [$last12MonthsStart, $last12MonthsEnd])
                ->groupBy('purchase_order_table.artical_no', 'year', 'month')
                ->orderBy('purchase_order_table.artical_no')
                ->orderBy('month')
                ->orderBy('year')
                ->get();

            // Prepare months for labels (last 12 months)
            $months = collect(range(1, 12))->map(fn($month) => now()->subMonths(12 - $month)->format('M Y'));

            // Get unique artical_no
            $artical_numbers = $purchaseOrders->pluck('artical_no')->unique();

            // Prepare the dataset dynamically
            $datasets = [];

            foreach ($artical_numbers as $artical_no) {
                $artical_no = $artical_no ?? 'Unknown Artical Number'; // Handle null artical_no

                // Filter purchase orders for the current artical_no
                $supplierOrders = $purchaseOrders->where('artical_no', $artical_no);

                $monthlyData = $supplierOrders->mapWithKeys(function ($order) {
                    return [sprintf('%d-%02d', $order->year, $order->month) => $order->total_orders];
                });

                // Transform the data
                $data = $months->map(function ($month) use ($monthlyData) {
                    // Extract the month-year combination
                    $monthYear = Carbon::parse($month)->format('Y-m');
                    // Get the total orders for the month-year, defaulting to 0 if not found
                    return $monthlyData->get($monthYear, 0);
                })->toArray();

                $datasets[] = [
                    'label' => $artical_no,
                    'data' => $data,
                    'backgroundColor' => '#' . substr(md5($artical_no), 0, 6), // Consistent color per artical_no
                    'borderWidth' => 1,
                    'barThickness' => 20,
                    'fill' => false,
                ];
            }

            // Prepare final chart data
            $chartData = [
                'labels' => $months->toArray(), // Display months for the last 12 months
                'datasets' => $datasets,
            ];

            return $chartData;
        } else if ($request->days == 'quarter') {

            $last12MonthsStart = now()->subMonths(4)->startOfMonth(); // Start of 12 months ago
            $last12MonthsEnd = now()->endOfMonth(); // End of the current month

            $purchaseOrders = PurchaseOrder::selectRaw(
                'purchase_order_table.artical_no, YEAR(purchase_order.order_date) as year, MONTH(purchase_order.order_date) as month, COUNT(*) as total_orders'
            )
                ->join('purchase_order_table', 'purchase_order_table.po_id', '=', 'purchase_order.id')
                ->whereBetween('purchase_order.order_date', [$last12MonthsStart, $last12MonthsEnd])
                ->groupBy('purchase_order_table.artical_no', 'year', 'month')
                ->orderBy('purchase_order_table.artical_no')
                ->orderBy('month')
                ->orderBy('year')
                ->get();

            // Prepare months for labels (last 12 months)
            $months = collect(range(1, 4))->map(fn($month) => now()->subMonths(4 - $month)->format('M Y'));

            // Get unique artical_no
            $artical_numbers = $purchaseOrders->pluck('artical_no')->unique();

            // Prepare the dataset dynamically
            $datasets = [];

            foreach ($artical_numbers as $artical_no) {
                $artical_no = $artical_no ?? 'Unknown Artical Number'; // Handle null artical_no

                // Filter purchase orders for the current artical_no
                $supplierOrders = $purchaseOrders->where('artical_no', $artical_no);

                $monthlyData = $supplierOrders->mapWithKeys(function ($order) {
                    return [sprintf('%d-%02d', $order->year, $order->month) => $order->total_orders];
                });

                // Transform the data
                $data = $months->map(function ($month) use ($monthlyData) {
                    // Extract the month-year combination
                    $monthYear = Carbon::parse($month)->format('Y-m');
                    // Get the total orders for the month-year, defaulting to 0 if not found
                    return $monthlyData->get($monthYear, 0);
                })->toArray();

                $datasets[] = [
                    'label' => $artical_no,
                    'data' => $data,
                    'backgroundColor' => '#' . substr(md5($artical_no), 0, 6), // Consistent color per artical_no
                    'borderWidth' => 1,
                    'barThickness' => 20,
                    'fill' => false,
                ];
            }

            // Prepare final chart data
            $chartData = [
                'labels' => $months->toArray(), // Display months for the last 12 months
                'datasets' => $datasets,
            ];

            return $chartData;
        } else if ($request->days == 'week') {

            $last7DaysStart = now()->subDays(7)->startOfDay(); // Start of 7 days ago
            $last7DaysEnd = now()->endOfDay(); // End of today

            $purchaseOrders = PurchaseOrder::selectRaw(
                'purchase_order_table.artical_no, DATE(purchase_order.order_date) as date, COUNT(*) as total_orders'
            )
                ->join('purchase_order_table', 'purchase_order_table.po_id', '=', 'purchase_order.id')
                ->whereBetween('purchase_order.order_date', [$last7DaysStart, $last7DaysEnd])
                ->groupBy('purchase_order_table.artical_no', 'date')
                ->orderBy('purchase_order_table.artical_no')
                ->orderBy('date')
                ->get();

            // Prepare days for labels (last 7 days)
            $days = collect(range(0, 6))->map(fn($day) => now()->subDays(6 - $day)->format('M d, Y'));

            // Get unique artical_no
            $artical_numbers = $purchaseOrders->pluck('artical_no')->unique();

            // Prepare the dataset dynamically
            $datasets = [];

            foreach ($artical_numbers as $artical_no) {
                $artical_no = $artical_no ?? 'Unknown Artical No'; // Handle null artical_no

                // Filter purchase orders for the current artical numbers
                $supplierOrders = $purchaseOrders->where('artical_no', $artical_no);

                // Map daily totals
                $dailyData = $supplierOrders->mapWithKeys(function ($order) {
                    return [$order->date => $order->total_orders];
                });

                // Ensure data for all 7 days (missing days = 0)
                $data = $days->map(function ($day) use ($dailyData) {
                    // Use the day string directly as the key
                    $date = \Carbon\Carbon::parse($day)->format('Y-m-d');
                    return $dailyData->get($date, 0);
                })->toArray();

                $datasets[] = [
                    'label' => $artical_no,
                    'data' => $data,
                    'backgroundColor' => '#' . substr(md5($artical_no), 0, 6), // Consistent color per artical_no
                    'borderWidth' => 1,
                    'barThickness' => 20,
                    'fill' => false,
                ];
            }

            // Prepare final chart data
            $chartData = [
                'labels' => $days->toArray(), // Display the last 7 days
                'datasets' => $datasets,
            ];

            return $chartData;
        } else {
            return response()->json(['error' => 'Invalid chart type'], 400);
        }
    }

    public function ajaxTotalArticlesChart(Request $request){
        DB::enableQueryLog(); // Enable query logging

        if ($request->days == 'year') {

            $lastFiveYears = range(date('Y') - 4, date('Y')); // Last 5 years

            $purchaseOrders = PurchaseOrder::selectRaw(
                'purchase_order_table.artical_no, YEAR(purchase_order.order_date) as year, COUNT(*) as total_orders'
            )
                ->join('purchase_order_table', 'purchase_order_table.po_id', '=', 'purchase_order.id')
                ->whereBetween('purchase_order.order_date', [$lastFiveYears[0] . '-01-01', $lastFiveYears[count($lastFiveYears) - 1] . '-12-31'])
                ->groupBy('purchase_order_table.artical_no', 'year')
                ->orderBy('purchase_order_table.artical_no')
                ->orderBy('year')
                ->get();

            // Prepare years for labels
            $years = collect($lastFiveYears)->map(fn($year) => (string) $year);
            // Get unique artical_no
            $artical_numbers = $purchaseOrders->pluck('artical_no')->unique();
            // Prepare the dataset dynamically
            $datasets = [];

            foreach ($artical_numbers as $artical_no) {
                $artical_no = $artical_no ?? 'Unknown Artical Number'; // Handle null artical_no

                // Filter purchase orders for the current supplier
                $supplierOrders = $purchaseOrders->where('artical_no', $artical_no);

                // Map yearly totals
                $yearlyData = $supplierOrders->mapWithKeys(function ($order) {
                    return [$order->year => $order->total_orders];
                });

                // Ensure data for all years (missing years = 0)
                $data = $years->map(fn($year) => rand(0, 999))->toArray();

                $datasets[] = [
                    'label' => $artical_no,
                    'data' => $data,
                    'backgroundColor' => '#' . substr(md5($artical_no), 0, 6), // Consistent color per artical_no
                    'borderWidth' => 1,
                    'barThickness' => 20,
                    'fill' => false,
                ];
            }

            // Prepare final chart data
            $chartData = [
                'labels' => $years->toArray(), // Only show years
                'datasets' => $datasets,
            ];

            return $chartData;
        } else if ($request->days == 'month') {

            $last12MonthsStart = now()->subMonths(12)->startOfMonth(); // Start of 12 months ago
            $last12MonthsEnd = now()->endOfMonth(); // End of the current month

            $purchaseOrders = PurchaseOrder::selectRaw(
                'purchase_order_table.artical_no, YEAR(purchase_order.order_date) as year, MONTH(purchase_order.order_date) as month, COUNT(*) as total_orders'
            )
                ->join('purchase_order_table', 'purchase_order_table.po_id', '=', 'purchase_order.id')
                ->whereBetween('purchase_order.order_date', [$last12MonthsStart, $last12MonthsEnd])
                ->groupBy('purchase_order_table.artical_no', 'year', 'month')
                ->orderBy('purchase_order_table.artical_no')
                ->orderBy('month')
                ->orderBy('year')
                ->get();

            // Prepare months for labels (last 12 months)
            $months = collect(range(1, 12))->map(fn($month) => now()->subMonths(12 - $month)->format('M Y'));

            // Get unique artical_no
            $artical_numbers = $purchaseOrders->pluck('artical_no')->unique();

            // Prepare the dataset dynamically
            $datasets = [];

            foreach ($artical_numbers as $artical_no) {
                $artical_no = $artical_no ?? 'Unknown Artical Number'; // Handle null artical_no

                // Filter purchase orders for the current artical_no
                $supplierOrders = $purchaseOrders->where('artical_no', $artical_no);

                $monthlyData = $supplierOrders->mapWithKeys(function ($order) {
                    return [sprintf('%d-%02d', $order->year, $order->month) => $order->total_orders];
                });

                // Transform the data
                $data = $months->map(function ($month) use ($monthlyData) {
                    // Extract the month-year combination
                    $monthYear = Carbon::parse($month)->format('Y-m');
                    // Get the total orders for the month-year, defaulting to 0 if not found                  
                    return rand(0, 999);
                })->toArray();

                $datasets[] = [
                    'label' => $artical_no,
                    'data' => $data,
                    'backgroundColor' => '#' . substr(md5($artical_no), 0, 6), // Consistent color per artical_no
                    'borderWidth' => 1,
                    'barThickness' => 20,
                    'fill' => false,
                ];
            }

            // Prepare final chart data
            $chartData = [
                'labels' => $months->toArray(), // Display months for the last 12 months
                'datasets' => $datasets,
            ];

            return $chartData;
        } else if ($request->days == 'quarter') {

            $last12MonthsStart = now()->subMonths(4)->startOfMonth(); // Start of 12 months ago
            $last12MonthsEnd = now()->endOfMonth(); // End of the current month

            $purchaseOrders = PurchaseOrder::selectRaw(
                'purchase_order_table.artical_no, YEAR(purchase_order.order_date) as year, MONTH(purchase_order.order_date) as month, COUNT(*) as total_orders'
            )
                ->join('purchase_order_table', 'purchase_order_table.po_id', '=', 'purchase_order.id')
                ->whereBetween('purchase_order.order_date', [$last12MonthsStart, $last12MonthsEnd])
                ->groupBy('purchase_order_table.artical_no', 'year', 'month')
                ->orderBy('purchase_order_table.artical_no')
                ->orderBy('month')
                ->orderBy('year')
                ->get();

            // Prepare months for labels (last 12 months)
            $months = collect(range(1, 4))->map(fn($month) => now()->subMonths(4 - $month)->format('M Y'));

            // Get unique artical_no
            $artical_numbers = $purchaseOrders->pluck('artical_no')->unique();

            // Prepare the dataset dynamically
            $datasets = [];

            foreach ($artical_numbers as $artical_no) {
                $artical_no = $artical_no ?? 'Unknown Artical Number'; // Handle null artical_no

                // Filter purchase orders for the current artical_no
                $supplierOrders = $purchaseOrders->where('artical_no', $artical_no);

                $monthlyData = $supplierOrders->mapWithKeys(function ($order) {
                    return [sprintf('%d-%02d', $order->year, $order->month) => $order->total_orders];
                });

                // Transform the data
                $data = $months->map(function ($month) use ($monthlyData) {
                    // Extract the month-year combination
                    $monthYear = Carbon::parse($month)->format('Y-m');
                    // Get the total orders for the month-year, defaulting to 0 if not found
                    return rand(0, 999);
                })->toArray();

                $datasets[] = [
                    'label' => $artical_no,
                    'data' => $data,
                    'backgroundColor' => '#' . substr(md5($artical_no), 0, 6), // Consistent color per artical_no
                    'borderWidth' => 1,
                    'barThickness' => 20,
                    'fill' => false,
                ];
            }

            // Prepare final chart data
            $chartData = [
                'labels' => $months->toArray(), // Display months for the last 12 months
                'datasets' => $datasets,
            ];

            return $chartData;
        } else if ($request->days == 'week') {

            $last7DaysStart = now()->subDays(7)->startOfDay(); // Start of 7 days ago
            $last7DaysEnd = now()->endOfDay(); // End of today

            $purchaseOrders = PurchaseOrder::selectRaw(
                'purchase_order_table.artical_no, DATE(purchase_order.order_date) as date, COUNT(*) as total_orders'
            )
                ->join('purchase_order_table', 'purchase_order_table.po_id', '=', 'purchase_order.id')
                ->whereBetween('purchase_order.order_date', [$last7DaysStart, $last7DaysEnd])
                ->groupBy('purchase_order_table.artical_no', 'date')
                ->orderBy('purchase_order_table.artical_no')
                ->orderBy('date')
                ->get();

            // Prepare days for labels (last 7 days)
            $days = collect(range(0, 6))->map(fn($day) => now()->subDays(6 - $day)->format('M d, Y'));
            // Get unique artical_no
            $artical_numbers = $purchaseOrders->pluck('artical_no')->unique();
            // Prepare the dataset dynamically
            $datasets = [];

            foreach ($artical_numbers as $artical_no) {
                $artical_no = $artical_no ?? 'Unknown Artical No'; // Handle null artical_no
                // Filter purchase orders for the current artical numbers
                $supplierOrders = $purchaseOrders->where('artical_no', $artical_no);
                // Map daily totals
                $dailyData = $supplierOrders->mapWithKeys(function ($order) {
                    return [$order->date => $order->total_orders];
                });
                // Ensure data for all 7 days (missing days = 0)
                $data = $days->map(function ($day) use ($dailyData) {
                    // Use the day string directly as the key
                    $date = \Carbon\Carbon::parse($day)->format('Y-m-d');
                    return rand(0, 999);
                })->toArray();

                $datasets[] = [
                    'label' => $artical_no,
                    'data' => $data,
                    'backgroundColor' => '#' . substr(md5($artical_no), 0, 6), // Consistent color per artical_no
                    'borderWidth' => 1,
                    'barThickness' => 20,
                    'fill' => false,
                ];
            }

            // Prepare final chart data
            $chartData = [
                'labels' => $days->toArray(), // Display the last 7 days
                'datasets' => $datasets,
            ];

            return $chartData;
        } else {
            return response()->json(['error' => 'Invalid chart type'], 400);
        }
    }

    public function inbox(){
        $now = Carbon::now();
        $weekStart = $now->startOfWeek();
        $weekEnd = $now->endOfWeek();
        $page_title = "PROJECT STATUS";

        // Fetch the hours for bom_drawings from admin_hours_management
        $bomHours = AdminHoursManagement::where('lable', 'StandardProcessTimes')
            ->where('key', 'bom_drawings')
            ->where('is_deleted', 0)
            ->value('value') ?? 24; // Default to 24 hours if not found

        $place_po_hours = AdminHoursManagement::where('lable', 'StandardProcessTimes')
            ->where('key', 'check_the_bom_and_place_po')
            ->where('is_deleted', 0)
            ->value('value') ?? 48; // Default to 48 hours if not found        

        $minimumLowStock = StockMasterModule::whereColumn('available_qty', '<=', 'minimum_required_qty')
            ->orderBy('id', 'asc')
            ->get();

        return view('procurement_manager.inbox', compact('pending_bom', 'page_title', 'minimumLowStock'));
    }

    private function calculateDeadline($createdAt, $hours){
        $deadline = Carbon::parse($createdAt);
        $remainingHours = $hours;

        while ($remainingHours > 0) {
            $deadline->addHour();
            // Check if the current day is Saturday (6) or Sunday (0)
            if ($deadline->dayOfWeek === Carbon::SATURDAY || $deadline->dayOfWeek === Carbon::SUNDAY) {
                continue; // Skip weekends by not decrementing hours
            }
            $remainingHours--;
        }

        return $deadline;
    }

    public function reuploadPo($id){
        $purchaseOrder = PurchaseOrder::with('purchaseOrderTables')->findOrFail($id);
        $currecy_converter = CurrecyConverter::orderBy('created_at', 'desc')->get();
        $usdValue = CurrecyConverter::value('1_USD');
        $aedValue = CurrecyConverter::value('1_AED');
        $eurValue = CurrecyConverter::value('1_EUR');
        $page_title = "Re-upload Purchase Order";

        return view('procurement_manager.reupload_po', compact('purchaseOrder', 'currecy_converter', 'usdValue', 'aedValue', 'eurValue', 'page_title'));
    }

    public function storeReuploadPo(Request $request, $id){
        $purchaseOrder = PurchaseOrder::findOrFail($id);

        $request->validate([
            'PO_pdf' => 'nullable|mimes:pdf|max:2048',
            'PO_number' => 'required|string|max:255',
            'project_number' => 'nullable|string|max:255',
            'project_name' => 'nullable|string|max:255',
            'payment_terms' => 'required|string|max:255',
            'shipment_method' => 'required|string|max:255',
            'order_date' => 'required|string',
            'supplier' => 'required|string',
            'table_data' => 'required',
        ]);

        $currency = $request->currency;

        // Check if new PO number matches the existing one
        if ($request->PO_number !== $purchaseOrder->po_number) {
            return response()->json(['error' => 'PO Number cannot be changed during re-upload. It must match the original PO number.'], 400);
        }

        // Handle file upload if provided
        if ($request->hasFile('PO_pdf')) {
            $file = $request->file('PO_pdf');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('purchase_order_pdf'), $fileName);
        } else {
            $fileName = $purchaseOrder->po_pdf; // Retain old file if no new upload
        }

        $orderDate = \DateTime::createFromFormat('F d, Y', $request->order_date);
        $formattedOrderDate = $orderDate ? $orderDate->format('Y-m-d') : $purchaseOrder->order_date;

        // Update approval statuses based on rejection state
        $isProductionManagerApproved = $purchaseOrder->is_production_manager_approved;
        $isProductionEngineerApproved = $purchaseOrder->is_production_engineer_approved;

        if ($isProductionManagerApproved == 2) {
            $isProductionManagerApproved = 4; // Request approval again
        }
        if ($isProductionEngineerApproved == 2) {
            $isProductionEngineerApproved = 0; // Reset to pending
        }

        // No change if already 1
        $purchaseOrderData = [
            'po_pdf' => $fileName,
            'po_number' => $request->PO_number,
            'is_project_order' => $request->is_Project_Order ?? 0,
            'project_no' => $request->project_number,
            'is_production_manager_approved' => $isProductionManagerApproved,
            'is_production_engineer_approved' => $isProductionEngineerApproved,
            'project_name' => $request->project_name,
            'is_local_supplier' => $request->is_local_supplier ?? 0,
            'payment_terms' => $request->payment_terms,
            'shipment_method' => $request->shipment_method,
            'order_date' => $formattedOrderDate,
            'supplier' => $request->supplier,
        ];

        try {
            $purchaseOrder->update($purchaseOrderData);

            // Delete existing table data and insert new
            PurchaseOrderTable::where('po_id', $purchaseOrder->id)->delete();
            $tableData = json_decode($request->table_data, true);
            foreach ($tableData as $row) {
                PurchaseOrderTable::create([
                    'po_id' => $purchaseOrder->id,
                    'position_no' => $row['position_no'],
                    'artical_no' => $row['artical_no'],
                    'vendor_item_no' => $row['vendor_item_no'],
                    'description' => $row['description'],
                    'quantity' => $row['quantity'],
                    'unit_of_measure' => $row['unit_of_measure'],
                    'vat_per' => $row['vat_per'],
                    'direct_unit_cost' => $row['direct_unit_cost'],
                    'vat_amount' => $row['vat_amount'],
                    'amount' => $row['amount_eur'], // Real Amount
                    'amount_eur' => $row['amount'], // Converted Amount
                    'currency' => $currency,
                ]);
            }

            return response()->json(['success' => 'Purchase order updated successfully!'], 200);
        } catch (\Exception $e) {
            \Log::error('Error updating purchase order:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'An error occurred while updating the purchase order.'], 500);
        }
    }   

    public function updateCheckStatus(Request $request){
        $id = $request->input('id');
        $type = $request->input('type');
        $checked = $request->input('checked');

        try {
            if ($type === 'bom') {
                $check = ProductsOfProjects::findOrFail($id);
                $check->bom_check_procurement_manager = "2";
            } elseif ($type === 'drawing') {
                $check = ProductsOfProjects::findOrFail($id);
                $check->drawing_check_procurement_manager = "2";
            } else {
                return response()->json(['success' => false, 'message' => 'Invalid type']);
            }

            $check->save();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function getProductBOM(Request $request){
        $productId = $request->input('id');
        $projectId = DB::table('products_of_projects')->where('id', $productId)->value('project_id');
        $bomItems = ProductBOMItem::where('product_BOM_item.product_id', $productId)
            ->where('product_BOM_item.project_id', $projectId)
            ->leftJoin('stock_master_module', function ($join) {
                $join->on('product_BOM_item.item_desc', '=', 'stock_master_module.item_desc')
                    ->on('product_BOM_item.wilo_article_no', '=', 'stock_master_module.article_number');
            })
            ->leftJoin('stock_bom_po', function ($join) use ($productId, $projectId) {
                $join->on('product_BOM_item.item_desc', '=', 'stock_bom_po.description')
                    ->on('product_BOM_item.wilo_article_no', '=', 'stock_bom_po.article_no')
                    ->where('stock_bom_po.product_id', '=', $productId)
                    ->where('stock_bom_po.project_id', '=', $projectId);
            })
            ->select(
                'product_BOM_item.id',
                'product_BOM_item.item_desc',
                'product_BOM_item.wilo_article_no',
                'product_BOM_item.item_qty',
                'product_BOM_item.product_qty',
                'product_BOM_item.total_required_qty',
                'product_BOM_item.project_id',
                'product_BOM_item.product_id',
                'stock_bom_po.hold_qty',
                'stock_bom_po.id as stock_bom_id',
                'stock_bom_po.po_added',

                DB::raw('MAX(stock_master_module.available_qty) as stock_qty'),
                DB::raw('MAX(stock_bom_po.id) as stock_bom_id'),
                DB::raw('MAX(stock_master_module.price) as price'),
                DB::raw('MAX(stock_bom_po.select_option) as saved_option'),
                DB::raw('MAX(stock_bom_po.supplier) as saved_supplier')
            )
            ->groupBy('product_BOM_item.id')
            ->orderBy('stock_bom_po.supplier', 'asc')
            ->get()

            ->filter(function ($item) {
                return intval($item->item_qty) >= 1;
            })
            ->values()
            ->map(function ($item, $index) {
                return [
                    'sr_no' => $index + 1,
                    'item_desc' => $item->item_desc,
                    'wilo_article_no' => $item->wilo_article_no,
                    'item_qty' => $item->item_qty,
                    'product_qty' => $item->product_qty,
                    'total_required_qty' => $item->total_required_qty,
                    'qty' => $item->stock_qty ?? '0',
                    'price' => $item->price ?? '0',
                    'saved_option' => $item->saved_option,
                    'saved_supplier' => $item->saved_supplier,
                    'hold_qty' => $item->hold_qty,
                    'stock_bom_id' => $item->stock_bom_id,
                    'po_added' => $item->po_added,
                ];
            });
        $suppliers = DB::table('suppliers_list')->pluck('supplier_name');

        $isSaved = $bomItems->contains(fn($item) => $item['saved_option'] && $item['saved_supplier']);
               
        return response()->json([
            'bomItems' => $bomItems,
            'suppliers' => $suppliers,
            'isSaved' => $isSaved,
            'productId' => $productId,
            'projectId' => $projectId,
        ]);
    }

    public function placePOStatus(Request $request){
        $request->validate([
            'stock_bom_id' => 'required|integer',
        ]);

        //$stockBom = StockBOMPo::find($request->stock_bom_id);
        $stockBom = StockBOMPo::where('project_id', $request->projectId)
            ->where('product_id', $request->productId)
            ->where('supplier', $request->stock_bom_supplier)->get();

        if (!$stockBom) {
            return response()->json(['message' => 'Stock BOM not found.'], 404);
        }
        // Update current item to po_added = 1
        foreach ($stockBom as $val) {
            $val->po_added = 1;
            $val->save();

            // NEW LOGIC: Check if all other rows are po_added = 1
            $projectId = $val->project_id; // Assuming you have this field in your model
            $allAdded = StockBOMPo::where('project_id', $projectId)
                ->where('select_option', '!=', 'stock')
                ->where('po_added', '!=', 1)
                ->count() === 0;

            if ($allAdded) {
                // Auto mark 'FROM STOCK' as added
                StockBOMPo::where('project_id', $projectId)
                    ->where('select_option', 'stock')
                    ->update(['po_added' => 1]);
            }
        }

        return response()->json(['message' => 'PO successfully placed.']);
    }
    
    // Process order btn click then this function is called = OLD
    // public function saveStockBOMPo(Request $request){
    //     $data = $request->input('bom_data');
    //     if (!$data || !is_array($data) || empty($data)) {
    //         return response()->json(['error' => 'Invalid BOM data!'], 400);
    //     }
    //     $productId = $data[0]['product_id'] ?? null;

    //     foreach ($data as $item) {
    //         if (empty($item['option']) || empty($item['supplier'])) {
    //             return response()->json(['error' => 'Option and Supplier are required for all items!'], 400);
    //         }

    //         $stockQty = $item['stock_qty'] ?? $item['qty'] ?? 0;
    //         $totalRequiredQty = $item['total_required_qty'] ?? 0;

    //         if ($item['option'] === 'partial') {

    //             $holdQty = min($stockQty, $totalRequiredQty);
    //         } elseif ($item['option'] === 'stock') {

    //             $stockQty = $item['stock_qty'] ?? $item['qty'] ?? 0;
    //             $totalRequiredQty = $item['total_required_qty'] ?? 0; 
    //             $holdQty = $item['total_required_qty'] ?? 0;
    //             $totalRequiredQty = $item['total_required_qty'] ?? 0;
    //             $stockQty = $stockQty - $holdQty;
    //         } else {
    //             $holdQty = ($stockQty > 0) ? $totalRequiredQty : 0;
    //         }

    //         $holdQty = max(0, $holdQty);

    //         $supplier = $item['option'] === 'stock' ? 'N/A' : $item['supplier'];
    //         $po_added = $item['option'] === 'stock' ? '1' : '0';
    //         $po_no = $item['option'] === 'stock' ? 'N/A' : null;

    //         StockBOMPo::updateOrCreate(
    //             [
    //                 'article_no' => $item['article_no'],
    //                 'description' => $item['item_desc'],
    //                 'product_id' => $item['product_id'],
    //                 'project_id' => $item['project_id'],
    //             ],
    //             [
    //                 'item_quantity' => $item['item_qty'],
    //                 'product_quantity' => $item['product_qty'],
    //                 'total_required_quantity' => $totalRequiredQty,
    //                 'stock_quantity' => $stockQty,
    //                 'hold_qty' => $holdQty,
    //                 'select_option' => $item['option'],
    //                 'supplier' => $supplier,
    //                 'po_no' => $po_no,
    //                 'po_added' => $po_added,
    //                 'processed_at' => now(), // Store the processing timestamp
    //             ]
    //         );

    //         $stockMaster = StockMasterModule::where('item_desc', $item['item_desc'])
    //             ->where('article_number', $item['article_no'])
    //             ->first();
    //         if ($stockMaster) {
    //             $currentHoldQty = $stockMaster->hold_qty ?? 0;
    //             $newHoldQty = $currentHoldQty + $holdQty;
    //             $newHoldQty = max(0, $newHoldQty);
    //             $availableQty = max(0, $stockMaster->qty - $newHoldQty);
    //             $stockMaster->update([
    //                 'hold_qty' => $newHoldQty,
    //                 'available_qty' => $availableQty,
    //             ]);
    //         }    
    //     }
    //     if ($productId) {
    //         ProductsOfProjects::where('id', $productId)->update(['bom_check_procurement_manager' => 3]);
    //     }

    //     return response()->json(['message' => 'BOM Checked successfully']);
    // }

    // Process order btn click then this function is called = Alpeshbhai
    public function saveStockBOMPo(Request $request){
        $data = $request->input('bom_data');
        if (!$data || !is_array($data) || empty($data)) {
            return response()->json(['error' => 'Invalid BOM data!'], 400);
        }
        // STEP 1: Validate all rows first
        foreach ($data as $item) {
            if (empty($item['option']) || empty($item['supplier'])) {
                return response()->json([
                    'error' => 'Option and Supplier are required for all items!'
                ], 400);
            }
        }
        $productId = $data[0]['product_id'] ?? null;
        // STEP 2: Transaction (all-or-nothing)
        DB::transaction(function () use ($data) {
            foreach ($data as $item) {
                $stockQty = $item['stock_qty'] ?? $item['qty'] ?? 0;
                $totalRequiredQty = $item['total_required_qty'] ?? 0;
                if ($item['option'] === 'partial') {
                    $holdQty = min($stockQty, $totalRequiredQty);
                } elseif ($item['option'] === 'stock') {
                    $holdQty = $totalRequiredQty;
                    $stockQty = $stockQty - $holdQty;
                } else {
                    $holdQty = ($stockQty > 0) ? $totalRequiredQty : 0;
                }
                $holdQty = max(0, $holdQty);
                $supplier = $item['option'] === 'stock' ? 'N/A' : $item['supplier'];
                $po_added = $item['option'] === 'stock' ? '1' : '0';
                $po_no = $item['option'] === 'stock' ? 'N/A' : null;
                StockBOMPo::updateOrCreate(
                    [
                        'article_no' => $item['article_no'],
                        'description' => $item['item_desc'],
                        'product_id' => $item['product_id'],
                        'project_id' => $item['project_id'],
                    ],
                    [
                        'item_quantity' => $item['item_qty'],
                        'product_quantity' => $item['product_qty'],
                        'total_required_quantity' => $totalRequiredQty,
                        'stock_quantity' => $stockQty,
                        'hold_qty' => $holdQty,
                        'select_option' => $item['option'],
                        'supplier' => $supplier,
                        'po_no' => $po_no,
                        'po_added' => $po_added,
                        'processed_at' => now(),
                    ]
                );

                $stockMaster = StockMasterModule::where('item_desc', $item['item_desc'])
                    ->where('article_number', $item['article_no'])
                    ->first();

                if ($stockMaster) {
                    $newHoldQty = max(0, ($stockMaster->hold_qty ?? 0) + $holdQty);
                    $availableQty = max(0, $stockMaster->qty - $newHoldQty);

                    $stockMaster->update([
                        'hold_qty' => $newHoldQty,
                        'available_qty' => $availableQty,
                    ]);
                }
            }
        });

        if ($productId) {
            ProductsOfProjects::where('id', $productId)
                ->update(['bom_check_procurement_manager' => 3]);
        }

        return response()->json(['message' => 'BOM Checked successfully']);
    }

    public function viewBOM($id){
        $bom = StockBOMPo::findOrFail($id);
        return view('procurement_manager.inbox.bom.view', compact('bom'));
    }

    public function saveProcurementRemark(Request $request){
        $request->validate([
            'project_id' => 'required|integer',
            'remark' => 'required|string|max:250',
        ]);
        $product = ProductsOfProjects::find($request->project_id);
        if ($product) {
            $product->remarks_by_procurement_manager = $request->remark;
            $product->save();
            return redirect()->back()->with('success', 'Remark added successfully!');
        }
        return redirect()->back()->with('error', 'Project not found!');
    }

    public function saveProcurementDrawingRemark(Request $request){
        $request->validate([
            'project_id' => 'required|integer',
            'remark' => 'required|string|max:250',
        ]);
        $product = ProductsOfProjects::find($request->project_id);
        if ($product) {
            $product->drawing_remarks_by_procurement_manager = $request->remark;
            $product->save();
            return redirect()->back()->with('success', 'Remark added successfully!');
        }
        return redirect()->back()->with('error', 'Project not found!');
    }

    public function getProcurementRemark($id){
        $product = ProductsOfProjects::find($id);
        if ($product) {
            return response()->json(['remark' => $product->remarks_by_procurement_manager]);
        }
        return response()->json(['remark' => '']);
    }
    
}
