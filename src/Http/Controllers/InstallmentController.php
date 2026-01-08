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
        protected CommissionRateController $commission,
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
    public function index(Request $request): array
    {
        $token = $this->getToken();

        $commissionRates = $this->commission->index();

        $response = Http::timeout(20)
            ->withToken($token)
            ->acceptJson()
            ->post($this->halkode->getPaymentUrl() . '/api/getpos', [
                'merchant_key'  => $this->halkode->getMerchantKey(),
                'credit_card'   => $request->credit_card,
                'amount'        => $request->amount,
                'currency_code' => 'TRY',
            ]);

        $installments = [];

        foreach ($response['data'] as $item) {
            $num = (int) $item['installments_number'];

            $program = strtolower(trim($item['card_program'] ?? 'default'));

            $rate =
                $commissionRates[$num][$program]
                ?? $commissionRates[$num]['default']
                ?? 0;

            if ($num === 1) {
                $gross = $request->amount;
                $net = round($gross * (1 - $rate), 2);
                $net = round($gross * (1 - $rate), 2);
            } else {
                $gross = round($request->amount / (1 - $rate), 2);
                $net = round($gross * (1 - $rate), 2);
            }

            $installments[] = [
                'installments_number' => $num,
                'amount'             => number_format($gross, 2, '.', ''),
                'monthly_amount'     => number_format($gross / $num, 2, '.', ''),
                'merchant_net'       => number_format($net, 2, '.', ''),
                'commission_amount'  => number_format($gross - $net, 2, '.', ''),
                'commission_rate'    => number_format($rate * 100, 2) . '%',
                'currency'           => $item['currency_code'],
                'card_program'       => $program,
            ];
        }

        usort($installments, fn ($a, $b) => $a['installments_number'] <=> $b['installments_number']);

        return $installments;
    }
}
