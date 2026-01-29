<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductProcessPausedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $uniqueId;
    public $projectId;
    public $productId;
    public $orderQty;
    public $remainingTime;

    public function __construct($uniqueId,$projectId, $productId, $orderQty, $remainingTime)
    {
        $this->uniqueId = $uniqueId;
        $this->projectId = $projectId;
        $this->productId = $productId;
        $this->orderQty = $orderQty;
        $this->remainingTime = $remainingTime;
    }

    public function broadcastOn()
    {
         return new Channel('timer.project.' . $this->projectId . '.product.' . $this->productId . '.order.' . $this->orderQty);
    }

    public function broadcastAs()
    {
        return 'timer.paused';
    }
}