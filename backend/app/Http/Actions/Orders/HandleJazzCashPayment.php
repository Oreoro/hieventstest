<?php

namespace HiEvents\Http\Actions\Orders;

use HiEvents\Http\Actions\BaseAction;
use HiEvents\Services\Payment\JazzCashService;
use Illuminate\Http\Request;

class HandleJazzCashPayment extends BaseAction
{
    private JazzCashService $jazzCashService;

    public function __construct(JazzCashService $jazzCashService)
    {
        $this->jazzCashService = $jazzCashService;
    }

    public function handle(Request $request)
    {
        $response = $request->all();
        $isVerified = $this->jazzCashService->verifyPayment($response);

        if ($isVerified) {
            // Update order status to completed
            // Redirect to order summary page
            return redirect()->route('order.summary', ['eventId' => $response['pp_BillReference'], 'orderShortId' => $response['pp_TxnRefNo']]);
        } else {
            // Handle payment failure
            return redirect()->route('order.payment', ['eventId' => $response['pp_BillReference'], 'orderShortId' => $response['pp_TxnRefNo'], 'payment_failed' => true]);
        }
    }
}