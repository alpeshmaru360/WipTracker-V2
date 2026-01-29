<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('project.{projectId}.{productId}.{orderQty}', function ($user, $projectId, $productId, $orderQty) {
    return true;
});

// Broadcast::channel('project.{projectId}.{orderQty}', function ($user, $projectId, $orderQty) {
//     // Check if the user has permission to access this project and order
//     return $user->canAccessProject($projectId, $orderQty);
// });
