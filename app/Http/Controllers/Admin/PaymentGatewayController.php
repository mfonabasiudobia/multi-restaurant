<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentGatewayRequest;
use App\Models\PaymentGateway;
use App\Models\BankDetail;
use Illuminate\Http\Request;
use App\Repositories\PaymentGatewayRepository;

class PaymentGatewayController extends Controller
{
    /**
     * Show payment gateway
     */
    public function index()
    {
        $paymentGateways = PaymentGatewayRepository::getAll();
        $bankDetails = BankDetail::first();
        \Log::info('bank_details', ['bank_details' => $bankDetails]);

        return view('admin.payment-gateway.index', compact('paymentGateways', 'bankDetails'));
    }
    public function updateBankDetails(Request $request)
    {
        // Validate the input
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'iban' => 'required|string|max:34',
            'swift_bic' => 'required|string|max:11',
            'bank_name' => 'required|string|max:255',
        ]);

        $bankDetails = BankDetail::first();

        if ($bankDetails) {
            // Update the existing bank details
            $bankDetails->update($validated);
        } else {
            // Create new bank details if not present
            BankDetail::create($validated);
        }

        return back()->withSuccess(__('Bank Details Updated Successfully'));
    }

    /**
     * Update payment gateway
     */
    public function update(PaymentGatewayRequest $request, PaymentGateway $paymentGateway)
    {
        if (app()->environment() == 'local') {
            return back()->with('demoMode', 'You can not update the payment gateway in demo mode');
        }

        PaymentGatewayRepository::updateByRequest($request, $paymentGateway);

        return back()->withSuccess(__('Payment Gateway Updated Successfully'));
    }

    /**
     * Toggle payment gateway status
     */
    public function toggle(PaymentGateway $paymentGateway)
    {

        if (app()->environment() == 'local') {
            return back()->with('demoMode', 'You can not update the payment gateway status in demo mode');
        }

        $paymentGateway->update([
            'is_active' => ! $paymentGateway->is_active,
        ]);

        return back()->withSuccess(__('Status Updated Successfully'));
    }
}
