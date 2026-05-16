<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enquiry Feedback</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            font-size: 20px;
            color: #333;
        }

        .content {
            margin-top: 20px;
            line-height: 1.6;
            color: #666;
        }

        .button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            Hello {{ $user->name ?? 'User' }} New device login alert ...
        </div>
        <div class="content">
            We noticed a new device login to your account at {{ now()->format('Y-m-d H:i:s') }} from {{ $device ?? '' }}.
            <br><br>
            Regards,<br>
            {{ config('app.name') }}
        </div>
    </div>
</body>

</html>
