<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MRF Notification - {{ $emailData['project_no'] }} - {{ $emailData['project_name'] }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 20px;
            color: #333;
        }

        .container {
            border: 2px #169e88 solid;
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            padding: 0;
        }

        .header {
            background-color: #169e88;
            color: #fff;
            padding: 15px;
            text-align: center;
            font-size: 1.5em;
        }

        .content {
            padding: 20px;
        }

        h3 {
            color: #169e88;
        }

        p {
            font-size: 1em;
            line-height: 1.6;
            color: #555;
            margin: 10px 0;
        }

        .footer {
            text-align: center;
            padding: 15px;
            background-color: #f1f1f1;
            font-size: 0.85em;
            color: #aaa;
        }

        .icon {
            font-size: 1.1em;
            color: #28a745;
            margin-right: 8px;
        }

        .link {
            display: inline-block;
            padding: 10px 20px;
            background-color: #169e88;
            color: white !important;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            📋 MRF Notification - {{ $emailData['project_no'] }} - {{ $emailData['project_name'] }}
        </div>
        <div class="content">
            <h3>Hi {{$emailData['recipient_name'] }},</h3>

            <!-- <p>Please check the attached MRF for the following project:</p> -->

            <p>Please check the attached <strong>MRF document (Full or Partial)</strong> for the following project:</p>

            <h3>Project Details</h3>
            <p><span class="icon">🏗️</span><strong>Project Number:</strong> {{ $emailData['project_no'] }}</p>
            <p><span class="icon">📄</span><strong>Project Name:</strong> {{ $emailData['project_name'] }}</p>
            <p><span class="icon">🔩</span><strong>Product Description:</strong> {{ $emailData['description'] }}</p>
            <p><span class="icon">📦</span><strong>Article Number:</strong> {{ $emailData['full_article_number'] }}</p>

            <p>Kindly handover the materials to Production as per the attached MRF document.</p>

            <p><span class="icon"> 👉 </span><strong></strong>Click the button below once the materials for this product are ready.</p>

            <p>
                <a href="{{ url('/production-superwisor/mark-materials-ready?product_id=' . $emailData['product_id'] . '&project_id=' . $emailData['project_id'] . '&batch=' . $emailData['batch'] . '&email=' . urlencode($emailData['recipient_email'])) }}" class="link">
                    Materials Ready
                </a>
            </p>
        </div>
        <div class="footer">
            © {{ date('Y') }} WIP Tracker. All rights reserved.
        </div>
    </div>
</body>

</html>