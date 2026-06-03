<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>404 - الصفحة غير موجودة</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">

    <style>
        body {
            margin: 0;
            padding: 0;
            background: linear-gradient(to bottom, #e0f7fa, #b2ebf2);
            font-family: 'Tajawal','Cairo', sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            color: #006064;
            text-align: center;
        }

        .fish-icon {
            width: 140px;
            height: 140px;
            margin-bottom: 20px;
        }

        h1 {
            font-size: 80px;
            margin: 0;
        }

        h2 {
            font-size: 28px;
            margin-top: 10px;
        }

        p {
            font-size: 18px;
            margin-top: 10px;
            color: #004d40;
        }

        a {
            margin-top: 25px;
            display: inline-block;
            background: #00acc1;
            color: white;
            padding: 12px 24px;
            border-radius: 10px;
            text-decoration: none;
            font-size: 16px;
            transition: background 0.3s ease;
        }

        a:hover {
            background: #00838f;
        }
    </style>
</head>
<body>

<!-- أيقونة سمكة SVG -->
<svg class="fish-icon" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg">
    <path d="M2 32C10 20 22 14 34 14C46 14 58 22 62 32C58 42 46 50 34 50C22 50 10 44 2 32Z" fill="#00BCD4"/>
    <circle cx="45" cy="32" r="3" fill="#004D40"/>
    <path d="M8 32L2 26M8 32L2 38" stroke="#00796B" stroke-width="2"/>
</svg>

<h1>404</h1>
<h2>الصفحة غير موجودة</h2>
<p>عذرًا، يبدو أن هذه الصفحة ضاعت في أعماق البحر 🐟</p>
<a href="javascript:history.back()">العودة إلى الصفحة السابقة</a>

</body>
</html>
