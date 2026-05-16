<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>App is live</title>
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
            App Live Notification
        </div>
        <div class="content">
            Our app is now live. Please click the button below to go to the app store.
            <br><br>
            <a href="{{ $linkIOS }}" class="button">Go to App Store</a>
            <a href="{{ $linkAndroid }}" class="button">Go to Play Store</a>
            <br><br>
            Thank you for your patience.
            <br><br>
            Regards,<br>
            {{ config('app.name') }}
        </div>
    </div>
</body>

</html>
