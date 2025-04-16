<!-- resources/views/checkout/index.blade.php -->
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تکمیل سفارش</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Tahoma, Arial, sans-serif;
            background-color: #f8f9fa;
        }
        .checkout-container {
            max-width: 800px;
            margin: 40px auto;
        }
        .order-summary {
            background-color: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }
        .price {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container checkout-container">
        <div class="row">
            <div class="col-12 text-center mb-4">
                <h1>تکمیل سفارش</h1>
            </div>
        </div>

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <div class="row">
            <div class="col-md-7">
                <div class="order-summary mb-4">
                    <h3>اطلاعات سفارش #{{ $order->id }}</h3>
                    <hr>

                    <div class="mb-3">
                        <strong>نام مشتری:</strong> {{ $order->customer_name }}
                    </div>

                    @if($order->customer_email)
                        <div class="mb-3">
                            <strong>ایمیل:</strong> {{ $order->customer_email }}
                        </div>
                    @endif

                    @if($order->customer_phone)
                        <div class="mb-3">
                            <strong>تلفن همراه:</strong> {{ $order->customer_phone }}
                        </div>
                    @endif

                    <hr>

                    <h4>محصولات</h4>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>محصول</th>
                                    <th>تعداد</th>
                                    <th>قیمت واحد</th>
                                    <th>قیمت کل</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->orderItems as $item)
                                    <tr>
                                        <td>{{ $item->product_name }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>{{ number_format($item->unit_price) }} تومان</td>
                                        <td>{{ number_format($item->quantity * $item->unit_price) }} تومان</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-5">
                <div class="order-summary">
                    <h3>خلاصه سفارش</h3>
                    <hr>

                    <div class="d-flex justify-content-between mb-2">
                        <span>مجموع محصولات:</span>
                        <span class="price">{{ number_format($order->subtotal) }} تومان</span>
                    </div>

                    @if($order->tax > 0)
                        <div class="d-flex justify-content-between mb-2">
                            <span>مالیات:</span>
                            <span class="price">{{ number_format($order->tax) }} تومان</span>
                        </div>
                    @endif

                    @if($order->shipping > 0)
                        <div class="d-flex justify-content-between mb-2">
                            <span>هزینه ارسال:</span>
                            <span class="price">{{ number_format($order->shipping) }} تومان</span>
                        </div>
                    @endif

                    @if($order->discount > 0)
                        <div class="d-flex justify-content-between mb-2 text-success">
                            <span>تخفیف:</span>
                            <span class="price">{{ number_format($order->discount) }} تومان</span>
                        </div>
                    @endif

                    <hr>

                    <div class="d-flex justify-content-between mb-4">
                        <span><strong>مبلغ قابل پرداخت:</strong></span>
                        <span class="price fs-5">{{ number_format($order->total_amount) }} تومان</span>
                    </div>

                    <div class="d-grid">
                        <a href="{{ route('payment.pay', $order->id) }}" class="btn btn-primary btn-lg">
                            پرداخت با زرین‌پال
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>