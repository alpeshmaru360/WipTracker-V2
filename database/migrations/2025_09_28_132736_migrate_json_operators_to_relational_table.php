<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\ProjectProcessStdTime;
use App\Models\OperatorTimeTracking;

return new class extends Migration
{
    public function up()
    {
        // Migrate existing JSON data to new table structure
        ProjectProcessStdTime::whereNotNull('operators_time_tracking')
            ->chunk(100, function ($processes) {
                foreach ($processes as $process) {
                    $operatorData = json_decode($process->operators_time_tracking, true);
                    
                    if (is_array($operatorData)) {
                        foreach ($operatorData as $operatorId => $data) {
                            OperatorTimeTracking::updateOrCreate(
                                [
                                    'process_id' => $process->id,
                                    'operator_id' => $operatorId
                                ],
                                [
                                    'status' => $data['status'] ?? 'stopped',
                                    'total_seconds' => $data['elapsedTime'] ?? 0,
                                    'session_start' => isset($data['startTime']) ? 
                                        \Carbon\Carbon::parse($data['startTime']) : null,
                                    'last_activity' => now()
                                ]
                            );
                        }
                    }
                }
            });
    }

    public function down()
    {
        // Optionally restore JSON data if needed
        OperatorTimeTracking::truncate();
    }
};