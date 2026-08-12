<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <title>{{ __('billing.razorpay_page_title') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/views/billing/razorpay-launch.css') }}">
</head>
<body>
    <div class="box">
        <div class="spinner"></div>
        <p>{{ __('billing.razorpay_launching_msg') }}</p>
    </div>

    {{-- Razorpay Checkout SDK — REQUIRED to load from checkout.razorpay.com
         per Razorpay's official integration documentation. Self-hosting
         this script is explicitly not supported by Razorpay: the SDK
         signs payment requests against their CDN-served version to
         prevent tampering, and is updated server-side without buyer
         intervention. Only loads on tenants who have enabled Razorpay
         as a payment gateway. --}}
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script>
        var options = {
            key: @json($keyId),
            order_id: @json($orderId),
            name: @json(config('app.name', 'LeadHub')),
            description: @json(__('billing.razorpay_app_description')),
            handler: function () { window.location.href = @json(url('/admin') . '?checkout=success&gateway=razorpay'); },
            modal: { ondismiss: function () { window.location.href = @json(url('/admin') . '?checkout=cancelled&gateway=razorpay'); } }
        };
        var rzp = new Razorpay(options);
        rzp.open();
    </script>
</body>
</html>
