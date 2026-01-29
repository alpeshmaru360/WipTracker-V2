<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MRF Response - WIP Tracker</title>
    <link href="{{ asset('css/product_superwisor.css') }}" rel="stylesheet" />
</head>

<body class="materials_ready_response_page">
    <div class="container">
        <div class="header">
            📋 MRF Response
        </div>
        <div class="content">
            <p class="{{ $status }}">{{ $message }}</p>
            @if($status === 'success')
            <p>Thank you for confirming that the materials are ready for production.</p>
            @elseif($status === 'already_responded')
            @else
            <p>Please contact the administrator if this issue persists.</p>
            @endif
        </div>
        <div class="footer">
            © {{ date('Y') }} WIP Tracker. All rights reserved.
        </div>
    </div>
</body>

</html>