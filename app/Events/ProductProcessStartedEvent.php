<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductProcessStartedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    // public $channel;
    public $uniqueId;
    public $projectId;
    public $productId;
    public $orderQty;
    public $processName;
    public $startTime;
    // public $endTime;
    // public $remainingTime;

    //public function __construct($uniqueId,$projectId,$productId,$orderQty,$processName,$startTime,$endTime, $remainingTime = null)
    public function __construct($uniqueId,$projectId,$productId,$orderQty,$processName,$startTime)
    {
        $this->uniqueId = $uniqueId;
        $this->projectId = $projectId;
        $this->productId = $productId;
        $this->processName = $processName;
        $this->startTime = $startTime;
        $this->orderQty = $orderQty;
        // $this->endTime = $endTime;
        // $this->remainingTime = $remainingTime;
    }

    public function broadcastOn()
    {
        return new Channel('timer.project.' . $this->projectId . '.product.' . $this->productId . '.order.' . $this->orderQty);

    }

    public function broadcastAs()
    {
        // return 'process.started';
        return 'timer.started';
    }
}
