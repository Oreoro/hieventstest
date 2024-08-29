<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JazzCash Payment</title>
</head>
<body>
    <form name="redirect-to-payment-gateway" method="POST" action="{{ config('jazzcash.' . env('JAZZCASH_ENVIRONMENT') . '.endpoint') }}">
        @csrf
        @foreach($data as $key => $value)
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endforeach
        <button type="submit">Pay with JazzCash</button>
    </form>
</body>
</html>
