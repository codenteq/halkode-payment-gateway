<?php

use Webkul\Halkode\Payment\Halkode;

return [
    'halkode'  => [
        'class'        => Halkode::class,
        'code'         => 'halkode',
        'title'        => 'Halk Öde',
        'description'  => 'Halk Öde',
        'active'       => true,
        'sandbox'      => true,
        'merchant_key' => 'MERCHANT_KEY',
        'app_secret'   => 'APP_SECRET',
        'sort'         => 1,
    ],
];