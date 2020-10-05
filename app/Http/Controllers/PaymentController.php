<?php

namespace App\Http\Controllers;

use App\Models\PaymentPlatform;
use App\Resolvers\PaymentPlatformResolver;
use Illuminate\Http\Request;
use App\Services\PayPalService;

class PaymentController extends Controller {
    protected $paymentPlatformResolver;

    public function __construct(PaymentPlatformResolver $paymentPlatformResolver) {
        $this->middleware('auth');
        $this->paymentPlatformResolver = $paymentPlatformResolver;
    }

    public function pay(Request $request) {
        $rules = [
            'value' => 'required|numeric|min:5',
            'currency' => 'required|exists:App\Models\Currency,iso',
            'payment_platform' => 'required|exists:App\Models\PaymentPlatform,id'
        ];
        $request->validate($rules);
        $paymentPlatform = $this->paymentPlatformResolver->resolveService($request->payment_platform);
        session()->put('paymentPlatformId', $request->payment_platform);
        return $paymentPlatform->handlePayment($request);
    }

    public function approval() {
        if (session()->has('paymentPlatformId')) {
            $paymentPlatform = $this->paymentPlatformResolver->resolveService(session()->get('paymentPlatformId'));
            $paymentPlatform = resolve(PayPalService::class);
            return $paymentPlatform->handleApproval();
        }
        return redirect()
            ->route('home')
            ->withErrors('No podemos obtener la plataforma de pago elegida. Por favor, vuelva a intentar.');
    }

    public function cancelled() {
        return redirect()
            ->route('home')
            ->withErrors('Has cancelado el pago.');
    }
}
