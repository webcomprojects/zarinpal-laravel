<!-- resources/views/checkout/failed.blade.php -->
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>خطا در پرداخت</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Tahoma, Arial, sans-serif;
            background-color: #f8f9fa;
        }
        .failed-container {
            max-width: 600px;
            margin: 80px auto;
            text-align: center;
        }
        .failed-card {
            background-color: #fff;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }
        .failed-icon {
            font-size: 80px;
            color: #dc3545;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container failed-container">
        <div class="failed-card">
            <div class="failed-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="currentColor" class="bi bi-x-circle-fill" viewBox="0 0 16 16">
                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z"/>
                </svg>
            </div>
            
            <h1 class="mb-4">خطا در پرداخت</h1>
            
            <p class="mb-3">متأسفانه مشکلی در روند پرداخت رخ داده است.</p>
            
            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
            
            <p class="text-muted small mb-4">
                لطفاً مجدداً تلاش کنید یا با پشتیبانی تماس بگیرید.
            </p>
            
            <div class="mt-4">
                <a href="{{ url()->previous() }}" class="btn btn-primary me-2">تلاش مجدد</a>
                <a href="{{ route('home') }}" class="btn btn-outline-secondary">بازگشت به صفحه اصلی</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>