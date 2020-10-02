<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PaymentController extends Controller {
    public function pay(Request $request) {
        $data = $request->validate([
            'value' => 'required|numeric|min:5',
            'currency' => 'required|exists:App\Models\Currency,iso',
            'payment_platform' => 'required|exists:App\Models\PaymentPlatform,id'
        ]);

        dd($data);
    }

    public function approval() {
        
    }

    public function cancelled() {
        
    }
}
