<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class OperatorTimeTracking extends Model
{
    use HasFactory;

    protected $table = 'operators_time_tracking';

    protected $fillable = [
        'process_id',
        'operator_id', 
        'status',
        'session_start',
        'session_end',
        'total_seconds'
    ];

    protected $casts = [
        'session_start' => 'datetime',
        'session_end' => 'datetime',
        'total_seconds' => 'integer'
    ];

    // Relationship to process
    public function process(){
        return $this->belongsTo(ProjectProcessStdTime::class, 'process_id');
    }

    // Calculate current elapsed time
    public function getCurrentElapsedTime(){
        if ($this->status === 'running' && $this->session_start) { 
            $sessionTime = $this->session_start->diffInSeconds(now(), false);
            return $this->total_seconds + max(0, $sessionTime);
        }
        return $this->total_seconds;
    }

    // Format time for display
    public function getFormattedTime(){
        $totalSeconds = max(0, $this->getCurrentElapsedTime()); // never negative

        $hours = floor($totalSeconds / 3600);
        $minutes = floor(($totalSeconds % 3600) / 60);
        $seconds = $totalSeconds % 60;
        
        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }

}