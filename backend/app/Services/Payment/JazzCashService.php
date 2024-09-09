<?php

namespace HiEvents\Services\Payment;

use Exception;
use AKCybex\JazzCash\Facades\JazzCash;
use HiEvents\backend\exceptions\ApplicationException;

class JazzCashService
{
    public function initiatePayment($amount, $order_id, $returnUrl): array
    {
        $txnDateTime = now()->format('YmdHis');
        $txnExpiryDateTime = now()->addDay()->format('YmdHis');
        $txnRefNumber = 'T' . $txnDateTime;

        $data = [
            'pp_Version' => '1.1',
            'pp_TxnType' => '',
            'pp_MerchantID' => config('jazzcash.merchant_id'),
            'pp_Language' => 'EN',
            'pp_SubMerchantID' => '',
            'pp_Password' => config('jazzcash.password'),
            'pp_TxnRefNo' => $txnRefNumber,
            'pp_Amount' => $amount * 100, // Convert to lowest currency unit
            'pp_TxnCurrency' => 'PKR',
            'pp_TxnDateTime' => $txnDateTime,
            'pp_TxnExpiryDateTime' => $txnExpiryDateTime,
            'pp_BillReference' => $order_id,
            'pp_Description' => 'Order payment',
            'pp_ReturnURL' => $returnUrl,
            'pp_SecureHash' => '',
            'ppmpf_1' => '1',
            'ppmpf_2' => '2',
            'ppmpf_3' => '3',
            'ppmpf_4' => '4',
            'ppmpf_5' => '5',
        ];
        $data['pp_SecureHash'] = $this->calculateSecureHash($data);

        return $data;
    }

    private function calculateSecureHash(array $data): string
    {
        $hashString = '';
        $salt = config('jazzcash.integrity_salt');

        foreach ($data as $key => $value) {
            if (!empty($value)) {
                $hashString .= "&$key=$value";
            }
        }

        $hashString = $salt . $hashString;
        return hash_hmac('sha256', $hashString, $salt);
    }

    public function verifyPayment(array $response): bool
    {
        if ($response['pp_ResponseCode'] !== '000') {
            return false;
        }

        $calculatedHash = $this->calculateSecureHash($response);
        return $calculatedHash === $response['pp_SecureHash'];
    }
}