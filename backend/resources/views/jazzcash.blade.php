<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JazzCash Payment</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f0f0f0;
        }
        form {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <form name="jsform" method="post" action="https://sandbox.jazzcash.com.pk/CustomerPortal/transactionmanagement/merchantform/">
        <input type="hidden" name="pp_Version" value="1.1">
        <input type="hidden" name="pp_TxnType" value="MWALLET">
        <input type="hidden" name="pp_Language" value="EN">
        <input type="hidden" name="pp_MerchantID" value="{{ config('jazzcash.merchant_id') }}">
        <input type="hidden" name="pp_SubMerchantID" value="">
        <input type="hidden" name="pp_Password" value="{{ config('jazzcash.password') }}">
        <input type="hidden" name="pp_BankID" value="TBANK">
        <input type="hidden" name="pp_ProductID" value="RETL">
        <input type="hidden" name="pp_TxnRefNo" value="{{ $txnRefNo }}">
        <input type="hidden" name="pp_Amount" value="{{ $amount }}">
        <input type="hidden" name="pp_TxnCurrency" value="PKR">
        <input type="hidden" name="pp_TxnDateTime" value="{{ $txnDateTime }}">
        <input type="hidden" name="pp_BillReference" value="{{ $billRef }}">
        <input type="hidden" name="pp_Description" value="{{ $description }}">
        <input type="hidden" name="pp_TxnExpiryDateTime" value="{{ $txnExpiryDateTime }}">
        <input type="hidden" name="pp_ReturnURL" value="{{ $returnUrl }}">
        <input type="hidden" name="pp_SecureHash" value="{{ $secureHash }}">
        <input type="hidden" name="ppmpf_1" value="1">
        <input type="hidden" name="ppmpf_2" value="2">
        <input type="hidden" name="ppmpf_3" value="3">
        <input type="hidden" name="ppmpf_4" value="4">
        <input type="hidden" name="ppmpf_5" value="5">
        <button type="submit">Pay with JazzCash</button>
    </form>
</body>
</html>
