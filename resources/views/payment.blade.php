<!DOCTYPE html>
<html>
<head>
    <title>Send Request</title>
</head>
<body>
<form method="POST" action="{{ config('payment.driver.paystar.payment_address') }}">
    @csrf
    <input type="hidden" value="{{$token}}" name="token">
    <button type="submit">Send</button>
</form>
</body>
</html>
