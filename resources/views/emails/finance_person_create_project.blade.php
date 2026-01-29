<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request for Finance Approval</title>
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            💬 Request for Finance Approval
        </div>
        <div class="content">
            <h3>Project Create Request Details</h3>
            <p><span class="icon">🏗️</span><strong>Project Name:</strong> {{ $formData['project_name'] }}</p>
            <p><span class="icon">📄</span><strong>Assembly Quotation No.:</strong> {{ $formData['assembly_quotation_ref'] }}</p>
            <p><span class="icon">🤝</span><strong>Sales Name:</strong> {{ $formData['sales_name'] }}</p>
            <p><span class="icon">👥</span><strong>Customer Name:</strong> {{ $formData['customer_name'] }}</p>
            <p><span class="icon">🌍</span><strong>Country:</strong> {{ $formData['country'] }}</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} WIP Tracker. All rights reserved.
        </div>
    </div>
</body>
</html>
