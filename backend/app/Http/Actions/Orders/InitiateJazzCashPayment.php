<?php

namespace HiEvents\Http\Actions\Orders;

use AKCybex\JazzCash\Facades\JazzCash;
use HiEvents\Http\Actions\BaseAction;
use HiEvents\Services\Payment\JazzCashService;
use Illuminate\Http\Request;
use HiEvents\DomainObjects\AbstractDomainObject;
use Illuminate\Support\Facades\Log;
use Exception;

class InitiateJazzCashPayment extends BaseAction
{
    private JazzCashService $jazzCashService;

    public function __construct(JazzCashService $jazzCashService)
    {
        $this->jazzCashService = $jazzCashService;
    }

    public function handle(Request $request, $eventId, $ticketId)
    {
        $amount = $request->input('amount');
        $orderId = $ticketId; // Use ticket ID as order ID for simplicity

        $redirectUrl = $this->jazzCashService->initiatePayment($amount, $orderId);

        return response()->json(['redirect_url' => $redirectUrl]);
    }

  
}
