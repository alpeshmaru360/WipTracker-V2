<?php

namespace App\Http\Controllers\QRCode;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

use Illuminate\Support\Facades\Storage;

class QrCodeController extends Controller
{
   public function download(Request $request, $text)
    {
        // Generate the QR code
        $qrCode = QrCode::size(200)
            ->margin(10)
            ->encoding('UTF-8')
            ->generate($text);

        // Store the QR code image on the server
        $filename = 'qrcode.png';
        Storage::disk('public')->put($filename, $qrCode);

        // Create a response with the QR code image
        $response = response()->download(storage_path('app/public/' . $filename), $filename, [
            'Content-Type' => 'image/png',
        ]);

        // Return the response
        return $response;
    }
}
