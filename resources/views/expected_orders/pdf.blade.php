<!DOCTYPE html>
<html>

<head>
    <title>Expected Orders</title>
    <style>
        /* Add your styles here */
        body {font-family: Arial, sans-serif;}
        table {width: 100%;border-collapse: collapse;}
        th,
        td {border: 1px solid #000;padding: 8px;text-align: left;}
        th {background-color: #f2f2f2;}
    </style>
</head>

<body>
    <h1>Expected Orders</h1>
    <p>Assembly Quotation Ref: {{ $quotation_ref }}</p>
    <p>Expected Order Date: {{ $expected_order_date }}</p>
    <p>Expected Delivery Date: {{ $expected_delivery_date }}</p>

    <table>
        <thead>
            <tr>
                <th>SR NO.</th>
                <th>ARTICLE NUMBER</th>
                <th>DESCRIPTION</th>
                <th>QTY</th>
                <th>PRODUCT NAME</th>
                <th>PRODUCT TYPE</th>
            </tr>
        </thead>
        <tbody>
            @foreach($quotation_items as $item)
            <tr>
                <td>{{ $item['sr_no'] }}</td>
                <td>{{ $item['full_article_number'] }}</td>
                <td>{{ $item['description'] }}</td>
                <td>{{ $item['qty'] }}</td>
                <td>{{ $item['cart_model_name'] }}</td>
                <td>{{ $item['product_type'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>