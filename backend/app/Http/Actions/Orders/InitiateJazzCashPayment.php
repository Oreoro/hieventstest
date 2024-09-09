<?php

namespace HiEvents\Http\Actions\Orders;

use HiEvents\Http\Actions\BaseAction;
use HiEvents\Services\Payment\JazzCashService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use HiEvents\Repository\Eloquent\OrderRepository;
use Illuminate\Support\Facades\Log;
use HiEvents\DomainObjects\Interfaces\DomainObjectInterface;

class InitiateJazzCashPayment extends BaseAction
{
    private JazzCashService $jazzCashService;
    private OrderRepository $orderRepository;

    public function __construct(JazzCashService $jazzCashService, OrderRepository $orderRepository)
    {
        $this->jazzCashService = $jazzCashService;
        $this->orderRepository = $orderRepository;
    }

    public function __invoke(Request $request, $event_id, $order_id)
    {
        Log::info('Received request method: ' . $request->method());
        Log::info('Request data: ' . json_encode($request->all()));

           // Cast $order_id to integer
           //Check if $order_id is numeric before casting

        if (!preg_match('/^o_[a-zA-Z0-9]+$/', $order_id)) {
            abort(400, 'Invalid order ID format');
        }

        Log::info('Searching for order with ID: ' . $order_id);
        $order = $this->orderRepository->findById($order_id);
        Log::info('Order found: ' . ($order ? 'Yes' : 'No'));

        if (!$order) {
            abort(404, 'Order not found');
        }

        $amount = $order->getTotalGross();
        $returnUrl = "http://localhost/public/jazzcash/response/{$event_id}/{$order_id}";
        $formData = $this->jazzCashService->initiatePayment($amount, $order->getId(), $returnUrl);
        
        Log::info('JazzCash form data: ' . json_encode($formData));

        return response()->json([
            'formData' => $formData,
            'postUrl' => 'https://sandbox.jazzcash.com.pk/CustomerPortal/transactionmanagement/merchantform/'
        ]);
    }
}

