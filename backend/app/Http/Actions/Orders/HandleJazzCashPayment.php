<?php

namespace HiEvents\Http\Actions\Orders;

use HiEvents\Http\Actions\BaseAction;
use HiEvents\Services\Payment\JazzCashService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class HandleJazzCashPayment extends BaseAction
{
    private JazzCashService $jazzCashService;

    public function __construct(JazzCashService $jazzCashService)
    {
        $this->jazzCashService = $jazzCashService;
    }

    public function handle(Request $request, $eventId, $orderShortId): RedirectResponse
{
    $response = $request->all();
    $isVerified = $this->jazzCashService->verifyPayment($response);

    if ($isVerified) {
        // Update order status to completed
        // Redirect to order summary page
        return redirect()->route('order.summary', ['eventId' => $eventId, 'orderShortId' => $orderShortId]);
    } else {
        // Handle payment failure
        return redirect()->route('order.payment', ['eventId' => $eventId, 'orderShortId' => $orderShortId, 'payment_failed' => true]);
    }
}
}