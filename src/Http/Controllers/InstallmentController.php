<?php

namespace Webkul\Halkode\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Webkul\Halkode\Payment\Halkode;

class InstallmentController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected Halkode $halkode
    ) {
        //
    }

    /**
     * Get token to verify the merchant
     */
    public function getToken(): string
    {
        return Cache::remember('halkode_token', 3500, function () {
            $response = Http::timeout(20)->post($this->halkode->getPaymentUrl() . '/api/token', [
                'app_id'     => $this->halkode->getApplicationKey(),
                'app_secret' => $this->halkode->getAppSecret(),
            ]);

            $json = $response->json();

            return $json['data']['token'];
        });
    }

    /**
     * Installment payment information.
     */
    public function index(Request $request)
    {
        $token = $this->getToken();

        $response = Http::timeout(20)
            ->withToken($token)
            ->acceptJson()
            ->post($this->halkode->getPaymentUrl() . '/api/getpos', [
                'credit_card'   => $request->credit_card,
                'amount'        => (float) $request->amount,
                'currency_code' => 'TRY',
                'merchant_key'  => $this->halkode->getMerchantKey(),
            ]);

        if ($response->failed()) {
            throw new \Exception('error: ' . $response->body());
        }

        return $response->json();
    }
}
