<?php

namespace HiEvents\Services\Payment;

use Exception;
use AKCybex\JazzCash\Facades\JazzCash;
use HiEvents\backend\exceptions\ApplicationException;

class JazzCashService
{
    public function initiatePayment($amount): array
    {
        try {
            $data = JazzCash::request()->setAmount($amount)->toArray();

            return $data;
        } catch (Exception $e) {
            throw new ApplicationException('Failed to initiate JazzCash payment: ' . $e->getMessage());
        }
    }

    public function verifyPayment(array $response): bool
    {
        return $response['pp_ResponseCode'] === '000';
    }
}