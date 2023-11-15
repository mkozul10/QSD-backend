<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
        }
        table {
            width: 100%;
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-collapse: collapse;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        table td {
            padding: 20px;
            text-align: center;
            border: 1px solid #eeeeee;
        }
        .verification-code {
            font-size: 28px;
            font-weight: bold;
            color: #3498db;
            margin-bottom: 10px;
        }
        .instructions {
            font-size: 16px;
            color: #555555;
            margin-bottom: 20px;
        }
        .additional-info {
            font-size: 14px;
            color: #777777;
        }
        .footer {
            padding: 10px;
            background-color: #f2f2f2;
            border-top: 1px solid #dddddd;
            border-bottom-left-radius: 8px;
            border-bottom-right-radius: 8px;
        }
    </style>
</head>
<body>

<table>
    <tr>
        <td>
            <p>Hello, {{$user->name}}</p>
            <p class="verification-code">{{ $number }}</p>
            <p class="instructions">Please enter this code to verify your email.</p>
            <p class="additional-info">This verification code is valid and available for use for next 2 hours</p>
        </td>
    </tr>
    <tr>
        <td class="footer">
            <p class="additional-info">Sent by QSD Webshop &bull; {{ now()->format('F d, Y H:i:s') }}</p>
        </td>
    </tr>
</table>

</body>
</html>