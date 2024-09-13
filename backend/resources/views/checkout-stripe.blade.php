<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stripe Payment</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        @if (session('payment_success'))
            <div class="alert alert-success">
                {{ session('payment_success') }}
            </div>
        @elseif(session('payment_error'))
            <div class="alert alert-danger">
                {{ session('payment_error') }}
            </div>
        @else
        @endif
        <h2 class="text-center mb-4">Stripe Payment</h2>
        <form id="payment-form" action="{{ route('process.payment') }}" method="POST" class="row g-3">
            @csrf
            <div class="col-md-6">
                <label for="cardholderName" class="form-label">Cardholder's Name</label>
                <input type="text" class="form-control" id="cardholderName" name="cardholderName" required>
            </div>

            <div class="col-md-6">
                <label for="amount" class="form-label">Amount</label>
                <input type="text" class="form-control" id="amount" name="amount" required>
            </div>

            <!-- Stripe Card Element -->
            <div class="col-12">
                <label for="card-element" class="form-label">Credit or Debit Card</label>
                <div id="card-element" class="form-control">
                    <!-- A Stripe Element will be inserted here. -->
                </div>
                <!-- Error message -->
                <div id="card-errors" role="alert" class="text-danger mt-2"></div>
            </div>

            <div class="col-12">
                <button type="submit" class="btn btn-primary w-100" id="submit-button">Submit Payment</button>
            </div>
        </form>
    </div>

    <!-- Bootstrap JS and Stripe JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        // Initialize Stripe with your publishable key
        const stripe = Stripe('{{ env('STRIPE_KEY') }}');

        // Create an instance of Elements
        const elements = stripe.elements();

        // Create an instance of the card Element
        const card = elements.create('card', {
            style: {
                base: {
                    fontSize: '16px',
                    color: '#32325d',
                },
            },
        });

        // Add the card Element to the DOM
        card.mount('#card-element');

        // Handle real-time validation errors from the card Element
        card.on('change', function(event) {
            const displayError = document.getElementById('card-errors');
            if (event.error) {
                displayError.textContent = event.error.message;
            } else {
                displayError.textContent = '';
            }
        });

        // Handle form submission
        const form = document.getElementById('payment-form');
        form.addEventListener('submit', function(event) {
            event.preventDefault();

            stripe.createToken(card).then(function(result) {
                if (result.error) {
                    // Inform the user if there was an error
                    const errorElement = document.getElementById('card-errors');
                    errorElement.textContent = result.error.message;
                } else {
                    // Send the token to your server
                    stripeTokenHandler(result.token);
                }
            });
        });

        // Submit the form with the token ID
        function stripeTokenHandler(token) {
            // Insert the token ID into the form so it gets submitted to the server
            const form = document.getElementById('payment-form');
            const hiddenInput = document.createElement('input');
            hiddenInput.setAttribute('type', 'hidden');
            hiddenInput.setAttribute('name', 'stripeToken');
            hiddenInput.setAttribute('value', token.id);
            form.appendChild(hiddenInput);

            // Submit the form
            form.submit();
        }
    </script>
</body>

</html>
