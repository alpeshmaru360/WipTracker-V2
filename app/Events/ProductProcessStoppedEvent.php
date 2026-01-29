<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductProcessStoppedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $uniqueId;
    public $projectId;
    public $productId;
    public $orderQty;
    public $project_actual_time;
    public $project_status;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($uniqueId,$projectId, $productId, $orderQty, $project_actual_time, $project_status)
    {
        $this->uniqueId = $uniqueId;
        $this->projectId = $projectId;
        $this->productId = $productId;
        $this->orderQty = $orderQty;
        $this->project_actual_time = $project_actual_time;
        $this->project_status = $project_status;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('timer.project.' . $this->projectId . '.product.' . $this->productId . '.order.' . $this->orderQty);
    }


    public function broadcastAs()
    {
        return 'timer.stopped';
    }

    public function broadcastWith()
    {
        return [
            'uniqueId' => $this->uniqueId,
            'projectId' => $this->projectId,
            'productId' => $this->productId,
            'orderQty' => $this->orderQty,
            'actualTime' => $this->project_actual_time,
            'status' => $this->project_status
        ];
    }
}