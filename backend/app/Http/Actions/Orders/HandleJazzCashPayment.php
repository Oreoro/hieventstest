<?php

namespace HiEvents\Http\Actions\Orders;

use Illuminate\Support\Facades\Log;
use HiEvents\Http\Actions\BaseAction;
use HiEvents\Services\Payment\JazzCashService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use HiEvents\Models\Order;
// Assuming OrderCompleted event is in App\Events namespace

class HandleJazzCashPayment extends BaseAction
{
    private JazzCashService $jazzCashService;

    public function __construct(JazzCashService $jazzCashService)
    {
        $this->jazzCashService = $jazzCashService;
    }

    public function handle()
    {
        $request = request();
        
        
        $event_id = $request->route('event_id');
        $order_id = $request->route('order_id');

        Log::info('JazzCash response received', [
            'data' => $request->all(),
            'headers' => $request->headers->all(),
            'ip' => $request->ip()
        ]);

        $response = $request->all();
        $isVerified = $this->jazzCashService->verifyPayment($response);

        if ($isVerified) {
            Log::info('JazzCash payment verified', ['event_id' => $event_id, 'order_id' => $order_id]);
            // Update order status to completed
            try {
                $order = Order::where('id', $order_id)->firstOrFail();
                $order->update(['status' => 'completed']);
                
                
                
                return response()->json(['success' => true, 'message' => 'Payment successful']);
            } catch (\Exception $e) {
                Log::error('Error updating order after JazzCash payment', [
                    'event_id' => $event_id,
                    'order_id' => $order_id,
                    'error' => $e->getMessage()
                ]);
                return response()->json(['success' => false, 'message' => 'Payment received but order update failed'], 500);
            }
        } else {
            Log::warning('JazzCash payment failed', ['event_id' => $event_id, 'order_id' => $order_id]);
            return response()->json(['success' => false, 'message' => 'Payment verification failed'], 400);
        }
    }
}