<?php

namespace HiEvents\Http\Actions\Orders;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Stripe\Stripe;
use Stripe\Charge;

class StripeController extends Controller
{
    protected $stripe;
    public function __construct()
    {
        $this->stripe = Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function checkoutStripe()
    {
        return view('checkout-stripe');
    }

    public function processPayment(Request $request)
    {
        try {
            $charge = Charge::create([
                'amount' => $request->input('amount') * 100, // Convert to cents
                'currency' => 'usd',
                'description' => 'Payment Description',
                'source' => $request->input('stripeToken'),
            ]);
            if ($charge->status == 'succeeded') {
                // Payment successful, redirect to confirmation page
                return redirect()->back()->with('payment_success', 'Payment successful!');
            } else {
                // Payment failed, display error message
                return redirect()->back()->withErrors(['payment_error' => 'Payment failed. Please try again.']);
            }
        } catch (\Stripe\Exception\CardException $e) {
            return back()->withErrors(['payment_error' => $e->getMessage()]);
        } catch (\Exception $e) {
            \Log::error($e);
            return back()->withErrors(['payment_error' => 'Something went wrong. Please try again.']);
        }
    }
}
