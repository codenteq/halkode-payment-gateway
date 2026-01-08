<?php

namespace Webkul\Halkode\Payment;

use Illuminate\Support\Facades\Storage;
use Webkul\Payment\Payment\Payment;

class Halkode extends Payment
{
    /**
     * Payment method code
     *
     * @var string
     */
    protected $code  = 'halkode';

    /**
     * Get redirect url.
     */
    public function getRedirectUrl(): string
    {
        return route('halkode.redirect');
    }

    /**
     * Check if payment method is available.
     *
     * @return bool
     */
    public function isAvailable(): bool
    {
        return parent::isAvailable() && $this->hasValidCredentials();
    }

    /**
     * Get payment method title.
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->getConfigData('title') ?? trans('halkode::app.halkode.system.title');
    }

    /**
     * Get payment method description.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->getConfigData('description') ?? trans('halkode::app.halkode.system.description');
    }

    /**
     * Get payment method image/logo.
     *
     * @return string|null
     */
    public function getImage(): ?string
    {
        $url = $this->getConfigData('image');

        return $url ? Storage::url($url) : asset('vendor/halkode/images/halkode.svg');
    }

    /**
     * Get merchant key from configuration.
     *
     * @return string|null
     */
    public function getMerchantKey(): ?string
    {
        return $this->getConfigData('merchant_key');
    }

    /**
     * Get app secret from configuration.
     *
     * @return string|null
     */
    public function getAppSecret(): ?string
    {
        return $this->getConfigData('app_secret');
    }

    /**
     * Get application key from configuration.
     *
     * @return string|null
     */
    public function getApplicationKey(): ?string
    {
        return $this->getConfigData('application_key');
    }

    /**
     * Check if sandbox mode is enabled.
     *
     * @return bool
     */
    public function isSandbox(): bool
    {
        return (bool) $this->getConfigData('sandbox');
    }

    /**
     * Get payment gateway URL based on environment.
     *
     * @return string
     */
    public function getPaymentUrl(): string
    {
        return $this->isSandbox()
            ? 'https://testapp.halkode.com.tr/ccpayment'
            : 'https://app.halkode.com.tr/ccpayment';
    }

    /**
     * Validate merchant credentials.
     *
     * @return bool
     */
    public function hasValidCredentials(): bool
    {
        return ! empty($this->getMerchantKey()) && ! empty($this->getAppSecret());
    }
}
