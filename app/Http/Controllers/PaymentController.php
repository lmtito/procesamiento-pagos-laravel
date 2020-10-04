<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PayPalService;

class PaymentController extends Controller {
    public function pay(Request $request) {
        $rules = [
            'value' => 'required|numeric|min:5',
            'currency' => 'required|exists:App\Models\Currency,iso',
            'payment_platform' => 'required|exists:App\Models\PaymentPlatform,id'
        ];
        $request->validate($rules);
        $paymentPlatform = resolve(PayPalService::class);
        return $paymentPlatform->handlePayment($request);
    }

    public function approval() {
        $paymentPlatform = resolve(PayPalService::class);
        return $paymentPlatform->handleApproval();
    }

    public function cancelled() {
        //
    }
}
