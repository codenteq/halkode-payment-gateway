<?php

namespace Webkul\Halkode\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Webkul\Halkode\Payment\Halkode;

class CommissionRateController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected Halkode $halkode
    )
    {
        //
    }

    /**
     * Get token to verify the merchant
     */
    public function getToken(): string
    {
        return Cache::remember('halkode_token', 3500, function () {
            $response = Http::timeout(20)->post($this->halkode->getPaymentUrl() . '/api/token', [
                'app_id' => $this->halkode->getApplicationKey(),
                'app_secret' => $this->halkode->getAppSecret(),
            ]);

            $json = $response->json();

            return $json['data']['token'];
        });
    }

    /**
     * Get merchant commissions information.
     */
    public function index()
    {
        $token = $this->getToken();

        $response = Http::timeout(20)
            ->withToken($token)
            ->acceptJson()
            ->post($this->halkode->getPaymentUrl() . '/api/commissions', [
                'merchant_key' => $this->halkode->getMerchantKey(),
            ]);

        $rates = [];

        foreach ($response['data'] as $installment => $items) {
            foreach ($items as $item) {
                if (!isset($item['merchant_commission_percentage'])) {
                    continue;
                }

                $cardProgram = strtolower(trim($item['card_program'] ?? 'default'));

                $rates[(int)$installment][$cardProgram] =
                    ((float)$item['merchant_commission_percentage']) / 100;
            }
        }

        return $rates;
    }
}
