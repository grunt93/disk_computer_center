<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>重設密碼通知</title>
    <style>
        body {
            font-family: '微軟正黑體', sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
            background-color: #f8f9fa;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .header {
            background-color: #0d6efd;
            color: white;
            padding: 20px;
            text-align: center;
            border-top-left-radius: 5px;
            border-top-right-radius: 5px;
        }

        .content {
            padding: 20px;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #0d6efd !important;
            color: white !important;
            text-decoration: none;
            border-radius: 5px;
        }

        .footer {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>重設密碼通知</h1>
        </div>
        <div class="content">
            <p>親愛的使用者您好：</p>
            <p>我們收到了您重設密碼的請求。請點擊下方按鈕進行密碼重設：</p>

            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ $url }}" class="btn">
                    重設密碼
                </a>
            </div>

            <p>如果您沒有要求重設密碼，請忽略此郵件。</p>
            <p>此連結將在 {{ config('auth.passwords.users.expire') }} 分鐘後失效。</p>

            <div class="footer">
                如果您無法點擊「重設密碼」按鈕，請複製下列網址至瀏覽器中開啟：<br>
                {{ $url }}
            </div>
        </div>
    </div>
</body>

</html>