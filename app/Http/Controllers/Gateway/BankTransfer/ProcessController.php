<?php

namespace App\Http\Controllers\Gateway;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\BankDetail;

class ProcessController extends Controller
{
    /**
     * Process Bank Transfer Payment
     *
     * @param string $paymentGateway
     * @param Payment $payment
     * @return array
     */
    public static function processBankTransfer($paymentGateway, Payment $payment)
    {
        $bankDetails = BankDetail::first();

        if (!$bankDetails) {
            return ['error' => 'Bank details are not configured.'];
        }

        $description = 'Total order ' . $payment->orders->count() . ' total amount ' . $payment->amount . ' Order IDs: ' . implode(',', $payment->orders->pluck('id')->toArray());

        return [
            'company_name' => $bankDetails->company_name,
            'iban' => $bankDetails->iban,
            'swift_bic' => $bankDetails->swift_bic,
            'bank_name' => $bankDetails->bank_name,
            'amount' => $payment->amount,
            'description' => $description,
        ];
    }
}