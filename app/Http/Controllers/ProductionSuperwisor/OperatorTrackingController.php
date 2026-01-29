<?php

namespace App\Http\Controllers\ProductionSuperwisor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\Project;
use App\Models\ProductsOfProjects;

class OperatorTrackingController extends Controller
{
    public function index(Request $request){
        $page_title = "Operator Tracking";
        $trackingOperatorsfilter = (object)[];
        $last_filter_column = $request->input('last_filter_column'); 

        if (isset($last_filter_column)) {
            $trackingOperatorsfilter = ProductsOfProjects::select('id', 'project_id', 'full_article_number', 'description', 'qty', 'product_type')
                ->with('projects')
                ->with('assignedProducts')
                ->with('assignedOperatorProductQtyWiseIDs')

                // FIXED: removed ->where('project_status', 1)
                ->with(['projectProcessStdTimes' => function($q) {
                    $q->select('id', 'projects_id', 'product_id', 'order_qty', 'project_status', 'project_process_name', 'operators_time_tracking', 'timer_started_at');
                }])
                ->whereHas('projectProcessStdTimes', function($q) {
                    $q->select('product_id', 'order_qty')
                        ->groupBy('product_id', 'order_qty')
                        ->havingRaw('COUNT(*) = SUM(CASE WHEN project_status = 1 THEN 1 ELSE 0 END)');
                })
                ->orderBy('id', 'desc')
                ->get();
        }

        $filters = $request->input('filters', []);  

        // Load only matching child rows
        $query = ProductsOfProjects::select('id', 'project_id', 'full_article_number', 'description', 'qty', 'product_type')
            ->with(['projects' => function ($q) use ($filters) {
                $q->orderBy('id', 'desc');

                $columnMap = [
                    'filter_col_2'  => 'project_no',
                    'filter_col_3'  => 'project_name'
                ];

                foreach ($columnMap as $filterKey => $dbColumn) {
                    if (!empty($filters[$filterKey])) {
                        $filterValues = $filters[$filterKey];
                        $hasEmpty = in_array('__EMPTY__', $filterValues);
                        $nonEmptyValues = array_filter($filterValues, fn($v) => $v !== '__EMPTY__');
                        $q->where(function ($subQuery) use ($dbColumn, $hasEmpty, $nonEmptyValues) {
                            if (!empty($nonEmptyValues)) {
                                $subQuery->whereIn($dbColumn, $nonEmptyValues);
                            }
                            if ($hasEmpty) {
                                $subQuery->orWhereNull($dbColumn)->orWhere($dbColumn, '');
                            }
                        });
                    }
                }
            }])
            ->with('assignedProducts')
            ->with('assignedOperatorProductQtyWiseIDs')

            // FIXED: removed ->where('project_status', 1)
            ->with(['projectProcessStdTimes' => function($q) {
                $q->select('id', 'projects_id', 'product_id', 'order_qty', 'project_status', 'project_process_name', 'operators_time_tracking', 'timer_started_at');
            }])

            ->whereHas('projectProcessStdTimes', function($q) {
                $q->select('product_id', 'order_qty')
                    ->groupBy('product_id', 'order_qty')
                    ->havingRaw('COUNT(*) = SUM(CASE WHEN project_status = 1 THEN 1 ELSE 0 END)');
            })
            ->orderBy('id', 'desc'); 

        // Apply parent-level filters
        foreach ($filters as $key => $values) {
            if (empty($values)) continue;
            switch ($key) {
                // Direct parent-table filters
                case 'filter_col_1':
                    $dates = array_map(function($date){
                        return \Carbon\Carbon::createFromFormat('d-m-Y', $date)->format('Y-m-d');
                    }, $values);
                    $query->whereHas('projectProcessStdTimes', function ($q) use ($dates) {
                        $q->whereIn(DB::raw("DATE(timer_started_at)"), $dates);
                    });
                    break;
                case 'filter_col_4': $query->whereIn('full_article_number', $values); break;
                case 'filter_col_5': $query->whereIn('description', $values); break;
                case 'filter_col_6': $query->whereIn('product_type', $values); break;
                case 'filter_col_7': $query->whereIn('qty', $values); break;             
                case 'filter_col_8':
                    $qtyFilters = array_map('intval', $values); // ensure integers
                    $query->whereHas('projectProcessStdTimes', function ($q) use ($qtyFilters) {
                        $q->select('product_id')
                            ->groupBy('product_id')
                            ->havingRaw("
                                SUM(CASE WHEN project_status = 1 THEN 1 ELSE 0 END) = COUNT(*)
                            ")
                            ->havingRaw("COUNT(DISTINCT order_qty) IN (".implode(',', $qtyFilters).")");
                    });
                    break;               
                case 'filter_col_9':
                    $operatorCounts = array_map('intval', $values); // ensure integers
                    $query->whereHas('projectProcessStdTimes', function ($q) use ($operatorCounts) {
                        $q->select('product_id')
                            ->groupBy('product_id')
                            ->havingRaw("
                                (
                                    SELECT COUNT(DISTINCT JSON_EXTRACT(op.value, '$.id'))
                                    FROM project_process_std_time ppt2,
                                        JSON_TABLE(
                                            ppt2.operators_time_tracking,
                                            '$[*]' COLUMNS (
                                                value JSON PATH '$'
                                            )
                                        ) AS op
                                    WHERE ppt2.product_id = project_process_std_time.product_id
                                ) IN (".implode(',', $operatorCounts).")
                            ");
                    });
                    break;  
                case 'filter_col_10':
                    $wiloCounts = array_map('intval', $values); // ensure integers
                    $query->whereHas('projectProcessStdTimes', function ($q) use ($wiloCounts) {
                        $q->select('product_id')
                            ->groupBy('product_id')
                            ->havingRaw("
                                (
                                    SELECT COUNT(DISTINCT u.id)
                                    FROM project_process_std_time ppt2
                                    JOIN JSON_TABLE(
                                        ppt2.operators_time_tracking,
                                        '$[*]' COLUMNS (
                                            op_id INT PATH '$.id'
                                        )
                                    ) jt ON TRUE
                                    JOIN users u ON u.id = jt.op_id AND u.role = 'Wilo Operator'
                                    WHERE ppt2.product_id = project_process_std_time.product_id
                                ) IN (".implode(',', $wiloCounts).")
                            ");
                    });
                    break;              
                case 'filter_col_11':
                    $thirdPartyCounts = array_map('intval', $values); // ensure integers
                    $query->whereHas('projectProcessStdTimes', function ($q) use ($thirdPartyCounts) {
                        $q->select('product_id')
                            ->groupBy('product_id')
                            ->havingRaw("
                                (
                                    SELECT COUNT(DISTINCT u.id)
                                    FROM project_process_std_time ppt2
                                    JOIN JSON_TABLE(
                                        ppt2.operators_time_tracking,
                                        '$[*]' COLUMNS (
                                            op_id INT PATH '$.id'
                                        )
                                    ) jt ON TRUE
                                    JOIN users u 
                                        ON u.id = jt.op_id 
                                        AND u.role = '3rd Party Operator'
                                    WHERE ppt2.product_id = project_process_std_time.product_id
                                ) IN (".implode(',', $thirdPartyCounts).")
                            ");
                    });
                    break;
                case 'filter_col_12':
                    $hoursFilters = array_map('floatval', $values); // ensure floats
                    $query->whereHas('projectProcessStdTimes', function ($q) use ($hoursFilters) {
                        $q->select('product_id')
                            ->groupBy('product_id')
                            ->havingRaw("
                                ROUND(
                                    COALESCE(
                                        (
                                            SELECT SUM(jt.total_time) / 60
                                            FROM project_process_std_time ppt2
                                            JOIN JSON_TABLE(
                                                ppt2.operators_time_tracking,
                                                '$[*]' COLUMNS (
                                                    total_time INT PATH '$.total_time'
                                                )
                                            ) jt
                                            WHERE ppt2.product_id = project_process_std_time.product_id
                                        ),
                                        0
                                    ), 2
                                ) IN (".implode(',', $hoursFilters).")
                            ");
                    });
                    break;                  
                case 'filter_col_13':
                    $hoursFilters = array_map('floatval', $values); // ensure floats
                    $query->whereRaw("
                        ROUND(
                            COALESCE(
                                (
                                    SELECT SUM(jt.total_time)/60
                                    FROM project_process_std_time ppt2
                                    JOIN JSON_TABLE(
                                        ppt2.operators_time_tracking,
                                        '$[*]' COLUMNS (
                                            op_id INT PATH '$.id',
                                            total_time INT PATH '$.total_time'
                                        )
                                    ) jt ON TRUE
                                    JOIN users u 
                                        ON u.id = jt.op_id 
                                        AND u.role = 'Wilo Operator'
                                    WHERE ppt2.product_id = products_of_projects.id
                                ),
                                0
                            ), 2
                        ) IN (".implode(',', $hoursFilters).")
                    ");
                    break;          
                case 'filter_col_14':
                    $hoursFilters = array_map('floatval', $values); // ensure floats
                    $query->whereRaw("
                        ROUND(
                            COALESCE(
                                (
                                    SELECT SUM(jt.total_time)/60
                                    FROM project_process_std_time ppt2
                                    JOIN JSON_TABLE(
                                        ppt2.operators_time_tracking,
                                        '$[*]' COLUMNS (
                                            op_id INT PATH '$.id',
                                            total_time INT PATH '$.total_time'
                                        )
                                    ) jt ON TRUE
                                    JOIN users u 
                                        ON u.id = jt.op_id 
                                        AND u.role = '3rd Party Operator'
                                    WHERE ppt2.product_id = products_of_projects.id
                                ),
                                0
                            ), 2
                        ) IN (".implode(',', $hoursFilters).")
                    ");
                    break;
                case 'filter_col_15':
                    $unitHoursFilters = array_map('floatval', $values); // ensure floats
                    $query->whereRaw("
                        EXISTS (
                            SELECT 1
                            FROM project_process_std_time ppt
                            WHERE ppt.product_id = products_of_projects.id

                            -- Completed qty check (all statuses = 1 for that order_qty)
                            AND (
                                SELECT SUM(CASE WHEN project_status = 1 THEN 1 ELSE 0 END)
                                FROM project_process_std_time s1
                                WHERE s1.product_id = ppt.product_id
                                AND s1.order_qty = ppt.order_qty
                            ) = (
                                SELECT COUNT(*)
                                FROM project_process_std_time s2
                                WHERE s2.product_id = ppt.product_id
                                AND s2.order_qty = ppt.order_qty
                            )
                            -- Ratio: ROUND( TotalManHours / CompletedQtyDistinctOrderQty, 2 )
                            AND ROUND(
                                (
                                    (
                                        SELECT SUM(jt.total_time) / 60
                                        FROM project_process_std_time ppt2
                                        JOIN JSON_TABLE(
                                            ppt2.operators_time_tracking,
                                            '$[*]' COLUMNS (
                                                total_time INT PATH '$.total_time'
                                            )
                                        ) jt
                                        WHERE ppt2.product_id = ppt.product_id
                                    )
                                    /
                                    NULLIF(
                                        (
                                            SELECT COUNT(DISTINCT order_qty)
                                            FROM project_process_std_time ppt3
                                            WHERE ppt3.product_id = ppt.product_id
                                            AND (
                                                SELECT SUM(CASE WHEN project_status = 1 THEN 1 ELSE 0 END)
                                                FROM project_process_std_time ppt4
                                                WHERE ppt4.product_id = ppt3.product_id
                                                AND ppt4.order_qty = ppt3.order_qty
                                            ) = (
                                                SELECT COUNT(*)
                                                FROM project_process_std_time ppt5
                                                WHERE ppt5.product_id = ppt3.product_id
                                                AND ppt5.order_qty = ppt3.order_qty
                                            )
                                        ),
                                        0
                                    )
                                ),
                                2
                            ) IN (".implode(',', $unitHoursFilters).")
                        )
                    ");
                    break;

                
                    // All child-table filters handled below
                default:
                    $childColumnMap = [
                        'filter_col_2'  => 'project_no',
                        'filter_col_3'  => 'project_name'
                    ];
                    if (isset($childColumnMap[$key])) {
                        $column = $childColumnMap[$key];
                        $hasEmpty = in_array('__EMPTY__', $values);
                        $nonEmptyValues = array_filter($values, fn($v) => $v !== '__EMPTY__');
                        $query->whereHas('projects', function ($q) use ($column, $nonEmptyValues, $hasEmpty) {
                            $q->where(function ($sub) use ($column, $nonEmptyValues, $hasEmpty) {
                                if (!empty($nonEmptyValues)) {
                                    $sub->whereIn($column, $nonEmptyValues);
                                }
                                if ($hasEmpty) {
                                    $sub->orWhereNull($column)->orWhere($column, '');
                                }
                            });
                        });
                    }
                    break;
            }
        }

        // A Code: 26-12-2025 Start
        $query->whereHas('projects', function ($q) {
            $q->where('is_deleted', 0);
        });
        // A Code: 26-12-2025 End

        $trackingOperators = $query->get();          

        return view('production_superwisor.OperatorTracking.index', compact(
            'page_title', 
            'trackingOperators', 
            'filters', 
            'trackingOperatorsfilter', 
            'last_filter_column'
        ));
    }

    public function exportCSV(Request $request){
        $filters = $request->input('filters', []); 
        // Load only matching child rows
        $query = ProductsOfProjects::with(['projects' => function ($q) use ($filters) {
            $q->orderBy('id', 'desc');
            $columnMap = [
                'filter_col_2'  => 'project_no',
                'filter_col_3'  => 'project_name'
            ];
            foreach ($columnMap as $filterKey => $dbColumn) {
                if (!empty($filters[$filterKey])) {
                    $filterValues = $filters[$filterKey];

                    $hasEmpty = in_array('__EMPTY__', $filterValues);
                    $nonEmptyValues = array_filter($filterValues, fn($v) => $v !== '__EMPTY__');

                    $q->where(function ($subQuery) use ($dbColumn, $hasEmpty, $nonEmptyValues) {
                        if (!empty($nonEmptyValues)) {
                            $subQuery->whereIn($dbColumn, $nonEmptyValues);
                        }

                        if ($hasEmpty) {
                            $subQuery->orWhereNull($dbColumn)->orWhere($dbColumn, '');
                        }
                    });
                }
            }

        }])
        ->with('assignedProducts')
        ->with('assignedOperatorProductQtyWiseIDs')
        ->with(['projectProcessStdTimes' => function ($q) use ($filters) {
            $q->where('project_status', 1)->orderBy('id', 'desc');

            $columnMap = [
                'filter_col_1'  => 'operators_time_tracking'
            ];
            foreach ($columnMap as $filterKey => $dbColumn) {
                if (!empty($filters[$filterKey])) {
                    $filterValues = $filters[$filterKey];
                    $hasEmpty = in_array('__EMPTY__', $filterValues);
                    $nonEmptyValues = array_filter($filterValues, fn($v) => $v !== '__EMPTY__');
                    $q->where(function ($subQuery) use ($dbColumn, $hasEmpty, $nonEmptyValues) {
                        if (!empty($nonEmptyValues)) {
                            $subQuery->whereIn($dbColumn, $nonEmptyValues);
                        }
                        if ($hasEmpty) {
                            $subQuery->orWhereNull($dbColumn)->orWhere($dbColumn, '');
                        }
                    });
                }
            }
        }])
        ->whereHas('projectProcessStdTimes', function($q) {
            $q->select('product_id','order_qty')
                ->groupBy('product_id','order_qty')
                ->havingRaw('COUNT(*) = SUM(CASE WHEN project_status = 1 THEN 1 ELSE 0 END)');
            })
        ->orderBy('id', 'desc'); 
        // Apply parent-level filters
        foreach ($filters as $key => $values) {
            if (empty($values)) continue;
            switch ($key) {
                // Direct parent-table filters
                case 'filter_col_1':
                $dates = array_map(function($date){
                    return \Carbon\Carbon::createFromFormat('d-m-Y', $date)->format('Y-m-d');
                }, $values);
                $query->whereHas('projectProcessStdTimes', function ($q) use ($dates) {
                    $q->whereIn(DB::raw("DATE(timer_started_at)"), $dates);
                });
                break;
                case 'filter_col_4': $query->whereIn('full_article_number', $values); break;
                case 'filter_col_5': $query->whereIn('description', $values); break;
                case 'filter_col_6': $query->whereIn('product_type', $values); break;
                case 'filter_col_7': $query->whereIn('qty', $values); break;
                case 'filter_col_8':
                    $qtyFilters = array_map('intval', $values); // ensure integers
                    $query->whereHas('projectProcessStdTimes', function ($q) use ($qtyFilters) {
                        $q->select('product_id')
                            ->groupBy('product_id')
                            ->havingRaw("
                                SUM(CASE WHEN project_status = 1 THEN 1 ELSE 0 END) = COUNT(*)
                            ")
                            ->havingRaw("COUNT(DISTINCT order_qty) IN (".implode(',', $qtyFilters).")");
                    });
                    break;               
                case 'filter_col_9':
                    $operatorCounts = array_map('intval', $values); // ensure integers
                    $query->whereHas('projectProcessStdTimes', function ($q) use ($operatorCounts) {
                        $q->select('product_id')
                            ->groupBy('product_id')
                            ->havingRaw("
                                (
                                    SELECT COUNT(DISTINCT JSON_EXTRACT(op.value, '$.id'))
                                    FROM project_process_std_time ppt2,
                                        JSON_TABLE(
                                            ppt2.operators_time_tracking,
                                            '$[*]' COLUMNS (
                                                value JSON PATH '$'
                                            )
                                        ) AS op
                                    WHERE ppt2.product_id = project_process_std_time.product_id
                                ) IN (".implode(',', $operatorCounts).")
                            ");
                    });
                    break;  
                case 'filter_col_10':
                    $wiloCounts = array_map('intval', $values); // ensure integers
                    $query->whereHas('projectProcessStdTimes', function ($q) use ($wiloCounts) {
                        $q->select('product_id')
                            ->groupBy('product_id')
                            ->havingRaw("
                                (
                                    SELECT COUNT(DISTINCT u.id)
                                    FROM project_process_std_time ppt2
                                    JOIN JSON_TABLE(
                                        ppt2.operators_time_tracking,
                                        '$[*]' COLUMNS (
                                            op_id INT PATH '$.id'
                                        )
                                    ) jt ON TRUE
                                    JOIN users u ON u.id = jt.op_id AND u.role = 'Wilo Operator'
                                    WHERE ppt2.product_id = project_process_std_time.product_id
                                ) IN (".implode(',', $wiloCounts).")
                            ");
                    });
                    break;              
                case 'filter_col_11':
                    $thirdPartyCounts = array_map('intval', $values); // ensure integers
                    $query->whereHas('projectProcessStdTimes', function ($q) use ($thirdPartyCounts) {
                        $q->select('product_id')
                            ->groupBy('product_id')
                            ->havingRaw("
                                (
                                    SELECT COUNT(DISTINCT u.id)
                                    FROM project_process_std_time ppt2
                                    JOIN JSON_TABLE(
                                        ppt2.operators_time_tracking,
                                        '$[*]' COLUMNS (
                                            op_id INT PATH '$.id'
                                        )
                                    ) jt ON TRUE
                                    JOIN users u 
                                        ON u.id = jt.op_id 
                                        AND u.role = '3rd Party Operator'
                                    WHERE ppt2.product_id = project_process_std_time.product_id
                                ) IN (".implode(',', $thirdPartyCounts).")
                            ");
                    });
                    break;                    
                case 'filter_col_12':
                    $hoursFilters = array_map('floatval', $values); // ensure floats
                    $query->whereHas('projectProcessStdTimes', function ($q) use ($hoursFilters) {
                        $q->select('product_id')
                            ->groupBy('product_id')
                            ->havingRaw("
                                ROUND(
                                    COALESCE(
                                        (
                                            SELECT SUM(jt.total_time) / 60
                                            FROM project_process_std_time ppt2
                                            JOIN JSON_TABLE(
                                                ppt2.operators_time_tracking,
                                                '$[*]' COLUMNS (
                                                    total_time INT PATH '$.total_time'
                                                )
                                            ) jt
                                            WHERE ppt2.product_id = project_process_std_time.product_id
                                        ),
                                        0
                                    ), 2
                                ) IN (".implode(',', $hoursFilters).")
                            ");
                    });
                    break;
                case 'filter_col_13':
                    $hoursFilters = array_map('floatval', $values); // ensure floats
                    $query->whereRaw("
                        ROUND(
                            COALESCE(
                                (
                                    SELECT SUM(jt.total_time)/60
                                    FROM project_process_std_time ppt2
                                    JOIN JSON_TABLE(
                                        ppt2.operators_time_tracking,
                                        '$[*]' COLUMNS (
                                            op_id INT PATH '$.id',
                                            total_time INT PATH '$.total_time'
                                        )
                                    ) jt ON TRUE
                                    JOIN users u 
                                        ON u.id = jt.op_id 
                                        AND u.role = 'Wilo Operator'
                                    WHERE ppt2.product_id = products_of_projects.id
                                ),
                                0
                            ), 2
                        ) IN (".implode(',', $hoursFilters).")
                    ");
                    break;              
                case 'filter_col_14':
                    $hoursFilters = array_map('floatval', $values); // ensure floats
                    $query->whereRaw("
                        ROUND(
                            COALESCE(
                                (
                                    SELECT SUM(jt.total_time)/60
                                    FROM project_process_std_time ppt2
                                    JOIN JSON_TABLE(
                                        ppt2.operators_time_tracking,
                                        '$[*]' COLUMNS (
                                            op_id INT PATH '$.id',
                                            total_time INT PATH '$.total_time'
                                        )
                                    ) jt ON TRUE
                                    JOIN users u 
                                        ON u.id = jt.op_id 
                                        AND u.role = '3rd Party Operator'
                                    WHERE ppt2.product_id = products_of_projects.id
                                ),
                                0
                            ), 2
                        ) IN (".implode(',', $hoursFilters).")
                    ");
                    break;
                case 'filter_col_15':
                    $unitHoursFilters = array_map('floatval', $values); // ensure floats
                    $query->whereRaw("
                        EXISTS (
                            SELECT 1
                            FROM project_process_std_time ppt
                            WHERE ppt.product_id = products_of_projects.id

                            -- Completed qty check (all statuses = 1 for that order_qty)
                            AND (
                                SELECT SUM(CASE WHEN project_status = 1 THEN 1 ELSE 0 END)
                                FROM project_process_std_time s1
                                WHERE s1.product_id = ppt.product_id
                                AND s1.order_qty = ppt.order_qty
                            ) = (
                                SELECT COUNT(*)
                                FROM project_process_std_time s2
                                WHERE s2.product_id = ppt.product_id
                                AND s2.order_qty = ppt.order_qty
                            )
                            -- Ratio: ROUND( TotalManHours / CompletedQtyDistinctOrderQty, 2 )
                            AND ROUND(
                                (
                                    (
                                        SELECT SUM(jt.total_time) / 60
                                        FROM project_process_std_time ppt2
                                        JOIN JSON_TABLE(
                                            ppt2.operators_time_tracking,
                                            '$[*]' COLUMNS (
                                                total_time INT PATH '$.total_time'
                                            )
                                        ) jt
                                        WHERE ppt2.product_id = ppt.product_id
                                    )
                                    /
                                    NULLIF(
                                        (
                                            SELECT COUNT(DISTINCT order_qty)
                                            FROM project_process_std_time ppt3
                                            WHERE ppt3.product_id = ppt.product_id
                                            AND (
                                                SELECT SUM(CASE WHEN project_status = 1 THEN 1 ELSE 0 END)
                                                FROM project_process_std_time ppt4
                                                WHERE ppt4.product_id = ppt3.product_id
                                                AND ppt4.order_qty = ppt3.order_qty
                                            ) = (
                                                SELECT COUNT(*)
                                                FROM project_process_std_time ppt5
                                                WHERE ppt5.product_id = ppt3.product_id
                                                AND ppt5.order_qty = ppt3.order_qty
                                            )
                                        ),
                                        0
                                    )
                                ),
                                2
                            ) IN (".implode(',', $unitHoursFilters).")
                        )
                    ");
                    break;

                // All child-table filters handled below
                default:
                    $childColumnMap = [
                        'filter_col_2'  => 'project_no',
                        'filter_col_3'  => 'project_name'
                    ];
                    
                    if (isset($childColumnMap[$key])) {
                        $column = $childColumnMap[$key];

                        $hasEmpty = in_array('__EMPTY__', $values);
                        $nonEmptyValues = array_filter($values, fn($v) => $v !== '__EMPTY__');
                        $query->whereHas('projects', function ($q) use ($column, $nonEmptyValues, $hasEmpty) {
                            $q->where(function ($sub) use ($column, $nonEmptyValues, $hasEmpty) {
                                if (!empty($nonEmptyValues)) {
                                    $sub->whereIn($column, $nonEmptyValues);
                                }

                                if ($hasEmpty) {
                                    $sub->orWhereNull($column)->orWhere($column, '');
                                }
                            });
                        });
                    }
                    break;
            }
        }    

        // A Code: 26-12-2025 Start
        $query->whereHas('projects', function ($q) {
            $q->where('is_deleted', 0);
        });
        // A Code: 26-12-2025 End
        
        // Final query
        $trackingOperators = $query->get();
        // Export CSV Code
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d_H-i-s');
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="operator_filter_export_' . $timestamp . '.csv"',
        ];      
        $columns = ['SR No', 'Month/Year', 'Project No.', 'Project Name', 'Product Article No.', 'Product Description', 'Product Type', 'Total Product Qty', 'Completed Product Qty', 'Qty of Total Operators', 'Qty Wilo Operators', 'Qty 3rd Party Operators', 'Total Man hours', 'Total Wilo Operator Hours', 'Total 3rd Party Operators Hours', 'Total Hours/Unit'];
        $callback = function () use ($trackingOperators, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            $sr_no = 1;
            foreach ($trackingOperators as $val) {
                $colSrNo = $sr_no++;                
                $minStartDate = optional($val->projectProcessStdTimes->first())->timer_started_at;
                $colMonthYear = $minStartDate ? \Carbon\Carbon::parse($minStartDate)->format('d-m-Y') : '-';
                $colProjectNo = '="' . ($val->projects['project_no'] ?? 'N/A') . '"'; // Excel-safe
                $colProjectName = $val->projects['project_name'] ?? 'N/A';
                $colProductArticleNo = $val->full_article_number ?? 'N/A';
                $colProductDescription = $val->description ?? 'N/A';
                $colProductType = $val->product_type ?? 'N/A';
                $colTotalProductQty = $val->qty;
                $colCompletedProductQty = $val->projectProcessStdTimes->groupBy('order_qty')->count();
                $operatorIds = $val->projectProcessStdTimes->pluck('operators_time_tracking')
                                        ->filter()
                                        ->map(fn($json) => json_decode($json, true))
                                        ->filter(fn($arr) => is_array($arr))
                                        ->flatten(1)
                                        ->pluck('id')
                                        ->unique();
                $completed_qty = $val->projectProcessStdTimes->groupBy('order_qty')->count(); 
                $colQtyofTotalOperators = $operatorIds->count();      
                $colQtyWiloOperators = \App\Models\User::whereIn('id', $operatorIds)->where('role', 'Wilo Operator')->count();
                $colQty3rdPartyOperators = \App\Models\User::whereIn('id', $operatorIds)->where('role', '3rd Party Operator')->count();

                $operatorTimes = $val->projectProcessStdTimes->pluck('operators_time_tracking')
                                        ->filter()
                                        ->map(fn($json) => json_decode($json, true))
                                        ->filter(fn($arr) => is_array($arr))
                                        ->flatten(1);
                $totalHours = $operatorTimes->sum('total_time') / 60;

                $wiloHours = $operatorTimes->filter(function($op){
                                        return \App\Models\User::where('id', $op['id'])->where('role', 'Wilo Operator')->exists();
                                    })->sum('total_time') / 60;

                $thirdPartyHours = $operatorTimes->filter(function($op){
                                        return \App\Models\User::where('id', $op['id'])->where('role', '3rd Party Operator')->exists();
                                    })->sum('total_time') / 60;

                $colTotalManhours = number_format($totalHours, 2) . " HRS";
                $colTotalWiloOperatorHours = number_format($wiloHours, 2) . " HRS";
                $colTotal3rdPartyOperatorsHours = number_format($thirdPartyHours, 2) . " HRS";

                $colTotalHoursUnit = ($completed_qty > 0 
                                        ? number_format($totalHours / $completed_qty, 2)
                                        : 0) . " HRS/Unit";
                fputcsv($file, [
                    $colSrNo,
                    $colMonthYear,
                    $colProjectNo,
                    $colProjectName,
                    $colProductArticleNo,
                    $colProductDescription,
                    $colProductType,
                    $colTotalProductQty,
                    $colCompletedProductQty,
                    $colQtyofTotalOperators,
                    $colQtyWiloOperators,
                    $colQty3rdPartyOperators,
                    $colTotalManhours,
                    $colTotalWiloOperatorHours,
                    $colTotal3rdPartyOperatorsHours,
                    $colTotalHoursUnit
                ]);
            }
            fclose($file);
        };
        return Response::stream($callback, 200, $headers);
    }
}
