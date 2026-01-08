<?php

namespace Webkul\Halkode\Listeners;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Webkul\Admin\Listeners\Base;
use Webkul\Admin\Mail\Order\RefundedNotification;
use Webkul\Halkode\Payment\Halkode;

class Refund extends Base
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
     * After order is created
     */
    public function afterCreated(\Webkul\Sales\Contracts\Refund $refund): void
    {
        $this->refundOrder($refund);

        try {
            if (! core()->getConfigData('emails.general.notifications.emails.general.notifications.new_refund')) {
                return;
            }

            $this->prepareMail($refund, new RefundedNotification($refund));
        } catch (\Exception $e) {
            report($e);
        }
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
     * After Refund is created
     */
    public function refundOrder(\Webkul\Sales\Contracts\Refund $refund): void
    {
        $order = $refund->order;

        $token = $this->getToken();

        if ($order->payment->method === 'halkode') {
            if ($refund->total_qty > 0) {
                $response = Http::timeout(20)
                    ->withToken($token)
                    ->post($this->halkode->getPaymentUrl() . '/api/refund', [
                        'merchant_key'   => $this->halkode->getMerchantKey(),
                        'invoice_id'     => $order->payment->additional['invoice_id'],
                        'amount'         => number_format($order->grand_total, 2, '.', ''),
                    ]);

                $json = $response->json();

                logger()->info('Halkode Refund Response', $json);
            }
        }
    }
}
