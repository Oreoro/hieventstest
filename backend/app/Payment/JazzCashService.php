<?php

namespace HiEvents\Services\Payment;

use Exception;
use AKCybex\JazzCash\Facades\JazzCash;
use HiEvents\backend\exceptions\ApplicationException;

class JazzCashService
{
    public function createPayment(float $amount, string $orderId): array
    {
        
            return JazzCash::request()
                ->setAmount($amount)
                ->setTransactionId($orderId)
                ->toArray();
        
    }

    public function verifyPayment(array $response): bool
    {
       
        return $response['pp_ResponseCode'] === '000';
    }

    // Consider
}