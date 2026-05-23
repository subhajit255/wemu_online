<!DOCTYPE html>
<html lang="en">
<head>
    <title>WEMU - Pending Approval</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: #060608;
            color: #fff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .bg-mesh {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            background:
                radial-gradient(circle at 12% 20%, rgba(76,29,149,0.42) 0%, transparent 45%),
                radial-gradient(circle at 88% 80%, rgba(225,29,72,0.36) 0%, transparent 45%),
                radial-gradient(circle at 55% 50%, rgba(37,99,235,0.28) 0%, transparent 50%);
            background-color: #060608;
            animation: bgBreathe 16s ease-in-out infinite alternate;
        }
        @keyframes bgBreathe {
            0%   { opacity: 0.85; transform: scale(1); }
            100% { opacity: 1;    transform: scale(1.07); }
        }
        .auth-card {
            background: rgba(255,255,255,0.022);
            backdrop-filter: blur(40px);
            -webkit-backdrop-filter: blur(40px);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 22px;
            padding: 50px 40px;
            width: 100%;
            max-width: 500px;
            margin: 20px;
            box-shadow: 0 24px 60px rgba(0,0,0,0.55), inset 0 1px 0 rgba(255,255,255,0.08);
            position: relative;
            z-index: 10;
            text-align: center;
        }
        .icon-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: rgba(99,102,241,0.12);
            border: 2px solid rgba(99,102,241,0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
        }
        .icon-circle i {
            font-size: 32px;
            color: #6366f1;
        }
        .step-heading {
            font-size: 28px;
            font-weight: 800;
            margin: 0 0 16px;
            letter-spacing: 0.5px;
        }
        .step-sub {
            font-size: 15px;
            color: rgba(255,255,255,0.6);
            line-height: 1.6;
            margin-bottom: 30px;
        }
        .btn-logout {
            background: rgba(255,255,255,0.05);
            color: #fff;
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 12px;
            padding: 12px 24px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }
        .btn-logout:hover {
            background: rgba(255,255,255,0.1);
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="bg-mesh"></div>
    <div class="auth-card">
        <div class="icon-circle">
            <i class="fas fa-hourglass-half"></i>
        </div>
        <h2 class="step-heading">Account Under Review</h2>
        <p class="step-sub">
            Your artist application is currently pending admin approval. 
            We are reviewing your identity verification documents and will notify you once approved. 
            Please check back later!
        </p>
        <a href="{{ route('artist.logout') }}" class="btn-logout">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
</body>
</html>
