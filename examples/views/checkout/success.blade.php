<!-- resources/views/checkout/success.blade.php -->
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>پرداخت موفق</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Tahoma, Arial, sans-serif;
            background-color: #f8f9fa;
        }
        .success-container {
            max-width: 600px;
            margin: 80px auto;
            text-align: center;
        }
        .success-card {
            background-color: #fff;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }
        .success-icon {
            font-size: 80px;
            color: #28a745;
            margin-bottom: 20px;
        }
        .reference-id {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            font-weight: bold;
            margin: 20px auto;
            max-width: 300px;
        }
    </style>
</head>
<body>
    <div class="container success-container">
        <div class="success-card">
            <div class="success-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="currentColor" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                </svg>
            </div>
            
            <h1 class="mb-4">پرداخت موفقیت‌آمیز</h1>
            
            <p class="mb-3">سفارش شما با موفقیت ثبت شد و پرداخت انجام گردید.</p>
            
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            
            <div class="reference-id">
                کد پیگیری: {{ session('referenceId', 'NA') }}
            </div>
            
            <p class="text-muted small mb-4">
                رسید پرداخت به ایمیل شما ارسال خواهد شد.
            </p>
            
            <div class="mt-4">
                <a href="{{ route('home') }}" class="btn btn-primary me-2">بازگشت به صفحه اصلی</a>
                <a href="{{ route('user.orders') }}" class="btn btn-outline-secondary">مشاهده سفارش‌ها</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>