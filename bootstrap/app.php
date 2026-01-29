<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

require __DIR__.'/../vendor/autoload.php';

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->validateCsrfTokens(except: [
            'api/get_wiTrack_project_details',
            'api/notify_wiTrack_project_cancelled',
            'api/get_wiTrack_po_details',
            'api/sendProjectCompleteMsgToWitrack',
            'api/sendProjectFullCompleteMsgToWitrack',
            'api/sendProjectPartialCompleteMsgToWitrack',
            'api/reject-order-pro-eng',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Handle exceptions if needed
    })
    ->create();