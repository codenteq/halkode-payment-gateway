<?php

use Webkul\Halkode\Payment\Halkode;

return [
    'halkode'  => [
        'class'           => Halkode::class,
        'code'            => 'halkode',
        'title'           => 'Halk Öde',
        'description'     => 'Halk Öde',
        'active'          => true,
        'sandbox'         => true,
        'merchant_key'    => 'MERCHANT_KEY',
        'app_secret'      => 'APP_SECRET',
        'application_key' => 'APPLICATION_KEY',
        'sort'            => 1,
    ],
];
