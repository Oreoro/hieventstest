<?php

namespace HiEvents\Http\Actions\Orders;

use HiEvents\Http\Actions\BaseAction;
use HiEvents\Services\Payment\JazzCashService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use HiEvents\Repository\Eloquent\OrderRepository;

class InitiateJazzCashPayment extends BaseAction
{
    private JazzCashService $jazzCashService;
    private OrderRepository $orderRepository;

    public function __construct(JazzCashService $jazzCashService, OrderRepository $orderRepository)
    {
        $this->jazzCashService = $jazzCashService;
        $this->orderRepository = $orderRepository;
    }
    public function handle(Request $request, $eventId, $orderShortId): JsonResponse
    {
        $order = $this->orderRepository->findByShortId($orderShortId);
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }
        $amount = $order->getTotalGross();
        $data = $this->jazzCashService->initiatePayment($amount, $order->getId());
        return response()->json([
            'payment_data' => $data,
            'redirect_url' => config('jazzcash.' . env('JAZZCASH_ENVIRONMENT') . '.endpoint') . '?' . http_build_query($data)
        ]);
    }
}

