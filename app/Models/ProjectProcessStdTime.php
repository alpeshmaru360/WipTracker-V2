<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ProjectProcessStdTime extends Model
{
    use HasFactory;
    protected $table = 'project_process_std_time';

    protected $fillable = [
        'product_id',
        'projects_id', 
        'timer_status',
        'elapsed_time',
        'timer_current_time',
        'timer_ends_at',
        'project_status',
        'project_actual_time',
        'operators_time_tracking'
    ];

    public function product(){
        return $this->belongsTo(ProductsOfProjects::class, 'product_id', 'id');
    }
    public function project(){
        return $this->belongsTo(Project::class, 'projects_id', 'id');
    }
    public function calculateElapsedTime(){
        if ($this->timer_status == "completed") {
            return $this->elapsed_time ?? 0;
        }

        $storedElapsedTime = $this->elapsed_time ?? 0;

        // If timer is paused, just return the stored elapsed time
        if ($this->timer_status == "paused") {
            return $storedElapsedTime;
        }

        // If timer is not running or no current time is set, return stored elapsed time
        if ($this->timer_status !== 'running' || !$this->timer_current_time) {
            return $storedElapsedTime;
        }

        // Calculate additional elapsed time since timer was started/resumed
        $timerStartedAt = new \DateTime($this->timer_current_time);
        $now = new \DateTime(); // Current time
        $currentSessionElapsed = $now->getTimestamp() - $timerStartedAt->getTimestamp();

        // Return total elapsed time (stored + current session)
        return $storedElapsedTime + max(0, $currentSessionElapsed);
    }
    public function calculateRemainingTime(){
        // Convert standard time from hours to seconds
        $totalTime = $this->process_std_time * 3600;

        // Get current elapsed time
        $elapsedTime = $this->calculateElapsedTime();

        // Return remaining time (total - elapsed)
        return max(0, $totalTime - $elapsedTime);
    }
    public function operatorTrackings(){
        return $this->hasMany(OperatorTimeTracking::class, 'process_id');
    }
    public function getActiveOperators(){
        return $this->operatorTrackings()
            ->where('status', 'running')
            ->pluck('operator_id')
            ->toArray();
    }
    public function latestStatusForOperator(int $operatorId): ?string{
        $row = $this->operatorTrackings()
            ->where('operator_id', $operatorId)
            ->orderBy('updated_at', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        return $row?->status; // 'running' | 'paused' | 'stopped' | null
    }
}
