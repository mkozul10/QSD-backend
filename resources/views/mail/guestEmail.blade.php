<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }

        h2, h3 {
            color: #333;
        }

        div {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div>
        <h2>Order Details</h2>

        <div>
            <strong>Address:</strong> {{ $data['address'] }}<br>
            <strong>City:</strong> {{ $data['city'] }}<br>
            <strong>Zip Code:</strong> {{ $data['zip_code'] }}<br>
            <strong>Phone:</strong> {{ $data['phone'] }}<br>
            <strong>Transaction ID:</strong> {{ $data['transaction_id'] }}<br>
            <strong>Price:</strong> ${{ $data['price'] }}<br>
            <strong>Email:</strong> {{ $user }}<br>
            <strong>Order ID:</strong> {{ $data['id'] }}<br>
            <strong>Created At:</strong> {{ $data['created_at'] }}<br>
            <strong>Updated At:</strong> {{ $data['updated_at'] }}<br>
        </div>

        <h3>Products</h3>

        <table border="1">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['products'] as $product)
                    <tr>
                        <td>{{ $product['product']['name'] }}</td>
                        <td>{{ $product['pivot']['quantity'] }}</td>
                        <td>${{ $product['product']['price'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
